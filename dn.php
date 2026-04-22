<?php
include 'conn.php';
session_start();
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (!empty($username) && !empty($password)) {
        $result = $conn->query("SELECT * FROM users WHERE username = '$username'");
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if ($password === $user['password']) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['fullname'] = $user['fullname'];
                $_SESSION['role'] = $user['role'];
                header("Location: index.php");
                exit();
            } else {
                $error = "Mật khẩu không chính xác!";
            }
        } else {
            $error = "Tài khoản không tồn tại!";
        }
    } else {
        $error = "Vui lòng nhập tài khoản và mật khẩu!";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập - Hà Linh Transport</title>
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
            background: white; width: 480px; padding: 40px;
            border-radius: 15px; box-shadow: 0 15px 35px rgba(0,0,0,0.4);
        }
        .logo-text { font-size: 22px; font-weight: 800; color: var(--ha-linh-red); margin-bottom: 25px; display: block; text-decoration: none; text-align: center; }
        h2 { text-align: center; color: #333; margin-bottom: 30px; }
        .form-group {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        .form-group label {
            width: 100px; 
            text-align: left;
            font-weight: bold;
            color: #555;
        }
        .form-group input {
            flex: 1; 
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            outline: none;
        }
        .form-group input:focus { border-color: var(--ha-linh-red); }
        .btn-submit {
            width: 100%;
            padding: 13px;
            background: var(--ha-linh-red);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 10px;
        }
        .btn-submit:hover { background: var(--ha-linh-dark); }
        .password-options {
            display: flex;
            justify-content: space-between;
            margin-left: 100px; 
            margin-bottom: 20px;
            font-size: 13px;
        }
        .show-pass { cursor: pointer; display: flex; align-items: center; }
        .show-pass input { margin-right: 5px; }
        .forgot-link { color: var(--ha-linh-red); text-decoration: none; font-weight: bold; }

        .switch-link { text-align: center; margin-top: 25px; font-size: 14px; color: #666; }
        .switch-link a { color: var(--ha-linh-red); text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>
<div class="auth-container">
    <a href="index.php" class="logo-text">HA LINH TRANSPORT</a>
    <h2>ĐĂNG NHẬP</h2>

    <form action="dn.php" method="POST">
        <div class="form-group">
            <label for="username">Tài khoản:</label>
            <input type="text" name="username" id="username" placeholder="Số điện thoại hoặc Email" required>
        </div>

        <div class="form-group">
            <label for="passwordField">Mật khẩu:</label>
            <input type="password" name="password" id="passwordField" placeholder="Nhập mật khẩu" required>
        </div>

        <div class="password-options">
            <label class="show-pass">
                <input type="checkbox" onclick="togglePassword()"> Hiện mật khẩu
            </label>
            <a href="quen_mk.php" class="forgot-link">Quên mật khẩu?</a>
        </div>

        <button type="submit" class="btn-submit">ĐĂNG NHẬP</button>
    </form>

    <div class="switch-link">
        Chưa có tài khoản? <a href="dk.php">Đăng ký ngay</a>
    </div>
</div>
<script>
    function togglePassword() {
        var x = document.getElementById("passwordField");
        x.type = (x.type === "password") ? "text" : "password";
    }
</script>
</body>
</html>