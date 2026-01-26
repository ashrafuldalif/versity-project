<?php
include 'funcs/connect.php';
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Page</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/root.css">
  <link rel="stylesheet" href="assets/css/scroll-fix.css">
  <link rel="stylesheet" href="assets/css/login.css">

</head>

<body>

  <div class="login-card text-center">
    <h3>Member Login</h3>
    <form action="login.php" method="POST">
      <div class="mb-3 text-start">
        <label for="studentId" class="form-label">Student ID</label>
        <input type="number" name="studentId" id="studentId" class="form-control" placeholder="Enter your ID" required>
      </div>
      <div class="mb-3 text-start">
        <label for="password" class="form-label">Password</label>
        <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password" required>
      </div>
      <button type="submit" name="submit" class="btn btn-login mt-2">Login</button>

      <p class="form-text">dont have an account? <a href="register.php">register here</a></p>
    </form>

  </div>
  <script src="http://localhost:35729/livereload.js"></script>
</body>

</html>

<?php

if (isset($_POST['submit'])) {

  $id =  $_POST['studentId'];
  $pass =  $_POST['password'];
  $_SESSION["id"] = $id;
  $_SESSION["pass"] = $pass;

  $sql = "SELECT * FROM `club_members` WHERE id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param('i', $id);
  $stmt->execute();
  $result = $stmt->get_result();

  $info = $result->fetch_assoc();

  if (password_verify($pass, $info['pass'])) {
    // echo "<h1 class= \" haha\">yo bitch</h1>";
    header("location: index.php");
    exit();
  }
}
?>