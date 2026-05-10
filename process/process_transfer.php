<?php
session_start();
require_once '../config/db_config.php';
require_once '../send_mail.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../pages/auth/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $_SESSION['pending_transfer'] = [
        'receiver_phone' => $_POST['receiver_phone'],
        'amount' => $_POST['amount'],
        'fee_payer' => $_POST['fee_payer'] ?? 'sender',
        'note' => $_POST['note'] ?? ''
    ];

    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT email, full_name FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $sender = $stmt->fetch();

    if ($sender) {
        $otp = rand(100000, 999999);
        $_SESSION['otp_code'] = $otp;
        $_SESSION['otp_time'] = time();

        $subject = "OTP Verification - Digital Wallet";
        $content = "
            <h3>Confirm Money Transfer</h3>
            <p>Hello <b>{$sender['full_name']}</b>,</p>
            <p>Your OTP is: <b style='font-size:22px;color:red;'>$otp</b></p>
            <p>This code is valid for 60 seconds.</p>
        ";

        if (sendEmail($sender['email'], $subject, $content)) {
            header("Location: ../pages/user/verify_otp.php");
            exit();
        } else {
            echo "<script>alert('Cannot send OTP!'); history.back();</script>";
        }
    }
}
?>