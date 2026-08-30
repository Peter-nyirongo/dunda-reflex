<?php

session_start();

// Remove all session variables
$_SESSION = [];

// Destroy the session
session_destroy();

// Send user back to login
header("Location: login.php");
exit;

?>