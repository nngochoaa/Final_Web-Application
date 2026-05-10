<?php
session_start();
require_once 'db_config.php';
// Nạp file chứa hàm gửi mail và notifyTransaction
require_once 'send_mail.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $card_num = $_POST['card_number'];
    $expiry = $_POST['expiry_date'];
    $cvv = $_POST['cvv'];
    $amount = $_POST['amount'];

    // 1. Kiểm tra xem thẻ này có tồn tại trong bảng credit_cards không
    $stmt = $conn->prepare("SELECT * FROM credit_cards WHERE card_number = ? AND expiry_date = ? AND cvv = ?");
    $stmt->execute([$card_num, $expiry, $cvv]);
    $card = $stmt->fetch();

    if (!$card) {
        die("<script>alert('Thẻ này không được hỗ trợ hoặc sai thông tin!'); window.history.back();</script>");
    }

    // 2. Logic xử lý từng loại thẻ theo đề bài
    if ($card_num == '333333') {
        die("<script>alert('Thẻ hết tiền (Thẻ luôn báo lỗi)!'); window.history.back();</script>");
    }

    if ($card_num == '222222' && $amount > 1000000) {
        die("<script>alert('Thẻ này chỉ nạp tối đa 1.000.000 VND mỗi lần!'); window.history.back();</script>");
    }

    // 3. Nếu mọi thứ OK (Thẻ 111111 hoặc thẻ 222222 nạp đúng số tiền)
    try {
        $conn->beginTransaction();

        // Cập nhật số dư cho User
        $updateSql = "UPDATE users SET balance = balance + ? WHERE id = ?";
        $conn->prepare($updateSql)->execute([$amount, $user_id]);

        // Lưu vào lịch sử giao dịch
        // Lưu ý: Thêm receiver_id = $user_id nếu cấu trúc bảng yêu cầu receiver_id NOT NULL
        $logSql = "INSERT INTO transactions (sender_id, receiver_id, type, amount, total_amount, status, note) VALUES (?, ?, 'deposit', ?, ?, 'success', ?)";
        $conn->prepare($logSql)->execute([$user_id, $user_id, $amount, $amount, "Nạp tiền từ thẻ $card_num"]);

        $conn->commit();
        
        // --- PHẦN ADD THÊM: GỬI MAIL THÔNG BÁO NẠP TIỀN ---
        // Lấy thông tin email và số dư mới nhất
        $stmt_info = $conn->prepare("SELECT email, full_name, balance FROM users WHERE id = ?");
        $stmt_info->execute([$user_id]);
        $u_info = $stmt_info->fetch();

        if ($u_info) {
            notifyTransaction(
                $u_info['email'], 
                $u_info['full_name'], 
                'deposit', 
                $amount, 
                $u_info['balance']
            );
        }
        // --------------------------------------------------

        echo "<script>alert('Nạp tiền thành công!'); window.location.href='index.php';</script>";

    } catch (Exception $e) {
        if ($conn->inTransaction()) $conn->rollBack();
        echo "Lỗi: " . $e->getMessage();
    }
}
?>