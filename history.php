<?php
session_start();
include 'db_config.php';
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

$user_id = $_SESSION['user_id'];

// Lấy danh sách giao dịch
$stmt = $conn->prepare("SELECT * FROM transactions WHERE sender_id = ? OR receiver_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id, $user_id]);
$history = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Lịch sử giao dịch</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="d-flex justify-content-between mb-4">
        <h4>📜 Lịch sử giao dịch</h4>
        <a href="buy_card.php" class="btn btn-primary btn-sm">Mua thêm thẻ</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Ngày</th>
                        <th>Loại</th>
                        <th>Số tiền</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($history as $row): ?>
                    <tr>
                        <td><?= $row['created_at'] ?></td>
                        <td>
                            <span class="badge bg-secondary">
                                <?= ($row['type'] == 'buy_card') ? 'Mua thẻ cào' : $row['type'] ?>
                            </span>
                        </td>
                        <td class="fw-bold text-danger">
                            -<?= number_format($row['total_amount']) ?>đ
                        </td>
                        <td><span class="badge bg-success"><?= $row['status'] ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>