<?php
session_start();
include 'conn.php';
if (!isset($_SESSION['user_id'])) { header("Location: dn.php"); exit(); }
$result = $conn->query("SELECT * FROM danh_muc_xe ORDER BY id ASC");
$rows = [];
while ($row = $result->fetch_assoc()) $rows[] = $row;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh mục Xe – Hà Linh</title>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Be Vietnam Pro', sans-serif; background: #f1f3f8; color: #1a1f36; min-height: 100vh; }

        .page-wrapper { margin: 24px 28px; padding: 0 0 40px; }

        .breadcrumb { display: flex; align-items: center; gap: 6px; margin-bottom: 16px; font-size: 12px; color: #9ca3af; }
        .breadcrumb a { color: #be0000; text-decoration: none; font-weight: 500; }
        .breadcrumb a:hover { text-decoration: underline; }

        .card { background: #fff; border-radius: 16px; box-shadow: 0 4px 24px rgba(0,0,0,.07); overflow: hidden; }

        .card-header {
            background: linear-gradient(135deg, #be0000 0%, #7b0000 100%);
            padding: 24px 32px 20px; position: relative; overflow: hidden;
        }
        .card-header::after {
            content: ''; position: absolute; right: -40px; top: -40px;
            width: 180px; height: 180px; border-radius: 50%;
            background: rgba(255,255,255,.07);
        }
        .card-header h1 { color: #fff; font-size: 20px; font-weight: 700; position: relative; }
        .card-header p  { color: rgba(255,255,255,.65); font-size: 12px; margin-top: 4px; position: relative; }

        /* Toolbar */
        .toolbar { display: flex; gap: 8px; padding: 16px 24px; border-bottom: 1px solid #f0f0f5; flex-wrap: wrap; align-items: center; }
        .btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 18px; border-radius: 8px; font-family: inherit;
            font-size: 13px; font-weight: 600; cursor: pointer; border: none;
            transition: all .18s; text-decoration: none;
        }
        .btn-primary  { background: #be0000; color: #fff; }
        .btn-primary:hover  { background: #950000; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(190,0,0,.3); }
        .btn-secondary { background: #eef0f7; color: #3a4060; }
        .btn-secondary:hover { background: #e0e3f0; transform: translateY(-1px); }
        .btn-danger   { background: #fff0f0; color: #c62828; }
        .btn-danger:hover   { background: #ffe0e0; transform: translateY(-1px); }
        .btn-ghost    { background: transparent; color: #6b7280; border: 1px solid #e5e7eb; margin-left: auto; }
        .btn-ghost:hover    { background: #f9fafb; }

        /* Search */
        .search-wrap { padding: 12px 24px; border-bottom: 1px solid #f0f0f5; }
        .search-input {
            width: 280px; padding: 8px 14px 8px 36px;
            border: 1px solid #e5e7eb; border-radius: 8px;
            font-family: inherit; font-size: 13px; color: #374151;
            background: #f8f9fc url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' fill='none' stroke='%239ca3af' stroke-width='2' viewBox='0 0 24 24'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cpath d='M21 21l-4.35-4.35'/%3E%3C/svg%3E") 12px center no-repeat;
            transition: border-color .15s, box-shadow .15s;
        }
        .search-input:focus { outline: none; border-color: #be0000; box-shadow: 0 0 0 3px rgba(190,0,0,.08); background-color: #fff; }

        /* Table */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        thead tr { background: #f8f9fc; border-bottom: 2px solid #eef0f7; }
        th {
            padding: 11px 20px; text-align: left;
            font-size: 11px; font-weight: 700;
            text-transform: uppercase; letter-spacing: .8px; color: #8892a4;
        }
        th:first-child, th:nth-child(2) { width: 52px; text-align: center; }

        tbody tr { border-bottom: 1px solid #f3f4f8; transition: background .12s; cursor: pointer; }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: #fdf5f5; }
        tbody tr.selected { background: #fff5f5; }

        td { padding: 13px 20px; font-size: 13.5px; color: #374151; }
        td:first-child, td:nth-child(2) { text-align: center; }
        td:nth-child(2) { color: #9ca3af; font-size: 12px; }

        /* Biển số */
        .plate {
            display: inline-flex; align-items: center; gap: 6px;
            background: #1a1f36; color: #fff;
            padding: 4px 12px; border-radius: 6px;
            font-size: 13px; font-weight: 800; letter-spacing: 1.5px;
            font-family: monospace;
        }
        .plate-dot { width: 5px; height: 5px; background: #fbbf24; border-radius: 50%; }

        /* Loại xe badge */
        .xe-badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 3px 10px; border-radius: 6px;
            background: #eff6ff; color: #1e40af;
            font-size: 12px; font-weight: 600;
        }

        /* Lái xe cell */
        .driver-cell { display: flex; align-items: center; gap: 8px; }
        .driver-avatar {
            width: 28px; height: 28px; border-radius: 50%;
            background: linear-gradient(135deg, #be0000, #7b0000);
            color: #fff; font-size: 11px; font-weight: 700;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .driver-name { font-weight: 500; color: #374151; }

        .code-badge {
            display: inline-block; padding: 2px 8px; border-radius: 5px;
            background: #f3f4f6; color: #6b7280;
            font-size: 11px; font-weight: 600; font-family: monospace;
        }

        .empty { text-align: center; padding: 48px 20px; color: #9ca3af; }

        .table-footer {
            padding: 10px 24px; border-top: 1px solid #f3f4f8;
            font-size: 12px; color: #9ca3af;
            display: flex; justify-content: space-between; align-items: center;
        }
        .table-footer strong { color: #374151; }

        /* Modal */
        .modal { display: none; position: fixed; inset: 0; z-index: 1000; background: rgba(15,20,40,.45); backdrop-filter: blur(3px); align-items: center; justify-content: center; }
        .modal.open { display: flex; }
        .modal-box { background: #fff; border-radius: 16px; width: 520px; max-width: 96vw; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,.2); animation: slideUp .22s ease; }
        @keyframes slideUp { from { transform: translateY(24px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        .modal-head { background: linear-gradient(135deg, #be0000, #7b0000); padding: 16px 24px; display: flex; align-items: center; justify-content: space-between; }
        .modal-head h2 { color: #fff; font-size: 14px; font-weight: 700; }
        .modal-close { background: rgba(255,255,255,.15); border: none; color: #fff; width: 28px; height: 28px; border-radius: 50%; cursor: pointer; font-size: 16px; display: flex; align-items: center; justify-content: center; transition: background .15s; }
        .modal-close:hover { background: rgba(255,255,255,.3); }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="page-wrapper">
    <div class="breadcrumb">
        <a href="index.php">Trang chủ</a>
        <span>›</span>
        <span>Khai báo danh mục</span>
        <span>›</span>
        <span>Xe</span>
    </div>

    <div class="card">
        <div class="card-header">
            <h1>🚘 Danh mục Xe</h1>
            <p>Quản lý thông tin phương tiện và lái xe phụ trách</p>
        </div>

        <div class="toolbar">
            <button class="btn btn-primary" onclick="openModal('form_xe.php?action=add','THÊM MỚI XE')">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                Thêm
            </button>
            <button class="btn btn-secondary" onclick="editSelected()">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                Sửa
            </button>
            <button class="btn btn-danger" onclick="deleteSelected()">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                Xóa
            </button>
            <a href="index.php" class="btn btn-ghost">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 18l-6-6 6-6"/></svg>
                Quay lại
            </a>
        </div>

        <div class="search-wrap">
            <input type="text" class="search-input" id="searchInput" placeholder="Tìm theo biển số..." oninput="filterRows()">
        </div>

        <div class="table-wrap">
            <table id="vehicleTable">
                <thead>
                    <tr>
                        <th>Chọn</th>
                        <th>STT</th>
                        <th>Biển số</th>
                        <th>Loại xe</th>
                        <th>Mã lái xe</th>
                        <th>Lái xe phụ trách</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                <?php if (empty($rows)): ?>
                <tr><td colspan="6"><div class="empty">Chưa có xe nào trong danh mục</div></td></tr>
                <?php else: $stt = 1; foreach ($rows as $row):
                    $initial = strtoupper(mb_substr($row['ten_lai_xe'], 0, 1));
                ?>
                <tr onclick="selectRow(this, <?= $row['id'] ?>)">
                    <td><input type="radio" name="vehicle_id" value="<?= $row['id'] ?>" onclick="event.stopPropagation(); selectRow(this.closest('tr'), <?= $row['id'] ?>)"></td>
                    <td><?= $stt++ ?></td>
                    <td>
                        <span class="plate">
                            <span class="plate-dot"></span>
                            <?= htmlspecialchars($row['bien_so']) ?>
                        </span>
                    </td>
                    <td>
                        <span class="xe-badge">
                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h5l2 4v4h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                            <?= htmlspecialchars($row['loai_xe']) ?>
                        </span>
                    </td>
                    <td><span class="code-badge"><?= htmlspecialchars($row['ma_lai_xe']) ?></span></td>
                    <td>
                        <div class="driver-cell">
                            <div class="driver-avatar"><?= $initial ?></div>
                            <span class="driver-name"><?= htmlspecialchars($row['ten_lai_xe']) ?></span>
                        </div>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>

        <div class="table-footer">
            <span>Tổng: <strong id="rowCount"><?= count($rows) ?></strong> xe</span>
            <span id="selectedInfo" style="display:none;">Đang chọn: <strong id="selectedName"></strong></span>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="vehicleModal" class="modal">
    <div class="modal-box">
        <div class="modal-head">
            <h2 id="modalTitle">THÔNG TIN XE</h2>
            <button class="modal-close" onclick="closeModal()">×</button>
        </div>
        <iframe id="vehicleIframe" src="" style="width:100%;height:370px;border:none;"></iframe>
    </div>
</div>

<script>
let selectedId = null;

function selectRow(row, id) {
    document.querySelectorAll('#vehicleTable tbody tr').forEach(r => r.classList.remove('selected'));
    row.classList.add('selected');
    selectedId = id;
    const plate = row.querySelector('.plate')?.innerText?.trim() || '';
    document.getElementById('selectedInfo').style.display = '';
    document.getElementById('selectedName').innerText = plate;
    const rb = row.querySelector('input[type="radio"]');
    if (rb) rb.checked = true;
}

function openModal(url, title) {
    document.getElementById('modalTitle').innerText = title;
    document.getElementById('vehicleIframe').src = url;
    document.getElementById('vehicleModal').classList.add('open');
}
function closeModal() {
    document.getElementById('vehicleModal').classList.remove('open');
    window.location.reload();
}
document.getElementById('vehicleModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});

function editSelected() {
    if (selectedId) openModal('form_xe.php?action=edit&id=' + selectedId, 'CHỈNH SỬA XE');
    else alert('Vui lòng chọn xe cần sửa!');
}
function deleteSelected() {
    if (!selectedId) { alert('Vui lòng chọn xe cần xóa!'); return; }
    if (confirm('Bạn có chắc chắn muốn xóa xe này?'))
        window.location.href = 'xoa_xe.php?id=' + selectedId;
}

function filterRows() {
    const q = document.getElementById('searchInput').value.toUpperCase();
    let count = 0;
    document.querySelectorAll('#tableBody tr').forEach(tr => {
        const show = tr.innerText.toUpperCase().includes(q);
        tr.style.display = show ? '' : 'none';
        if (show) count++;
    });
    document.getElementById('rowCount').innerText = count;
}

document.addEventListener('keydown', function(e) {
    if (!e.altKey) return;
    const k = e.key.toLowerCase();
    if (k === 'a') { e.preventDefault(); openModal('form_xe.php?action=add','THÊM MỚI XE'); }
    if (k === 'z') { e.preventDefault(); editSelected(); }
    if (k === 'd') { e.preventDefault(); deleteSelected(); }
    if (k === 'e') { e.preventDefault(); location.href = 'index.php'; }
});
</script>
</body>
</html>