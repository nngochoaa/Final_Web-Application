<?php
session_start();
if (!isset($_SESSION['reset_otp'])) { 
    header("Location: " . __DIR__ . "/forgot_password.php"); 
    exit(); 
}

$time_left = 60 - (time() - $_SESSION['otp_time']);
if ($time_left <= 0) {
    unset($_SESSION['reset_otp']);
    echo "<script>alert('OTP has expired!'); window.location.href='forgot_password.php';</script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Verify OTP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="col-md-4 mx-auto bg-white p-4 shadow rounded text-center">
        <h4>Verify OTP</h4>
        <p>Code has been sent to your email.</p>
        <form action="/process/process_verify_reset.php" method="POST">
            <input type="text" name="otp_input" class="form-control mb-3 text-center fs-3" maxlength="6" placeholder="000000" required>
            <button type="submit" class="btn btn-primary w-100">Verify</button>
        </form>
        <div class="mt-3 text-danger fw-bold">
            Remaining time: <span id="timer"><?= $time_left ?></span>s
        </div>
    </div>
</div>

<script>
    let timeLeft = <?= $time_left ?>;
    let timer = setInterval(() => {
        timeLeft--;
        document.getElementById('timer').innerText = timeLeft;
        if(timeLeft <= 0) {
            clearInterval(timer);
            alert("OTP has expired!");
            window.location.href = "forgot_password.php";
        }
    }, 1000);
</script>
</body>
</html>