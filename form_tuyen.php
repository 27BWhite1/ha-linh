<?php
include 'conn.php';
$action = $_GET['action'] ?? 'add';
$id = $_GET['id'] ?? 0;
$list_khach = $conn->query("SELECT ten_khachhang FROM danh_muc_khach_hang ORDER BY ten_khachhang ASC");
$next_ma_tuyen = "";
if ($action == 'add') {
    $res_ma = $conn->query("SELECT MAX(CAST(ma_tuyen AS UNSIGNED)) as max_ma FROM danh_muc_tuyen_duong");
    $row_ma = $res_ma->fetch_assoc();
    $next_ma_tuyen = ($row_ma['max_ma'] ?? 0) + 1;
}
$row = [
    'ma_tuyen' => $next_ma_tuyen, 
    'ten_ca' => '', 'ten_tuyen' => '', 'khach_hang' => '',
    'gio_don' => '', 'gio_ve' => '',
    'don_gia_04' => 0, 'don_gia_07' => 0, 'don_gia_16' => 0, 'don_gia_29' => 0, 'don_gia_34' => 0, 'don_gia_45' => 0,
    'luong_xe_04' => 0, 'luong_xe_07' => 0, 'luong_xe_16' => 0, 'luong_xe_29' => 0, 'luong_xe_34' => 0, 'luong_xe_45' => 0
];
if ($action == 'edit' && $id > 0) {
    $stmt = $conn->prepare("SELECT * FROM danh_muc_tuyen_duong WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Nhập sửa tuyến đường</title>
    <style>
        body { font-family: "Segoe UI", Tahoma, Arial; font-size: 13px; background-color: #f4f4f4; margin: 0; padding: 10px; }
        .window-frame { background: #fff; border: 1px solid #707070; box-shadow: 2px 2px 8px rgba(0,0,0,0.2); }
        .form-table { width: 100%; border-collapse: separate; border-spacing: 5px; padding: 10px; }
        .label { width: 100px; white-space: nowrap; color: #333; }
        input[type="text"], select { width: 100%; padding: 3px; border: 1px solid #ccc; box-sizing: border-box; font-size: 13px; }
        input[readonly] { background-color: #f0f0f0; color: #be0000; font-weight: bold; }
        .number-input { text-align: right; font-weight: bold; color: #0056b3; }
        .grid-container { display: flex; gap: 20px; padding: 0 10px; }
        .grid-col { flex: 1; }
        .col-title { font-weight: bold; color: #be0000; border-bottom: 1px solid #ccc; margin-bottom: 8px; padding-bottom: 3px; }
        .button-group { margin-top: 15px; text-align: right; padding: 8px; background: #f0f0f0; border-top: 1px solid #ccc; }
        .btn { padding: 5px 15px; margin-left: 5px; cursor: pointer; border: 1px solid #707070; background: #e1e1e1; font-size: 13px; }
        .btn-save { border-color: #0078d7; font-weight: bold; }
    </style>
</head>
<body>
<div class="window-frame">
    <form action="save_tuyen.php" method="POST">
        <input type="hidden" name="action" value="<?php echo $action; ?>">
        <input type="hidden" name="id" value="<?php echo $id; ?>">

        <table class="form-table">
            <tr>
                <td class="label">Mã tuyến:</td>
                <td colspan="3"><input type="text" name="ma_tuyen" value="<?php echo $row['ma_tuyen']; ?>" readonly></td>
            </tr>
            <tr>
                <td class="label">Tên ca:</td>
                <td><input type="text" name="ten_ca" value="<?php echo $row['ten_ca']; ?>" autofocus required></td>
                <td class="label" style="padding-left:20px">Khách hàng:</td>
                <td>
                    <select name="khach_hang" required>
                        <option value="">-- Chọn khách hàng --</option>
                        <?php 
                        if ($list_khach && $list_khach->num_rows > 0) {
                            while($kh = $list_khach->fetch_assoc()) {
                                $selected = ($row['khach_hang'] == $kh['ten_khachhang']) ? 'selected' : '';
                                echo '<option value="'.$kh['ten_khachhang'].'" '.$selected.'>'.$kh['ten_khachhang'].'</option>';
                            }
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="label">Tên tuyến:</td>
                <td colspan="3"><input type="text" name="ten_tuyen" value="<?php echo $row['ten_tuyen']; ?>" required></td>
            </tr>
            <tr>
                <td class="label">Giờ đón:</td>
                <td><input type="text" name="gio_don" value="<?php echo $row['gio_don']; ?>"></td>
                <td class="label" style="padding-left:20px">Giờ về:</td>
                <td><input type="text" name="gio_ve" value="<?php echo $row['gio_ve']; ?>"></td>
            </tr>
        </table>
        
        <div class="grid-container">
            <div class="grid-col">
                <div class="col-title">Đơn giá xe</div>
                <table width="100%">
                    <?php $types = ['04', '07', '16', '29', '34', '45']; foreach($types as $t): ?>
                    <tr>
                        <td class="label">Xe <?php echo $t; ?>:</td>
                        <td><input type="text" name="don_gia_<?php echo $t; ?>" class="number-input" value="<?php echo number_format($row['don_gia_'.$t], 0, '', '.'); ?>"></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
            <div class="grid-col">
                <div class="col-title">Lương xe</div>
                <table width="100%">
                    <?php foreach($types as $t): ?>
                    <tr>
                        <td class="label">Xe <?php echo $t; ?>:</td>
                        <td><input type="text" name="luong_xe_<?php echo $t; ?>" class="number-input" value="<?php echo number_format($row['luong_xe_'.$t], 0, '', '.'); ?>"></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>

        <div class="button-group">
            <button type="submit" class="btn btn-save">Chấp nhận (Alt+S)</button>
            <button type="button" class="btn" onclick="parent.closeModal()">Huỷ bỏ (Esc)</button>
        </div>
    </form>
</div>
<script>
    document.querySelectorAll('.number-input').forEach(input => {
        input.addEventListener('input', function() {
            let val = this.value.replace(/\D/g, "");
            this.value = val.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        });
    });
</script>
</body>
</html>