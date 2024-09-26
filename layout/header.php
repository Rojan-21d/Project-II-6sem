<?php
// Check if the session has not started, then start the session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Check if the user is not logged in
if(!isset($_SESSION['email'])) {
    // Redirect the user to the login page or any other authentication page
    header("Location: ../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/headerfooterstyle.css">
</head>
<body>
    <header>  
        <nav>
            <a href="home.php">
                <img class="logo" src="img/defaultImg/mainLogo2.png" alt="logo">
            </a>
            <div class="nav__links">
                    <img src="<?php echo $_SESSION['profilePic'] ?>" onclick='toggleMenu()'>
            </div>
            <!-- dropdown -->
            <div class="sub-menu-wrap" id="subMenu">
                <div class="sub-menu">
                    <div class="user-info">
                        <img src="<?php echo $_SESSION['profilePic'] ?>">
                        <h2><?php echo $_SESSION['name'];?></h2>
                    </div>
                    <hr>
                    <a href="profile.php" class="sub-menu-link">
                        <img src="<?php echo $_SESSION['profilePic'] ?>">
                        <p>Profile</p>                      
                    </a>
                    <a href="home.php" class="sub-menu-link">
                        <img src="img/defaultImg/home.png">
                        <p>Home</p>                      
                    </a>
                    <?php if($_SESSION['usertype'] == 'carrier'){?>
                    <a href="history.php" class="sub-menu-link">
                        <img src="img/defaultImg/setting.png">
                        <p>History</p>                      
                    </a>
                    <?php } ?>
                    <a href="backend/logoutmodule.php" class="sub-menu-link">
                        <img src="img/defaultImg/logout.png">
                        <p>Logout</p>                      
                    </a>
                </div>
            </div>
        </nav>
    </header>
    <script src="js/dropdownmenu.js"></script>
</body>
</html>