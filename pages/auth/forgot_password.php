<?php 
$page_title = "Quên mật khẩu";
require_once __DIR__ . '/../../includes/header.php'; 
?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card p-4 shadow">
                <h3 class="text-center mb-4">Quên mật khẩu</h3>
                <form action="/process/process_forgot_pass.php" method="POST">
                    <div class="mb-3">
                        <label>Login Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Phone number</label>
                        <input type="text" name="phone" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Gửi mã OTP</button>
                </form>
                <div class="text-center mt-3">
                    <a href="login.php">← Quay lại đăng nhập</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>