<?php
session_start();
require_once '../config/db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $card_num = $_POST['card_number'];
    $expiry = $_POST['expiry_date'];
    $cvv = $_POST['cvv'];
    $amount = (int)$_POST['amount'];

    $stmt = $conn->prepare("SELECT * FROM credit_cards WHERE card_number = ? AND expiry_date = ? AND cvv = ?");
    $stmt->execute([$card_num, $expiry, $cvv]);
    $card = $stmt->fetch();

    if (!$card) {
        die("<script>alert('Card information is invalid!'); window.history.back();</script>");
    }

    if ($card_num == '333333') {
        die("<script>alert('This card has no balance!'); window.history.back();</script>");
    }
    if ($card_num == '222222' && $amount > 1000000) {
        die("<script>alert('This card can only deposit up to 1 million VND per transaction!'); window.history.back();</script>");
    }

    try {
        $conn->beginTransaction();
        
        $update = $conn->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
        $update->execute([$amount, $user_id]);

        $log = $conn->prepare("INSERT INTO transactions (sender_id, type, amount, total_amount, status, note) 
                              VALUES (?, 'deposit', ?, ?, 'success', ?)");
        $log->execute([$user_id, $amount, $amount, "Nạp từ thẻ $card_num"]);

        $conn->commit();
        echo "<script>alert('Deposit successful!'); window.location.href='../user/index.php';</script>";
    } catch (Exception $e) {
        $conn->rollBack();
        echo "Error: " . $e->getMessage();
    }
}
?>