<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . "/vendor/autoload.php"; 

$mail = new PHPMailer(true);
$mail->isSMTP();
$mail->SMTPAuth = true;
$mail->Host = "in-v3.mailjet.com"; 
$mail->Port = 587; 
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Username = "fe19b6a09c7c5ac0b01b9561bc5d00f4"; 
$mail->Password = "ecabd82d93dccef6cb955982f01df362"; 
$mail->isHTML(true);

return $mail;
?>
