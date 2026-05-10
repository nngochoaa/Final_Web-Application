<?php
session_start();
require_once '../config/db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $amount = (int)$_POST['amount'];
    $fee = $amount * 0.05;
    $total_deduct = $amount + $fee;

    if ($amount % 50000 != 0) {
        die("<script>alert('Amount must be a multiple of 50,000!'); history.back();</script>");
    }

    $stmt = $conn->prepare("SELECT balance FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if ($total_deduct > $user['balance']) {
        die("<script>alert('Insufficient balance!'); history.back();</script>");
    }

    $status = ($amount > 5000000) ? 'pending' : 'success';
    $message = ($status == 'success') ? 'Withdrawal successful!' : 'Withdrawal request is pending admin approval!';

    try {
        $conn->beginTransaction();

        if ($status == 'success') {
            $update = $conn->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
            $update->execute([$total_deduct, $user_id]);
        }

        $log = $conn->prepare("INSERT INTO transactions (sender_id, type, amount, fee, total_amount, status) 
                              VALUES (?, 'withdraw', ?, ?, ?, ?)");
        $log->execute([$user_id, $amount, $fee, $total_deduct, $status]);

        $conn->commit();
        echo "<script>alert('$message'); window.location.href='../user/index.php';</script>";
    } catch (Exception $e) {
        $conn->rollBack();
        echo "Error: " . $e->getMessage();
    }
}
?>