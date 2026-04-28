<?php
include 'conn.php';
$action = $_GET['action'] ?? 'add';
$id = intval($_GET['id'] ?? 0);
$row = ['ma_lai_xe'=>'','ten_lai_xe'=>'','so_cccd'=>'','luong_trach_nhiem'=>0,'da_nghi'=>0,'ngay_nghi'=>''];

if ($action == 'edit' && $id) {
    $stmt = $conn->prepare("SELECT * FROM danh_muc_lai_xe WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ma    = $_POST['ma_lai_xe'];
    $ten   = $_POST['ten_lai_xe'];
    $cccd  = $_POST['so_cccd'];
    $luong = floatval($_POST['luong_trach_nhiem']);
    $nghi  = isset($_POST['da_nghi']) ? 1 : 0;
    $ngay_nghi = !empty($_POST['ngay_nghi']) ? $_POST['ngay_nghi'] : null;

    if ($action == 'add') {
        $stmt = $conn->prepare("INSERT INTO danh_muc_lai_xe (ma_lai_xe, ten_lai_xe, so_cccd, luong_trach_nhiem, da_nghi, ngay_nghi) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssdis", $ma, $ten, $cccd, $luong, $nghi, $ngay_nghi);
    } else {
        $stmt = $conn->prepare("UPDATE danh_muc_lai_xe SET ma_lai_xe=?, ten_lai_xe=?, so_cccd=?, luong_trach_nhiem=?, da_nghi=?, ngay_nghi=? WHERE id=?");
        $stmt->bind_param("sssdisi", $ma, $ten, $cccd, $luong, $nghi, $ngay_nghi, $id);
    }
    if ($stmt->execute()) {
        echo "<script>window.parent.closeModal();</script>";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { 
            font-family: Arial; 
            padding: 20px; 
            font-size: 13px; 
        }
        .form-group { margin-bottom: 10px; }
        label { 
            display: inline-block; 
            width: 130px; 
        }
        input[type="text"], input[type="number"], input[type="date"] { width: 220px; padding: 5px; border: 1px solid #ccc; }
        .footer { 
            margin-top: 20px; 
            text-align: center; 
        }
    </style>
</head>
<body>
<form method="POST">
    <div class="form-group">
        <label>Mã lái xe:</label>
        <input type="text" name="ma_lai_xe" value="<?php echo htmlspecialchars($row['ma_lai_xe']); ?>" required>
    </div>
    <div class="form-group">
        <label>Tên lái xe:</label>
        <input type="text" name="ten_lai_xe" value="<?php echo htmlspecialchars($row['ten_lai_xe']); ?>" required>
    </div>
    <div class="form-group">
        <label>Số CCCD:</label>
        <input type="text" name="so_cccd" value="<?php echo htmlspecialchars($row['so_cccd']); ?>">
    </div>
    <div class="form-group">
        <label>Lương trách nhiệm:</label>
        <input type="number" name="luong_trach_nhiem" value="<?php echo $row['luong_trach_nhiem']; ?>">
    </div>
    <div class="form-group">
        <label>Đã nghỉ:</label>
        <input type="checkbox" name="da_nghi" <?php echo $row['da_nghi'] ? 'checked' : ''; ?>>
        <label style="width:auto; margin-left:20px;">Ngày nghỉ:</label>
        <input type="date" name="ngay_nghi" style="width:130px;" value="<?php echo $row['ngay_nghi']; ?>">
    </div>
    <div class="footer">
        <button type="submit" style="background:#000080; color:white; padding:8px 20px; border:none; cursor:pointer;">LƯU DỮ LIỆU</button>
        <button type="button" onclick="window.parent.closeModal()" style="padding:8px 15px; margin-left:5px;">HỦY BỎ</button>
    </div>
</form>
</body>
</html>