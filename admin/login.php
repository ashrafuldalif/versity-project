<?php
session_start();
include '../funcs/connect.php';

// If already logged in as admin, redirect to admin dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: index.php");
    exit();
}

$error = "";

if (isset($_POST['submit'])) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password.";
    } else {
        $stmt = $conn->prepare("SELECT id, username, password, email FROM admins WHERE username = ?");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();
            
            if (password_verify($password, $admin['password'])) {
                // Set session variables
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                $_SESSION['admin_email'] = $admin['email'];

                // Update last login
                $updateStmt = $conn->prepare("UPDATE admins SET last_login = NOW() WHERE id = ?");
                $updateStmt->bind_param('i', $admin['id']);
                $updateStmt->execute();
                $updateStmt->close();

                // Redirect to admin dashboard
                header("Location: index.php");
                exit();
            } else {
                $error = "Invalid username or password.";
            }
        } else {
            $error = "Invalid username or password.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
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
            padding: 40px 40px;
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.4);
            color: white;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 400px;
            backdrop-filter: blur(330px);
        }

        .login-card h3 {
            margin-bottom: 30px;
            font-weight: 600;
            color: white;
            text-align: center;
        }

        .btn-login {
            width: 100%;
            background-color: #409cffff;
            font-weight: bold;
            transition: 0.3s;
            color: white;
            border: none;
        }

        .btn-login:hover {
            background-color: #0056b3;
            color: white;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.4);
        }

        .alert-danger {
            background-color: rgba(220, 53, 69, 0.9);
            border-color: rgba(220, 53, 69, 0.5);
            color: white;
        }

        input:focus,
        textarea:focus,
        select:focus {
            outline: none;
            box-shadow: none;
            border-color: transparent;
        }

        .admin-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="login-card text-center">
        <div class="admin-badge">🔐 Admin Panel</div>
        <h3>Admin Login</h3>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="mb-3 text-start">
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" id="username" class="form-control" 
                       placeholder="Enter your username" required autofocus>
            </div>
            <div class="mb-3 text-start">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" 
                       placeholder="Enter your password" required>
            </div>
            <button type="submit" name="submit" class="btn btn-login mt-3">Login</button>
        </form>
    </div>
</body>

</html>

