<?php
session_start();
include 'funcs/connect.php';

if (!isset($_SESSION['id'])) {
  header("Location: login.php");
  exit;
}

$member_id = $_SESSION['id'];

// Get logged-in user data (hidden from user)
$stmt = $conn->prepare("SELECT name, department, batch, mail, phone, img, bloodGroup FROM club_members WHERE id = ?");
$stmt->bind_param("i", $member_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Get clubs & positions
$clubs = $conn->query("SELECT id, name FROM clubs ORDER BY name")->fetch_all(MYSQLI_ASSOC);
$positions = $conn->query("SELECT id, position_name FROM positions ORDER BY id")->fetch_all(MYSQLI_ASSOC);

$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $club_id     = (int)$_POST['club'];
  $position_id = (int)$_POST['position'];

  // Prepare the INSERT – 12 columns: 10 placeholders + 0,1 literals
  $ins = $conn->prepare("INSERT INTO executives 
        (id, name, position_id, email, phone, department, batch, blood_group, club_id, img, approved, active)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 1)");

  // Fix types: batch = integer, img = string (with fallback)
  $member_id_int   = (int)$member_id;
  $position_id_int = (int)$position_id;
  $club_id_int     = (int)$club_id;
  $batch_int       = (int)$user['batch'] ?? 0;               // Safe int fallback
  $img             = $user['img'] ?: 'defaultUser.jpg';      // No NULL

  // 10 variables → 10 type chars: i s i s s s i s i s
  $ins->bind_param(
    "isisssisis",         // 10 chars for 10 values
    $member_id_int,       // i
    $user['name'],        // s
    $position_id_int,     // i
    $user['mail'],        // s
    $user['phone'],       // s
    $user['department'],  // s
    $batch_int,           // i
    $user['bloodGroup'],  // s
    $club_id_int,         // i
    $img                  // s
  );

  if ($ins->execute()) {
    $msg = "<div class='alert alert-success text-center fw-bold'>Application Submitted Successfully!</div>";
  } else {
    $msg = "<div class='alert alert-danger text-center'>You have already applied for this position or an error occurred.</div>";
  }
  $ins->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Apply as Executive</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/root.css">
  <style>
    body {
      overflow: hidden;
      background: linear-gradient(125deg, #ffc98fff, white, orange);
      min-height: 100vh;
      display: flex;
      align-items: center;
    }

    .card {
      max-width: 420px;
      margin: auto;
      border-radius: 20px;
      box-shadow: var(--shadow-lg);
    }

    .btn-apply {
      background: #D98324;
      color: white;
      font-weight: 800;
      padding: 1rem 2rem;
      border-radius: 50px;
      font-size: 1.1rem;
    }
  </style>
</head>

<body>

  <div class="container">
    <div class="card p-4 p-md-5 text-center">
      <h2 class="mb-4 fw-bold" style="color: var(--text-dark);">Become an Executive</h2>

      <?= $msg ?>

      <form method="POST" class="mt-4">
        <div class="mb-4">
          <select name="club" class="form-select form-select-lg" required>
            <option value="">Choose Your Club</option>
            <?php foreach ($clubs as $c): ?>
              <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="mb-4">
          <select name="position" class="form-select form-select-lg" required>
            <option value="">Choose Position</option>
            <?php foreach ($positions as $p): ?>
              <option value="<?= $p['id'] ?>"><?= $p['position_name'] ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <button type="submit" class="btn btn-apply w-100">
          Submit Application
        </button>
      </form>

      <a href="index.php" class="btn btn-link mt-3 text-muted">Back to Home</a>
    </div>
  </div>

</body>

</html>