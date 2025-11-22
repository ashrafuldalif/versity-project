<?php

/**
 * Admin Account Creation Script
 * 
 * WARNING: Delete this file after creating your admin account!
 * This script should only be run once to set up the initial admin account.
 * 
 * Usage: Navigate to http://your-domain/admin/create_admin.php
 */

include '../funcs/connect.php';


$message = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $email = trim($_POST['email'] ?? '');

    if (empty($username) || empty($password) || empty($email)) {
        $error = "All fields are required.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } else {
        // Check if admin already exists
        $checkStmt = $conn->prepare("SELECT id FROM admins WHERE username = ? OR email = ?");
        $checkStmt->bind_param('ss', $username, $email);
        $checkStmt->execute();
        $result = $checkStmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Admin with this username or email already exists.";
        } else {
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert admin
            $stmt = $conn->prepare("INSERT INTO admins (username, password, email) VALUES (?, ?, ?)");
            $stmt->bind_param('sss', $username, $hashedPassword, $email);

            if ($stmt->execute()) {
                $message = "Admin account created successfully! You can now <a href='login.php'>login here</a>.";
            } else {
                $error = "Error creating admin account: " . $stmt->error;
            }
            $stmt->close();
        }
        $checkStmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Admin Account</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1e00ffff, #000000ff, #093b9fff);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .setup-card {
            background: linear-gradient(135deg, #ffffff43, #fff7f732);
            padding: 40px;
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.4);
            color: white;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 450px;
            backdrop-filter: blur(330px);
        }

        .warning-box {
            background: rgba(255, 193, 7, 0.2);
            border: 1px solid rgba(255, 193, 7, 0.5);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="setup-card">
        <h3 class="text-center mb-4">Create Admin Account</h3>

        <div class="warning-box">
            <strong>⚠️ Security Warning:</strong><br>
            Delete this file after creating your admin account!
        </div>

        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" id="username" class="form-control"
                    placeholder="Enter username" required autofocus>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control"
                    placeholder="Enter email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control"
                    placeholder="Enter password (min 6 characters)" required minlength="6">
            </div>
            <button type="submit" class="btn btn-primary w-100">Create Admin Account</button>
        </form>

        <div class="text-center mt-3">
            <a href="login.php" class="text-white">Already have an account? Login</a>
        </div>
    </div>
</body>

</html>