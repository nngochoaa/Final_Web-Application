<?php
session_start();
require_once '../config/db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $target_dir = "../../uploads/";

    if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);

    $front_name = time() . "_front_" . basename($_FILES["id_front"]["name"]);
    $back_name = time() . "_back_" . basename($_FILES["id_back"]["name"]);

    if (move_uploaded_file($_FILES["id_front"]["tmp_name"], $target_dir . $front_name) &&
        move_uploaded_file($_FILES["id_back"]["tmp_name"], $target_dir . $back_name)) {

        $stmt = $conn->prepare("UPDATE users SET id_front_image = ?, id_back_image = ?, status = 'pending' WHERE id = ?");
        $stmt->execute([$front_name, $back_name, $user_id]);

        echo "<script>alert('Update images successful! Please wait for Admin review.'); window.location.href='../user/index.php';</script>";
    } else {
        echo "<script>alert('Error uploading images!'); history.back();</script>";
    }
}
?>