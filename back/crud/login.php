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

  <style>
    body {
      background: linear-gradient(135deg, #1e00ffff, #000000ff, #093b9fff);
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .login-card {
      background: linear-gradient(135deg, #ffffff43, #fff7f732);
      /* filter: drop-shadow(0 0 40px #ffe346ff); */
      padding: 30px 30px;
      border-radius: 15px;
      /* -webkit-backdrop-filter: blur(10px); */
      /* Safari support */
      border: 1px solid rgba(255, 255, 255, 0.4);
      color: white;
      box-shadow: 0 4px 30px rgba(0, 0, 0, 0.3);
      width: 100%;
      max-width: 320px;

      backdrop-filter: blur(330px);

    }

    .login-card h3 {
      margin-bottom: 25px;
      font-weight: 600;
      color: black;
    }

    .btn-login {
      width: 100%;
      background-color: #409cffff;
      font-weight: bold;
      transition: 0.3s;
    }

    .btn-login:hover {
      background-color: #0056b3;
      color: white;
      color: #fff;
    }

    .form-control:focus {
      border-color: #007bff;
      box-shadow: 0 0 5px rgba(0, 123, 255, 0.4);
    }

    .form-text {
      /* margin: 10px 0; */
      text-align: center;
      color: #dededee9;
      font-size: 0.9rem;
      margin-top: 10px;
    }

    a {
      background-color: rgba(55, 55, 255, 0.47);
      padding: 2px 0.5rem 0.3rem;
      color: orange;
      backdrop-filter: blur(20px);
      border-radius: 6px;
      text-decoration: none;
    }

    a:hover {
      color: blue;
      scale: (1.2);
      background-color: #ff8181e0;
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
    header("location: myaccount.php");
    exit();
  }
}
?>