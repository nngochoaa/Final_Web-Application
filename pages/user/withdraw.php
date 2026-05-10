<?php 
$page_title = "Withdraw Money";
require_once '../includes/header.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php"); exit();
}
require_once '../../config/db_config.php';

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>
<div class="container mt-5">
    <div class="card mx-auto" style="max-width: 500px;">
        <div class="card-body">
            <h4 class="text-center">Withdraw Money</h4>
            <p>Current Balance: <strong><?= number_format($user['balance']) ?> VND</strong></p>
            <form action="../../process/process_withdraw.php" method="POST">
                <div class="mb-3">
                    <label>Amount to Withdraw (multiple of 50,000 VND)</label>
                    <input type="number" name="amount" class="form-control" step="50000" required>
                </div>
                <button type="submit" class="btn btn-danger w-100">Confirm Withdrawal</button>
            </form>
        </div>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>