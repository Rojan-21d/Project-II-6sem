<!-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> -->
<script src="../js/sweetalert.js"></script>

<link rel="stylesheet" href="../css/sweetAlert.css">

<?php
session_start();

$email = isset($_POST['email']) ? $_POST['email'] : $_SESSION['resetEmail'];
$userSelects = isset($_POST['userselects']) ? $_POST['userselects'] : $_SESSION['userSelects'];
$_SESSION['resetEmail'] = $email;

$randomNumberOTP = mt_rand(100000, 999999);
$otp_hash = hash("sha256", $randomNumberOTP);
date_default_timezone_set('Asia/Kathmandu');
$expiry = date("y-m-d H:i:s", time() + 60 * 3);

require '../backend/databaseconnection.php';

$_SESSION['userSelects'] = $userSelects; // Set the userSelects in the session
$_SESSION['resetEmail'] = $email;
$table = ($userSelects === "carrier") ? "carrierdetails" : "consignordetails";
$sql = "UPDATE $table 
        SET reset_otp_hash = ? ,
            reset_otp_expires_at = ?
        WHERE email = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare error: " . $conn->error);
}

$stmt->bind_param("sss", $otp_hash, $expiry, $email);
$stmt->execute();

if ($conn->affected_rows) {
    $mail = require __DIR__ . "/mailer.php";
    $mail->setFrom("gantabyaproject@gmail.com");
    $mail->addAddress($email);
    $mail->Subject = "Password Reset";
    $mail->Body = <<<END
Your OTP to reset password: $randomNumberOTP. OTP will expire in 3 minutes.
END;

    try {
        $mail->send();
        // Redirect to the OTP verification page
        ?>
        .<script>
        Swal.fire({
            title: 'Email sent.',
            text: 'Please check your inbox.',
            showCancelButton: false,
            confirmButtonText: 'OK'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'otpVerify.php';
            }
        });
        </script>    
        <?php
        exit;
    } catch (Exception $e) {
        ?>
        .<script>
        Swal.fire({
            title: 'Error',
            text: 'Message could not be sent. Mailer error: <?=$mail->ErrorInfo?>',
            footer: '<a href="forgot_password.php">Next</a> to go to forgot_password.php'
        });
        </script>
        <?php
        exit;
    }
}
?>

<!-- Display Mail Send even with wrong email to improve security -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
    Swal.fire({
        title: 'Email sent.',
        text: 'Please check your inbox.',
        showCancelButton: false,
        confirmButtonText: 'OK'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'otpVerify.php';
        }
    });
    });
</script>