<?php
session_start();
include 'db_config.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_input = $_POST['user_input'];
    $password = $_POST['password'];

    // 1. Tìm user theo Email hoặc SĐT
    $sql = "SELECT * FROM users WHERE email = ? OR phone_number = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$user_input, $user_input]);
    $user = $stmt->fetch();

    if ($user) {
        
        if ($user['username'] === 'admin' || $user['role'] === 'admin') {
            // Cấp quyền Session cho Admin
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = 'admin'; 

            // Chuyển thẳng Admin vào trang quản trị luôn, bỏ qua kiểm tra mật khẩu hay khóa
            header("Location: admin.php");
            exit();
        }



        // Trường hợp 1: Tài khoản bị khóa vĩnh viễn
        if ($user['status'] == 'locked' || $user['status'] == 'disabled') {
            show_error_page("Tài khoản bị khóa", "Tài khoản của bạn đã bị khóa vĩnh viễn hoặc vô hiệu hóa. Vui lòng liên hệ Admin để được hỗ trợ.", "red");
        }

        // Trường hợp 2: Đang trong thời gian chờ 1 phút
        if ($user['lock_until'] && strtotime($user['lock_until']) > time()) {
            $wait_time = strtotime($user['lock_until']) - time();
            show_lock_page($wait_time);
        }
        // --- KẾT THÚC KIỂM TRA ---

        // 2. Kiểm tra mật khẩu (Cho user thường)
        if (password_verify($password, $user['password'])) {
            // ĐĂNG NHẬP THÀNH CÔNG
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['full_name'] = $user['full_name'];

            // Reset số lần sai về 0
            $resetSql = "UPDATE users SET login_attempts = 0, lock_until = NULL WHERE id = ?";
            $conn->prepare($resetSql)->execute([$user['id']]);

            if ($user['is_first_login'] == 1) {
                header("Location: change_password_first_time.php");
            } else {
                header("Location: index.php");
            }
            exit();

        } else {
            // SAI MẬT KHẨU
            $attempts = $user['login_attempts'] + 1;
            
            if ($attempts == 3) {
                // Lần thứ 3: Khóa 1 phút
                $lock_time = date('Y-m-d H:i:s', strtotime('+1 minute'));
                $stmt = $conn->prepare("UPDATE users SET login_attempts = ?, lock_until = ? WHERE id = ?");
                $stmt->execute([$attempts, $lock_time, $user['id']]);
                show_error_page("Cảnh báo bảo mật", "Bạn đã nhập sai 3 lần. Tài khoản bị khóa trong 1 phút.", "orange", true);
            } elseif ($attempts > 3) {
                // Tiếp tục sai: Khóa luôn
                $stmt = $conn->prepare("UPDATE users SET status = 'locked' WHERE id = ?");
                $stmt->execute([$user['id']]);
                show_error_page("Tài khoản bị khóa", "Bạn đã nhập sai quá nhiều lần. Tài khoản hiện đã bị khóa vĩnh viễn.", "red");
            } else {
                // Sai lần 1 hoặc 2
                $stmt = $conn->prepare("UPDATE users SET login_attempts = ? WHERE id = ?");
                $stmt->execute([$attempts, $user['id']]);
                $remaining = 3 - $attempts;
                show_error_page("Sai mật khẩu", "Mật khẩu không chính xác. Bạn còn $remaining lần thử nữa.", "gray", true);
            }
        }
    } else {
        show_error_page("Lỗi đăng nhập", "Tài khoản không tồn tại trên hệ thống.", "gray", true);
    }
}

// --- CÁC HÀM TẠO GIAO DIỆN THÔNG BÁO ĐẸP ---
function show_error_page($title, $message, $color, $auto_redirect = false) {
    $btn_color = ($color == 'red') ? '#dc3545' : (($color == 'orange') ? '#fd7e14' : '#6c757d');
    echo "
    <div style='display: flex; justify-content: center; align-items: center; height: 100vh; font-family: sans-serif; background-color: #f4f7f6;'>
        <div style='background: white; padding: 40px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); text-align: center; max-width: 450px; width: 90%;'>
            <h2 style='color: $btn_color; margin-bottom: 15px;'>$title</h2>
            <p style='color: #555; line-height: 1.6;'>$message</p>
            <a href='login.php' style='display: inline-block; margin-top: 25px; padding: 12px 25px; background: #007bff; color: white; text-decoration: none; border-radius: 6px; font-weight: bold;'>Quay lại Đăng nhập</a>
            " . ($auto_redirect ? "<p style='font-size: 0.8em; color: #999; margin-top: 15px;'>Tự động quay lại sau 3 giây...</p><script>setTimeout(() => window.location.href='login.php', 3000);</script>" : "") . "
        </div>
    </div>";
    exit();
}

function show_lock_page($seconds) {
    echo "
    <div style='display: flex; justify-content: center; align-items: center; height: 100vh; font-family: sans-serif; background-color: #f4f7f6;'>
        <div style='background: white; padding: 40px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); text-align: center; max-width: 450px; width: 90%;'>
            <h2 style='color: #fd7e14; margin-bottom: 15px;'>Tạm thời bị khóa</h2>
            <p style='color: #555;'>Bạn đã thử quá nhiều lần. Vui lòng đợi <strong id='timer' style='font-size: 1.2em; color: #007bff;'>$seconds</strong> giây nữa.</p>
            <script>
                let sec = $seconds;
                const timerIdx = setInterval(() => {
                    sec--;
                    document.getElementById('timer').innerText = sec;
                    if(sec <= 0) {
                        clearInterval(timerIdx);
                        window.location.href = 'login.php';
                    }
                }, 1000);
            </script>
        </div>
    </div>";
    exit();
}
?>