<?php 
$page_title = "Nạp tiền";
require_once '../includes/header.php'; 

if (!isset($_SESSION['user_id'])) header("Location: ../auth/login.php");
require_once '../../config/db_config.php';

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>
<div class="container mt-5">
    <div class="card mx-auto" style="max-width: 500px;">
        <div class="card-body">
            <h4 class="text-center">Nạp tiền vào ví</h4>
            <p class="text-center">Số dư: <strong><?= number_format($user['balance']) ?> VND</strong></p>
            <form action="../../process/process_deposit.php" method="POST">
                <div class="mb-3">
                    <label>Card Number (6 digits)</label>
                    <input type="text" name="card_number" maxlength="6" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Expiry Date</label>
                    <input type="date" name="expiry_date" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>CVV</label>
                    <input type="text" name="cvv" maxlength="3" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Amount</label>
                    <input type="number" name="amount" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-success w-100">Confirm Deposit</button>
            </form>
        </div>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>