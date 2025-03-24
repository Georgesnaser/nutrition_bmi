<?php
session_start();  // Start the session

// Destroy all session variables
session_unset();

// Destroy the session itself
session_destroy();

// Redirect the user to the home or login page
header("Location: login.php");  // You can change this to the page you want
exit();  // Make sure to exit after the redirect
?>