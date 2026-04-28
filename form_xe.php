<?php
include 'conn.php';
$action = $_GET['action'] ?? 'add';
$id = intval($_GET['id'] ?? 0);
$row = ['bien_so'=>'','loai_xe'=>'','ma_lai_xe'=>'','ten_lai_xe'=>''];

if ($action == 'edit' && $id) {
    $stmt = $conn->prepare("SELECT * FROM danh_muc_xe WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}
$list_lai_xe = $conn->query("SELECT id, ma_lai_xe, ten_lai_xe FROM danh_muc_lai_xe WHERE da_nghi = 0 ORDER BY ten_lai_xe ASC");
$list_loai_xe = $conn->query("SELECT ma_loai_xe FROM danh_muc_loai_xe ORDER BY ma_loai_xe ASC");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bs = $_POST['bien_so'];
    $lx = $_POST['loai_xe'];
    $ml = $_POST['ma_lai_xe'];
    $tl = $_POST['ten_lai_xe'];

    if ($action == 'add') {
        $stmt = $conn->prepare("INSERT INTO danh_muc_xe (bien_so, loai_xe, ma_lai_xe, ten_lai_xe) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $bs, $lx, $ml, $tl);
    } else {
        $stmt = $conn->prepare("UPDATE danh_muc_xe SET bien_so=?, loai_xe=?, ma_lai_xe=?, ten_lai_xe=? WHERE id=?");
        $stmt->bind_param("ssssi", $bs, $lx, $ml, $tl, $id);
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
            padding: 15px; 
            font-size: 13px; 
            background: #f9f9f9; 
        }
        .f-row { 
            margin-bottom: 10px; 
            display: flex; 
            align-items: center; 
        }
        label { 
            width: 110px; 
            font-weight: bold; 
            color: #333; 
        }
        input, select { 
            flex: 1; 
            padding: 6px; 
            border: 1px solid #ccc; 
        }
        .footer { 
            text-align: center; 
            margin-top: 20px; 
            border-top: 1px solid #ddd; 
            padding-top: 15px; 
        }
        .btn-save { 
            background: #000080; 
            color: white; 
            padding: 8px 25px; 
            border: none; 
            cursor: pointer; 
            font-weight: bold; 
        }
    </style>
</head>
<body>
<form method="POST">
    <div class="f-row">
        <label>Biển số xe:</label>
        <input type="text" name="bien_so" value="<?php echo htmlspecialchars($row['bien_so']); ?>" required autofocus>
    </div>
    <div class="f-row">
        <label>Loại xe:</label>
        <select name="loai_xe">
            <option value="">-- Chọn loại xe --</option>
            <?php
            if ($list_loai_xe) {
                while($lx = $list_loai_xe->fetch_assoc()) {
                    $sel = ($row['loai_xe'] == $lx['ma_loai_xe']) ? 'selected' : '';
                    echo "<option value='{$lx['ma_loai_xe']}' $sel>{$lx['ma_loai_xe']}</option>";
                }
            }
            ?>
        </select>
    </div>
    <div class="f-row">
        <label>Lái xe:</label>
        <select name="ma_lai_xe" id="sel_lai_xe" onchange="fillTenLaiXe(this)">
            <option value="">-- Chọn lái xe --</option>
            <?php
            if ($list_lai_xe) {
                while($lx = $list_lai_xe->fetch_assoc()) {
                    $sel = ($row['ma_lai_xe'] == $lx['ma_lai_xe']) ? 'selected' : '';
                    echo "<option value='{$lx['ma_lai_xe']}' data-ten='{$lx['ten_lai_xe']}' $sel>{$lx['ten_lai_xe']}</option>";
                }
            }
            ?>
        </select>
    </div>
    <div class="f-row">
        <label>Tên lái xe:</label>
        <input type="text" name="ten_lai_xe" id="ten_lai_xe" value="<?php echo htmlspecialchars($row['ten_lai_xe']); ?>" readonly style="background:#f0f0f0;">
    </div>

    <div class="footer">
        <button type="submit" class="btn-save">💾 LƯU DỮ LIỆU</button>
        <button type="button" onclick="window.parent.closeModal()" style="padding: 8px 15px; margin-left:5px;">Hủy bỏ</button>
    </div>
</form>
<script>
    function fillTenLaiXe(sel) {
        document.getElementById('ten_lai_xe').value = sel.options[sel.selectedIndex].dataset.ten || '';
    }
</script>
</body>
</html>