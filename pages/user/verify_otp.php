<?php
session_start();
if (!isset($_SESSION['otp_code'])) { 
    header("Location: transfer.php"); 
    exit(); 
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Xác thực OTP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="col-md-4 mx-auto bg-white p-4 shadow rounded text-center">
        <h4>Confirm Transaction</h4>
        <p>Please enter the OTP sent to your email.</p>
        <form action="../../process/confirm_transfer.php" method="POST">
            <input type="text" name="otp_input" class="form-control mb-3 text-center fs-3" maxlength="6" placeholder="000000" required>
            <button type="submit" class="btn btn-success w-100">Confirm Transfer</button>
        </form>
        <div class="mt-3">
            <small id="timer">Time remaining: 60s</small>
        </div>
    </div>
</div>

<script>
    let timeLeft = 60;
    let timer = setInterval(function(){
        timeLeft--;
        document.getElementById('timer').innerText = "Time remaining: " + timeLeft + "s";
        if(timeLeft <= 0) {
            clearInterval(timer);
            alert("OTP has expired!");
            window.location.href = "transfer.php";
        }
    }, 1000);
</script>
</body>
</html>