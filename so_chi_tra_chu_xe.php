<?php
include 'conn.php';
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: dn.php"); exit(); }
$conn->query("CREATE TABLE IF NOT EXISTS `so_chi_tra_chu_xe` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `chu_xe` varchar(255) DEFAULT NULL,
    `so_chung_tu` varchar(50) DEFAULT NULL,
    `dien_giai` varchar(255) DEFAULT NULL,
    `so_tien_phai_tra` decimal(15,0) DEFAULT 0,
    `so_tien_da_tra` decimal(15,0) DEFAULT 0,
    `ngay_thu` date DEFAULT NULL,
    `ghi_chu` text DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");
$conn->query("ALTER TABLE so_chi_tra_chu_xe ADD COLUMN IF NOT EXISTS `ngay_thu` date DEFAULT NULL");
$conn->query("ALTER TABLE so_chi_tra_chu_xe ADD COLUMN IF NOT EXISTS `so_chung_tu` varchar(50) DEFAULT NULL");
$res = $conn->query("SELECT * FROM so_chi_tra_chu_xe ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Số chi trả chủ xe – Hà Linh</title>
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
        margin: 20px; 
        padding-bottom: 32px; 
    }
    .breadcrumb { 
        display: flex; 
        align-items: center; 
        gap: 6px; 
        margin-bottom: 14px; 
        font-size: 12px; 
        color: #9ca3af; 
    }
    .breadcrumb a { 
        color: #be0000; 
        text-decoration: none; 
        font-weight: 500; 
    }
    .card { 
        background: #fff; 
        border-radius: 16px; 
        box-shadow: 0 4px 24px rgba(0,0,0,.07); 
        overflow: hidden; 
        margin-bottom: 12px; 
    }
    .card-header { 
        background: linear-gradient(135deg, #be0000 0%, #7b0000 100%); 
        padding: 20px 28px 16px; 
        position: relative; 
        overflow: hidden; 
    }
    .card-header::after { 
        content:''; 
        position:absolute; 
        right:-40px; 
        top:-40px; 
        width:180px; 
        height:180px; 
        border-radius:50%; 
        background:rgba(255,255,255,.07); 
    }
    .card-header h1 { 
        color:#fff; 
        font-size:18px; 
        font-weight:700; 
        position:relative; 
    }
    .card-header p  { 
        color:rgba(255,255,255,.65); 
        font-size:11px; 
        margin-top:3px; 
        position:relative; 
    }
    .form-panel { 
        display: grid; 
        grid-template-columns: 1fr 1fr; 
        border-bottom: 1px solid #f0f0f5; 
    }
    .form-col { 
        padding: 16px 22px; 
        border-right: 1px solid #f0f0f5; 
    }
    .form-col:last-child { border-right: none; }
    .col-title { 
        font-size: 10px; 
        font-weight: 700; 
        text-transform: uppercase; 
        letter-spacing: 1px; 
        color: #9ca3af; 
        margin-bottom: 12px; 
    }
    .field-row { 
        display: flex; 
        align-items: center; 
        margin-bottom: 8px; 
    }
    .field-row label { 
        width: 130px; 
        flex-shrink: 0; 
        font-size: 12px; 
        font-weight: 600; 
        color: #6b7280; 
    }
    .field-row input, .field-row select { 
        flex: 1; 
        border: 1px solid #e5e7eb; 
        border-radius: 6px; 
        padding: 4px 9px; 
        height: 28px; 
        font-size: 12px; 
        font-family: inherit; 
        color: #1a1f36; 
        background: #fff; 
        transition: border-color .15s, box-shadow .15s; 
    }
    .field-row input:focus, .field-row select:focus { 
        outline: none; 
        border-color: #be0000; 
        box-shadow: 0 0 0 3px rgba(190,0,0,.07); 
    }
    .field-row input.money-red   { 
        font-weight:700; 
        color:#c62828; 
        font-size:13px; 
    }
    .field-row input.money-green { 
        font-weight:700; 
        color:#166534; 
        font-size:13px; 
    }
    .field-row input.con-no-field { 
        background:#fff5f5; 
        border-color:#fecaca; 
        font-weight:700; 
        font-size:13px; 
        color:#c62828; 
    }
    .filter-bar { 
        background:#f8f9fc; 
        border-bottom:1px solid #f0f0f5; 
        padding:8px 22px; 
        display:flex; 
        gap:10px; 
        align-items:center; 
        flex-wrap:wrap; 
    }
    .filter-bar label { 
        font-size:11px; 
        font-weight:600; 
        color:#6b7280; 
    }
    .filter-bar input[type="text"] { 
        border:1px solid #e5e7eb; 
        border-radius:6px; 
        padding:3px 8px; 
        height:26px; 
        font-size:11px; 
        font-family:inherit; 
        background:#fff; 
    }
    .filter-bar input[type="text"]:focus { 
        outline:none; 
        border-color:#be0000; 
    }
    .filter-bar input[type="checkbox"] { 
        width:14px; 
        height:14px; 
        accent-color:#be0000; 
    }
    .btn-filter { 
        padding:4px 12px; 
        border-radius:6px; 
        border:1px solid #e5e7eb; 
        background:#fff; 
        color:#6b7280; 
        font-size:11px; 
        font-weight:600; 
        cursor:pointer; 
        font-family:inherit; 
    }
    .btn-filter:hover { background:#f3f4f6; }
    .toolbar { 
        display:flex; 
        gap:7px; 
        padding:12px 20px; 
        border-bottom:1px solid #f0f0f5; 
        flex-wrap:wrap; 
        align-items:center; 
    }
    .btn { 
        display:inline-flex; 
        align-items:center; 
        gap:5px; 
        padding:7px 16px; 
        border-radius:8px; 
        font-family:inherit; 
        font-size:12px; 
        font-weight:600; 
        cursor:pointer; 
        border:none; 
        transition:all .18s; 
    }
    .btn-add  { 
        background:#be0000; 
        color:#fff; 
    }
    .btn-add:hover  { 
        background:#950000; 
        transform:translateY(-1px); 
        box-shadow:0 4px 10px rgba(190,0,0,.3); 
    }
    .btn-edit { 
        background:#eef0f7; 
        color:#3a4060; 
    }
    .btn-edit:hover { 
        background:#e0e3f0; 
        transform:translateY(-1px); 
    }
    .btn-del  { 
        background:#fff0f0; 
        color:#c62828; 
    }
    .btn-del:hover  { 
        background:#ffe0e0; 
        transform:translateY(-1px); 
    }
    .btn-new  { 
        background:transparent; 
        color:#6b7280; 
        border:1px solid #e5e7eb; 
    }
    .btn-new:hover  { background:#f9fafb; }
    .btn-exit { 
        background:transparent; 
        color:#6b7280; 
        border:1px solid #e5e7eb; 
    }
    .btn-exit:hover { background:#f9fafb; }
    .summary-group { 
        margin-left:auto; 
        display:flex; gap:8px; 
    }
    .sbox { 
        border-radius:8px; 
        padding:5px 14px; 
        text-align:center; 
        font-size:10px; 
    }
    .sbox span { 
        display:block; 
        font-size:14px; 
        font-weight:800; 
        margin-top:1px; 
    }
    .sbox-orange { 
        background:#fff7ed; 
        border:1px solid #fed7aa; 
        color:#9a3412; 
    }
    .sbox-orange span { color:#ea580c; }
    .sbox-green  { 
        background:#f0fdf4; 
        border:1px solid #bbf7d0; 
        color:#166534; 
    }
    .sbox-green span  { color:#16a34a; }
    .sbox-red    { 
        background:#fff5f5; 
        border:1px solid #fecaca; 
        color:#991b1b; 
    }
    .sbox-red span    { color:#dc2626; }
    .grid-wrapper { 
        height:calc(100vh - 510px); 
        min-height:220px; 
        overflow:auto; 
    }
    table { 
        width:100%; 
        border-collapse:collapse; 
        min-width:900px; 
    }
    thead tr { background:#f8f9fc; }
    th { 
        padding:10px 14px; 
        position:sticky; 
        top:0; 
        background:#f8f9fc; 
        z-index:10; 
        font-size:10.5px; 
        font-weight:700; 
        text-transform:uppercase; 
        letter-spacing:.6px; 
        color:#8892a4; 
        border-bottom:2px solid #eef0f7; 
        border-right:1px solid #f3f4f8; 
        white-space:nowrap; 
        text-align:center; 
    }
    tbody tr { 
        border-bottom:1px solid #f3f4f8; 
        transition:background .1s; 
        cursor:pointer; 
    }
    tbody tr:last-child { border-bottom:none; }
    tbody tr:hover { background:#fdf5f5; }
    tbody tr.selected { background:#fff5f5 !important; }
    tbody tr.selected td { color:#1a1f36; }
    td { 
        padding:10px 14px; 
        font-size:12px; 
        color:#374151; 
        border-right:1px solid #f3f4f8; 
        text-align:center; 
        white-space:nowrap; 
    }
    .td-left { text-align:left !important; }
    .ct-badge { 
        display:inline-block; 
        padding:2px 8px; 
        border-radius:5px; 
        background:#f3f4f6; 
        color:#6b7280; 
        font-size:11px; 
        font-weight:600; 
        font-family:monospace; 
    }
    .hang-cell { 
        display:flex; 
        align-items:center; 
        gap:7px; 
    }
    .hang-av { 
        width:28px; 
        height:28px; 
        border-radius:6px; 
        background:linear-gradient(135deg,#92400e,#b45309); 
        color:#fff; 
        font-size:11px; 
        font-weight:700; 
        display:flex; 
        align-items:center; 
        justify-content:center; 
        flex-shrink:0; 
    }
    .money-red   { 
        text-align:right !important; 
        font-weight:700; 
        color:#c62828; 
    }
    .money-green { 
        text-align:right !important; 
        font-weight:700; 
        color:#166534; 
    }
    .status-debt { 
        display:inline-block; 
        padding:2px 8px; 
        border-radius:20px; 
        background:#fee2e2; 
        color:#c62828; 
        font-size:11px; 
        font-weight:600; 
    }
    .status-ok   { 
        display:inline-block; 
        padding:2px 8px; 
        border-radius:20px; 
        background:#d1fae5; 
        color:#065f46; 
        font-size:11px; 
        font-weight:600; 
    }
    .empty { 
        text-align:center; 
        padding:48px; 
        color:#9ca3af; 
        }
</style>
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="page-wrapper">
    <div class="breadcrumb">
        <a href="index.php">Trang chủ</a> <span>›</span>
        <span>Quản lý chi phí</span> <span>›</span>
        <span>Số chi trả chủ xe</span>
    </div>
    <div class="card">
        <div class="card-header">
            <h1>🏢 Số chi trả chủ xe</h1>
            <p>Ghi nhận và theo dõi các khoản thanh toán cho hãng vận tải, chủ xe</p>
        </div>
        <form id="mainForm" method="POST" action="save_chi_chu_xe.php">
        <input type="hidden" name="action" id="form_action" value="add">
        <input type="hidden" name="edit_id" id="form_edit_id" value="">
        <div class="form-panel">
            <div class="form-col">
                <div class="col-title">📋 Thông tin chứng từ</div>
                <div class="field-row">
                    <label>Chủ xe:</label>
                    <select name="chu_xe" id="chu_xe">
                        <option value="">-- Chọn chủ xe --</option>
                        <?php $cl=$conn->query("SELECT ten_hang FROM danh_muc_hang_van_tai ORDER BY ten_hang");
                        while($c=$cl->fetch_assoc()) echo "<option value='{$c['ten_hang']}'>{$c['ten_hang']}</option>"; ?>
                    </select>
                </div>
                <div class="field-row"><label>Số chứng từ:</label><input type="text" name="so_chung_tu" id="so_chung_tu" placeholder="VD: CT-001, HD-2026..."></div>
                <div class="field-row"><label>Diễn giải:</label><input type="text" name="dien_giai" id="dien_giai" placeholder="VD: Tiền xe tháng 4/2026..."></div>
                <div class="field-row"><label>Ngày trả:</label><input type="date" name="ngay_thu" id="ngay_thu"></div>
            </div>
            <div class="form-col">
                <div class="col-title">💵 Số tiền</div>
                <div class="field-row"><label>Số tiền phải trả:</label><input type="text" name="so_tien_phai_tra" id="so_tien_phai_tra" class="money-red" onkeyup="formatNum(this);tinhCon();" placeholder="0"></div>
                <div class="field-row"><label>Số tiền đã trả:</label><input type="text" name="so_tien_da_tra" id="so_tien_da_tra" class="money-green" onkeyup="formatNum(this);tinhCon();" placeholder="0"></div>
                <div class="field-row"><label>Còn nợ:</label><input type="text" id="con_no_display" class="con-no-field" readonly></div>
                <div class="field-row"><label>Ghi chú:</label><input type="text" name="ghi_chu" id="ghi_chu"></div>
            </div>
        </div>
        <div class="filter-bar">
            <label>🔍 Lọc:</label>
            <label>Chủ xe:</label><input type="text" id="f_chu" oninput="filterTable()" placeholder="Tên chủ xe..." style="width:150px;">
            <label>Số CT:</label><input type="text" id="f_sct" oninput="filterTable()" placeholder="Số chứng từ..." style="width:130px;">
            <label style="display:flex;align-items:center;gap:5px;"><input type="checkbox" id="f_chuatra" onchange="filterTable()"> Chỉ còn nợ</label>
            <button type="button" onclick="resetFilter()" class="btn-filter">↩ Bỏ lọc</button>
        </div>
        <div class="toolbar">
            <button type="button" class="btn btn-add" onclick="submitAdd()">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>Thêm (Alt+A)
            </button>
            <button type="button" class="btn btn-edit" onclick="submitEdit()">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>Sửa (Alt+E)
            </button>
            <button type="button" class="btn btn-del" onclick="deleteRow()">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>Xóa (Alt+D)
            </button>
            <button type="button" class="btn btn-new" onclick="clearForm()">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>Làm mới
            </button>
            <button type="button" class="btn btn-exit" onclick="window.location.href='index.php'">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>Thoát
            </button>
            <div class="summary-group">
                <div class="sbox sbox-orange">Phải trả<span id="sum_phai">0</span></div>
                <div class="sbox sbox-green">Đã trả<span id="sum_da">0</span></div>
                <div class="sbox sbox-red">Còn nợ<span id="sum_con">0</span></div>
            </div>
        </div>
        </form>
        <div class="grid-wrapper">
        <table id="mainTable">
        <thead><tr>
            <th width="40">Stt</th><th width="40">Chọn</th>
            <th width="160">Chủ xe</th><th width="120">Số CT</th>
            <th>Diễn giải</th>
            <th width="95">Ngày trả</th>
            <th width="120">Phải trả</th><th width="120">Đã trả</th>
            <th width="110">Trạng thái</th><th width="150">Ghi chú</th>
        </tr></thead>
        <tbody id="tableBody">
        <?php $stt=1; while($r=$res->fetch_assoc()):
            $con = $r['so_tien_phai_tra'] - $r['so_tien_da_tra'];
            $initial = strtoupper(mb_substr($r['chu_xe'], 0, 1));
        ?>
        <tr onclick="selectRow(this)"
            data-id="<?=$r['id']?>"
            data-chu="<?=htmlspecialchars($r['chu_xe']??'')?>"
            data-sct="<?=htmlspecialchars($r['so_chung_tu']??'')?>"
            data-dg="<?=htmlspecialchars($r['dien_giai']??'')?>"
            data-ngaytra="<?=$r['ngay_thu']??''?>"
            data-phai="<?=$r['so_tien_phai_tra']?>"
            data-da="<?=$r['so_tien_da_tra']?>"
            data-ghichu="<?=htmlspecialchars($r['ghi_chu']??'')?>">
            <td><?=$stt++?></td>
            <td><input type="radio" name="row_select" value="<?=$r['id']?>" onclick="event.stopPropagation();"></td>
            <td class="td-left">
                <div class="hang-cell">
                    <div class="hang-av"><?=$initial?></div>
                    <span><?=htmlspecialchars($r['chu_xe']??'')?></span>
                </div>
            </td>
            <td><span class="ct-badge"><?=htmlspecialchars($r['so_chung_tu']??'')?></span></td>
            <td class="td-left"><?=htmlspecialchars($r['dien_giai']??'')?></td>
            <td><?=$r['ngay_thu']?date('d/m/Y',strtotime($r['ngay_thu'])):''?></td>
            <td class="money-red"><?=number_format($r['so_tien_phai_tra'])?></td>
            <td class="money-green"><?=number_format($r['so_tien_da_tra'])?></td>
            <td><?= $con > 0
                ? '<span class="status-debt">Còn '.number_format($con).'</span>'
                : '<span class="status-ok">Đã đủ</span>'
            ?></td>
            <td class="td-left"><?=htmlspecialchars($r['ghi_chu']??'')?></td>
        </tr>
        <?php endwhile; ?>
        </tbody>
        </table>
        </div>
    </div>
</div>
<script>
let selectedRow=null;
function tinhCon(){
    const p=parseNum(document.getElementById('so_tien_phai_tra').value);
    const d=parseNum(document.getElementById('so_tien_da_tra').value);
    const el=document.getElementById('con_no_display');
    const c=p-d; el.value=fmtStr(c);
    el.style.color=c>0?'#c62828':'#166534';
}
function selectRow(row){
    document.querySelectorAll('#mainTable tbody tr').forEach(r=>r.classList.remove('selected'));
    row.classList.add('selected'); selectedRow=row;
    row.querySelector('input[type="radio"]').checked=true;
    const d=row.dataset;
    setSelect('chu_xe',d.chu);
    document.getElementById('so_chung_tu').value=d.sct;
    document.getElementById('dien_giai').value=d.dg;
    document.getElementById('ngay_thu').value=d.ngaytra;
    document.getElementById('so_tien_phai_tra').value=fmtStr(d.phai);
    document.getElementById('so_tien_da_tra').value=fmtStr(d.da);
    document.getElementById('ghi_chu').value=d.ghichu;
    document.getElementById('form_edit_id').value=d.id; tinhCon();
}
function setSelect(id,val){
    const s=document.getElementById(id);
    for(let i=0;i<s.options.length;i++)if(s.options[i].value==val){
        s.selectedIndex=i;
        return;
    }
}
function submitAdd(){
    document.getElementById('form_action').value='add';
    document.getElementById('form_edit_id').value='';
    document.getElementById('mainForm').submit();
}
function submitEdit(){
    if(!selectedRow){
        alert('Chọn dòng để sửa!');
        return;
    }
    document.getElementById('form_action').value='edit';
    document.getElementById('mainForm').submit();
}
function deleteRow(){
    const r=document.querySelector('input[name="row_select"]:checked');
    if(!r){
        alert('Chọn dòng!');
        return;
    }
    if(confirm('Xác nhận xóa?'))window.location.href='xoa_chi_chu_xe.php?id='+r.value;
}
function clearForm(){
    document.getElementById('mainForm').reset();
    document.getElementById('form_edit_id').value='';
    document.getElementById('form_action').value='add';
    document.getElementById('con_no_display').value='';
    document.querySelectorAll('#mainTable tbody tr').forEach(r=>r.classList.remove('selected'));
    selectedRow=null;
}
function filterTable(){
    const fc=document.getElementById('f_chu').value.toUpperCase();
    const fs=document.getElementById('f_sct').value.toUpperCase();
    const fct=document.getElementById('f_chuatra').checked;
    let sp=0,sd=0;
    document.querySelectorAll('#tableBody tr').forEach(row=>{
        const cells=row.getElementsByTagName('td');let show=true;
        if(fc&&!cells[2]?.innerText.toUpperCase().includes(fc))show=false;
        if(fs&&!cells[3]?.innerText.toUpperCase().includes(fs))show=false;
        if(fct&&!cells[8]?.innerText.includes('Còn'))show=false;
        row.style.display=show?'':'none';
        if(show){sp+=parseNum(cells[6]?.innerText||'0');sd+=parseNum(cells[7]?.innerText||'0');}
    });
    document.getElementById('sum_phai').innerText=fmtStr(sp);
    document.getElementById('sum_da').innerText=fmtStr(sd);
    document.getElementById('sum_con').innerText=fmtStr(sp-sd);
}
function resetFilter(){
    document.getElementById('f_chu').value='';
    document.getElementById('f_sct').value='';
    document.getElementById('f_chuatra').checked=false;
    filterTable();
}
function parseNum(s){return parseInt(String(s).replace(/\./g,'').replace(/,/g,''))||0;}
function fmtStr(n){return Math.round(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g,'.');}
function formatNum(i){
    let v=i.value.replace(/\D/g,'');
    i.value=v.replace(/\B(?=(\d{3})+(?!\d))/g,'.');
}
document.addEventListener('keydown',e=>{if(e.altKey){if(e.key.toLowerCase()==='a'){e.preventDefault();submitAdd();}if(e.key.toLowerCase()==='e'){e.preventDefault();submitEdit();}if(e.key.toLowerCase()==='d'){e.preventDefault();deleteRow();}}});
window.addEventListener('load',()=>filterTable());
</script>
</body>
</html>