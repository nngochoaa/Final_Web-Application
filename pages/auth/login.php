<?php 
$page_title = "Login";
require_once __DIR__ . '/../../includes/header.php'; 

if (isLoggedIn()) {
    header("Location: /user/index.php");
    exit();
}
?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card p-4 shadow">
                <h3 class="text-center mb-4">Login to E-Wallet</h3>
                <form action="/process/process_login.php" method="POST">
                    <div class="mb-3">
                        <input type="text" name="user_input" class="form-control" placeholder="Email or Phone Number" required>
                    </div>
                    <div class="mb-3">
                        <input type="password" name="password" class="form-control" placeholder="Password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>
                <div class="text-center mt-3">
                    <a href="/pages/auth/forgot_password.php">Forgot Password?</a><br>
                    <a href="/pages/auth/register.php">Don't have an account? Register</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once '/Users/macos/Documents/School/Application/Final/includes/footer.php'; ?>