<?php
session_start();
require_once '../config/db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pass = $_POST['password'];
    $confirm_pass = $_POST['confirm_password'];
    $user_id = $_SESSION['reset_user_id'];

    if ($pass !== $confirm_pass) {
        die("<script>alert('Passwords do not match!'); window.history.back();</script>");
    }

    $hashed_password = password_hash($pass, PASSWORD_DEFAULT);

    try {
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hashed_password, $user_id]);

        unset($_SESSION['reset_otp'], $_SESSION['reset_user_id'], $_SESSION['otp_time']);

        echo "<script>alert('Password reset successful! Please login again.'); window.location.href='../pages/auth/login.php';</script>";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>