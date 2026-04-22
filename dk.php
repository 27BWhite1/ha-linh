<?php
include 'conn.php';
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = $_POST['fullname'];
    $username = $_POST['username']; 
    $password = $_POST['password']; 
    $role = $_POST['role']; 

    if (!empty($username) && !empty($password) && !empty($role)) {
        $check = $conn->query("SELECT * FROM users WHERE username = '$username'");
        if ($check && $check->num_rows > 0) {
            $error = "Số điện thoại này đã được đăng ký!";
        } else {
            $sql = "INSERT INTO users (username, password, fullname, role) 
                    VALUES ('$username', '$password', '$fullname', '$role')";
            if ($conn->query($sql)) {
                echo "<script>alert('Đăng ký thành công với quyền $role!'); window.location.href='dn.php';</script>";
                exit();
            } else {
                $error = "Lỗi hệ thống: " . $conn->error;
            }
        }
    } else {
        $error = "Vui lòng nhập đầy đủ thông tin!";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký - Hà Linh Transport</title>
    <style>
        :root { --ha-linh-red: #be0000; --ha-linh-dark: #8b0000; }
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), 
                        url('https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?auto=format&fit=crop&q=80&w=2069');
            background-size: cover; background-position: center;
            height: 100vh; display: flex; justify-content: center; align-items: center; margin: 0;
        }
        .auth-container {
            background: white; width: 380px; padding: 40px;
            border-radius: 15px; box-shadow: 0 15px 35px rgba(0,0,0,0.4);
            text-align: center;
        }
        .logo-text { font-size: 22px; font-weight: 800; color: var(--ha-linh-red); margin-bottom: 30px; display: block; text-decoration: none; }
        h2 { margin-bottom: 20px; color: #333; }
        input, select { 
            width: 100%; padding: 12px; margin-bottom: 15px; 
            border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; 
            font-family: inherit;
        }
        select { background-color: #fff; cursor: pointer; }
        .btn-submit { width: 100%; padding: 12px; background: var(--ha-linh-red); color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; transition: 0.3s; }
        .btn-submit:hover { background: var(--ha-linh-dark); }
        .switch-link { margin-top: 20px; font-size: 14px; color: #666; }
        .switch-link a { color: var(--ha-linh-red); text-decoration: none; font-weight: bold; }
        .error-msg { color: red; font-size: 13px; margin-bottom: 10px; }
        label { display: block; text-align: left; margin-bottom: 5px; font-size: 14px; font-weight: bold; color: #555; }
    </style>
</head>
<body>
<div class="auth-container">
    <a href="index.php" class="logo-text">HA LINH TRANSPORT</a>
    <h2>ĐĂNG KÝ</h2>
    <?php if($error) echo "<div class='error-msg'>$error</div>"; ?>
    <form action="dk.php" method="POST">
        <input type="text" name="fullname" placeholder="Họ và tên" required>
        <input type="tel" name="username" placeholder="Số điện thoại" required>
        <input type="password" name="password" placeholder="Mật khẩu" required>
        
        <label for="role">Kiểu tài khoản:</label>
        <select name="role" id="role" required>
            <option value="Nhân Viên">Nhân Viên</option>
            <option value="Kế Toán">Kế Toán</option>
            <option value="Quản lý">Quản lý</option>
        </select>

        <button type="submit" class="btn-submit">TẠO TÀI KHOẢN</button>
    </form>
    <div class="switch-link">
        Đã có tài khoản? <a href="dn.php">Đăng nhập</a>
    </div>
</div>
</body>
</html>