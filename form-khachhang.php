<?php
include 'conn.php';
$action = $_GET['action'] ?? 'add';
$id = $_GET['id'] ?? '';
$title = ($action == 'edit') ? "SỬA THÔNG TIN KHÁCH HÀNG" : "THÊM KHÁCH HÀNG MỚI";
$row = ['ma_khachhang' => '', 'ten_khachhang' => '', 'so_cccd' => '', 'no_cu' => 0];
if ($action == 'edit' && $id) {
    $res = $conn->query("SELECT * FROM danh_muc_khach_hang WHERE id = $id");
    $row = $res->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="form-container" style="width: 500px; margin: 50px auto; border: 1px solid #ccc; padding: 20px;">
        <h2 style="color: #be0000; text-align: center;"><?= $title ?></h2>
        <form action="xuly_khachhang.php" method="POST">
            <input type="hidden" name="id" value="<?= $id ?>">
            <input type="hidden" name="action" value="<?= $action ?>">

            <div style="margin-bottom: 10px;">
                <label>Mã khách hàng:</label><br>
                <input type="text" name="ma_khachhang" value="<?= $row['ma_khachhang'] ?>" required style="width: 100%; padding: 8px;">
            </div>

            <div style="margin-bottom: 10px;">
                <label>Tên khách hàng:</label><br>
                <input type="text" name="ten_khachhang" value="<?= $row['ten_khachhang'] ?>" required style="width: 100%; padding: 8px;">
            </div>

            <div style="margin-bottom: 10px;">
                <label>Số CCCD:</label><br>
                <input type="text" name="so_cccd" value="<?= $row['so_cccd'] ?>" style="width: 100%; padding: 8px;">
            </div>

            <div style="margin-bottom: 20px;">
                <label>Nợ cũ:</label><br>
                <input type="number" name="no_cu" value="<?= $row['no_cu'] ?>" style="width: 100%; padding: 8px;">
            </div>

            <div style="text-align: right;">
                <button type="submit" style="background: green; color: white; border: none; padding: 10px 20px; cursor: pointer;">LƯU LẠI</button>
                <button type="button" onclick="location.href='danh-muc-khachhang.php'" style="background: #666; color: white; border: none; padding: 10px 20px; cursor: pointer;">HỦY</button>
            </div>
        </form>
    </div>
</body>
</html>