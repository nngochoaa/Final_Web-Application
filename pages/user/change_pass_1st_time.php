<?php 
$page_title = "Change Password First Time";
require_once '../includes/header.php'; 
?>
<div class="container mt-5">
    <div class="card mx-auto" style="max-width: 450px;">
        <div class="card-body">
            <h4 class="text-center">Change Password First Time</h4>
            <form action="/Users/macos/Documents/School/Application/Final/process/process_change_pass_first.php" method="POST">
                <div class="mb-3">
                    <label>New Password</label>
                    <input type="password" name="new_pass" class="form-control" minlength="6" required>
                </div>
                <div class="mb-3">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_pass" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-warning w-100">Update</button>
            </form>
        </div>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>