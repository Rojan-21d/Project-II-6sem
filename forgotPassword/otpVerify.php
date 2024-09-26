<?php
if(isset($_POST["verify"])){
    $otp = $_POST["otp"];

    date_default_timezone_set('Asia/Kathmandu');
    $otp_hash = hash("sha256", $otp); // This should be $token, not $otp

    require '../backend/databaseconnection.php';

    session_start();
    $userSelects = $_SESSION['userSelects'];

    $table = ($userSelects === "carrier") ? "carrierdetails" : "consignordetails";
    $sql = "SELECT * FROM $table WHERE reset_otp_hash = ?";
    $stmt = $conn->prepare($sql);

    $stmt->bind_param("s", $otp_hash); // Binding

    $stmt->execute();

    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if($user === null) {
        $error_message = "Invalid OTP!"; // Set an error message for invalid OTP
    }

    if ($user !== null && strtotime($user["reset_otp_expires_at"]) <= time()) {
        $error_message = "OTP Expired!"; // Set an error message for expired OTP
    }

    if (!isset($error_message)) {
        // After verifying the OTP and before redirection
        $_SESSION['otp'] = $otp; // Store the verified OTP in a session variable

        header ('location: reset_password.php'); // This will cause a redirection
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/login.css">
    <title>OTP Verification</title>
</head>
<body>
    <div class="container"> 
        <div class="form-box">
            <h1>OTP Verification</h1>
            <form action="" method="post">
                <div class="input-group-login" >
                    <div class="input-field " >
                        <input type="text" maxlength="6" inputmode="numeric" placeholder="OTP Here" name="otp" id="otp" required style="padding-left: 15px;">
                    </div>
                    <!-- Error Message Display  -->
                    <?php if (isset($error_message)) : ?>
                        <div class="errorOTP error-message" id="errorOTP">
                            <?php echo $error_message; ?>
                        </div>
                    <?php endif; ?>                
                    <div class="btn-field">
                        <?php if (isset($user) && strtotime($user["reset_otp_expires_at"]) <= time()) : ?>
                            <button type="button" id="resendOTPButton">Resend OTP</button>
                        <?php else : ?>
                            <button type="submit" name="verify">Verify</button>
                        <?php endif; ?>
                    </div>
                </div>
                <small><a href="../login.php">Go to Login</a></small>
            </form>
        </div>
    </div>
    <script>
        <?php if (isset($user) && strtotime($user["reset_otp_expires_at"]) <= time()) : ?>
            document.getElementById('resendOTPButton').addEventListener('click', function() {
                window.location.href = 'send_reset_password.php';
            });
        <?php endif; ?>
    </script>
</body>
</html>

