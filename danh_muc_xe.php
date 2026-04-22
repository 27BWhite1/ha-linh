<?php
session_start();
include 'conn.php';
$result = $conn->query("SELECT * FROM danh_muc_xe ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh mục Xe - HA LINH TRANSPORT</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 13px; margin: 0; padding: 0; background: #fff; }
        .top-header { display: flex; justify-content: space-between; padding: 10px 20px; align-items: center; }
        .logo { color: #2ecc71; font-weight: bold; font-size: 20px; text-decoration: none; }
        .menu-list { display: flex; list-style: none; padding: 0; margin: 0; background: #fff; border-bottom: 1px solid #ccc; }
        .menu-item { padding: 10px 20px; display: block; text-decoration: none; color: #333; font-weight: bold; }
        .menu-item-container { position: relative; }
        .menu-item-container:hover .dropdown-content { display: block; }
        .dropdown-content { display: none; position: absolute; background: #fff; min-width: 200px; box-shadow: 0 8px 16px rgba(0,0,0,0.2); z-index: 10; }
        .dropdown-item { padding: 10px; display: block; text-decoration: none; color: #333; border-bottom: 1px solid #eee; }
        .dropdown-item:hover { background: #f1f1f1; }
        .header-container { text-align: center; margin: 10px 0; }
        .header-title { 
            color: #be0000; 
            font-weight: bold; 
            text-transform: uppercase; 
            border-bottom: 3px double red; 
            display: inline-block;
            padding-bottom: 3px;
            font-size: 18px;
        }
        .action-bar { 
            background: #f4f4f4; 
            padding: 8px 15px; 
            border: 1px solid #ccc; 
            display: flex; 
            justify-content: space-between; 
            align-items: center;
            margin: 0 5px;
        }
        .search-section { display: flex; align-items: center; gap: 5px; }
        .btn-group { display: flex; gap: 5px; }
        .btn-tool { 
            border: 1px solid #999; 
            background: #fff; 
            padding: 5px 12px; 
            cursor: pointer; 
            display: flex; 
            align-items: center; 
            gap: 5px; 
            font-size: 12px;
            border-radius: 3px;
        }
        .btn-tool:hover { background: #e2e2e2; }
        .data-table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        .data-table th { 
            background-color: #000080; 
            color: #fff; 
            border: 1px solid #ccc; 
            padding: 8px; 
            font-weight: bold;
        }
        .data-table td { border: 1px solid #ccc; padding: 7px; text-align: center; }
        tr:hover { background-color: #f9f9f9; cursor: pointer; }
        .selected-row { background-color: #0000ff !important; color: white; }
        .modal { display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); }
        .modal-content { background: #fff; margin: 8% auto; width: 500px; border: 2px solid #000080; }
        .modal-header { background: #000080; color: white; padding: 10px; font-weight: bold; display: flex; justify-content: space-between; }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="header-container">
    <h3 class="header-title">DANH MỤC XE</h3>
</div>
<div class="action-bar">
    <div class="search-section">
        <strong>Tìm theo Biển số:</strong> 
        <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="Nhập biển số cần tìm..." style="padding: 5px; width: 250px; border: 1px solid #ccc;">
    </div>
    <div class="btn-group">
        <button class="btn-tool" onclick="openModal('form_xe.php?action=add', 'THÊM MỚI XE')">
            <span style="color: green;">➕</span> Thêm (Alt+A)
        </button>
        <button class="btn-tool" onclick="editSelected()">
            <span style="color: blue;">📝</span> Sửa (Alt+Z)
        </button>
        <button class="btn-tool" onclick="deleteSelected()">
            <span style="color: red;">❌</span> Xóa (Alt+D)
        </button>
        <button class="btn-tool" onclick="window.location.href='index.php'">
            <span style="color: brown;">🚪</span> Thoát (Alt+E)
        </button>
    </div>
</div>
<div style="padding: 0 5px;">
    <table class="data-table" id="vehicleTable">
        <thead>
            <tr>
                <th width="50">Stt</th>
                <th width="50">Chọn</th>
                <th width="150">Biển số</th>
                <th width="150">Loại xe</th>
                <th width="100">Mã lái xe</th>
                <th>Tên lái xe</th>
            </tr>
        </thead>
        <tbody>
            <?php $stt = 1; while($row = $result->fetch_assoc()): ?>
            <tr onclick="selectRow(this)">
                <td><?php echo $stt++; ?></td>
                <td>
                    <input type="radio" name="vehicle_id" value="<?php echo $row['id']; ?>" onclick="event.stopPropagation();">
                </td>
                <td><b><?php echo htmlspecialchars($row['bien_so']); ?></b></td>
                <td><?php echo htmlspecialchars($row['loai_xe']); ?></td>
                <td><?php echo htmlspecialchars($row['ma_lai_xe']); ?></td>
                <td style="text-align: left; padding-left: 15px;"><?php echo htmlspecialchars($row['ten_lai_xe']); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<div id="vehicleModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <span id="modalHeaderText">THÔNG TIN XE</span>
            <span style="cursor:pointer" onclick="closeModal()">×</span>
        </div>
        <iframe id="vehicleIframe" src="" style="width:100%; height:350px; border:none;"></iframe>
    </div>
</div>
<script>
    function selectRow(row) {
        document.querySelectorAll('#vehicleTable tr').forEach(r => r.classList.remove('selected-row'));
        row.classList.add('selected-row');
        row.querySelector('input[type="radio"]').checked = true;
    }
    function openModal(url, title) {
        document.getElementById('modalHeaderText').innerText = title;
        document.getElementById('vehicleIframe').src = url;
        document.getElementById('vehicleModal').style.display = 'block';
    }
    function closeModal() {
        document.getElementById('vehicleModal').style.display = 'none';
        window.location.reload();
    }
    function editSelected() {
        let sel = document.querySelector('input[name="vehicle_id"]:checked');
        if(sel) openModal('form_xe.php?action=edit&id=' + sel.value, 'CHỈNH SỬA XE');
        else alert('Vui lòng chọn xe!');
    }
    function deleteSelected() {
        let sel = document.querySelector('input[name="vehicle_id"]:checked');
        if(sel && confirm('Xác nhận xóa xe này?')) window.location.href = 'xoa_xe.php?id=' + sel.value;
        else if(!sel) alert('Vui lòng chọn xe!');
    }
    function searchTable() {
        let input = document.getElementById("searchInput").value.toUpperCase();
        let tr = document.getElementById("vehicleTable").getElementsByTagName("tr");
        for (let i = 1; i < tr.length; i++) {
            let td = tr[i].getElementsByTagName("td")[2]; 
            if (td) tr[i].style.display = td.innerText.toUpperCase().indexOf(input) > -1 ? "" : "none";
        }
    }
</script>
</body>
</html>