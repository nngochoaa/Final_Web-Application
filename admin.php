<?php
session_start();
include 'db_config.php';
require_once 'send_mail.php'; 

// --- 1. XỬ LÝ LOGIC KHI ADMIN BẤM NÚT ---
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $action = $_GET['action'];

    // --- Logic duyệt hồ sơ & Mở khóa (GIỮ NGUYÊN CŨ) ---
    if ($action == 'verify') {
        $stmt = $conn->prepare("UPDATE users SET status = 'verified' WHERE id = ?");
    } elseif ($action == 'cancel') {
        $stmt = $conn->prepare("UPDATE users SET status = 'disabled' WHERE id = ?");
    } elseif ($action == 'require_info') {
        $stmt = $conn->prepare("UPDATE users SET status = 'waiting_for_updates' WHERE id = ?");
    } elseif ($action == 'unlock') {
        $stmt = $conn->prepare("UPDATE users SET status = 'verified', login_attempts = 0, lock_until = NULL WHERE id = ?");
    } 
    
    // --- Logic Duyệt Giao Dịch (CẬP NHẬT MỚI: CỘNG TIỀN NGƯỜI NHẬN) ---
    elseif ($action == 'approve_withdraw' || $action == 'reject_withdraw') {
        try {
            $conn->beginTransaction();
            
            // Lấy thông tin chi tiết giao dịch
            $stmt_trans = $conn->prepare("SELECT * FROM transactions WHERE id = ? AND status = 'pending'");
            $stmt_trans->execute([$id]);
            $trans = $stmt_trans->fetch();

            if ($trans) {
                $user_id = $trans['sender_id'];
                $total_amount = $trans['total_amount']; // Số tiền bao gồm phí để trừ người gửi
                $amount_net = $trans['amount'];         // Số tiền thực chuyển để cộng người nhận

                if ($action == 'approve_withdraw') {
                    // BƯỚC A: Đổi trạng thái giao dịch thành THÀNH CÔNG
                    $stmt = $conn->prepare("UPDATE transactions SET status = 'success', note = 'Admin đã duyệt' WHERE id = ?");
                    
                    // BƯỚC B: Trừ tiền người gửi (Vì lúc làm lệnh chưa trừ)
                    $update_sender = $conn->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
                    $update_sender->execute([$total_amount, $user_id]);

                    // BƯỚC C: Nếu là CHUYỂN TIỀN (transfer) -> CỘNG TIỀN CHO NGƯỜI NHẬN
                    if ($trans['type'] == 'transfer') {
                        // A. Cộng tiền cho người nhận
                        $update_receiver = $conn->prepare("UPDATE users SET balance = balance + ? WHERE phone_number = ?");
                        $update_receiver->execute([$amount_net, $trans['receiver_phone']]);

                        // B. Lấy ID của người nhận để lưu vào lịch sử
                        $stmt_rec = $conn->prepare("SELECT id FROM users WHERE phone_number = ?");
                        $stmt_rec->execute([$trans['receiver_phone']]);
                        $receiver_id = $stmt_rec->fetchColumn();

                        // C. QUAN TRỌNG: Tạo một dòng lịch sử giao dịch "+" cho người nhận
                        // Giả sử bảng transactions của Khiêm có các cột như bên dưới
                        $insert_history = $conn->prepare("INSERT INTO transactions (sender_id, amount, type, status, note, receiver_phone) 
                                                        VALUES (?, ?, 'transfer', 'success', ?, ?)");
                        $note_for_receiver = "Nhận tiền từ " . $trans['sender_id']; // Có thể thay bằng tên người gửi nếu muốn
                        $insert_history->execute([$receiver_id, $amount_net, $note_for_receiver, $trans['receiver_phone']]);
                    }

                    $mail_subject = "Giao dịch đã được phê duyệt";
                    $mail_content = "Giao dịch " . number_format($amount_net) . "đ đã thành công. Tài khoản đã được xử lý.";
                } else {
                    // TỪ CHỐI: Chỉ đổi trạng thái, không trừ tiền ai cả
                    $stmt = $conn->prepare("UPDATE transactions SET status = 'refused', note = 'Admin từ chối' WHERE id = ?");
                    
                    $mail_subject = "Giao dịch bị từ chối";
                    $mail_content = "Giao dịch " . number_format($amount_net) . "đ đã bị Admin từ chối.";
                }

                $stmt->execute([$id]);
                $conn->commit();

                // Gửi mail thông báo cho người gửi
                $stmt_u = $conn->prepare("SELECT email FROM users WHERE id = ?");
                $stmt_u->execute([$user_id]);
                $user_email = $stmt_u->fetchColumn();
                sendEmail($user_email, $mail_subject, $mail_content);
            }
        } catch (Exception $e) {
            $conn->rollBack();
            die("Lỗi xử lý: " . $e->getMessage());
        }
    }

    if (isset($stmt)) {
        if ($action != 'approve_withdraw' && $action != 'reject_withdraw') {
             $stmt->execute([$id]);
        }
        header("Location: admin.php");
        exit();
    }
}

// --- 2. LẤY DỮ LIỆU ĐỂ HIỂN THỊ (GIỮ NGUYÊN & CẢI TIẾN TRUY VẤN) ---
$pending_users = $conn->query("SELECT * FROM users WHERE status = 'pending'")->fetchAll();
$locked_users = $conn->query("SELECT * FROM users WHERE status = 'locked'")->fetchAll();

// Lấy cả 'withdraw' và 'transfer' đang chờ duyệt
$sql_pending = "SELECT t.*, u.full_name 
                FROM transactions t 
                JOIN users u ON t.sender_id = u.id 
                WHERE t.status = 'pending' AND (t.type = 'withdraw' OR t.type = 'transfer')";
$pending_withdraws = $conn->query($sql_pending)->fetchAll();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hệ thống Quản trị Admin</title>
    <style>
        body { font-family: sans-serif; background: #f4f7f6; padding: 20px; }
        .container { max-width: 1100px; margin: auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; margin-top: 40px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #007bff; color: white; }
        .btn { padding: 6px 12px; text-decoration: none; border-radius: 4px; font-size: 13px; color: white; display: inline-block; margin: 2px; border: none; cursor: pointer; }
        .btn-verify { background: #28a745; }
        .btn-cancel { background: #dc3545; }
        .btn-info { background: #17a2b8; }
        .btn-unlock { background: #6f42c1; }
        .cccd-img { width: 50px; cursor: pointer; border-radius: 4px; }
        .cccd-img:hover { transform: scale(5); transition: 0.3s; z-index: 100; position: relative; }
        .badge { padding: 3px 8px; border-radius: 12px; font-size: 11px; font-weight: bold; }
    </style>
</head>
<body>

<div class="container">
    <h1>Trang Quản Trị Ví Điện Tử</h1>

    <h2>1. Danh sách chờ duyệt hồ sơ</h2>
    <table>
        <tr>
            <th>Họ tên</th>
            <th>Email</th>
            <th>Ảnh CCCD</th>
            <th>Thao tác</th>
        </tr>
        <?php foreach ($pending_users as $u): ?>
        <tr>
            <td><?= htmlspecialchars($u['full_name']) ?></td>
            <td><?= htmlspecialchars($u['email']) ?></td>
            <td>
                <img src="uploads/<?= $u['id_front_image'] ?>" class="cccd-img" title="Mặt trước">
                <img src="uploads/<?= $u['id_back_image'] ?>" class="cccd-img" title="Mặt sau">
            </td>
            <td>
                <a href="admin.php?id=<?= $u['id'] ?>&action=verify" class="btn btn-verify">Xác thực</a>
                <a href="admin.php?id=<?= $u['id'] ?>&action=cancel" class="btn btn-cancel">Hủy</a>
                <a href="admin.php?id=<?= $u['id'] ?>&action=require_info" class="btn btn-info">Cần bổ sung</a>
            </td>
        </tr>
        <?php endforeach; if(empty($pending_users)) echo "<tr><td colspan='4'>Không có hồ sơ nào chờ duyệt.</td></tr>"; ?>
    </table>

    <h2>2. Duyệt giao dịch (> 5.000.000 VND)</h2>
    <table>
        <tr>
            <th>Loại</th>
            <th>Người gửi</th>
            <th>Người nhận (nếu có)</th>
            <th>Số tiền</th>
            <th>Tổng trừ ví</th>
            <th>Thao tác</th>
        </tr>
        <?php foreach ($pending_withdraws as $tw): ?>
        <tr>
            <td>
                <span class="badge" style="background: <?= $tw['type']=='transfer' ? '#ffc107' : '#17a2b8' ?>; color: #000;">
                    <?= strtoupper($tw['type']) ?>
                </span>
            </td>
            <td><b><?= htmlspecialchars($tw['full_name']) ?></b></td>
            <td><?= $tw['receiver_phone'] ? $tw['receiver_phone'] : 'N/A' ?></td>
            <td><?= number_format($tw['amount']) ?>đ</td>
            <td style="color:red; font-weight:bold;"><?= number_format($tw['total_amount']) ?>đ</td>
            <td>
                <a href="admin.php?id=<?= $tw['id'] ?>&action=approve_withdraw" class="btn btn-verify" onclick="return confirm('Xác nhận duyệt và thực hiện chuyển/rút tiền?')">Đồng ý chi</a>
                <a href="admin.php?id=<?= $tw['id'] ?>&action=reject_withdraw" class="btn btn-cancel" onclick="return confirm('Từ chối giao dịch này?')">Từ chối</a>
            </td>
        </tr>
        <?php endforeach; if(empty($pending_withdraws)) echo "<tr><td colspan='6'>Không có giao dịch nào cần duyệt.</td></tr>"; ?>
    </table>

    <h2>3. Quản lý tài khoản bị khóa (Locked)</h2>
    <table>
        <tr>
            <th>Họ tên</th>
            <th>Email</th>
            <th>Số lần sai</th>
            <th>Thao tác</th>
        </tr>
        <?php foreach ($locked_users as $u): ?>
        <tr>
            <td><?= htmlspecialchars($u['full_name']) ?></td>
            <td><?= htmlspecialchars($u['email']) ?></td>
            <td><span style="color:red"><?= $u['login_attempts'] ?> lần</span></td>
            <td>
                <a href="admin.php?id=<?= $u['id'] ?>&action=unlock" class="btn btn-unlock" onclick="return confirm('Mở khóa tài khoản này?')">Mở khóa (Unlock)</a>
            </td>
        </tr>
        <?php endforeach; if(empty($locked_users)) echo "<tr><td colspan='4'>Hiện không có tài khoản nào bị khóa.</td></tr>"; ?>
    </table>
</div>

</body>
</html>