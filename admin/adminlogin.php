<?php
require '../backend/databaseconnection.php';
session_start();
if(isset($_POST['login']))
{
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $sql = "SELECT * FROM admininfo WHERE username = '$username' and password = '$password'";

    $result = $conn->query($sql);
    if($result->num_rows > 0){
        $_SESSION['username'] = $username;

        // For logout Purpose
        $_SESSION['admin'] = 1;
        // Redirect to the admin panel page
        header ("Location: adminpanel.php");
        exit();
    }else{
        // Redirect to the same login page with an error message
        header("Location: adminlogin.php?error=1");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/7b1b8b2fa3.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../css/login.css">
    <title>Admin login</title>
</head>
<body>
<body>
    <div class="container">
        <div class="form-box">
            <h1>Admin Login</h1>
            <?php if (isset($_GET['error'])) { ?>
                <div class="error-message">
                    Username or Password Invalid!
                </div>
            <?php } ?>
            <form action= "" method="post">
                <div class="input-field">
                    <i class="fa fa-user fa-solid"></i>
                    <input type="text" name="username" placeholder="Username" required>
                </div>
                <div class="input-field">
                    <i class="fa fa-lock fa-solid"></i>
                    <input type="password" name="password" placeholder="Password" name="password" id="password" required>
                    <i class="fa-regular fa-eye" id="togglePassword"></i>
                </div>
                <div class="btn-field">
                    <button type="submit" name="login">Login</button>
                </div>
            </form>
        </div>
    </div>
    <script src="../js/showpwd.js"></script>

</body>
</html>