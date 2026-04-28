<?php
include 'conn.php';
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: dn.php"); exit(); }
$conn->query("CREATE TABLE IF NOT EXISTS `luong_lai_xe` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `thang` int(2) NOT NULL, `nam` int(4) NOT NULL,
    `lai_xe_id` int(11) DEFAULT NULL,
    `ten_lai_xe` varchar(100) DEFAULT NULL,
    `luong_trach_nhiem` decimal(15,0) DEFAULT 0,
    `tong_luong_chuyen_cn` decimal(15,0) DEFAULT 0,
    `tong_luong_chuyen_dl` decimal(15,0) DEFAULT 0,
    `phu_cap` decimal(15,0) DEFAULT 0,
    `khau_tru` decimal(15,0) DEFAULT 0,
    `tong_luong` decimal(15,0) DEFAULT 0,
    `da_thanh_toan` tinyint(1) DEFAULT 0,
    `ngay_thanh_toan` date DEFAULT NULL,
    `ghi_chu` text DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");
$thang_loc = intval($_GET['thang'] ?? date('n'));
$nam_loc   = intval($_GET['nam']   ?? date('Y'));
$res = $conn->query("SELECT * FROM luong_lai_xe WHERE thang=$thang_loc AND nam=$nam_loc ORDER BY ten_lai_xe ASC");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Quản lý lương lái xe – Hà Linh</title>
<link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Be Vietnam Pro', sans-serif; background: #f1f3f8; color: #1a1f36; min-height: 100vh; }
    .page-wrapper { margin: 20px; padding-bottom: 32px; }
    .breadcrumb { display: flex; align-items: center; gap: 6px; margin-bottom: 14px; font-size: 12px; color: #9ca3af; }
    .breadcrumb a { color: #be0000; text-decoration: none; font-weight: 500; }
    .card { background: #fff; border-radius: 16px; box-shadow: 0 4px 24px rgba(0,0,0,.07); overflow: hidden; margin-bottom: 12px; }
    .card-header { background: linear-gradient(135deg, #be0000 0%, #7b0000 100%); padding: 20px 28px 16px; position: relative; overflow: hidden; }
    .card-header::after { content:''; position:absolute; right:-40px; top:-40px; width:180px; height:180px; border-radius:50%; background:rgba(255,255,255,.07); }
    .card-header h1 { color:#fff; font-size:18px; font-weight:700; position:relative; }
    .card-header p  { color:rgba(255,255,255,.65); font-size:11px; margin-top:3px; position:relative; }

    /* Period bar */
    .period-bar { display: flex; align-items: center; gap: 10px; padding: 12px 22px; background: #f8f9fc; border-bottom: 1px solid #f0f0f5; flex-wrap: wrap; }
    .period-bar label { font-size: 12px; font-weight: 700; color: #374151; }
    .period-bar select, .period-bar input[type="number"] { border: 1px solid #e5e7eb; border-radius: 6px; padding: 4px 8px; height: 28px; font-size: 12px; font-family: inherit; }
    .btn-view { background: #1e40af; color: #fff; border: none; border-radius: 8px; padding: 6px 16px; font-size: 12px; font-weight: 600; cursor: pointer; font-family: inherit; display: inline-flex; align-items: center; gap: 5px; }
    .btn-view:hover { background: #1e3a8a; }
    .btn-gen  { background: #166534; color: #fff; border: none; border-radius: 8px; padding: 6px 16px; font-size: 12px; font-weight: 600; cursor: pointer; font-family: inherit; display: inline-flex; align-items: center; gap: 5px; }
    .btn-gen:hover { background: #14532d; }

    /* Form */
    .form-panel { display: grid; grid-template-columns: 1fr 1fr 1fr; border-bottom: 1px solid #f0f0f5; }
    .form-col { padding: 14px 18px; border-right: 1px solid #f0f0f5; }
    .form-col:last-child { border-right: none; }
    .col-title { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #9ca3af; margin-bottom: 12px; }
    .field-row { display: flex; align-items: center; margin-bottom: 7px; }
    .field-row label { width: 130px; flex-shrink: 0; font-size: 12px; font-weight: 600; color: #6b7280; }
    .field-row input, .field-row select { flex: 1; border: 1px solid #e5e7eb; border-radius: 6px; padding: 4px 8px; height: 28px; font-size: 12px; font-family: inherit; color: #1a1f36; background: #fff; transition: border-color .15s; }
    .field-row input:focus, .field-row select:focus { outline: none; border-color: #be0000; box-shadow: 0 0 0 3px rgba(190,0,0,.07); }
    .field-row input[readonly] { background: #f8f9fc; color: #9ca3af; }
    .field-row input.total-field { background: #f0fdf4; border-color: #bbf7d0; color: #166534; font-weight: 700; font-size: 14px; }
    .field-row input.deduct-field { color: #c62828; font-weight: 600; }
    .field-row input[type="checkbox"] { width: 16px; height: 16px; flex: none; accent-color: #166534; }

    /* Toolbar */
    .toolbar { display: flex; gap: 7px; padding: 12px 20px; border-bottom: 1px solid #f0f0f5; flex-wrap: wrap; align-items: center; }
    .btn { display: inline-flex; align-items: center; gap: 5px; padding: 7px 16px; border-radius: 8px; font-family: inherit; font-size: 12px; font-weight: 600; cursor: pointer; border: none; transition: all .18s; }
    .btn-add  { background: #be0000; color: #fff; }
    .btn-add:hover  { background: #950000; transform: translateY(-1px); box-shadow: 0 4px 10px rgba(190,0,0,.3); }
    .btn-edit { background: #eef0f7; color: #3a4060; }
    .btn-edit:hover { background: #e0e3f0; transform: translateY(-1px); }
    .btn-del  { background: #fff0f0; color: #c62828; }
    .btn-del:hover  { background: #ffe0e0; transform: translateY(-1px); }
    .btn-new  { background: transparent; color: #6b7280; border: 1px solid #e5e7eb; }
    .btn-new:hover  { background: #f9fafb; }
    .btn-exit { background: transparent; color: #6b7280; border: 1px solid #e5e7eb; }
    .btn-exit:hover { background: #f9fafb; }
    .summary-group { margin-left: auto; display: flex; gap: 8px; }
    .sbox { border-radius: 8px; padding: 5px 14px; text-align: center; font-size: 10px; }
    .sbox span { display: block; font-size: 14px; font-weight: 800; margin-top: 1px; }
    .sbox-blue   { background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; }
    .sbox-blue span   { color: #1d4ed8; }
    .sbox-green  { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }
    .sbox-green span  { color: #16a34a; }
    .sbox-red    { background: #fff5f5; border: 1px solid #fecaca; color: #991b1b; }
    .sbox-red span    { color: #dc2626; }

    /* Table */
    .grid-wrapper { height: calc(100vh - 530px); min-height: 220px; overflow: auto; }
    table { width: 100%; border-collapse: collapse; min-width: 1100px; }
    thead tr { background: #f8f9fc; }
    th { padding: 10px 12px; position: sticky; top: 0; background: #f8f9fc; z-index: 10; font-size: 10.5px; font-weight: 700; text-transform: uppercase; letter-spacing: .6px; color: #8892a4; border-bottom: 2px solid #eef0f7; border-right: 1px solid #f3f4f8; white-space: nowrap; text-align: center; }
    tbody tr { border-bottom: 1px solid #f3f4f8; transition: background .1s; cursor: pointer; }
    tbody tr:last-child { border-bottom: none; }
    tbody tr:hover { background: #fdf5f5; }
    tbody tr.selected { background: #fff5f5 !important; }
    tbody tr.selected td { color: #1a1f36; }
    td { padding: 10px 12px; font-size: 12px; color: #374151; border-right: 1px solid #f3f4f8; text-align: center; white-space: nowrap; }
    .td-left { text-align: left !important; }
    .driver-cell { display: flex; align-items: center; gap: 8px; }
    .driver-av { width: 28px; height: 28px; border-radius: 50%; background: linear-gradient(135deg,#be0000,#7b0000); color:#fff; font-size:11px; font-weight:700; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .money { text-align: right !important; font-weight: 600; color: #1a1f36; }
    .money-red { text-align: right !important; font-weight: 600; color: #c62828; }
    .money-total { text-align: right !important; font-weight: 800; color: #166534; font-size: 13px; }
    .status-paid   { display:inline-flex; align-items:center; gap:4px; padding:2px 9px; border-radius:20px; background:#d1fae5; color:#065f46; font-size:11px; font-weight:600; }
    .status-unpaid { display:inline-flex; align-items:center; gap:4px; padding:2px 9px; border-radius:20px; background:#fee2e2; color:#c62828; font-size:11px; font-weight:600; }
</style>
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="page-wrapper">
    <div class="breadcrumb">
        <a href="index.php">Trang chủ</a> <span>›</span>
        <span>Quản lý chi phí</span> <span>›</span>
        <span>Quản lý lương lái xe</span>
    </div>
    <div class="card">
        <div class="card-header">
            <h1>👨‍✈️ Quản lý lương lái xe</h1>
            <p>Tổng hợp và quản lý lương lái xe theo từng tháng</p>
        </div>

        <!-- Period bar -->
        <div class="period-bar">
            <label>📅 Kỳ lương:</label>
            <form method="GET" style="display:flex;gap:8px;align-items:center;">
                <select name="thang">
                    <?php for($m=1;$m<=12;$m++) echo "<option value='$m' ".($m==$thang_loc?'selected':'').">Tháng $m</option>"; ?>
                </select>
                <input type="number" name="nam" value="<?=$nam_loc?>" style="width:80px;">
                <button type="submit" class="btn-view">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    Xem
                </button>
            </form>
            <form method="POST" action="gen_luong.php" style="display:flex;align-items:center;">
                <input type="hidden" name="thang" value="<?=$thang_loc?>">
                <input type="hidden" name="nam" value="<?=$nam_loc?>">
                <button type="submit" class="btn-gen"
                    onclick="return confirm('Tạo bảng lương tháng <?=$thang_loc?>/<?=$nam_loc?>?\n(Sẽ tổng hợp lương từ các chuyến đã chạy)')">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                    Tạo bảng lương T<?=$thang_loc?>/<?=$nam_loc?>
                </button>
            </form>
        </div>

        <!-- Form -->
        <form id="mainForm" method="POST" action="save_luong.php">
        <input type="hidden" name="action" id="form_action" value="add">
        <input type="hidden" name="edit_id" id="form_edit_id" value="">
        <input type="hidden" name="thang_ky" value="<?=$thang_loc?>">
        <input type="hidden" name="nam_ky" value="<?=$nam_loc?>">
        <div class="form-panel">
            <div class="form-col">
                <div class="col-title">👤 Thông tin lái xe</div>
                <div class="field-row">
                    <label>Lái xe:</label>
                    <select name="lai_xe_id" id="lai_xe_id" onchange="loadLaiXe(this)">
                        <option value="">-- Chọn lái xe --</option>
                        <?php $lxl=$conn->query("SELECT id,ten_lai_xe,luong_trach_nhiem FROM danh_muc_lai_xe WHERE da_nghi=0 ORDER BY ten_lai_xe");
                        while($lx=$lxl->fetch_assoc()) echo "<option value='{$lx['id']}' data-ten='{$lx['ten_lai_xe']}' data-luong='{$lx['luong_trach_nhiem']}'>{$lx['ten_lai_xe']}</option>"; ?>
                    </select>
                </div>
                <div class="field-row"><label>Tên lái xe:</label><input type="text" name="ten_lai_xe" id="ten_lai_xe" readonly></div>
                <div class="field-row"><label>Lương TN:</label><input type="text" name="luong_trach_nhiem" id="luong_trach_nhiem" onkeyup="formatNum(this);tinhTong();" style="color:#c62828;font-weight:700;"></div>
            </div>
            <div class="form-col">
                <div class="col-title">💰 Lương chuyến</div>
                <div class="field-row"><label>Lương CN (chuyến):</label><input type="text" name="tong_luong_chuyen_cn" id="tong_luong_chuyen_cn" onkeyup="formatNum(this);tinhTong();" placeholder="Tổng lương chuyến CN"></div>
                <div class="field-row"><label>Lương DL (chuyến):</label><input type="text" name="tong_luong_chuyen_dl" id="tong_luong_chuyen_dl" onkeyup="formatNum(this);tinhTong();" placeholder="Tổng lương chuyến DL"></div>
                <div class="field-row"><label>Phụ cấp:</label><input type="text" name="phu_cap" id="phu_cap" onkeyup="formatNum(this);tinhTong();" placeholder="0"></div>
                <div class="field-row"><label>Khấu trừ:</label><input type="text" name="khau_tru" id="khau_tru" class="deduct-field" onkeyup="formatNum(this);tinhTong();" placeholder="0"></div>
            </div>
            <div class="form-col">
                <div class="col-title">📊 Kết quả</div>
                <div class="field-row"><label>Tổng lương:</label><input type="text" id="tong_luong_display" class="total-field" readonly></div>
                <div class="field-row">
                    <label>Đã thanh toán:</label>
                    <input type="checkbox" name="da_thanh_toan" id="da_thanh_toan" onchange="toggleNgayTT()">
                </div>
                <div class="field-row"><label>Ngày TT:</label><input type="date" name="ngay_thanh_toan" id="ngay_thanh_toan"></div>
                <div class="field-row"><label>Ghi chú:</label><input type="text" name="ghi_chu" id="ghi_chu"></div>
            </div>
        </div>

        <!-- Toolbar -->
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
                <div class="sbox sbox-blue">Tổng lương tháng<span id="sum_luong">0</span></div>
                <div class="sbox sbox-green">Đã thanh toán<span id="sum_da">0</span></div>
                <div class="sbox sbox-red">Chưa TT<span id="sum_chua">0</span></div>
            </div>
        </div>
        </form>

        <!-- Table -->
        <div class="grid-wrapper">
        <table id="mainTable">
        <thead><tr>
            <th width="40">Stt</th><th width="40">Chọn</th>
            <th width="160">Tên lái xe</th>
            <th width="110">Lương TN</th><th width="110">Lương CN</th>
            <th width="110">Lương DL</th><th width="90">Phụ cấp</th>
            <th width="90">Khấu trừ</th><th width="130">Tổng lương</th>
            <th width="100">Trạng thái</th><th width="90">Ngày TT</th><th>Ghi chú</th>
        </tr></thead>
        <tbody id="tableBody">
        <?php $stt=1; while($r=$res->fetch_assoc()):
            $initial = strtoupper(mb_substr($r['ten_lai_xe'], 0, 1));
        ?>
        <tr onclick="selectRow(this)"
            data-id="<?=$r['id']?>" data-lxid="<?=$r['lai_xe_id']?>"
            data-ten="<?=htmlspecialchars($r['ten_lai_xe']??'')?>"
            data-ltn="<?=$r['luong_trach_nhiem']?>" data-lcn="<?=$r['tong_luong_chuyen_cn']?>"
            data-ldl="<?=$r['tong_luong_chuyen_dl']?>" data-pc="<?=$r['phu_cap']?>"
            data-kt="<?=$r['khau_tru']?>" data-tl="<?=$r['tong_luong']?>"
            data-datt="<?=$r['da_thanh_toan']?>" data-ngaytt="<?=$r['ngay_thanh_toan']??''?>"
            data-ghichu="<?=htmlspecialchars($r['ghi_chu']??'')?>">
            <td><?=$stt++?></td>
            <td><input type="radio" name="row_select" value="<?=$r['id']?>" onclick="event.stopPropagation();"></td>
            <td class="td-left">
                <div class="driver-cell">
                    <div class="driver-av"><?=$initial?></div>
                    <span style="font-weight:600;"><?=htmlspecialchars($r['ten_lai_xe']??'')?></span>
                </div>
            </td>
            <td class="money"><?=number_format($r['luong_trach_nhiem'])?></td>
            <td class="money"><?=number_format($r['tong_luong_chuyen_cn'])?></td>
            <td class="money"><?=number_format($r['tong_luong_chuyen_dl'])?></td>
            <td class="money"><?=number_format($r['phu_cap'])?></td>
            <td class="money-red"><?=number_format($r['khau_tru'])?></td>
            <td class="money-total"><?=number_format($r['tong_luong'])?></td>
            <td><?= $r['da_thanh_toan']
                ? '<span class="status-paid">✔ Đã TT</span>'
                : '<span class="status-unpaid">✖ Chưa TT</span>'
            ?></td>
            <td><?=$r['ngay_thanh_toan']?date('d/m/Y',strtotime($r['ngay_thanh_toan'])):''?></td>
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
function tinhTong(){
    const ltn=parseNum(document.getElementById('luong_trach_nhiem').value);
    const lcn=parseNum(document.getElementById('tong_luong_chuyen_cn').value);
    const ldl=parseNum(document.getElementById('tong_luong_chuyen_dl').value);
    const pc=parseNum(document.getElementById('phu_cap').value);
    const kt=parseNum(document.getElementById('khau_tru').value);
    document.getElementById('tong_luong_display').value=fmtStr(ltn+lcn+ldl+pc-kt);
}
function loadLaiXe(sel){
    const opt=sel.options[sel.selectedIndex];
    document.getElementById('ten_lai_xe').value=opt.dataset.ten||'';
    document.getElementById('luong_trach_nhiem').value=fmtStr(opt.dataset.luong||0);
    const id=sel.value;
    if(id){
        const thang=document.querySelector('input[name="thang_ky"]').value;
        const nam=document.querySelector('input[name="nam_ky"]').value;
        fetch(`xuly_ajax.php?type=get_luong_chuyen&lai_xe_id=${id}&thang=${thang}&nam=${nam}`)
        .then(r=>r.json()).then(d=>{
            if(d){
                document.getElementById('tong_luong_chuyen_cn').value=fmtStr(d.cn||0);
                document.getElementById('tong_luong_chuyen_dl').value=fmtStr(d.dl||0);
                tinhTong();
            }
        }).catch(()=>{});
    }
    tinhTong();
}
function selectRow(row){
    document.querySelectorAll('#mainTable tbody tr').forEach(r=>r.classList.remove('selected'));
    row.classList.add('selected'); selectedRow=row;
    row.querySelector('input[type="radio"]').checked=true;
    const d=row.dataset;
    setSelect('lai_xe_id',d.lxid);
    document.getElementById('ten_lai_xe').value=d.ten;
    document.getElementById('luong_trach_nhiem').value=fmtStr(d.ltn);
    document.getElementById('tong_luong_chuyen_cn').value=fmtStr(d.lcn);
    document.getElementById('tong_luong_chuyen_dl').value=fmtStr(d.ldl);
    document.getElementById('phu_cap').value=fmtStr(d.pc);
    document.getElementById('khau_tru').value=fmtStr(d.kt);
    document.getElementById('da_thanh_toan').checked=d.datt=='1';
    document.getElementById('ngay_thanh_toan').value=d.ngaytt;
    document.getElementById('ghi_chu').value=d.ghichu;
    document.getElementById('form_edit_id').value=d.id;
    tinhTong();
}
function setSelect(id,val){const s=document.getElementById(id);for(let i=0;i<s.options.length;i++)if(s.options[i].value==val){s.selectedIndex=i;return;}}
function toggleNgayTT(){document.getElementById('ngay_thanh_toan').value=document.getElementById('da_thanh_toan').checked?new Date().toISOString().split('T')[0]:'';}
function submitAdd(){document.getElementById('form_action').value='add';document.getElementById('form_edit_id').value='';document.getElementById('mainForm').submit();}
function submitEdit(){if(!selectedRow){alert('Chọn dòng để sửa!');return;}document.getElementById('form_action').value='edit';document.getElementById('mainForm').submit();}
function deleteRow(){const r=document.querySelector('input[name="row_select"]:checked');if(!r){alert('Chọn dòng!');return;}if(confirm('Xác nhận xóa?'))window.location.href='xoa_luong.php?id='+r.value+'&thang=<?=$thang_loc?>&nam=<?=$nam_loc?>';}
function clearForm(){document.getElementById('mainForm').reset();document.getElementById('form_edit_id').value='';document.getElementById('form_action').value='add';document.getElementById('tong_luong_display').value='';document.querySelectorAll('#mainTable tbody tr').forEach(r=>r.classList.remove('selected'));selectedRow=null;}
function calcSummary(){
    let tl=0,da=0;
    document.querySelectorAll('#tableBody tr').forEach(row=>{
        const cells=row.getElementsByTagName('td');
        if(row.style.display==='none')return;
        tl+=parseNum(cells[8]?.innerText||'0');
        if(cells[9]?.innerText.includes('Đã'))da+=parseNum(cells[8]?.innerText||'0');
    });
    document.getElementById('sum_luong').innerText=fmtStr(tl);
    document.getElementById('sum_da').innerText=fmtStr(da);
    document.getElementById('sum_chua').innerText=fmtStr(tl-da);
}
function parseNum(s){return parseInt(String(s).replace(/\./g,'').replace(/,/g,''))||0;}
function fmtStr(n){return Math.round(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g,'.');}
function formatNum(i){let v=i.value.replace(/\D/g,'');i.value=v.replace(/\B(?=(\d{3})+(?!\d))/g,'.');}
document.addEventListener('keydown',e=>{if(e.altKey){if(e.key.toLowerCase()==='a'){e.preventDefault();submitAdd();}if(e.key.toLowerCase()==='e'){e.preventDefault();submitEdit();}if(e.key.toLowerCase()==='d'){e.preventDefault();deleteRow();}}});
window.addEventListener('load',()=>calcSummary());
</script>
</body>
</html>