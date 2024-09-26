<?php
// Check if the session has not started, then start the session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if(!isset($_SESSION['username'])){
    header('location: adminlogin.php');
    exit;
}
include 'adminheader.php';
include 'maincontent.php';
?>
