<?php
session_start();   // Start the session to access it

// Remove all session variables
session_unset();

// Destroy the session completely
session_destroy();

// Optionally, redirect to login page
header("Location: login.php");
exit;
?>
