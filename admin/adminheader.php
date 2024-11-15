<?php
// Check if the session has not started, then start the session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if(!isset($_SESSION['username'])){
    header('location: adminlogin.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/headerfooterstyle.css">
    <title>Admin</title>
</head>
<body>
<nav class="adm-header">
    <a href="adminpanel.php">
        <img class="logo" src="../img/defaultImg/mainLogo2.png" alt="Logo">
    </a>
    <div class="mid">
        <a href="viewpricingconfig.php" class="price-config">Price Configuration</a>
        <a href="weightclasspricing.php" class="weight-class">Weight Class</a>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
</nav>
</body>
</html>
