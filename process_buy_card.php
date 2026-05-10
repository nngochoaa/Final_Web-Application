<?php
session_start();
include 'db_config.php';
// Nạp file chứa hàm gửi mail của Khiêm vào đây
require_once 'send_mail.php'; 

// 1. Kiểm tra dữ liệu đầu vào
if ($_SERVER["REQUEST_METHOD"] != "POST" || !isset($_POST['carrier'])) {
    header("Location: buy_card.php");
    exit();
}

$user_id  = $_SESSION['user_id'];
$carrier  = $_POST['carrier'];
$amount   = (int)$_POST['amount'];
$quantity = (int)$_POST['quantity'];
$total_price = $amount * $quantity;

try {
    // 2. Lấy số dư hiện tại và thông tin để gửi mail
    $stmt = $conn->prepare("SELECT balance, email, full_name FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if (!$user || $user['balance'] < $total_price) {
        echo "<script>alert('Số dư không đủ để thực hiện giao dịch!'); window.location.href='buy_card.php';</script>";
        exit();
    }

    $conn->beginTransaction();

    $up = $conn->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
    $up->execute([$total_price, $user_id]);

    $sql_trans = "INSERT INTO transactions (sender_id, receiver_id, type, amount, total_amount, status) 
                  VALUES (?, ?, 'buy_card', ?, ?, 'success')";
    $stmt_trans = $conn->prepare($sql_trans);
    $stmt_trans->execute([$user_id, $user_id, $amount, $total_price]);
    $transaction_id = $conn->lastInsertId();

    $created_cards = []; 
    for ($i = 0; $i < $quantity; $i++) {
        $prefix = ($carrier == 'Viettel') ? '10' : (($carrier == 'Mobifone') ? '20' : '30');
        $card_code = $prefix . mt_rand(10000000, 99999999);
        
        $created_cards[] = $card_code; 

        $stmt_card = $conn->prepare("INSERT INTO phone_cards (transaction_id, carrier, card_code, amount) VALUES (?, ?, ?, ?)");
        $stmt_card->execute([$transaction_id, $carrier, $card_code, $amount]);
    }

    $conn->commit();

    $new_balance = $user['balance'] - $total_price;
    
    $card_list_text = implode(', ', $created_cards);
    
    notifyTransaction(
        $user['email'], 
        $user['full_name'], 
        'buy_card', 
        $total_price, 
        $new_balance
    );


    echo "<script>alert('Mua thẻ thành công! Mã thẻ của bạn là: $card_list_text'); window.location.href='index.php';</script>";

} catch (Exception $e) {
    if ($conn->inTransaction()) $conn->rollBack();
    die("Lỗi hệ thống: " . $e->getMessage());
}