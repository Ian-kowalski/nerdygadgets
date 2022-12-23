<?php
// Initialize the session
include __DIR__ . '/header.php';

// Unset all of the session variables
$_SESSION = array();

// Destroy the session.
session_destroy();

// Redirect to login page
header("location: login.php");
exit;
include __DIR__ . "/footer.php";
?>
