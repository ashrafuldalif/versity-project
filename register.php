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
  } elseif ($email && !str_ends_with($mail, '@rpsu.edu.bd')) {
    $error = "must use student email address.";
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
  <link rel="stylesheet" href="assets/css/register.css">

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
      <!-- 1. Replace your entire select block with this clean version -->
      <div class="mb-3">
        <label class="form-label">Select Clubs (You can choose multiple)</label>

        <!-- This is the fake select that looks exactly like form-select -->
        <div class="dropdown">
          <button class="form-select text-start d-flex justify-content-between align-items-center"
            type="button"
            data-bs-toggle="dropdown"
            data-bs-auto-close="outside"
            aria-expanded="false"
            id="clubsDropdownBtn">
            <span id="clubsText" class="text-muted">Click to select clubs...</span>
            <span class="ms-3"></span>
          </button>

          <!-- Dropdown list with checkboxes -->
          <ul class="dropdown-menu w-100 px-2" style="max-height: 300px; overflow-y: auto;">
            <?php foreach ($clubs as $club): ?>
              <li>
                <div class="form-check d-flex align-items-center gap-3 px-4 border-bottom h-auto justify-content-start ">
                  <input class=" club-checkbox"
                    type="checkbox"
                    value="<?= $club['id'] ?>"
                    id="club_<?= $club['id'] ?>"
                    <?= in_array($club['id'], $inputs['clubs'] ?? []) ? 'checked' : '' ?>
                    onchange="updateClubsDisplay()">
                  <label class="form-check-label w-100 py-2" for="club_<?= $club['id'] ?>">
                    <?= htmlspecialchars($club['name']) ?>
                  </label>
                </div>
              </li>
            <?php endforeach; ?>

            <?php if (empty($clubs)): ?>
              <li class="dropdown-item text-muted small">No clubs available</li>
            <?php endif; ?>
          </ul>
        </div>

        <!-- Selected chips (shown below) -->
        <div id="selected-clubs-display" class="mt-3"></div>

        <!-- Hidden inputs so form submits correctly -->
        <div id="hidden-clubs-inputs" class="d-none">
          <?php foreach ($inputs['clubs'] ?? [] as $id): ?>
            <input type="hidden" name="clubs[]" value="<?= $id ?>">
          <?php endforeach; ?>
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
      <p class="form-text">Already have an account? <a href="login.php" style="color:var(--accent-color); text-decoration:none; font-weight:600;">Login here</a></p>
    </form>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <!-- 2. Add this tiny script at the bottom (before </body>) -->
  <script>
    function updateClubsDisplay() {
      const checkedBoxes = document.querySelectorAll('.club-checkbox:checked');
      const display = document.getElementById('selected-clubs-display');
      const hiddenContainer = document.getElementById('hidden-clubs-inputs');
      const btnText = document.getElementById('clubsText');

      // Update button text
      if (checkedBoxes.length === 0) {
        btnText.textContent = 'Click to select clubs...';
        btnText.classList.add('text-muted');
      } else {
        btnText.textContent = `${checkedBoxes.length} club${checkedBoxes.length > 1 ? 's' : ''} selected`;
        btnText.classList.remove('text-muted');
      }

      // Rebuild chips + hidden inputs
      display.innerHTML = '';
      hiddenContainer.innerHTML = '';

      checkedBoxes.forEach(cb => {
        const id = cb.value;
        const name = cb.nextElementSibling.textContent.trim();

        const badge = document.createElement('span');
        badge.className = 'badge chips rounded-pill me-2 mb-2 py-2 px-3';
        badge.innerHTML = `
        ${name}
        <button type="button" class="btn-close btn-close-white ms-2" style="font-size:0.7em;"
                onclick="unselectClub(${id})"></button>
      `;
        display.appendChild(badge);

        hiddenContainer.innerHTML += `<input type="hidden" name="clubs[]" value="${id}">`;
      });
    }

    function unselectClub(id) {
      const cb = document.getElementById('club_' + id);
      if (cb) {
        cb.checked = false;
        updateClubsDisplay();
      }
    }

    // Init
    document.addEventListener('DOMContentLoaded', () => {
      updateClubsDisplay();

      // Make sure Bootstrap is loaded
      if (typeof bootstrap === 'undefined') {
        console.error('Bootstrap is not loaded!');
        return;
      }

      const dropdownBtn = document.getElementById('clubsDropdownBtn');

      // Initialize dropdown manually if needed
      if (dropdownBtn && !bootstrap.Dropdown.getInstance(dropdownBtn)) {
        new bootstrap.Dropdown(dropdownBtn);
      }

      // Prevent dropdown from closing when clicking inside
      const dropdownMenu = dropdownBtn.nextElementSibling;
      if (dropdownMenu) {
        dropdownMenu.addEventListener('click', function(e) {
          e.stopPropagation();
        });
      }
    });
  </script>
</body>

</html>