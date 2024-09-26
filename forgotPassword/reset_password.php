<!-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> -->
<script src="../js/sweetalert.js"></script>

<link rel="stylesheet" href="../css/sweetAlert.css">
<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set('Asia/Kathmandu');
$otp = $_SESSION['otp'];
$otp_hash = hash("sha256", $otp);

require '../backend/databaseconnection.php';
$userselects = ($_SESSION['userSelects'] === "carrier") ? "carrier" : "consignor";

$table = ($userselects === "carrier") ? "carrierdetails" : "consignordetails";

$sql = "SELECT * FROM $table WHERE reset_otp_hash = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $otp_hash);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();


// Revalidating otp once again so that user will not be able to change password if the time is expired
if ($user === null) {
    echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                title: "Invalid OTP",
                text: "The OTP Not Found.",
                icon: "error",
                confirmButtonText: "OK"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "forgot_password.php";
                }
            });
        });
    </script>';
    exit;
}

if (strtotime($user["reset_otp_expires_at"]) <= time()) {
    echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                title: "OTP Expired",
                text: "The OTP has expired.",
                icon: "error",
                confirmButtonText: "OK"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "forgot_password.php";
                }
            });
        });
    </script>';
    exit;
}

$errors = [];

if (isset($_POST["verify"])) {
    $password = $_POST['password'];
    $password_confirmation = $_POST['password_confirmation'];
    
    // Password validation rules
    if (strlen($password) < 8 || strlen($password) > 24) {
        $errors[] = "Password must be between 8 and 24 characters";
    }
    
    if ($password !== $password_confirmation) {
        $errors[] = 'Passwords do not match!';
    }

    if (empty($errors)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
        $sql = "UPDATE $table 
                SET password = ?,
                    reset_otp_hash = NULL,
                    reset_otp_expires_at = NULL
                WHERE id = ?";
    
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $password_hash, $user['id']);
        $stmt->execute();
    
        if ($stmt->affected_rows > 0) {
            echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        title: "Success",
                        text: "Password Changed",
                        icon: "success",
                        confirmButtonText: "OK"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "../login.php";
                        }
                    });
                });
            </script>';
        } else {
            echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        title: "Error",
                        text: "Password Change Failed",
                        icon: "error",
                        confirmButtonText: "OK"
                    });
                });
            </script>';
        }
    }
}


// Display errors using SweetAlert
if (!empty($errors)) {
    $errorMessages = join("<br>", $errors);
    echo '<script>
        Swal.fire({
            icon: "error",
            title: "Password Change Errors",
            html: "'.$errorMessages.'",
            showCloseButton: true,
        });
    </script>';
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/7b1b8b2fa3.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../css/login.css">
    <title>Reset Password</title>
</head>
<body>
    <div class="container"> 
        <div class="form-box">
            <h1>Reset Password</h1>
            <form action="" method="post" class="login">    
                <div class="input-group-login" >
                    <div class="input-field ">
                        <i class="fa-solid fa-key"></i>
                        <input type="password" placeholder="Password *" name="password" id="password" required>
                        <i class="fa-regular fa-eye" id="togglePassword"></i>
                    </div>
                    <div class="input-field ">
                        <i class="fa-solid fa-key"></i>
                        <input type="password" placeholder="Confirm Password *" name="password_confirmation" id="password_confirmation" required>
                        <i class="fa-regular fa-eye" id="togglePassword2"></i>
                    </div>            
                    <div class="btn-field">
                        <button type="submit" name="verify">Reset Password</button>
                    </div>
                </div>        
            </form>
        </div>
    </div>
    <script src="../js/showpwd.js"></script>
    <!-- Confirm password validaion from js -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const form = document.querySelector(".login");
            const passwordField = document.getElementById("password");
            const confirmPasswordField = document.getElementById("password_confirmation");

            form.addEventListener("submit", function (event) {
                const password = passwordField.value;
                const confirmPassword = confirmPasswordField.value;
                const errors = [];

                if (password !== confirmPassword) {
                    errors.push("Passwords do not match.");
                }
                if (password.length < 8 || password.length > 24) {
                    errors.push("Password must be between 8 and 24 characters.");
                }

                if (errors.length > 0) {
                    event.preventDefault();
                    const errorMessages = errors.join("<br>");
                    
                    Swal.fire({
                        icon: "error",
                        title: "Password Reset Errors",
                        html: errorMessages,
                        showCloseButton: true,
                    });
                }
            });
        });
    </script>
</body>
</html>
