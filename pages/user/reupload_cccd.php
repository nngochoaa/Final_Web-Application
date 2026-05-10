<?php 
$page_title = "Update CCCD";
require_once '../includes/header.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php"); exit();
}
?>
<div class="container mt-5">
    <div class="card mx-auto" style="max-width: 500px;">
        <div class="card-body">
            <h4 class="text-center text-danger">Reupload CCCD Image</h4>
            <form action="../../process/process_reupload.php" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label>Front of CCCD</label>
                    <input type="file" name="id_front" class="form-control" accept="image/*" required>
                </div>
                <div class="mb-3">
                    <label>Back of CCCD</label>
                    <input type="file" name="id_back" class="form-control" accept="image/*" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Reupload Images</button>
            </form>
        </div>
    </div>
</div>
<?php require_once '../includes/footer.php'; ?>