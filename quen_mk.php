<?php
include 'conn.php';
$msg = "";
$msg_type = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username     = $_POST['username'] ?? '';
    $fullname     = $_POST['fullname'] ?? '';
    $new_password = $_POST['new_password'] ?? '';

    if (!empty($username) && !empty($fullname) && !empty($new_password)) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND fullname = ?");
        $stmt->bind_param("ss", $username, $fullname);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $stmt2 = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");
            $stmt2->bind_param("ss", $new_password, $username);
            if ($stmt2->execute()) {
                $msg = "Đổi mật khẩu thành công! Hãy quay lại đăng nhập.";
                $msg_type = "success";
            } else {
                $msg = "Lỗi hệ thống khi cập nhật.";
                $msg_type = "error";
            }
            $stmt2->close();
        } else {
            $msg = "Thông tin Số điện thoại hoặc Họ tên không chính xác!";
            $msg_type = "error";
        }
        $stmt->close();
    } else {
        $msg = "Vui lòng nhập đầy đủ thông tin!";
        $msg_type = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quên mật khẩu - HA LINH TRANSPORT</title>
    <style>
        :root { --ha-linh-red: #be0000; --ha-linh-dark: #8b0000; }
        body { font-family: 'Segoe UI', sans-serif; background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?auto=format&fit=crop&q=80&w=2069'); background-size: cover; background-position: center; height: 100vh; display: flex; justify-content: center; align-items: center; margin: 0; }
        .auth-container { background: white; width: 480px; padding: 40px; border-radius: 15px; box-shadow: 0 15px 35px rgba(0,0,0,0.4); }
        .logo-text { font-size: 22px; font-weight: 800; color: var(--ha-linh-red); margin-bottom: 25px; display: block; text-decoration: none; text-align: center; }
        h2 { text-align: center; color: #333; margin-bottom: 30px; }
        .form-group { display: flex; align-items: center; margin-bottom: 15px; }
        .form-group label { width: 120px; text-align: left; font-weight: bold; color: #555; }
        .form-group input { flex: 1; padding: 12px; border: 1px solid #ddd; border-radius: 8px; outline: none; }
        .form-group input:focus { border-color: var(--ha-linh-red); }
        .btn-submit { width: 100%; padding: 13px; background: var(--ha-linh-red); color: white; border: none; border-radius: 8px; font-weight: bold; font-size: 16px; cursor: pointer; transition: 0.3s; margin-top: 10px; }
        .btn-submit:hover { background: var(--ha-linh-dark); }
        .switch-link { text-align: center; margin-top: 25px; font-size: 14px; color: #666; }
        .switch-link a { color: var(--ha-linh-red); text-decoration: none; font-weight: bold; }
        .msg-error { color: red; font-size: 14px; margin-bottom: 15px; text-align: center; }
        .msg-success { color: green; font-size: 14px; margin-bottom: 15px; font-weight: bold; text-align: center; }
    </style>
</head>
<body>
<div class="auth-container">
    <a href="index.php" class="logo-text">HA LINH TRANSPORT</a>
    <h2>KHÔI PHỤC MẬT KHẨU</h2>

    <?php if ($msg != ""): ?>
        <div class="<?php echo $msg_type == 'success' ? 'msg-success' : 'msg-error'; ?>"><?php echo $msg; ?></div>
    <?php endif; ?>
    <form action="quen_mk.php" method="POST">
        <div class="form-group">
            <label>Tài khoản:</label>
            <input type="tel" name="username" placeholder="Nhập số điện thoại" required>
        </div>
        <div class="form-group">
            <label>Họ và tên:</label>
            <input type="text" name="fullname" placeholder="Nhập họ tên đã đăng ký" required>
        </div>
        <div class="form-group">
            <label>Mật khẩu mới:</label>
            <input type="password" name="new_password" placeholder="Nhập mật khẩu mới" required>
        </div>
        <button type="submit" class="btn-submit">ĐẶT LẠI MẬT KHẨU</button>
    </form>
    <div class="switch-link"><a href="dn.php">Quay lại Đăng nhập</a></div>
</div>
</body>
</html>