<?php
session_start();
include 'funcs/connect.php';

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
  <title>Join Our Varsity Club</title>
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
      overflow: hidden;
    }
    .form-container {
      background: var(--background-light);
      border-radius: 15px;
      box-shadow: 0 5px 20px rgba(68, 54, 39, 0.3);
      max-width: 600px;
      width: 100%;
      overflow: hidden;
      max-height: 90vh;
      overflow-y: auto;
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
  </style>
</head>
<body>
  <div class="form-container">
    <div class="form-header">
      <h2>Join Our Varsity Club</h2>
      <p>Become part of an amazing community of students who share your passion and interests.</p>
    </div>
    <div class="form-body">
      <form action="register.php" method="POST">
        <div class="mb-3">
          <label for="name" class="form-label">Full Name *</label>
          <input type="text" class="form-control" id="name" name="name" placeholder="Enter your full name" required>
        </div>
        <div class="mb-3">
          <label for="studentId" class="form-label">Varsity ID Number *</label>
          <input type="number" class="form-control" id="studentId" name="studentId" placeholder="e.g., 2023001234" required>
        </div>
        <div class="mb-3">
          <label for="department" class="form-label">Department *</label>
          <select class="form-select" id="department" name="department" required>
            <option value="">Select your department</option>
            <option value="CSE">Computer Science & Engineering</option>
            <option value="EEE">Electrical & Electronic Engineering</option>
            <option value="BBA">Business Administration</option>
            <option value="TFD">Textile Fashion Design</option>
            <option value="LHR">Law & Human Rights</option>
            <option value="PHR">Pharmacy</option>
            <option value="ENG">English</option>
          </select>
        </div>
        <div class="mb-3">
          <label for="mail" class="form-label">Varsity Email Address *</label>
          <input type="email" class="form-control" id="mail" name="mail" placeholder="e.g., yourname@university.edu" required>
        </div>
        <div class="mb-3">
          <label for="batch" class="form-label">Batch Number *</label>
          <input type="number" class="form-control" id="batch" name="batch" placeholder="e.g., 2023 or 48" required min="1" max="31">
        </div>
        <div class="mb-3">
          <label for="phone" class="form-label">Phone Number *</label>
          <input type="tel" class="form-control" id="phone" name="phone" placeholder="e.g., 01712345678" required pattern="[0-9]{11}">
        </div>
        <div class="mb-3">
          <label for="bloodGroup" class="form-label">Blood Group *</label>
          <select class="form-select" id="bloodGroup" name="bloodGroup" required>
            <option value="">Select blood group</option>
            <option value="A+">A+</option>
            <option value="A-">A-</option>
            <option value="B+">B+</option>
            <option value="B-">B-</option>
            <option value="AB+">AB+</option>
            <option value="AB-">AB-</option>
            <option value="O+">O+</option>
            <option value="O-">O-</option>
          </select>
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password *</label>
          <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required minlength="6">
        </div>
        <div class="mb-3">
          <label for="confirm_password" class="form-label">Confirm Password *</label>
          <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required>
        </div>

        <!-- Clubs Selection (Multiple) -->
        <div class="mb-3">
          <label class="form-label">Select Clubs (You can choose multiple) *</label>
          <div class="row">
            <?php foreach ($clubs as $club): ?>
              <div class="col-md-6 mb-2">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="clubs[]" value="<?php echo $club['id']; ?>" 
                         id="club_<?php echo $club['id']; ?>">
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

        <button type="submit" class="btn btn-submit">Submit Application</button>
        <div class="form-footer mt-3 text-center" style="font-size: 13px; color: #666;">
          Already have an account? <a href="login.php">Login here</a><br>
          By submitting this form, you agree to be contacted via your varsity email address regarding club activities and events.
        </div>
      </form>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

