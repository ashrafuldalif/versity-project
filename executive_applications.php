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

  // Fix types: batch = integer, img = string (with fallback)
  $member_id_int   = (int)$member_id;
  $position_id_int = (int)$position_id;
  $club_id_int     = (int)$club_id;
  $batch_int       = (int)$user['batch'] ?? 0;               // Safe int fallback
  $img             = $user['img'] ?: 'default.jpg';      // No NULL

  // 1) Check whether this member already applied for the same club & position
  $chk = $conn->prepare("SELECT 1 FROM executives WHERE id = ? AND position_id = ? AND club_id = ? LIMIT 1");
  if ($chk) {
    $chk->bind_param('iii', $member_id_int, $position_id_int, $club_id_int);
    $chk->execute();
    $res = $chk->get_result();
    if ($res && $res->fetch_assoc()) {
      $msg = "<div class='alert alert-warning text-center'>You have already applied for this club and position.</div>";
      $chk->close();
    } else {
      $chk->close();

      // 2) Insert new application
      $ins = $conn->prepare("INSERT INTO executives 
        (id, name, position_id, email, phone, department, batch, blood_group, club_id, img, approved, active)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 1)");

      if ($ins) {
        $ins->bind_param(
          "isisssisis",
          $member_id_int,
          $user['name'],
          $position_id_int,
          $user['mail'],
          $user['phone'],
          $user['department'],
          $batch_int,
          $user['bloodGroup'],
          $club_id_int,
          $img
        );

        if ($ins->execute()) {
          // Redirect back to home page after successful application
          header('Location: index.php?app=success');
          exit;
        } else {
          // Insert failed (could be duplicate primary key or other DB error)
          $msg = "<div class='alert alert-danger text-center'>An error occurred while submitting your application. If you already applied, contact support.</div>";
        }
        $ins->close();
      } else {
        $msg = "<div class='alert alert-danger text-center'>Server error preparing the application.</div>";
      }
    }
  } else {
    $msg = "<div class='alert alert-danger text-center'>Server error checking existing applications.</div>";
  }
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
      overflow-x: hidden; /* Only hide horizontal overflow */
      overflow-y: auto;   /* Allow vertical scrolling */
      background: linear-gradient(125deg, #ffc98fff, white, orange);
      min-height: 100vh;
      display: flex;
      align-items: center;
      padding: 1rem 0;    /* Add padding for mobile */
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
