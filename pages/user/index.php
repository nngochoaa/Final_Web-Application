<?php 
$page_title = "Dashboard";
require_once '../includes/header.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

require_once '../../config/db_config.php';
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if ($user['is_first_login'] == 1) {
    header("Location: change_password_first_time.php");
    exit();
}
?>
<div class="container mt-4">
    <div class="row">
        <div class="col-lg-4">
            <div class="card balance-card p-4 text-center mb-4">
                <h5>AVAILABLE BALANCE</h5>
                <h1 class="balance-amount"><?= number_format($user['balance'], 0, ',', '.') ?> VND</h1>
                <span class="badge bg-success">Verified</span>
            </div>
        </div>
        
        <div class="col-lg-8">
            <div class="row g-3">
                <div class="col-6 col-md-3">
                    <a href="deposit.php" class="btn btn-success w-100 py-4">DEPOSIT</a>
                </div>
                <div class="col-6 col-md-3">
                    <a href="withdraw.php" class="btn btn-danger w-100 py-4">WITHDRAW</a>
                </div>
                <div class="col-6 col-md-3">
                    <a href="transfer.php" class="btn btn-primary w-100 py-4">TRANSFER</a>
                </div>
                <div class="col-6 col-md-3">
                    <a href="get_user_info.php" class="btn btn-info w-100 py-4">PROFILE</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>