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

  <style>
    body {
      background: linear-gradient(135deg, var(--primary-color), var(--primary-hover), var(--accent-color));
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
    }

    .login-card {
      background: var(--overlay-light);
      padding: 30px 30px;
      border-radius: 15px;
      border: 1px solid rgba(68, 54, 39, 0.3);
      color: var(--text-dark);
      box-shadow: 0 4px 30px rgba(68, 54, 39, 0.4);
      width: 100%;
      max-width: 320px;
      backdrop-filter: blur(10px);
    }

    .login-card h3 {
      margin-bottom: 25px;
      font-weight: 600;
      color: var(--text-dark);
    }

    .btn-login {
      width: 100%;
      background-color: var(--accent-color);
      color: var(--text-dark);
      font-weight: bold;
      transition: 0.3s;
      border: none;
    }

    .btn-login:hover {
      background-color: var(--secondary-hover);
      color: var(--text-dark);
    }

    .form-control:focus {
      border-color: var(--accent-color);
      box-shadow: 0 0 5px rgba(217, 131, 36, 0.4);
    }

    .form-text {
      text-align: center;
      color: var(--text-dark);
      font-size: 0.9rem;
      margin-top: 10px;
    }

    a {
      background-color: var(--accent-color);
      padding: 2px 0.5rem 0.3rem;
      color: var(--text-dark);
      backdrop-filter: blur(20px);
      border-radius: 6px;
      text-decoration: none;
      font-weight: 600;
    }

    a:hover {
      color: var(--text-dark);
      background-color: var(--secondary-hover);
    }

    /* Chrome, Safari, Edge, Opera */
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
      -webkit-appearance: none;
      margin: 0;
    }

    /* Firefox */
    input[type=number] {
      -moz-appearance: textfield;
    }

    input:focus,
    textarea:focus,
    select:focus {
      outline: none;
      box-shadow: none;
      border-color: transparent;
    }
  </style>
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