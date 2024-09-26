<?php
session_start();

// Checking if user is already logged in
if(isset($_SESSION['email'])){
    // Redirect user to home page
    header ("Location: home.php");
    exit;    
}

if (isset($_POST['loginbtn'])) {
    // Get the username and password from the form
    $email = $_POST['email'];
    $password = $_POST['password'];
    $userselects = $_POST['userselects'];

    // Database connection
    require 'backend/databaseconnection.php';
    
    // Checking userselects
    if ($userselects == "carrier") {
        $sql = "SELECT * FROM carrierdetails WHERE email = ?";
    } elseif ($userselects == "consignor") {     
        $sql = "SELECT * FROM consignordetails WHERE email = ?";
    }

    // Using prepared statements to prevent SQL injection
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $thePassword = $row['password'];

        if (password_verify($password, $thePassword)) {
            // Password is correct, set session variables
            $_SESSION['id'] = $row['id'];
            $_SESSION['name'] = $row['name'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['contact'] = $row['contact'];
            $_SESSION['address'] = $row['address'];
            $_SESSION['profilePic'] = $row['img_srcs'];
            $_SESSION['usertype'] = $userselects;

            // Redirect the user to the home page
            header("Location: home.php");
            exit;
        } else {
            // Password is incorrect
            header("Location: login.php?errorPassword=1");
            exit;
        }
    } else {        
        // Incorrect email
        header("Location: login.php?errorEmail=1");
        exit;
    }
}    
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">    
    <!-- <script src="https://kit.fontawesome.com/7b1b8b2fa3.js" crossorigin="anonymous"></script> -->
    <script src="js/fontAwesome.js"></script>
    <link rel="stylesheet" href="css/login.css">
    <title>Gantabya - Log in</title>
</head>
<body>
    <div class="container"> 
        <div class="form-box">
            <h1>Log In</h1>  
            <?php if (isset($_GET['errorEmail'])) { ?>
                <div class="error-message">
                    User Not Found!
                </div>
            <?php } 
                if (isset($_GET['errorPassword'])) { ?>
                <div class="error-message">
                    Password Invalid!
                </div>
            <?php } ?>       
            <form method="post" action="" class="login" autocomplete="off">
            <div class="input-group-login" >
                    <div class="input-field " >
                        <i class="fa-solid fa-user"></i>
                        <input type="email" placeholder="Email *" name="email" required>
                    </div>
                    <div class="input-field ">
                        <i class="fa-solid fa-key"></i>
                        <input type="password" placeholder="Password *" name="password" id="password" required>
                        <i class="fa-regular fa-eye" id="togglePassword"></i>
                    </div>
                </div>          
                <div class="user-selects">
                    <div class="carrier-part">
                        <input type="radio" id="carrier" name="userselects" value="carrier" checked>
                        <label for="carrier">Carrier</label>
                    </div>
                    <div class="consignor-part">
                        <input type="radio" id="consignor" name="userselects" value="consignor">
                        <label for="consignor">Consignor</label>
                    </div>
                </div>
                
                <small><a href="forgotPassword/forgot_password.php">Forgot Password?</a></small>
                
                <div class="btn-field">
                    <button type="submit" name="loginbtn" value="login">Log In</button>
                </div> 
                
                <small><a href="signup.php">Sign Up Here!</a> </small>
                
            </form>
        </div>    
    </div>
    <script src="js/showpwd.js"></script>
</body>
</html>
