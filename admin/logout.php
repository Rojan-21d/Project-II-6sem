<?php
    session_start(); // Start the session
    // Clear all session variables
    $_SESSION = array();
    
    // Destroy the session
    session_destroy();
    
    // Redirect to regular login page
    header("Location: adminlogin.php");
    exit();
?>

