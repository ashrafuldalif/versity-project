<?php
/**
 * Admin Logout
 * Destroys admin session and redirects to login page
 */

session_start();

// Unset all admin session variables
unset($_SESSION['admin_logged_in']);
unset($_SESSION['admin_id']);
unset($_SESSION['admin_username']);
unset($_SESSION['admin_email']);

// Destroy the session
session_destroy();

// Redirect to admin login page
header("Location: ../login.php");
exit();

