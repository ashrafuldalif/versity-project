<?php
session_start();   // Start the session to access it

// Remove all session variables
session_unset();

// Destroy the session completely
session_destroy();

// Optionally, redirect to login page
define('BASE_URL', '/versity-porject'); // Change to your actual folder name

// Then use this anywhere in any file:
header("Location: " . BASE_URL . "/index.php");
exit;
