<?php
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendEmail($to, $subject, $content) {
    $mail = new PHPMailer(true);
    
    try {
        $mail->isSMTP();
        $mail->CharSet   = 'UTF-8';
        $mail->Host      = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'khiemthan01@gmail.com'; 
        $mail->Password   = 'ozym voto spjv tbql';    
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Tắt kiểm tra SSL (dùng cho localhost/Docker)
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        $mail->setFrom('khiemthan01@gmail.com', 'Ví Điện Tử');
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $content;

        $mail->send();
        return true;

    } catch (Exception $e) {
        error_log("Mail Error: " . $mail->ErrorInfo);
        return false;
    }
}
?>