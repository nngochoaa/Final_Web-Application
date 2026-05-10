<?php 
$page_title = "Transfer Money";
require_once '../includes/header.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

require_once '../../config/db_config.php';
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>
<div class="container mt-5">
    <div class="card mx-auto" style="max-width: 500px;">
        <div class="card-body">
            <h4>Transfer Money via Phone Number</h4>
            <form action="../../process/process_transfer.php" method="POST">
                <div class="mb-3">
                    <label>Receiver's Phone Number</label>
                    <input type="text" name="receiver_phone" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Amount</label>
                    <input type="number" name="amount" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Continue</button>
            </form>
        </div>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>