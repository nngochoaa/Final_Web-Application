<?php
session_start();
require_once '../config/db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $old_pass = $_POST['old_password'];
    $new_pass = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];

    if ($new_pass !== $confirm_pass) {
        die("<script>alert('New passwords do not match!'); window.history.back();</script>");
    }

    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if ($user && password_verify($old_pass, $user['password'])) {
        $hashed_new_pass = password_hash($new_pass, PASSWORD_DEFAULT);
        $update = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $update->execute([$hashed_new_pass, $user_id]);

        echo "<script>alert('Password changed successfully!'); window.location.href='../user/index.php';</script>";
    } else {
        echo "<script>alert('Current password is incorrect!'); window.history.back();</script>";
    }
}
?>