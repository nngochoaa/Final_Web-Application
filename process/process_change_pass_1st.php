<?php
session_start();
require_once '../config/db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_pass = $_POST['new_pass'];
    $confirm_pass = $_POST['confirm_pass'];
    $user_id = $_SESSION['user_id'];

    if ($new_pass !== $confirm_pass) {
        die("<script>alert('Confirm password does not match!'); window.history.back();</script>");
    }

    $hashed_password = password_hash($new_pass, PASSWORD_DEFAULT);

    try {
        $sql = "UPDATE users SET password = ?, is_first_login = 0 WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$hashed_password, $user_id]);

        echo "<script>alert('Password changed successfully!'); window.location.href='../user/index.php';</script>";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}