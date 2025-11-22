<?php
session_start(); // Start the session to access it

// Remove all session variables and destroy the session
session_unset();
session_destroy();

// Redirect to the site home. Use the correct project folder name.
// If your project sits at http://localhost/versity-project use '/versity-project'.
define('BASE_URL', '/versity-project');

header('Location: ' . BASE_URL . '/index.php');
exit;

?>
