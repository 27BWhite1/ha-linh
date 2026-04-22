<?php
include 'conn.php';
$action = $_GET['action'] ?? 'add';
$id = $_GET['id'] ?? '';
$row = ['username' => '', 'password' => '', 'role' => 'Nhân Viên'];

if ($action == 'edit' && $id) {
    $res = $conn->query("SELECT * FROM users WHERE id = $id");
    $row = $res->fetch_assoc();
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = $_POST['username'];
    $role = $_POST['role'];
    $pass = $_POST['password']; 

    if ($action == 'add') {
        $conn->query("INSERT INTO users (username, password, role) VALUES ('$user', '$pass', '$role')");
    } else {
        $conn->query("UPDATE users SET username='$user', password='$pass', role='$role' WHERE id=$id");
    }
    echo "<script>window.parent.closeModal();</script>";
}
?>
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial; padding: 20px; font-size: 13px; background: #f9f9f9; }
        .form-group { margin-bottom: 10px; display: flex; align-items: center; }
        .form-group label { width: 100px; }
        .form-group input, .form-group select { flex: 1; padding: 5px; border: 1px solid #ccc; }
        .footer { text-align: center; margin-top: 20px; border-top: 1px solid #ccc; padding-top: 15px; }
        .btn-save { background: #000080; color: #fff; padding: 8px 20px; border: none; cursor: pointer; font-weight: bold; }
    </style>
</head>
<body>
    <form method="POST">
        <div class="form-group">
            <label>ID:</label>
            <input type="text" value="<?php echo $id; ?>" disabled placeholder="Tự động">
        </div>
        <div class="form-group">
            <label>Tài Khoản:</label>
            <input type="text" name="username" value="<?php echo $row['username']; ?>" required>
        </div>
        <div class="form-group">
            <label>Mật Khẩu:</label>
            <input type="text" name="password" value="<?php echo $row['password']; ?>" required>
        </div>
        <div class="form-group">
            <label>Kiểu Tài Khoản:</label>
            <select name="role">
                <option value="Quản lý" <?php if($row['role'] == 'Quản lý') echo 'selected'; ?>>Quản lý</option>
                <option value="Kế Toán" <?php if($row['role'] == 'Kế Toán') echo 'selected'; ?>>Kế Toán</option>
                <option value="Nhân Viên" <?php if($row['role'] == 'Nhân Viên') echo 'selected'; ?>>Nhân Viên</option>
            </select>
        </div>
        <div class="footer">
            <button type="submit" class="btn-save">LƯU DỮ LIỆU</button>
            <button type="button" onclick="window.parent.closeModal()">HỦY BỎ</button>
        </div>
    </form>
</body>
</html>