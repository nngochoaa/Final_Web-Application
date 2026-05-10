<?php
session_start();
require_once '../../config/db_config.php';

$otp_input = $_POST['otp_input'];
$current_time = time();

if ($otp_input != $_SESSION['otp_code'] || ($current_time - $_SESSION['otp_time']) > 60) {
    die("<script>alert('OTP wrong or expired'); window.location.href='transfer.php';</script>");
}

if (!isset($_SESSION['pending_transfer'])) {
    die("<script>alert('No pending transfer found'); window.location.href='transfer.php';</script>");
}

$data = $_SESSION['pending_transfer'];
$sender_id = $_SESSION['user_id'];
$receiver_phone = $data['receiver_phone'];
$amount = $data['amount'];
$fee_payer = $data['fee_payer'];
$note = $data['note'] ?? '';
$fee = $amount * 0.05;

try {
    $conn->beginTransaction();

    $stmt = $conn->prepare("SELECT id FROM users WHERE phone_number = ?");
    $stmt->execute([$receiver_phone]);
    $receiver = $stmt->fetch();

    if (!$receiver) throw new Exception("Không tìm thấy người nhận.");

    if ($fee_payer == 'sender') {
        $total_deduct = $amount + $fee;
        $total_receive = $amount;
    } else {
        $total_deduct = $amount;
        $total_receive = $amount - $fee;
    }

    $status = ($amount > 5000000) ? 'pending' : 'success';

    if ($status == 'success') {
        $upSender = $conn->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
        $upSender->execute([$total_deduct, $sender_id]);

        $upReceiver = $conn->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
        $upReceiver->execute([$total_receive, $receiver['id']]);
    }

    $insLog = $conn->prepare("INSERT INTO transactions (sender_id, receiver_id, type, amount, fee, total_amount, status, note) 
                             VALUES (?, ?, 'transfer', ?, ?, ?, ?, ?)");
    $insLog->execute([$sender_id, $receiver['id'], $amount, $fee, $total_deduct, $status, $note]);

    $conn->commit();

    unset($_SESSION['otp_code'], $_SESSION['otp_time'], $_SESSION['pending_transfer']);

    $msg = ($status == 'success') ? "Transfer successful!" : "Transaction > 5tr is pending Admin approval!";
    echo "<script>alert('$msg'); window.location.href='../index.php';</script>";

} catch (Exception $e) {
    $conn->rollBack();
    echo "Error: " . $e->getMessage();
}
?>