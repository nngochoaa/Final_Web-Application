<?php
session_start();
require_once 'db_config.php';
require_once 'send_mail.php'; // Nạp file gửi mail

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $amount = (int)$_POST['amount'];
    $fee = $amount * 0.05; // Phí 5%
    $total_deduct = $amount + $fee; // Tổng tiền sẽ bị trừ khỏi ví

    // 1. Kiểm tra bội số 50.000
    if ($amount % 50000 != 0) {
        die("<script>alert('Số tiền rút phải là bội số của 50.000 VND'); window.history.back();</script>");
    }

    // 2. Lấy thông tin user để check số dư và lấy Email
    $stmt = $conn->prepare("SELECT email, full_name, balance FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if ($total_deduct > $user['balance']) {
        die("<script>alert('Số dư không đủ để thực hiện giao dịch (bao gồm phí 5%)'); window.history.back();</script>");
    }

    // 3. Kiểm tra ngưỡng 5 triệu để set trạng thái
    $status = ($amount > 5000000) ? 'pending' : 'success';
    $message = ($status == 'pending') ? 'Yêu cầu rút tiền trên 5 triệu đã được gửi, vui lòng đợi Admin duyệt!' : 'Rút tiền thành công!';

    try {
        $conn->beginTransaction();

        // LUÔN TRỪ TIỀN NGAY để "đóng băng" số tiền này (Tránh việc rút 1 số tiền nhiều lần)
        $updateSql = "UPDATE users SET balance = balance - ? WHERE id = ?";
        $conn->prepare($updateSql)->execute([$total_deduct, $user_id]);

        // Lưu lịch sử giao dịch (Thêm receiver_id = sender_id cho đồng bộ cấu hình bảng)
        $logSql = "INSERT INTO transactions (sender_id, receiver_id, type, amount, fee, total_amount, status, note) VALUES (?, ?, 'withdraw', ?, ?, ?, ?, ?)";
        $note = ($status == 'pending') ? "Chờ duyệt rút tiền > 5tr" : "Rút tiền thành công";
        $conn->prepare($logSql)->execute([$user_id, $user_id, $amount, $fee, $total_deduct, $status, $note]);

        $conn->commit();

        // --- GỬI MAIL THÔNG BÁO BIẾN ĐỘNG SỐ DƯ ---
        $new_balance = $user['balance'] - $total_deduct;
        
        if ($status == 'success') {
            // Nếu thành công luôn thì gửi mail báo biến động số dư như bình thường
            notifyTransaction($user['email'], $user['full_name'], 'withdraw', $total_deduct, $new_balance);
        } else {
            // Nếu chờ duyệt, gửi 1 mail thông báo yêu cầu đã được tiếp nhận
            $subject = "Yêu cầu rút tiền đang chờ duyệt";
            $content = "Chào " . $user['full_name'] . ", yêu cầu rút số tiền " . number_format($amount) . "đ đang được hệ thống kiểm tra vì giá trị trên 5 triệu đồng. Vui lòng đợi Admin xử lý trong 24h.";
            sendEmail($user['email'], $subject, $content);
        }

        echo "<script>alert('$message'); window.location.href='index.php';</script>";

    } catch (Exception $e) {
        if ($conn->inTransaction()) $conn->rollBack();
        echo "Lỗi: " . $e->getMessage();
    }
}
?>