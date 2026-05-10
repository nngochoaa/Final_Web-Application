<!-- <?php
include 'db_config.php';

// Xử lý khi bấm nút Unlock
if (isset($_GET['unlock_id'])) {
    $id = $_GET['unlock_id'];
    $stmt = $conn->prepare("UPDATE users SET status = 'verified', login_attempts = 0, lock_until = NULL WHERE id = ?");
    $stmt->execute([$id]);
    echo "<script>alert('Đã mở khóa tài khoản thành công!'); window.location.href='admin_users.php';</script>";
}

// Lấy danh sách user bị khóa
$sql = "SELECT id, full_name, email, status FROM users WHERE status = 'locked'";
$users = $conn->query($sql)->fetchAll();
?>

<h2>Danh sách tài khoản bị khóa</h2>
<table border="1" cellpadding="10">
    <tr>
        <th>Họ tên</th>
        <th>Email</th>
        <th>Trạng thái</th>
        <th>Thao tác</th>
    </tr>
    <?php foreach ($users as $u): ?>
    <tr>
        <td><?= $u['full_name'] ?></td>
        <td><?= $u['email'] ?></td>
        <td><span style="color:red;"><?= $u['status'] ?></span></td>
        <td>
            <a href="admin_users.php?unlock_id=<?= $u['id'] ?>" onclick="return confirm('Mở khóa tài khoản này?')">Mở khóa (Unlock)</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table> -->