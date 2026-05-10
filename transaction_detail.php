<?php
session_start();
require_once 'db_config.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$trans_id = $_GET['id'];

// 1. Lấy thông tin giao dịch
$stmt = $conn->prepare("SELECT * FROM transactions WHERE id = ? AND (sender_id = ? OR receiver_id = ?)");
$stmt->execute([$trans_id, $user_id, $user_id]);
$trans = $stmt->fetch();

if (!$trans) {
    die("Giao dịch không tồn tại hoặc bạn không có quyền xem.");
}

// 2. Nếu là giao dịch mua thẻ, lấy thêm danh sách mã thẻ
$cards = [];
if ($trans['type'] == 'buy_card') {
    $stmt_cards = $conn->prepare("SELECT * FROM phone_cards WHERE transaction_id = ?");
    $stmt_cards->execute([$trans_id]);
    $cards = $stmt_cards->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi tiết giao dịch #<?= $trans['id'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <div class="display-6 text-primary mb-2">
                            <i class="bi <?= $trans['type'] == 'buy_card' ? 'bi-phone' : 'bi-arrow-left-right' ?>"></i>
                        </div>
                        <h4 class="fw-bold">Chi tiết giao dịch</h4>
                        <span class="badge bg-success-subtle text-success rounded-pill px-3">Thành công</span>
                    </div>

                    <div class="list-group list-group-flush mb-4">
                        <div class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Mã giao dịch</span>
                            <span class="fw-bold text-dark">#<?= $trans['id'] ?></span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Thời gian</span>
                            <span class="text-dark"><?= date('H:i:s d/m/Y', strtotime($trans['created_at'])) ?></span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Loại dịch vụ</span>
                            <span class="text-dark fw-bold"><?= $trans['type'] == 'buy_card' ? 'Mua thẻ điện thoại' : 'Chuyển tiền' ?></span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Số tiền thanh toán</span>
                            <span class="text-danger fw-bold fs-5"><?= number_format($trans['total_amount']) ?>đ</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between px-0 border-0">
                            <span class="text-muted">Phí giao dịch</span>
                            <span class="text-dark">0đ</span>
                        </div>
                    </div>

                    <?php if ($trans['type'] == 'buy_card' && count($cards) > 0): ?>
                        <div class="bg-light p-3 rounded-4 border border-dashed">
                            <h6 class="fw-bold mb-3"><i class="bi bi-ticket-perforated me-2"></i>Mã thẻ đã mua:</h6>
                            <?php foreach ($cards as $index => $card): ?>
                                <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-white rounded-3 shadow-sm">
                                    <div>
                                        <small class="text-muted d-block"><?= $card['carrier'] ?> - <?= number_format($card['amount']) ?>đ</small>
                                        <span class="fw-bold fs-5 text-primary"><?= $card['card_code'] ?></span>
                                    </div>
                                    <button class="btn btn-sm btn-light" onclick="navigator.clipboard.writeText('<?= $card['card_code'] ?>')">Copy</button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <div class="mt-4">
                        <a href="index.php" class="btn btn-primary w-100 rounded-pill py-2 fw-bold shadow-sm">Quay lại trang chủ</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>