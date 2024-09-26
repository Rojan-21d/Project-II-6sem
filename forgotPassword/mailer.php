<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . "/vendor/autoload.php";

$mail = new PHPMailer(true);

// Enable verbose debug output for troubleshooting
// $mail->SMTPDebug = SMTP::DEBUG_SERVER;

$mail->isSMTP();
$mail->SMTPAuth = true;

// Set the SMTP server for Gmail
$mail->Host = "smtp.gmail.com";

// Enable TLS encryption
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

// Set the SMTP port for Gmail
$mail->Port = 587;

// Your Gmail address (sender)
$mail->Username = "gantabyaproject@gmail.com"; // Gmail address

// Use the generated App Password for Gmail
$password = "mtzvtmteckouphbk";
$mail->Password = $password; // Replace with your App Password

// Set the email format to HTML
$mail->isHTML(true);

// Return the $mail object
return $mail;
?>
