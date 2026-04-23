<?php
include 'conn.php';
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: dn.php"); exit(); }
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $conn->query("DELETE FROM danh_muc_loai_xe WHERE id = $id");
    header("Location: danh_muc_loai_xe.php");
    exit();
}
$result = $conn->query("SELECT * FROM danh_muc_loai_xe ORDER BY id ASC");
$rows = [];
while ($row = $result->fetch_assoc()) $rows[] = $row;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh mục Loại xe – Hà Linh</title>
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

        .toolbar { display: flex; gap: 8px; padding: 16px 24px; border-bottom: 1px solid #f0f0f5; flex-wrap: wrap; }
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

        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; max-width: 700px; }
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

        td { padding: 14px 20px; font-size: 13.5px; color: #374151; }
        td:first-child, td:nth-child(2) { text-align: center; }
        td:nth-child(2) { color: #9ca3af; font-size: 12px; }

        /* Xe type cell */
        .xe-cell { display: flex; align-items: center; gap: 10px; }
        .xe-icon {
            width: 36px; height: 36px; border-radius: 8px;
            background: linear-gradient(135deg, #1e3a8a, #1d4ed8);
            color: #fff; font-size: 11px; font-weight: 700;
            display: flex; align-items: center; justify-content: center;
            flex-direction: column; gap: 1px; flex-shrink: 0;
        }
        .xe-icon .xe-seats { font-size: 14px; line-height: 1; }
        .xe-icon .xe-label { font-size: 8px; opacity: .8; }
        .xe-name { font-weight: 700; color: #1a1f36; font-size: 14px; }
        .xe-sub  { font-size: 11px; color: #9ca3af; margin-top: 1px; }

        /* Percent badge */
        .pct-wrap { display: flex; align-items: center; justify-content: flex-end; gap: 10px; }
        .pct-badge {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 4px 12px; border-radius: 20px;
            font-size: 13px; font-weight: 700;
        }
        .pct-high   { background: #dbeafe; color: #1e40af; }
        .pct-medium { background: #ede9fe; color: #5b21b6; }
        .pct-low    { background: #d1fae5; color: #065f46; }

        /* Mini bar */
        .pct-bar-wrap { width: 80px; height: 6px; background: #f3f4f6; border-radius: 3px; overflow: hidden; }
        .pct-bar { height: 100%; border-radius: 3px; background: linear-gradient(90deg, #1e3a8a, #3b82f6); }

        .empty { text-align: center; padding: 48px 20px; color: #9ca3af; }

        .table-footer {
            padding: 10px 24px; border-top: 1px solid #f3f4f8;
            font-size: 12px; color: #9ca3af;
            display: flex; justify-content: space-between; align-items: center;
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
        <span>Loại xe</span>
    </div>

    <div class="card">
        <div class="card-header">
            <h1>🚗 Danh mục Loại xe</h1>
            <p>Quản lý loại xe và tỷ lệ % lương lái xe du lịch</p>
        </div>

        <div class="toolbar">
            <a href="form_loai_xe.php?action=add" class="btn btn-primary">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                Thêm
            </a>
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

        <div class="table-wrap">
            <table id="loaiXeTable">
                <thead>
                    <tr>
                        <th>Chọn</th>
                        <th>STT</th>
                        <th>Loại xe</th>
                        <th style="text-align:right; padding-right:28px;">% Lương lái du lịch</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                <?php if (empty($rows)): ?>
                <tr><td colspan="4"><div class="empty">Chưa có loại xe nào</div></td></tr>
                <?php else:
                    $maxPct = max(array_column($rows, 'phan_tram_luong_lai_xe')) ?: 100;
                    $stt = 1;
                    foreach ($rows as $row):
                        $pct = floatval($row['phan_tram_luong_lai_xe']);
                        $ma  = htmlspecialchars($row['ma_loai_xe']);
                        // Tách số ghế từ mã (Xe04 → 04)
                        preg_match('/\d+/', $ma, $m);
                        $seats = $m[0] ?? '';
                        $barW  = round(($pct / $maxPct) * 100);
                        $badgeClass = $pct >= 20 ? 'pct-high' : ($pct >= 17 ? 'pct-medium' : 'pct-low');
                ?>
                <tr onclick="selectRow(this, <?= $row['id'] ?>)">
                    <td><input type="radio" name="select_item" value="<?= $row['id'] ?>" onclick="event.stopPropagation(); selectRow(this.closest('tr'), <?= $row['id'] ?>)"></td>
                    <td><?= $stt++ ?></td>
                    <td>
                        <div class="xe-cell">
                            <div class="xe-icon">
                                <span class="xe-seats"><?= $seats ?></span>
                                <span class="xe-label">chỗ</span>
                            </div>
                            <div>
                                <div class="xe-name"><?= $ma ?></div>
                                <div class="xe-sub"><?= $seats ?> chỗ ngồi</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="pct-wrap">
                            <div class="pct-bar-wrap"><div class="pct-bar" style="width:<?= $barW ?>%"></div></div>
                            <span class="pct-badge <?= $badgeClass ?>">
                                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6M9 9l6 6" opacity="0"/><path d="M8 12h.01M12 8l4 4-4 4"/></svg>
                                <?= number_format($pct) ?> %
                            </span>
                        </div>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>

        <div class="table-footer">
            <span>Tổng: <strong><?= count($rows) ?></strong> loại xe</span>
            <span id="selectedInfo" style="display:none;">Đang chọn: <strong id="selectedName"></strong></span>
        </div>
    </div>
</div>

<script>
let selectedId = null;

function selectRow(row, id) {
    document.querySelectorAll('#loaiXeTable tbody tr').forEach(r => r.classList.remove('selected'));
    row.classList.add('selected');
    selectedId = id;
    const name = row.querySelector('.xe-name')?.innerText || '';
    document.getElementById('selectedInfo').style.display = '';
    document.getElementById('selectedName').innerText = name;
    const rb = row.querySelector('input[type="radio"]');
    if (rb) rb.checked = true;
}

function editSelected() {
    if (selectedId) window.location.href = 'form_loai_xe.php?action=edit&id=' + selectedId;
    else alert('Vui lòng chọn một loại xe để sửa!');
}
function deleteSelected() {
    if (!selectedId) { alert('Vui lòng chọn một loại xe để xóa!'); return; }
    if (confirm('Bạn có chắc chắn muốn xóa loại xe này?'))
        window.location.href = 'danh_muc_loai_xe.php?delete_id=' + selectedId;
}

document.addEventListener('keydown', function(e) {
    if (!e.altKey) return;
    const k = e.key.toLowerCase();
    if (k === 'a') { e.preventDefault(); location.href = 'form_loai_xe.php?action=add'; }
    if (k === 'z') { e.preventDefault(); editSelected(); }
    if (k === 'd') { e.preventDefault(); deleteSelected(); }
    if (k === 'e') { e.preventDefault(); location.href = 'index.php'; }
});
</script>
</body>
</html>