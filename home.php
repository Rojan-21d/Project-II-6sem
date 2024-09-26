<?php
// Check if the session has not started, then start the session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
    // Check if the user is not logged in
    if(!isset($_SESSION['email'])) {
        // Redirect the user to the login page or any other authentication page
        header("Location: login.php");
        exit;
    }

    // Database connection
    require 'backend/databaseconnection.php';

    include 'layout/header.php';
    if ($_SESSION['usertype'] == "carrier") {
        include 'layout/carriermain.php';

    } elseif ($_SESSION['usertype'] == "consignor") {
        include 'layout/consignormain.php';

    }
    include 'layout/footer.php';
?>


