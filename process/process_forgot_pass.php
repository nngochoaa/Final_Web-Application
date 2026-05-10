<?php
session_start();
require_once '../config/db_config.php';
require_once '../send_mail.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND phone_number = ?");
    $stmt->execute([$email, $phone]);
    $user = $stmt->fetch();

    if ($user) {
        $otp = rand(100000, 999999);
        $_SESSION['reset_otp'] = $otp;
        $_SESSION['reset_user_id'] = $user['id'];
        $_SESSION['otp_time'] = time();
    
        $subject = "OTP for Password Reset";
        $body = "Your OTP is: <b>$otp</b><br>This code is valid for 60 seconds.";

        if (sendEmail($email, $subject, $body)) {
            header("Location: ../pages/auth/verify_reset_otp.php");
        } else {
            echo "Error sending email!";
        }
    } else {
        echo "<script>alert('Information is incorrect!'); history.back();</script>";
    }
}
?>