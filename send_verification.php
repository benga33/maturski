<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'sandbox.smtp.mailtrap.io';
    $mail->SMTPAuth = true;
    $mail->Username = 'bad878c0f0a349';
    $mail->Password = '6436448db35d43';
    $mail->Port = 2525;

    $mail->setFrom('no-reply@mojaaplikacija.com', 'Moja Aplikacija');
    $mail->addAddress('test@gmail.com');

    $token = md5(rand());
    $verification_link = "http://localhost/maturski/verify.php?token=$token";

    $mail->isHTML(true);
    $mail->Subject = 'Potvrda emaila';
    $mail->Body = "
        <h2>Email verifikacija</h2>
        <p>Klikni na link da potvrdiš nalog:</p>
        <a href='$verification_link'>Verifikuj nalog</a>
    ";

    $mail->send();
    echo "Mail poslan";
} catch (Exception $e) {
    echo "Greška: {$mail->ErrorInfo}";
}
?>