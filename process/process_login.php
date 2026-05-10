<?php
session_start();
require_once '../config/db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_input = trim($_POST['user_input']);
    $password = $_POST['password'];

    // Tìm user theo email hoặc số điện thoại
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? OR phone_number = ?");
    $stmt->execute([$user_input, $user_input]);
    $user = $stmt->fetch();

    if ($user) {
        // Kiểm tra mật khẩu
        if (password_verify($password, $user['password'])) {
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['full_name'] = $user['full_name'];

            // Reset số lần đăng nhập sai
            $reset = $conn->prepare("UPDATE users SET abnormal_login_count = 0 WHERE id = ?");
            $reset->execute([$user['id']]);

            // Kiểm tra đổi mật khẩu lần đầu
            if ($user['is_first_login'] == 1) {
                header("Location: ../pages/user/change_pass_1st_time.php");
            } else {
                header("Location: ../pages/user/index.php");
            }
            exit();

        } else {
            echo "<script>alert('Incorrect password!'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Account does not exist!'); window.history.back();</script>";
    }
} else {
    header("Location: ../pages/auth/login.php");
}
?>