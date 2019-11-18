<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendMail($to,$subject,$body)
{

    $mail = new PHPMailer();
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'carpoolinguit';
    $mail->Password = 'g537Th!@';
    $mail->SMTPSecure = "tls";
    $mail->Port = 587;
    $mail->CharSet = 'UTF-8';

    $mail->setFrom("carpoolinguit@gmail.com");
    $mail->addAddress($to);

$mail->isHTML(true);
$mail->Subject = $subject;
$mail->Body = $body;
    $mail->send();


/*if(!$mail->send()) {
    echo 'Message could not be sent.';
 echo "Mailer Error: " . $mail->ErrorInfo;
} else {
    echo "ok";
}*/


}