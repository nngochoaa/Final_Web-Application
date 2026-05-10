<?php 
$page_title = "Đăng ký";
require_once __DIR__ . '/../../includes/header.php'; 
?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card p-4 shadow">
                <h3 class="text-center mb-4">Đăng ký tài khoản</h3>
                <form action="/process/process_register.php" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <input type="text" name="full_name" class="form-control" placeholder="Full Name" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <input type="date" name="dob" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <input type="text" name="phone" class="form-control" placeholder="Phone Number" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <input type="email" name="email" class="form-control" placeholder="Email" required>
                    </div>
                    <div class="mb-3">
                        <textarea name="address" class="form-control" placeholder="Address" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label>Front of ID Card</label>
                        <input type="file" name="id_front" class="form-control" accept="image/*" required>
                    </div>
                    <div class="mb-3">
                        <label>Back of ID Card</label>
                        <input type="file" name="id_back" class="form-control" accept="image/*" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Register</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>