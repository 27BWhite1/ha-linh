<?php
include 'conn.php';
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: dn.php"); exit(); }
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $conn->query("DELETE FROM danh_muc_hang_van_tai WHERE id = $id");
    header("Location: danh_muc_hang_van_tai.php");
    exit();
}
$result = $conn->query("SELECT * FROM danh_muc_hang_van_tai ORDER BY ten_hang ASC");
$rows = [];
while ($row = $result->fetch_assoc()) $rows[] = $row;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh mục Hãng vận tải – Hà Linh</title>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { 
            box-sizing: border-box; 
            margin: 0; 
            padding: 0; 
        }
        body { 
            font-family: 'Be Vietnam Pro', sans-serif; 
            background: #f1f3f8; 
            color: #1a1f36; 
            min-height: 100vh; 
        }
        .page-wrapper { 
            margin: 24px 28px; 
            padding: 0 0 40px; 
        }
        .breadcrumb { 
            display: flex; 
            align-items: center; 
            gap: 6px; 
            margin-bottom: 16px; 
            font-size: 12px; 
            color: #9ca3af; 
        }
        .breadcrumb a { 
            color: #be0000; 
            text-decoration: none; 
            font-weight: 500; 
        }
        .breadcrumb a:hover { text-decoration: underline; }

        .card { 
            background: #fff; 
            border-radius: 16px; 
            box-shadow: 0 4px 24px rgba(0,0,0,.07); 
            overflow: hidden; 
        }
        .card-header {
            background: linear-gradient(135deg, #be0000 0%, #7b0000 100%);
            padding: 24px 32px 20px; 
            position: relative; 
            overflow: hidden;
        }
        .card-header::after {
            content: ''; 
            position: absolute; 
            right: -40px; 
            top: -40px;
            width: 180px; 
            height: 180px; 
            border-radius: 50%;
            background: rgba(255,255,255,.07);
        }
        .card-header h1 { 
            color: #fff; 
            font-size: 20px; 
            font-weight: 700; 
            position: relative; 
        }
        .card-header p  { 
            color: rgba(255,255,255,.65); 
            font-size: 12px; 
            margin-top: 4px; 
            position: relative; 
        }
        .toolbar { 
            display: flex; 
            gap: 8px; 
            padding: 16px 24px; 
            border-bottom: 1px solid #f0f0f5; 
            flex-wrap: wrap; 
        }
        .btn {
            display: inline-flex; 
            align-items: center; 
            gap: 6px;
            padding: 8px 18px; 
            border-radius: 8px; 
            font-family: inherit;
            font-size: 13px; 
            font-weight: 600; 
            cursor: pointer; 
            border: none;
            transition: all .18s; 
            text-decoration: none;
        }
        .btn-primary  { 
            background: #be0000; 
            color: #fff; 
        }
        .btn-primary:hover  { 
            background: #950000; 
            transform: translateY(-1px); 
            box-shadow: 0 4px 12px rgba(190,0,0,.3); 
        }
        .btn-secondary { 
            background: #eef0f7; 
            color: #3a4060; 
        }
        .btn-secondary:hover { 
            background: #e0e3f0; 
            transform: translateY(-1px); 
        }
        .btn-danger   { 
            background: #fff0f0; 
            color: #c62828; 
        }
        .btn-danger:hover   { 
            background: #ffe0e0; 
            transform: translateY(-1px); 
        }
        .btn-ghost    { 
            background: transparent; 
            color: #6b7280; 
            border: 1px solid #e5e7eb; 
            margin-left: auto; 
        }
        .btn-ghost:hover    { background: #f9fafb; }
        .search-wrap { 
            padding: 12px 24px; 
            border-bottom: 1px solid #f0f0f5; 
        }
        .search-input {
            width: 280px; 
            padding: 8px 14px 8px 36px;
            border: 1px solid #e5e7eb; 
            border-radius: 8px;
            font-family: inherit; 
            font-size: 13px; 
            color: #374151;
            background: #f8f9fc url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' fill='none' stroke='%239ca3af' stroke-width='2' viewBox='0 0 24 24'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cpath d='M21 21l-4.35-4.35'/%3E%3C/svg%3E") 12px center no-repeat;
            transition: border-color .15s, box-shadow .15s;
        }
        .search-input:focus { 
            outline: none; 
            border-color: #be0000; 
            box-shadow: 0 0 0 3px rgba(190,0,0,.08); 
            background-color: #fff; 
        }
        .table-wrap { overflow-x: auto; }
        table { 
            width: 100%; 
            border-collapse: collapse; 
        }
        thead tr { 
            background: #f8f9fc; 
            border-bottom: 2px solid #eef0f7; 
        }
        th {
            padding: 11px 20px; 
            text-align: left;
            font-size: 11px; 
            font-weight: 700;
            text-transform: uppercase; 
            letter-spacing: .8px; 
            color: #8892a4;
        }
        th:first-child, th:nth-child(2) { 
            width: 52px; 
            text-align: center; 
        }
        tbody tr { 
            border-bottom: 1px solid #f3f4f8; 
            transition: background .12s; 
            cursor: pointer; 
        }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: #fdf5f5; }
        tbody tr.selected { background: #fff5f5; }
        td { 
            padding: 13px 20px; 
            font-size: 13.5px; 
            color: #374151; 
        }
        td:first-child, td:nth-child(2) { text-align: center; }
        td:nth-child(2) { 
            color: #9ca3af; 
            font-size: 12px; 
        }
        .hang-cell { 
            display: flex; 
            align-items: center; 
            gap: 10px; 
        }
        .hang-avatar {
            width: 34px; 
            height: 34px; 
            border-radius: 8px;
            background: linear-gradient(135deg, #be0000, #7b0000);
            color: #fff; 
            font-size: 13px; 
            font-weight: 700;
            display: flex; 
            align-items: center; 
            justify-content: center; 
            flex-shrink: 0;
        }
        .hang-name { 
            font-weight: 600; 
            color: #1a1f36; 
        }
        .code-badge {
            display: inline-block; 
            padding: 3px 9px; 
            border-radius: 6px;
            background: #f3f4f6; 
            color: #6b7280;
            font-size: 11.5px; 
            font-weight: 600; 
            font-family: monospace;
        }
        .money     { 
            font-weight: 600; 
            color: #374151; 
        }
        .money-zero { color: #d1d5db; }
        .empty { 
            text-align: center; 
            padding: 48px 20px; 
            color: #9ca3af; 
        }
        .table-footer {
            padding: 10px 24px; 
            border-top: 1px solid #f3f4f8;
            font-size: 12px; 
            color: #9ca3af;
            display: flex; 
            justify-content: space-between; 
            align-items: center;
        }
        .table-footer strong { color: #374151; }
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
        <span>Hãng vận tải (Chủ xe)</span>
    </div>

    <div class="card">
        <div class="card-header">
            <h1>🏢 Danh mục Hãng vận tải (Chủ xe)</h1>
            <p>Quản lý thông tin hãng vận tải, chủ xe và công nợ</p>
        </div>

        <div class="toolbar">
            <a href="form_hang_van_tai.php?action=add" class="btn btn-primary">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                Thêm
            </a>
            <button class="btn btn-secondary" id="btnEdit">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                Sửa
            </button>
            <button class="btn btn-danger" id="btnDelete">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                Xóa
            </button>
            <a href="index.php" class="btn btn-ghost">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 18l-6-6 6-6"/></svg>
                Quay lại
            </a>
        </div>

        <div class="search-wrap">
            <input type="text" class="search-input" id="searchInput" placeholder="Tìm kiếm hãng vận tải..." oninput="filterRows()">
        </div>

        <div class="table-wrap">
            <table id="hangTable">
                <thead>
                    <tr>
                        <th>Chọn</th>
                        <th>STT</th>
                        <th>Tên hãng vận tải</th>
                        <th>Mã hãng</th>
                        <th>Số CCCD</th>
                        <th>Nợ cũ</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                <?php if (empty($rows)): ?>
                <tr><td colspan="6"><div class="empty">
                    <svg width="48" height="48" fill="none" stroke="#d1d5db" stroke-width="1.5" viewBox="0 0 24 24" style="margin:0 auto 8px;display:block;"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
                    Chưa có hãng vận tải nào
                </div></td></tr>
                <?php else: $stt = 1; foreach ($rows as $row):
                    $initial = strtoupper(mb_substr($row['ten_hang'], 0, 1));
                    $noCu = floatval($row['no_cu']);
                ?>
                <tr onclick="selectRow(this, <?= $row['id'] ?>)">
                    <td><input type="checkbox" name="selected_id" value="<?= $row['id'] ?>" onclick="event.stopPropagation(); selectRow(this.closest('tr'), <?= $row['id'] ?>)"></td>
                    <td><?= $stt++ ?></td>
                    <td>
                        <div class="hang-cell">
                            <div class="hang-avatar"><?= $initial ?></div>
                            <span class="hang-name"><?= htmlspecialchars($row['ten_hang']) ?></span>
                        </div>
                    </td>
                    <td><span class="code-badge"><?= htmlspecialchars($row['ma_hang']) ?></span></td>
                    <td><?= htmlspecialchars($row['so_cccd'] ?? '') ?></td>
                    <td class="<?= $noCu > 0 ? 'money' : 'money-zero' ?>">
                        <?= $noCu > 0 ? number_format($noCu) : '—' ?>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>

        <div class="table-footer">
            <span>Tổng: <strong id="rowCount"><?= count($rows) ?></strong> hãng vận tải</span>
            <span id="selectedInfo" style="display:none;">Đang chọn: <strong id="selectedName"></strong></span>
        </div>
    </div>
</div>

<script>
let selectedId = null;

function selectRow(row, id) {
    document.querySelectorAll('#hangTable tbody tr').forEach(r => r.classList.remove('selected'));
    row.classList.add('selected');
    selectedId = id;
    const name = row.querySelector('.hang-name')?.innerText || '';
    document.getElementById('selectedInfo').style.display = '';
    document.getElementById('selectedName').innerText = name;
    const cb = row.querySelector('input[type="checkbox"]');
    if (cb) cb.checked = true;
}
document.getElementById('btnEdit').onclick = function() {
    if (selectedId) window.location.href = 'form_hang_van_tai.php?action=edit&id=' + selectedId;
    else alert('Vui lòng chọn một dòng để sửa!');
};
document.getElementById('btnDelete').onclick = function() {
    if (!selectedId) { alert('Vui lòng chọn một dòng để xóa!'); return; }
    if (confirm('Bạn có chắc chắn muốn xóa hãng vận tải này?'))
        window.location.href = 'danh_muc_hang_van_tai.php?delete_id=' + selectedId;
};
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
    if (e.key.toLowerCase() === 'a') { e.preventDefault(); location.href = 'form_hang_van_tai.php?action=add'; }
    if (e.key.toLowerCase() === 'z') { e.preventDefault(); document.getElementById('btnEdit').click(); }
    if (e.key.toLowerCase() === 'd') { e.preventDefault(); document.getElementById('btnDelete').click(); }
    if (e.key.toLowerCase() === 'e') { e.preventDefault(); location.href = 'index.php'; }
});
</script>
</body>
</html>