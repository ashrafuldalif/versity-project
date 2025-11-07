<?php
include 'funcs/connect.php';
session_start();

$error = '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Registration</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      overflow: hidden;
      background: linear-gradient(135deg, #667eea, #764ba2);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Poppins', sans-serif;
    }

    .form-container {
      background: #fff;
      border-radius: 20px;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
      width: 100%;
      max-width: 420px;
      padding: 35px 40px;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .form-container:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 35px rgba(0, 0, 0, 0.2);
    }

    .form-container h2 {
      text-align: center;
      font-weight: 700;
      color: #333;
      margin-bottom: 25px;
    }

    .form-label {
      font-weight: 500;
      color: #444;
    }

    .form-control,
    .form-select {
      border-radius: 12px;
      padding: 10px 14px;
      border: 1.5px solid #ccc;
      transition: all 0.3s ease;
    }

    .form-control:focus,
    .form-select:focus {
      border-color: #667eea;
      box-shadow: 0 0 5px rgba(102, 126, 234, 0.5);
    }

    .btn-custom {
      background: linear-gradient(135deg, #667eea, #764ba2);
      color: #fff;
      font-weight: 600;
      border: none;
      border-radius: 12px;
      padding: 10px 15px;
      width: 100%;
      transition: all 0.3s ease;
    }

    .btn-custom:hover {
      background: linear-gradient(135deg, #5a6fd6, #6a3f91);
      transform: scale(1.03);
    }

    .form-text {
      text-align: center;
      color: #666;
      font-size: 0.9rem;
      margin-top: 10px;
    }

    .text-danger {
      font-size: 0.875rem;
      text-align: center;
      margin-top: 10px;
    }
  </style>
</head>

<body>

  <div class="form-container">
    <h2>Student Registration</h2>

    <?php if ($error): ?>
      <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form action="" method="POST" class="form-group">

      <!-- Name + ID -->
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="name" class="form-label">Full Name</label>
          <input type="text" name="name" class="form-control" placeholder="Enter your name"
            value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
        </div>
        <div class="col-md-6">
          <label for="studentId" class="form-label">Student ID</label>
          <input type="text" name="studentId" class="form-control" placeholder="e.g. 10901234"
            value="<?php echo isset($_POST['studentId']) ? htmlspecialchars($_POST['studentId']) : ''; ?>" required>
        </div>
      </div>

      <!-- Batch + Department -->
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="batch" class="form-label">Batch</label>
          <input type="number" name="batch" class="form-control" placeholder="e.g. 31" min="1" max="31"
            value="<?php echo isset($_POST['batch']) ? htmlspecialchars($_POST['batch']) : ''; ?>" required>
        </div>
        <div class="col-md-6">
          <label for="department" class="form-label">Department</label>
          <select name="department" class="form-select" required>
            <option value="" disabled <?php echo !isset($_POST['department']) ? 'selected' : ''; ?>>Select</option>
            <option value="CSE" <?php echo ($_POST['department'] ?? '') === 'CSE' ? 'selected' : ''; ?>>CSE</option>
            <option value="EEE" <?php echo ($_POST['department'] ?? '') === 'EEE' ? 'selected' : ''; ?>>EEE</option>
            <option value="BBA" <?php echo ($_POST['department'] ?? '') === 'BBA' ? 'selected' : ''; ?>>BBA</option>
            <option value="TFD" <?php echo ($_POST['department'] ?? '') === 'TFD' ? 'selected' : ''; ?>>TFD</option>
            <option value="LHR" <?php echo ($_POST['department'] ?? '') === 'LHR' ? 'selected' : ''; ?>>LHR</option>
            <option value="PHR" <?php echo ($_POST['department'] ?? '') === 'PHR' ? 'selected' : ''; ?>>Pharmacy</option>
            <option value="ENG" <?php echo ($_POST['department'] ?? '') === 'ENG' ? 'selected' : ''; ?>>English</option>
          </select>
        </div>
      </div>

      <!-- Email (FULL WIDTH) -->
      <div class="mb-3">
        <label for="mail" class="form-label">Student Email</label>
        <input type="email" name="mail" class="form-control" placeholder="example@student.edu"
          value="<?php echo isset($_POST['mail']) ? htmlspecialchars($_POST['mail']) : ''; ?>" required>
      </div>

      <!-- Phone + Blood Group -->
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="phone" class="form-label">Phone Number</label>
          <input type="text" name="phone" class="form-control" placeholder="01XXXXXXXXX" maxlength="11"
            value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>" required>
        </div>
        <div class="col-md-6">
          <label for="bloodGroup" class="form-label">Blood Group</label>
          <select name="bloodGroup" class="form-select" required>
            <option value="" disabled <?php echo !isset($_POST['bloodGroup']) ? 'selected' : ''; ?>>Select</option>
            <option value="A+" <?php echo ($_POST['bloodGroup'] ?? '') === 'A+' ? 'selected' : ''; ?>>A+</option>
            <option value="A-" <?php echo ($_POST['bloodGroup'] ?? '') === 'A-' ? 'selected' : ''; ?>>A-</option>
            <option value="B+" <?php echo ($_POST['bloodGroup'] ?? '') === 'B+' ? 'selected' : ''; ?>>B+</option>
            <option value="B-" <?php echo ($_POST['bloodGroup'] ?? '') === 'B-' ? 'selected' : ''; ?>>B-</option>
            <option value="AB+" <?php echo ($_POST['bloodGroup'] ?? '') === 'AB+' ? 'selected' : ''; ?>>AB+</option>
            <option value="AB-" <?php echo ($_POST['bloodGroup'] ?? '') === 'AB-' ? 'selected' : ''; ?>>AB-</option>
            <option value="O+" <?php echo ($_POST['bloodGroup'] ?? '') === 'O+' ? 'selected' : ''; ?>>O+</option>
            <option value="O-" <?php echo ($_POST['bloodGroup'] ?? '') === 'O-' ? 'selected' : ''; ?>>O-</option>
          </select>
        </div>
      </div>

      <!-- Password + Confirm Password -->
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="password" class="form-label">Password</label>
          <input type="password" name="password" class="form-control" placeholder="Create password" required>
        </div>
        <div class="col-md-6">
          <label for="confirm_password" class="form-label">Confirm Password</label>
          <input type="password" name="confirm_password" class="form-control" placeholder="Retype password" required>
        </div>
      </div>

      <button type="submit" name="submit" class="btn btn-custom">Register Now</button>
      <p class="form-text">Already have an account? <a href="login.php" style="color:#667eea; text-decoration:none;">Login here</a></p>
    </form>
  </div>

  <script src="http://localhost:35729/livereload.js"></script>
</body>

</html>

<?php
if ($_SERVER["REQUEST_METHOD"] ?? '' === "POST") {
  // ---- 1. Gather & sanitise ----
  $name       = trim(htmlspecialchars($_POST['name'] ?? ''));
  $stdId      = trim(htmlspecialchars($_POST['studentId'] ?? ''));
  $batch      = (int)trim($_POST['batch'] ?? 0);
  $mail       = filter_var($_POST['mail'] ?? '', FILTER_VALIDATE_EMAIL);
  $department = trim(htmlspecialchars($_POST['department'] ?? ''));
  $phone      = trim(htmlspecialchars($_POST['phone'] ?? ''));
  $blood      = trim(htmlspecialchars($_POST['bloodGroup'] ?? ''));
  $password   = $_POST['password'] ?? '';
  $confirm    = $_POST['confirm_password'] ?? '';
  $defaultImg = 'default.jpg';

  // ---- 2. Basic validation ----
  if (!$mail)                     $error = 'Invalid email address.';
  elseif (
    empty($name) || empty($stdId) || $batch < 1 || $batch > 31
    || empty($department) || empty($phone) || empty($blood)
    || empty($password) || empty($confirm)
  ) {
    $error = 'All fields are required.';
  } elseif (!preg_match('/^\d{11}$/', $phone)) {
    $error = 'Phone must be 11 digits.';
  } elseif ($password !== $confirm) {
    $error = 'Passwords do not match.';
  }

  // ---- 3. If no validation error → DB work ----
  if (!$error) {
    // ---- duplicate check ----
    $check = $conn->prepare(
      "SELECT id FROM club_members WHERE id = ? OR mail = ?"
    );
    if (!$check) {
      $error = 'DB error: ' . $conn->error;
    } else {
      $check->bind_param('ss', $stdId, $mail);
      $check->execute();
      $check->store_result();

      if ($check->num_rows > 0) {
        $error = 'Student ID or Email already registered.';
      }
      $check->close();
    }

    // ---- insert ----
    if (!$error) {
      $hashedPass = password_hash($password, PASSWORD_DEFAULT);
      $stmt = $conn->prepare(
        "INSERT INTO club_members
                 (img, id, name, department, batch, mail, phone, pass, bloodGroup)
                 VALUES (?,?,?,?,?,?,?,?,?)"
      );
      if (!$stmt) {
        $error = 'DB error: ' . $conn->error;
      } else {
        $stmt->bind_param(
          'ssssissss',               // <-- correct types
          $defaultImg,
          $stdId,
          $name,
          $department,
          $batch,
          $mail,
          $phone,
          $hashedPass,
          $blood
        );

        if ($stmt->execute()) {
          $_SESSION['id'] = $stdId;
          $stmt->close();
          header('Location: myaccount.php');
          exit;
        } else {
          $error = 'Insert failed: ' . $stmt->error;
        }
        $stmt->close();
      }
    }
  }
}
?>