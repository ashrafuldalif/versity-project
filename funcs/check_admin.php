<?php
/**
 * Admin Authentication Check
 * Include this file at the top of every admin page to ensure only logged-in admins can access it
 */

session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Determine correct path to admin login page
    // If called from admin directory, use relative path, otherwise use full path
    $loginPath = 'login.php';
    if (strpos($_SERVER['PHP_SELF'], '/admin/') === false) {
        $loginPath = 'admin/login.php';
    }
    header("Location: " . $loginPath);
    exit();
}

// Optional: Verify admin still exists in database (security check)
if (isset($_SESSION['admin_id'])) {
    // Determine correct path to connect.php based on where this file is included from
    $connectPath = file_exists(__DIR__ . '/connect.php') ? __DIR__ . '/connect.php' : dirname(__DIR__) . '/funcs/connect.php';
    include $connectPath;
    
    $adminId = (int)$_SESSION['admin_id'];
    $stmt = $conn->prepare("SELECT id FROM admins WHERE id = ?");
    $stmt->bind_param('i', $adminId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        // Admin account was deleted, destroy session and redirect
        session_destroy();
        // Determine correct login path
        $loginPath = 'login.php';
        if (strpos($_SERVER['PHP_SELF'], '/admin/') === false) {
            $loginPath = 'admin/login.php';
        }
        header("Location: " . $loginPath);
        exit();
    }
    
    $stmt->close();
}

