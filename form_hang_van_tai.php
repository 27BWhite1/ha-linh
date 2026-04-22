<?php
include 'conn.php';
$id = intval($_GET['id'] ?? 0);
$action = $_GET['action'] ?? 'add';
$data = ['ma_hang' => '', 'ten_hang' => '', 'so_cccd' => '', 'no_cu' => 0];

if ($action == 'edit' && $id) {
    $stmt = $conn->prepare("SELECT * FROM danh_muc_hang_van_tai WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ma  = $_POST['ma_hang'];
    $ten = $_POST['ten_hang'];
    $cccd = $_POST['so_cccd'];
    $no  = floatval($_POST['no_cu']);

    if ($action == 'add') {
        $stmt = $conn->prepare("INSERT INTO danh_muc_hang_van_tai (ma_hang, ten_hang, so_cccd, no_cu) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssd", $ma, $ten, $cccd, $no);
    } else {
        $stmt = $conn->prepare("UPDATE danh_muc_hang_van_tai SET ma_hang=?, ten_hang=?, so_cccd=?, no_cu=? WHERE id=?");
        $stmt->bind_param("sssdi", $ma, $ten, $cccd, $no, $id);
    }
    if ($stmt->execute()) {
        header("Location: danh_muc_hang_van_tai.php");
        exit();
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Cập nhật Hãng vận tải</title>
    <style>
        .form-container { width: 400px; margin: 50px auto; padding: 20px; border: 1px solid #ccc; font-family: Arial; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input { width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ccc; }
        .btn-save { background: #000080; color: white; padding: 10px 20px; border: none; cursor: pointer; }
        .btn-save:hover { background: #0000aa; }
    </style>
</head>
<body>
<div class="form-container">
    <h3><?php echo $action == 'add' ? 'THÊM MỚI' : 'SỬA'; ?> HÃNG VẬN TẢI</h3>
    <form method="POST">
        <div class="form-group">
            <label>Mã hãng:</label>
            <input type="text" name="ma_hang" value="<?php echo htmlspecialchars($data['ma_hang']); ?>" required>
        </div>
        <div class="form-group">
            <label>Tên hãng vận tải:</label>
            <input type="text" name="ten_hang" value="<?php echo htmlspecialchars($data['ten_hang']); ?>" required>
        </div>
        <div class="form-group">
            <label>Số CCCD:</label>
            <input type="text" name="so_cccd" value="<?php echo htmlspecialchars($data['so_cccd']); ?>">
        </div>
        <div class="form-group">
            <label>Nợ cũ:</label>
            <input type="number" name="no_cu" value="<?php echo $data['no_cu']; ?>">
        </div>
        <button type="submit" class="btn-save">LƯU DỮ LIỆU</button>
        <a href="danh_muc_hang_van_tai.php" style="margin-left:10px;">Hủy bỏ</a>
    </form>
</div>
</body>
</html>