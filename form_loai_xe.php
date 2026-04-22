<?php
include 'conn.php';
$action = $_GET['action'] ?? 'add';
$id = $_GET['id'] ?? '';
$row = ['ma_loai_xe' => '', 'phan_tram_luong_lai_xe' => '0'];
if($action == 'edit' && $id) {
    $res = $conn->query("SELECT * FROM `danh_muc_loai_xe` WHERE id = " . intval($id));
    if($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
    }
}
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ma = $_POST['ma_loai_xe'];
    $pt = $_POST['phan_tram_luong_lai_xe'];

    if($action == 'add') {
        $sql = "INSERT INTO `danh_muc_loai_xe` (ma_loai_xe, phan_tram_luong_lai_xe) VALUES ('$ma', '$pt')";
    } else {
        $sql = "UPDATE `danh_muc_loai_xe` SET ma_loai_xe='$ma', phan_tram_luong_lai_xe='$pt' WHERE id=" . intval($id);
    }
    if($conn->query($sql)) {
        header("Location: danh_muc_loai_xe.php");
        exit();
    } else {
        $error_msg = "Lỗi: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?php echo ($action == 'add' ? 'Thêm mới' : 'Sửa'); ?> Loại xe</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f0f0f0; display: flex; justify-content: center; padding-top: 50px; }
        .form-container { background: white; width: 400px; border: 1px solid #000080; box-shadow: 2px 2px 10px rgba(0,0,0,0.1); }
        .form-header { background: #000080; color: white; padding: 10px; font-weight: bold; text-align: center; text-transform: uppercase; }
        .form-body { padding: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-size: 13px; font-weight: bold; margin-bottom: 5px; color: #333; }
        .form-group input { width: 100%; padding: 6px; border: 1px solid #ccc; box-sizing: border-box; }
        .form-footer { background: #eee; padding: 10px; text-align: center; border-top: 1px solid #ccc; display: flex; justify-content: center; gap: 10px; }
        .btn-save { background: #000080; color: white; border: none; padding: 8px 20px; cursor: pointer; font-weight: bold; }
        .btn-save:hover { background: #0000a0; }
        .btn-cancel { background: #ccc; color: #333; text-decoration: none; padding: 7px 20px; font-size: 13px; border: 1px solid #999; }
        .error { color: red; font-size: 12px; margin-bottom: 10px; text-align: center; }
    </style>
</head>
<body>
<div class="form-container">
    <div class="form-header">
        <?php echo ($action == 'add' ? 'THÊM MỚI' : 'CHỈNH SỬA'); ?> LOẠI XE
    </div>
    
    <form action="" method="POST">
        <div class="form-body">
            <?php if(isset($error_msg)) echo "<div class='error'>$error_msg</div>"; ?>
            
            <div class="form-group">
                <label>Mã Loại xe:</label>
                <input type="text" name="ma_loai_xe" value="<?php echo htmlspecialchars($row['ma_loai_xe']); ?>" required autofocus>
            </div>
            
            <div class="form-group">
                <label>% lương lái du lịch:</label>
                <input type="number" name="phan_tram_luong_lai_xe" value="<?php echo htmlspecialchars($row['phan_tram_luong_lai_xe']); ?>">
            </div>
        </div>
        
        <div class="form-footer">
            <button type="submit" class="btn-save">LƯU DỮ LIỆU</button>
            <a href="danh_muc_loai_xe.php" class="btn-cancel">HỦY BỎ</a>
        </div>
    </form>
</div>
</body>
</html>