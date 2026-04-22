<?php
include 'conn.php';
session_start();
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $sql_delete = "DELETE FROM `danh_muc_loai_xe` WHERE id = $id";
    if ($conn->query($sql_delete)) {
        header("Location: danh_muc_loai_xe.php");
        exit();
    } else {
        die("Lỗi khi xóa: " . $conn->error);
    }
}
$result = $conn->query("SELECT * FROM `danh_muc_loai_xe` ORDER BY id ASC");
if (!$result) {
    die("Lỗi truy vấn CSDL: " . $conn->error . ". Hãy kiểm tra lại tên bảng trong phpMyAdmin.");
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh mục Loại xe - Hà Linh Transport</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body { font-family: Arial, sans-serif; margin: 0; }
        .header-title { color: #be0000; text-align: center; font-weight: bold; margin: 15px 0; text-transform: uppercase; }
        .action-bar { background: #f4f4f4; padding: 10px; border: 1px solid #ddd; display: flex; gap: 8px; margin-bottom: 5px; }
        .btn-tool { border: 1px solid #ccc; background: #fff; padding: 5px 12px; cursor: pointer; display: flex; align-items: center; gap: 5px; font-size: 13px; text-decoration: none; color: #333; border-radius: 3px; }
        .btn-tool:hover { background: #e9ecef; }
        
        .data-table { width: 100%; border-collapse: collapse; font-size: 13px; }
        .data-table th { background-color: #000080; color: #fff; border: 1px solid #ddd; padding: 10px; text-align: left; }
        .data-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .data-table tr:nth-child(even) { background-color: #f9f9f9; }
        .data-table tr:hover { background-color: #f1f1f1; }
        .col-percent { font-weight: bold; color: blue; text-align: right !important; }
.modal {
    display: none; 
    position: fixed; 
    z-index: 1000; 
    left: 0; top: 0; 
    width: 100%; height: 100%; 
    background-color: rgba(0,0,0,0.5); 
}
.modal-content {
    background-color: #fff;
    margin: 10% auto;
    padding: 0;
    border: 1px solid #000080;
    width: 400px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}
.modal-header {
    background: #000080;
    color: white;
    padding: 10px;
    font-weight: bold;
    display: flex;
    justify-content: space-between;
}
.close-btn {
    cursor: pointer;
    font-size: 20px;
}
    </style>
</head>
<body>
<?php 
if (file_exists('navbar.php')) {
    include 'navbar.php'; 
} else {
    echo "<div style='color:red; padding:10px;'>Cảnh báo: Không tìm thấy file navbar.php</div>";
}
?>
<div class="container" style="padding: 0 20px;">
    <h3 class="header-title">DANH MỤC LOẠI XE</h3>
    <div class="action-bar">
        <a href="form_loai_xe.php?action=add" class="btn-tool" title="Alt + A">
            <span style="color: green; font-weight: bold;">+</span> Thêm (Alt + A)
        </a>
        <button class="btn-tool" onclick="editSelected()" title="Alt + Z">
            <span style="color: blue;">📝</span> Sửa (Alt + Z)
        </button>
        <button class="btn-tool" onclick="deleteSelected()" title="Alt + D">
            <span style="color: red;">❌</span> Xóa (Alt + D)
        </button>
        <a href="index.php" class="btn-tool" title="Alt + E">
            <span style="color: brown;">🚪</span> Thoát (Alt + E)
        </a>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th width="40" style="text-align: center;">Chọn</th>
                <th width="50">Stt</th>
                <th>Mã Loại xe</th>
                <th width="200">% lương lái du lịch</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $stt = 1; 
            if ($result->num_rows > 0):
                while($row = $result->fetch_assoc()): 
            ?>
            <tr>
                <td style="text-align: center;">
                    <input type="radio" name="select_item" value="<?php echo $row['id']; ?>">
                </td>
                <td><?php echo $stt++; ?></td>
                <td><?php echo htmlspecialchars($row['ma_loai_xe']); ?></td>
                <td class="col-percent"><?php echo number_format($row['phan_tram_luong_lai_xe']); ?> %</td>
            </tr>
            <?php 
                endwhile; 
            else:
            ?>
            <tr>
                <td colspan="4" style="text-align: center; padding: 20px;">Chưa có dữ liệu loại xe.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<script>
    function getSelectedId() {
        const selected = document.querySelector('input[name="select_item"]:checked');
        return selected ? selected.value : null;
    }
    function editSelected() {
        const id = getSelectedId();
        if(id) {
            window.location.href = 'form_loai_xe.php?action=edit&id=' + id;
        } else {
            alert('Vui lòng chọn một loại xe để sửa!');
        }
    }
    function deleteSelected() {
        const id = getSelectedId();
        if(id) {
            if(confirm('Bạn có chắc chắn muốn xóa loại xe này không?')) {
                window.location.href = 'danh_muc_loai_xe.php?delete_id=' + id;
            }
        } else {
            alert('Vui lòng chọn một loại xe để xóa!');
        }
    }
    document.addEventListener('keydown', function(e) {
        if (e.altKey) {
            const key = e.key.toLowerCase();
            if (key === 'a') {
                e.preventDefault();
                window.location.href = 'form_loai_xe.php?action=add';
            } else if (key === 'z') {
                e.preventDefault();
                editSelected();
            } else if (key === 'd') {
                e.preventDefault();
                deleteSelected();
            } else if (key === 'e') {
                e.preventDefault();
                window.location.href = 'index.php';
            }
        }
    });
</script>
</body>
</html>