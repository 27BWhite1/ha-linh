<?php
include 'conn.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: dn.php");
    exit();
}
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: quan_li_nguoi_dung.php");
    exit();
}
$result = $conn->query("SELECT * FROM users ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý người dùng – Hà Linh</title>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { 
            box-sizing: border-box; 
            margin: 0; padding: 0; 
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
        .card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0,0,0,.07);
            overflow: hidden;
        }
        .card-header {
            background: linear-gradient(135deg, #be0000 0%, #7b0000 100%);
            padding: 28px 32px 24px;
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
            letter-spacing: .5px;
            position: relative;
        }
        .card-header p {
            color: rgba(255,255,255,.65);
            font-size: 12px;
            margin-top: 4px;
            position: relative;
        }
        .toolbar {
            display: flex;
            gap: 8px;
            padding: 20px 28px;
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
            transition: all .18s ease;
            text-decoration: none;
        }
        .btn-primary {
            background: #be0000;
            color: #fff;
        }
        .btn-primary:hover { 
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
        .btn-danger {
            background: #fff0f0;
            color: #c62828;
        }
        .btn-danger:hover { 
            background: #ffe0e0; 
            transform: translateY(-1px); 
        }
        .btn-ghost {
            background: transparent;
            color: #6b7280;
            border: 1px solid #e5e7eb;
        }
        .btn-ghost:hover { background: #f9fafb; }
        .table-wrap {
            padding: 0 0 8px;
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        thead tr {
            background: #f8f9fc;
            border-bottom: 2px solid #eef0f7;
        }
        th {
            padding: 12px 20px;
            text-align: left;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .8px;
            color: #8892a4;
        }
        th:first-child { 
            width: 56px; 
            text-align: center; 
        }
        th:nth-child(2) { 
            width: 56px; 
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
            padding: 14px 20px;
            font-size: 13.5px;
            color: #374151;
        }
        td:first-child { text-align: center; }
        td:nth-child(2) { 
            text-align: center; 
            color: #9ca3af; 
            font-size: 12px; 
        }
        .user-cell {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .avatar {
            width: 32px; height: 32px;
            border-radius: 50%;
            background: linear-gradient(135deg, #be0000, #7b0000);
            color: #fff;
            font-size: 13px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .username { font-weight: 600; color: #1a1f36; }
        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }
        .badge-admin { 
            background: #fef3c7; 
            color: #92400e; 
        }
        .badge-ke-toan { 
            background: #dbeafe; 
            color: #1e40af; 
        }
        .badge-nhan-vien { 
            background: #d1fae5; 
            color: #065f46; 
        }
        .badge-default { 
            background: #f3f4f6; 
            color: #374151; 
        }
        .pw-mask { 
            letter-spacing: 2px; 
            color: #d1d5db; 
            font-size: 16px; 
        }
        .empty { 
            text-align: center; 
            padding: 48px 20px; 
            color: #9ca3af; 
        }
        .empty svg { 
            opacity: .35; 
            margin-bottom: 12px; 
        }
        .modal {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 1000;
            background: rgba(15,20,40,.45);
            backdrop-filter: blur(3px);
            align-items: center;
            justify-content: center;
        }
        .modal.open { display: flex; }
        .modal-box {
            background: #fff;
            border-radius: 16px;
            width: 460px;
            max-width: 95vw;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,.2);
            animation: slideUp .22s ease;
        }
        @keyframes slideUp {
            from { transform: translateY(24px); opacity: 0; }
            to   { transform: translateY(0);    opacity: 1; }
        }
        .modal-head {
            background: linear-gradient(135deg, #be0000, #7b0000);
            padding: 16px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .modal-head h2 { 
            color: #fff; 
            font-size: 14px; 
            font-weight: 700; 
        }
        .modal-close {
            background: rgba(255,255,255,.15);
            border: none;
            color: #fff;
            width: 28px; height: 28px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 16px;
            display: flex; align-items: center; 
            justify-content: center;
            transition: background .15s;
        }
        .modal-close:hover { background: rgba(255,255,255,.3); }
        iframe { display: block; }
        .page-meta {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 16px;
            font-size: 12px;
            color: #9ca3af;
        }
        .page-meta a { 
            color: #be0000; 
            text-decoration: none; 
            font-weight: 500; 
        }
        .page-meta a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="page-wrapper">

    <div class="page-meta">
        <a href="index.php">Trang chủ</a>
        <span>›</span>
        <span>Quản lý người dùng</span>
    </div>

    <div class="card">
        <div class="card-header">
            <h1>👤 Quản lý người dùng</h1>
            <p>Thêm, chỉnh sửa và phân quyền tài khoản hệ thống</p>
        </div>

        <div class="toolbar">
            <button class="btn btn-primary" onclick="openModal('form_user.php?action=add','THÊM NGƯỜI DÙNG')">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                Thêm
            </button>
            <button class="btn btn-secondary" onclick="editUser()">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                Sửa
            </button>
            <button class="btn btn-danger" onclick="deleteUser()">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                Xóa
            </button>
            <a href="index.php" class="btn btn-ghost" style="margin-left:auto;">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 18l-6-6 6-6"/></svg>
                Quay lại
            </a>
        </div>

        <div class="table-wrap">
            <table id="userTable">
                <thead>
                    <tr>
                        <th>Chọn</th>
                        <th>STT</th>
                        <th>Tài khoản</th>
                        <th>Mật khẩu</th>
                        <th>Phân quyền</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stt = 1;
                    $rows = [];
                    while ($row = $result->fetch_assoc()) $rows[] = $row;
                    if (empty($rows)):
                    ?>
                    <tr>
                        <td colspan="5">
                            <div class="empty">
                                <svg width="48" height="48" fill="none" stroke="#9ca3af" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
                                <p>Chưa có người dùng nào</p>
                            </div>
                        </td>
                    </tr>
                    <?php else: foreach ($rows as $row):
                        $initial = strtoupper(mb_substr($row['username'], 0, 1));
                        $role = $row['role'];
                        $badgeClass = match(true) {
                            str_contains($role, 'Quản') => 'badge-admin',
                            str_contains($role, 'Kế')   => 'badge-ke-toan',
                            str_contains($role, 'Nhân') => 'badge-nhan-vien',
                            default => 'badge-default'
                        };
                    ?>
                    <tr onclick="selectRow(this, <?= $row['id'] ?>)">
                        <td>
                            <input type="radio" name="user_select" value="<?= $row['id'] ?>"
                                   onclick="event.stopPropagation(); selectRow(this.closest('tr'), <?= $row['id'] ?>)">
                        </td>
                        <td><?= $stt++ ?></td>
                        <td>
                            <div class="user-cell">
                                <div class="avatar"><?= $initial ?></div>
                                <span class="username"><?= htmlspecialchars($row['username']) ?></span>
                            </div>
                        </td>
                        <td><span class="pw-mask">••••••</span></td>
                        <td><span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($role) ?></span></td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="userModal" class="modal">
    <div class="modal-box">
        <div class="modal-head">
            <h2 id="modalTitle">THÊM NGƯỜI DÙNG</h2>
            <button class="modal-close" onclick="closeModal()">×</button>
        </div>
        <iframe id="userIframe" src="" style="width:100%;height:360px;border:none;"></iframe>
    </div>
</div>

<script>
function selectRow(tr, id) {
    document.querySelectorAll('#userTable tbody tr').forEach(r => r.classList.remove('selected'));
    tr.classList.add('selected');
    const radio = tr.querySelector('input[type="radio"]');
    if (radio) radio.checked = true;
}
function openModal(url, title) {
    document.getElementById('modalTitle').innerText = title;
    document.getElementById('userIframe').src = url;
    document.getElementById('userModal').classList.add('open');
}
function closeModal() {
    document.getElementById('userModal').classList.remove('open');
    window.location.reload();
}
function getSelectedId() {
    const sel = document.querySelector('input[name="user_select"]:checked');
    return sel ? sel.value : null;
}
function editUser() {
    const id = getSelectedId();
    if (id) openModal('form_user.php?action=edit&id=' + id, 'SỬA NGƯỜI DÙNG');
    else alert('Vui lòng chọn người dùng cần sửa!');
}
function deleteUser() {
    const id = getSelectedId();
    if (!id) { alert('Vui lòng chọn người dùng cần xóa!'); return; }
    if (confirm('Bạn có chắc muốn xóa tài khoản này?'))
        window.location.href = 'quan_li_nguoi_dung.php?delete_id=' + id;
}
document.getElementById('userModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
</script>
</body>
</html>