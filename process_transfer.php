<?php
session_start();
require_once 'db_config.php';
require_once 'send_mail.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Ép kiểu số thực (float) và xóa bỏ mọi dấu chấm/phẩy nếu người dùng nhập vào
    $raw_amount = str_replace(['.', ','], '', $_POST['amount']);
    $amount = floatval($raw_amount);
    
    $receiver_phone = $_POST['receiver_phone'];
    $fee_payer = isset($_POST['fee_payer']) ? $_POST['fee_payer'] : 'sender';
    $note = $_POST['note'];
    $user_id = $_SESSION['user_id'];

    // 2. Lấy số dư người gửi
    $stmt = $conn->prepare("SELECT email, full_name, balance FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $sender = $stmt->fetch();

    if ($sender) {
        // 3. Tính toán phí
        $fee = $amount * 0.05;
        $total_needed = ($fee_payer == 'sender') ? ($amount + $fee) : $amount;

        // KIỂM TRA BẮT BUỘC: Nếu amount vẫn bằng 0 thì dừng lại báo lỗi luôn
        if ($amount <= 0) {
            die("Lỗi: Số tiền nhập vào không hợp lệ hoặc bằng 0. Bạn nhập là: " . $_POST['amount']);
        }

        if ($sender['balance'] < $total_needed) {
            echo "<script>alert('Số dư không đủ!'); window.history.back();</script>";
            exit();
        }

        // --- GIAO DỊCH TRÊN 5 TRIỆU ---
        if ($amount > 5000000) {
            try {
                // CHÚ Ý: Khiêm kiểm tra kỹ tên cột trong phpMyAdmin có giống y hệt thế này không
                $sql = "INSERT INTO transactions (sender_id, receiver_phone, type, amount, fee, total_amount, status, note) 
                        VALUES (:sid, :rphone, 'transfer', :amount, :fee, :total, 'pending', :note)";
                
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':sid', $user_id);
                $stmt->bindParam(':rphone', $receiver_phone);
                $stmt->bindParam(':amount', $amount);
                $stmt->bindParam(':fee', $fee);
                $stmt->bindParam(':total', $total_needed);
                $stmt->bindParam(':note', $note);

                if ($stmt->execute()) {
                    echo "<script>alert('Giao dịch " . number_format($amount) . "đ đang chờ duyệt!'); window.location.href='index.php';</script>";
                } else {
                    print_r($stmt->errorInfo()); // Hiện lỗi nếu thực thi thất bại
                }
                exit();
            } catch (PDOException $e) {
                die("Lỗi SQL: " . $e->getMessage());
            }
        }

        // --- GIAO DỊCH NHỎ (DÙNG OTP) ---
        $_SESSION['pending_transfer'] = [
            'receiver_phone' => $receiver_phone,
            'amount' => $amount,
            'fee_payer' => $fee_payer,
            'note' => $note,
            'fee' => $fee,
            'total_needed' => $total_needed
        ];

        $otp = rand(100000, 999999);
        $_SESSION['otp_code'] = $otp;
        $_SESSION['otp_time'] = time();

        $subject = "OTP chuyen tien";
        $content = "Mã OTP của bạn là: $otp cho giao dịch " . number_format($amount) . "đ";
        
        if (sendEmail($sender['email'], $subject, $content)) {
            header("Location: verify_otp.php");
        }
    }
}
?>