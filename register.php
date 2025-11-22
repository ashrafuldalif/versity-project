<?php
include 'funcs/connect.php';
session_start();

$error = '';
$inputs = []; // To store form values safely

// === 1. PROCESS FORM ONLY IF POSTED ===
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
  // Gather inputs safely
  $inputs['name']       = trim($_POST['name'] ?? '');
  $inputs['studentId']  = trim($_POST['studentId'] ?? '');
  $inputs['batch']      = trim($_POST['batch'] ?? '');
  $inputs['mail']       = $_POST['mail'] ?? '';
  $inputs['department'] = $_POST['department'] ?? '';
  $inputs['phone']      = trim($_POST['phone'] ?? '');
  $inputs['bloodGroup'] = $_POST['bloodGroup'] ?? '';
  $inputs['password']   = $_POST['password'] ?? '';
  $inputs['confirm_password'] = $_POST['confirm_password'] ?? '';
  $inputs['clubs'] = $_POST['clubs'] ?? []; // Array of club IDs (multiple selection)

  // Type casting
  $stdId = (int)$inputs['studentId'];
  $batch = (int)$inputs['batch'];
  $mail  = filter_var($inputs['mail'], FILTER_VALIDATE_EMAIL);
  $defaultImg = 'default.jpg';

  // === VALIDATION ===
  if (!$mail) {
    $error = 'Invalid email address.';
  } elseif (
    empty($inputs['name']) || $stdId <= 0 || $batch < 1 || $batch > 31 ||
    empty($inputs['department']) || empty($inputs['phone']) ||
    empty($inputs['bloodGroup']) || empty($inputs['password']) ||
    empty($inputs['confirm_password'])
  ) {
    $error = 'All fields are required.';
  } elseif (!preg_match('/^\d{11}$/', $inputs['phone'])) {
    $error = 'Phone must be 11 digits.';
  } elseif ($inputs['password'] !== $inputs['confirm_password']) {
    $error = 'Passwords do not match.';
  }

  // === DATABASE CHECK & INSERT ===
  if (!$error) {
    $check = $conn->prepare("SELECT id FROM club_members WHERE id = ? OR mail = ?");
    $check->bind_param('is', $stdId, $mail);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
      $error = 'Student ID or Email already registered.';
    } else {
      $hashedPass = password_hash($inputs['password'], PASSWORD_DEFAULT);
      $stmt = $conn->prepare("INSERT INTO club_members (img, id, name, department, batch, mail, phone, pass, bloodGroup) VALUES (?,?,?,?,?,?,?,?,?)");
      $stmt->bind_param('sississss', $defaultImg, $stdId, $inputs['name'], $inputs['department'], $batch, $mail, $inputs['phone'], $hashedPass, $inputs['bloodGroup']);

      if ($stmt->execute()) {
        // Insert club memberships
        if (!empty($inputs['clubs']) && is_array($inputs['clubs'])) {
          $clubStmt = $conn->prepare("INSERT INTO member_clubs (member_id, club_id) VALUES (?, ?)");
          foreach ($inputs['clubs'] as $clubId) {
            $clubId = (int)$clubId;
            if ($clubId > 0) {
              $clubStmt->bind_param('ii', $stdId, $clubId);
              $clubStmt->execute();
            }
          }
          $clubStmt->close();
        }


        $_SESSION['id'] = $stdId;
        $stmt->close();
        $check->close();
        header('Location: myaccount.php');
        exit;
      } else {
        $error = 'Registration failed. Please try again.';
      }
      $stmt->close();
    }
    $check->close();
  }
  } else {
  // Default empty values when page first loads
  $inputs = [
    'name' => '',
    'studentId' => '',
    'batch' => '',
    'mail' => '',
    'department' => '',
    'phone' => '',
    'bloodGroup' => '',
    'clubs' => []
  ];
}

// Fetch clubs for form
$clubsQuery = "SELECT id, name FROM clubs ORDER BY name ASC";
$clubsResult = $conn->query($clubsQuery);
$clubs = [];
while ($row = $clubsResult->fetch_assoc()) {
  $clubs[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Registration</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/root.css">
  <style>
    body {
      overflow: hidden;
      background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Poppins', sans-serif;
    }

    .form-container {
      background: var(--background-light);
      border-radius: 20px;
      box-shadow: 0 8px 25px rgba(68, 54, 39, 0.3);
      width: 100%;
      max-width: 600px;
      padding: 35px 40px;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      max-height: 90vh;
      overflow-y: auto;
    }

    .form-container:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 35px rgba(68, 54, 39, 0.4);
    }

    .form-container h2 {
      text-align: center;
      font-weight: 700;
      color: var(--text-dark);
      margin-bottom: 25px;
    }

    .form-label {
      font-weight: 500;
      color: var(--text-dark);
    }

    .form-control,
    .form-select {
      border-radius: 12px;
      padding: 10px 14px;
      border: 1.5px solid var(--accent-color);
      transition: all 0.3s ease;
    }

    .form-control:focus,
    .form-select:focus {
      border-color: var(--accent-color);
      box-shadow: 0 0 5px rgba(217, 131, 36, 0.5);
    }

    .btn-custom {
      background: var(--accent-color);
      color: var(--text-dark);
      font-weight: 600;
      border: none;
      border-radius: 12px;
      padding: 10px 15px;
      width: 100%;
      transition: all 0.3s ease;
    }

    .btn-custom:hover {
      background: var(--secondary-hover);
      transform: scale(1.03);
    }

    .form-text {
      text-align: center;
      color: var(--text-dark);
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
            value="<?php echo htmlspecialchars($inputs['name']); ?>" required>
        </div>
        <div class="col-md-6">
          <label for="studentId" class="form-label">Student ID</label>
          <input type="text" name="studentId" class="form-control" placeholder="e.g. 10901234"
            value="<?php echo htmlspecialchars($inputs['studentId']); ?>" required>
        </div>
      </div>

      <!-- Batch + Department -->
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="batch" class="form-label">Batch</label>
          <input type="number" name="batch" class="form-control" placeholder="e.g. 31" min="1" max="31"
            value="<?php echo  htmlspecialchars($inputs['batch']); ?>" required>
        </div>
        <div class="col-md-6">
          <label for="department" class="form-label">Department</label>
          <select name="department" class="form-select" required>
            <option value="" disabled <?php echo $inputs['department'] === '' ? 'selected' : ''; ?>>
              Select
            </option>
            <option value="CSE" <?php echo $inputs['department'] === 'CSE' ? 'selected' : ''; ?>>
              CSE

            </option>
            <option value="EEE" <?php echo $inputs['department'] === 'EEE' ? 'selected' : ''; ?>>
              EEE
            </option>
            <option value="BBA" <?php echo $inputs['department'] === 'BBA' ? 'selected' : ''; ?>>
              BBA
            </option>
            <option value="TFD" <?php echo $inputs['department'] === 'TFD' ? 'selected' : ''; ?>>
              TFD
            </option>
            <option value="LHR" <?php echo $inputs['department'] === 'LHR' ? 'selected' : ''; ?>>
              LHR
            </option>
            <option value="PHR" <?php echo $inputs['department'] === 'PHR' ? 'selected' : ''; ?>>
              Pharmacy
            </option>
            <option value="ENG" <?php echo $inputs['department'] === 'ENG' ? 'selected' : ''; ?>>
              English
            </option>
          </select>
        </div>
      </div>

      <!-- Email (FULL WIDTH) -->
      <div class="mb-3">
        <label for="mail" class="form-label">Student Email</label>
        <input type="email" name="mail" class="form-control" placeholder="example@student.edu"
          value="<?php echo htmlspecialchars($inputs['mail']); ?>" required>
      </div>

      <!-- Phone + Blood Group -->
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="phone" class="form-label">Phone Number</label>
          <input type="text" name="phone" class="form-control" placeholder="01XXXXXXXXX" maxlength="11"
            value="<?php echo htmlspecialchars($inputs['phone']); ?>" required>
        </div>
        <div class="col-md-6">
          <label for="bloodGroup" class="form-label">Blood Group</label>
          <select name="bloodGroup" class="form-select" required>
            <option value="" disabled <?php echo $inputs['bloodGroup'] === '' ? 'selected' : ''; ?>>
              Select
            </option>
            <option value="A+" <?php echo $inputs['bloodGroup'] === 'A+'  ? 'selected' : ''; ?>>A+</option>
            <option value="A-" <?php echo $inputs['bloodGroup'] === 'A-'  ? 'selected' : ''; ?>>A-</option>
            <option value="B+" <?php echo $inputs['bloodGroup'] === 'B+'  ? 'selected' : ''; ?>>B+</option>
            <option value="B-" <?php echo $inputs['bloodGroup'] === 'B-'  ? 'selected' : ''; ?>>B-</option>
            <option value="AB+" <?php echo $inputs['bloodGroup'] === 'AB+' ? 'selected' : ''; ?>>AB+</option>
            <option value="AB-" <?php echo $inputs['bloodGroup'] === 'AB-' ? 'selected' : ''; ?>>AB-</option>
            <option value="O+" <?php echo $inputs['bloodGroup'] === 'O+'  ? 'selected' : ''; ?>>O+</option>
            <option value="O-" <?php echo $inputs['bloodGroup'] === 'O-'  ? 'selected' : ''; ?>>O-</option>
          </select>
        </div>
      </div>

   <!-- Clubs Selection (Multiple) -->
   <div class="mb-3">
        <label class="form-label">Select Clubs (You can choose multiple)</label>
        <div class="row">
          <?php foreach ($clubs as $club): ?>
            
            <div class="col-md-6 mb-2">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="clubs[]" value="<?php echo $club['id']; ?>" 
                       id="club_<?php echo $club['id']; ?>"
                       <?php echo in_array($club['id'], $inputs['clubs']) ? 'checked' : ''; ?>>
                <label class="form-check-label" for="club_<?php echo $club['id']; ?>">
                  <?php echo htmlspecialchars($club['name']); ?>
                </label>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
        <?php if (empty($clubs)): ?>
          <p class="text-muted small">No clubs available. Please contact administrator.</p>
        <?php endif; ?>
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
      <p class="form-text">Already have an account? <a href="login.php" style="color:var(--accent-color); text-decoration:none; font-weight:600;">Login here</a></p>
    </form>
  </div>

  <script src="http://localhost:35729/livereload.js"></script>
</body>

</html>