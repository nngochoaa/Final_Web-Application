<?php
session_start();
require_once 'db_config.php';
require_once 'send_mail.php'; // Để gửi mail khi duyệt xong

// Kiểm tra quyền Admin (Khiêm tự thêm check admin ở đây nhé)
if (!isset($_GET['id']) || !isset($_GET['action'])) {
    header("Location: admin.php");
    exit();
}

$trans_id = $_GET['id'];
$action = $_GET['action']; // 'approve' hoặc 'reject'

try {
    // Lấy thông tin giao dịch
    $stmt = $conn->prepare("SELECT * FROM transactions WHERE id = ? AND status = 'pending'");
    $stmt->execute([$trans_id]);
    $trans = $stmt->fetch();

    if (!$trans) {
        die("Giao dịch không tồn tại hoặc đã được xử lý!");
    }

    $conn->beginTransaction();

    if ($action == 'approve') {
        // 1. Cập nhật trạng thái thành công
        $update = $conn->prepare("UPDATE transactions SET status = 'success', note = 'Admin đã duyệt' WHERE id = ?");
        $update->execute([$trans_id]);
        
        $msg = "Duyệt giao dịch thành công!";

    } elseif ($action == 'reject') {
        // 1. Cập nhật trạng thái từ chối
        $update = $conn->prepare("UPDATE transactions SET status = 'refused', note = 'Admin từ chối' WHERE id = ?");
        $update->execute([$trans_id]);

        // 2. HOÀN TIỀN lại cho người dùng vì bị từ chối rút
        $refund = $conn->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
        $refund->execute([$trans['total_amount'], $trans['sender_id']]);
        
        $msg = "Đã từ chối và hoàn tiền cho khách!";
    }

    $conn->commit();

    // --- GỬI MAIL THÔNG BÁO CHO KHÁCH ---
    $stmt_u = $conn->prepare("SELECT email, full_name, balance FROM users WHERE id = ?");
    $stmt_u->execute([$trans['sender_id']]);
    $u = $stmt_u->fetch();
    
    // Thông báo cho khách biết Admin đã xử lý xong
    $subject = ($action == 'approve') ? "Giao dịch rút tiền đã được duyệt" : "Giao dịch rút tiền bị từ chối";
    $content = "Chào " . $u['full_name'] . ", giao dịch rút " . number_format($trans['total_amount']) . "đ của bạn đã " . ($action == 'approve' ? 'được duyệt thành công.' : 'bị từ chối và tiền đã được hoàn lại ví.');
    sendEmail($u['email'], $subject, $content);

    echo "<script>alert('$msg'); window.location.href='admin.php';</script>";

} catch (Exception $e) {
    $conn->rollBack();
    die("Lỗi: " . $e->getMessage());
}
?>