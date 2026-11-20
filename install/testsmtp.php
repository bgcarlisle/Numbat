<?php

// For PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../phpmailer-src/Exception.php';
require_once __DIR__ . '/../phpmailer-src/PHPMailer.php';
require_once __DIR__ . '/../phpmailer-src/SMTP.php';

$mail = new PHPMailer(true);

$body = "<p>The email credentials you entered are working!</p>";
$body .= "<p>Host: " . $_POST['smtp_host'];
$body .= "<p>User: " . $_POST['smtp_user'];
$body .= "<p>Sent: " . date("Y-m-d H:i:s");

try {
    $mail->isSMTP();
    $mail->Host       = $_POST['smtp_host'];
    $mail->SMTPAuth   = true;
    $mail->Username   = $_POST['smtp_user'];
    $mail->Password   = $_POST['smtp_password'];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->setFrom($_POST['smtp_email'], 'Numbat');
    $mail->addAddress($_POST['smtp_email']);

    $mail->isHTML(true);
    $mail->Subject = "Numbat test email";
    $mail->Body    = $body;

    if ($mail->send()) {
	echo "Mail sent";
    } else {
	echo "Mail not sent";
    }
} catch (Exception $e) {
    error_log("Numbat Mailer Error: {$mail->ErrorInfo}");
    return false;
}

?>
