<?php
session_start();
include 'funcs/connect.php';

// Check if user is logged in
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

$memberId = (int)$_SESSION['id'];
$error = '';
$success = '';

// Fetch member info
$memberStmt = $conn->prepare("SELECT id, name, email, phone, department, batch, bloodGroup FROM club_members WHERE id = ?");
$memberStmt->bind_param('i', $memberId);
$memberStmt->execute();
$memberResult = $memberStmt->get_result();
$member = $memberResult->fetch_assoc();
$memberStmt->close();

if (!$member) {
    header('Location: login.php');
    exit();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clubId = isset($_POST['club_id']) ? (int)$_POST['club_id'] : 0;
    $positionId = isset($_POST['position_id']) ? (int)$_POST['position_id'] : 0;
    
    if ($clubId <= 0 || $positionId <= 0) {
        $error = 'Please select both club and position.';
    } else {
        // Check if executive request already exists
        $checkStmt = $conn->prepare("SELECT id FROM executives WHERE id = ?");
        $checkStmt->bind_param('i', $memberId);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        
        if ($checkResult->num_rows > 0) {
            // Update existing executive record
            $updateStmt = $conn->prepare("UPDATE executives SET position_id = ?, club_id = ?, approved = 0 WHERE id = ?");
            $updateStmt->bind_param('iii', $positionId, $clubId, $memberId);
            if ($updateStmt->execute()) {
                $success = 'Executive request updated successfully! It will be reviewed by admin.';
            } else {
                $error = 'Failed to update executive request. Please try again.';
            }
            $updateStmt->close();
        } else {
            // Create new executive record (pending approval)
            $batchStr = (string)$member['batch'];
            $insertStmt = $conn->prepare("INSERT INTO executives (id, name, position_id, email, phone, department, batch, club_id, blood_group, approved, active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 1)");
            $insertStmt->bind_param('isissssis', $memberId, $member['name'], $positionId, $member['email'], $member['phone'], $member['department'], $batchStr, $clubId, $member['bloodGroup']);
            if ($insertStmt->execute()) {
                $success = 'Executive request submitted successfully! It will be reviewed by admin.';
            } else {
                $error = 'Failed to submit executive request. Please try again.';
            }
            $insertStmt->close();
        }
        $checkStmt->close();
    }
}

// Fetch clubs and positions for form
$clubsQuery = "SELECT id, name FROM clubs ORDER BY name ASC";
$clubsResult = $conn->query($clubsQuery);
$clubs = [];
while ($row = $clubsResult->fetch_assoc()) {
    $clubs[] = $row;
}

$positionsQuery = "SELECT id, position_name FROM positions ORDER BY id ASC";
$positionsResult = $conn->query($positionsQuery);
$positions = [];
while ($row = $positionsResult->fetch_assoc()) {
    $positions[] = $row;
}

// Check if user already has an executive request
$existingExecStmt = $conn->prepare("SELECT position_id, club_id, approved FROM executives WHERE id = ?");
$existingExecStmt->bind_param('i', $memberId);
$existingExecStmt->execute();
$existingExecResult = $existingExecStmt->get_result();
$existingExec = $existingExecResult->fetch_assoc();
$existingExecStmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Request Executive Position - RPSU SWC</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/root.css">
  <style>
    body {
      background: linear-gradient(to bottom right, var(--primary-color), var(--accent-color));
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Poppins', sans-serif;
      padding: 20px;
      overflow: hidden;
    }
    .form-container {
      background: var(--background-light);
      border-radius: 15px;
      box-shadow: 0 5px 20px rgba(68, 54, 39, 0.3);
      max-width: 500px;
      width: 100%;
      overflow: hidden;
    }
    .form-header {
      background: var(--primary-color);
      color: var(--text-on-dark);
      text-align: center;
      padding: 40px 20px;
    }
    .form-body {
      padding: 30px;
    }
    .btn-submit {
      background-color: var(--accent-color);
      border: none;
      width: 100%;
      padding: 10px;
      color: var(--text-dark);
      border-radius: 10px;
      transition: 0.3s;
      font-weight: 600;
    }
    .btn-submit:hover {
      background-color: var(--secondary-hover);
    }
    .status-badge {
      padding: 8px 15px;
      border-radius: 20px;
      font-size: 0.9rem;
      font-weight: 500;
    }
    .status-pending {
      background-color: #fff3cd;
      color: #856404;
    }
    .status-approved {
      background-color: #d1e7dd;
      color: #0f5132;
    }
  </style>
</head>
<body>
  <div class="form-container">
    <div class="form-header">
      <h2>Request Executive Position</h2>
      <p>Apply to become an executive member of a club</p>
    </div>
    <div class="form-body">
      <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>
      
      <?php if ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
      <?php endif; ?>

      <?php if ($existingExec): ?>
        <div class="alert alert-info">
          <strong>Current Status:</strong><br>
          <span class="status-badge <?php echo $existingExec['approved'] ? 'status-approved' : 'status-pending'; ?>">
            <?php echo $existingExec['approved'] ? 'Approved' : 'Pending Approval'; ?>
          </span>
          <p class="mt-2 mb-0">You can update your request below.</p>
        </div>
      <?php endif; ?>

      <form method="POST" action="">
        <div class="mb-3">
          <label for="club_id" class="form-label">Select Club *</label>
          <select class="form-select" id="club_id" name="club_id" required>
            <option value="">Choose a club</option>
            <?php foreach ($clubs as $club): ?>
              <option value="<?php echo $club['id']; ?>" 
                      <?php echo ($existingExec && $existingExec['club_id'] == $club['id']) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($club['name']); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="mb-3">
          <label for="position_id" class="form-label">Select Position/Role *</label>
          <select class="form-select" id="position_id" name="position_id" required>
            <option value="">Choose a position</option>
            <?php foreach ($positions as $position): ?>
              <option value="<?php echo $position['id']; ?>" 
                      <?php echo ($existingExec && $existingExec['position_id'] == $position['id']) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($position['position_name']); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <button type="submit" class="btn btn-submit">
          <?php echo $existingExec ? 'Update Request' : 'Submit Request'; ?>
        </button>

        <div class="form-footer mt-3 text-center" style="font-size: 13px; color: #666;">
          <a href="myaccount.php">Back to My Account</a><br>
          <small>Your request will be reviewed by administrators.</small>
        </div>
      </form>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

