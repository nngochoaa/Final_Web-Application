<?php
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendEmail($to, $subject, $content) {
    $mail = new PHPMailer(true);
    try {
        // Cấu hình Server (GIỮ NGUYÊN CỦA KHIÊM)
        $mail->isSMTP();
        $mail->CharSet   = 'UTF-8'; 
        $mail->Host      = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'khiemthan01@gmail.com'; 
        $mail->Password   = 'ozym voto spjv tbql';    
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        $mail->setFrom('khiemthan01@gmail.com', 'Ví Điện Tử'); 
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $content;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mail Error: " . $mail->ErrorInfo);
        return false;
    }
}

// --- PHẦN ADD THÊM: CÁC HÀM GỌI NHANH 1 DÒNG ---

function notifyTransaction($to, $full_name, $type, $amount, $balance) {
    $title = "";
    $color = "";
    $action = "";

    switch ($type) {
        case 'deposit':
            $title = "Thông báo Nạp tiền thành công";
            $action = "Nạp tiền vào ví";
            $color = "#28a745"; // Xanh lá
            $sign = "+";
            break;
        case 'withdraw':
            $title = "Thông báo Rút tiền thành công";
            $action = "Rút tiền về ngân hàng";
            $color = "#dc3545"; // Đỏ
            $sign = "-";
            break;
        case 'transfer':
            $title = "Thông báo Chuyển tiền thành công";
            $action = "Chuyển tiền cho người dùng khác";
            $color = "#dc3545"; // Đỏ
            $sign = "-";
            break;
        case 'receive':
            $title = "Bạn vừa nhận được tiền";
            $action = "Nhận tiền từ người dùng khác";
            $color = "#28a745"; // Xanh lá
            $sign = "+";
            break;
    }

    $content = "
    <div style='font-family: Arial, sans-serif; max-width: 500px; margin: auto; border: 1px solid #ddd; border-radius: 10px; padding: 20px;'>
        <h2 style='color: #0d47a1; text-align: center;'>E-WALLET</h2>
        <p>Xin chào <b>$full_name</b>,</p>
        <p>Tài khoản của bạn vừa có biến động số dư từ giao dịch <b>$action</b>:</p>
        <div style='background: #f8f9fa; padding: 15px; border-radius: 8px; text-align: center; margin: 20px 0;'>
            <span style='font-size: 18px;'>Số tiền biến động:</span><br>
            <strong style='font-size: 24px; color: $color;'>$sign " . number_format($amount) . " VND</strong>
        </div>
        <p>Số dư khả dụng hiện tại: <b style='color: #0d6efd;'>" . number_format($balance) . " VND</b></p>
        <hr style='border: 0; border-top: 1px solid #eee;'>
        <p style='font-size: 12px; color: #777; text-align: center;'>Đây là tin nhắn tự động, vui lòng không phản hồi email này.</p>
    </div>";

    return sendEmail($to, $title, $content);
}