<?php
include 'conn.php';
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: dn.php"); exit(); }
$conn->query("CREATE TABLE IF NOT EXISTS `chi_phi_chung` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `ngay_chi` date NOT NULL, `so_chung_tu` varchar(50) DEFAULT NULL,
    `loai_chi_phi` varchar(100) DEFAULT NULL, `mo_ta` varchar(255) DEFAULT NULL,
    `so_tien` decimal(15,0) DEFAULT 0, `nguoi_chi` varchar(100) DEFAULT NULL,
    `ghi_chu` text DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");
$conn->query("ALTER TABLE chi_phi_chung ADD COLUMN IF NOT EXISTS `ngay_chi` date DEFAULT NULL");
$conn->query("ALTER TABLE chi_phi_chung ADD COLUMN IF NOT EXISTS `so_chung_tu` varchar(50) DEFAULT NULL");
$conn->query("ALTER TABLE chi_phi_chung ADD COLUMN IF NOT EXISTS `loai_chi_phi` varchar(100) DEFAULT NULL");
$conn->query("ALTER TABLE chi_phi_chung ADD COLUMN IF NOT EXISTS `nguoi_chi` varchar(100) DEFAULT NULL");
$conn->query("ALTER TABLE chi_phi_chung ADD COLUMN IF NOT EXISTS `ghi_chu` text DEFAULT NULL");
$res = $conn->query("SELECT * FROM chi_phi_chung ORDER BY ngay_chi DESC, id DESC");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Chi phí chung – Hà Linh</title>
<link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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
      content:''; position:absolute; 
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
      width: 120px; 
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
    .filter-bar { 
      background: #f8f9fc; 
      border-bottom: 1px solid #f0f0f5; 
      padding: 8px 22px; 
      display: flex; 
      gap: 10px; 
      align-items: center; 
      flex-wrap: wrap; 
    }
    .filter-bar label { 
      font-size: 11px; 
      font-weight: 600; 
      color: #6b7280; 
    }
    .filter-bar input, .filter-bar select { 
      border: 1px solid #e5e7eb; 
      border-radius: 6px; 
      padding: 3px 8px; 
      height: 26px; 
      font-size: 11px; 
      font-family: inherit; 
      background: #fff; 
    }
    .filter-bar input:focus, .filter-bar select:focus { 
      outline: none; 
      border-color: #be0000; 
    }
    .btn-filter { 
      padding: 4px 12px; 
      border-radius: 6px; 
      border: 1px solid #e5e7eb; 
      background: #fff; color: #6b7280; 
      font-size: 11px; font-weight: 600; 
      cursor: pointer; 
      font-family: inherit; 
    }
    .btn-filter:hover { background: #f3f4f6; }
    .toolbar { 
      display: flex; 
      gap: 7px; 
      padding: 12px 20px; 
      border-bottom: 1px solid #f0f0f5; 
      flex-wrap: wrap; 
      align-items: center; 
    }
    .btn { 
      display: inline-flex; 
      align-items: center; 
      gap: 5px; 
      padding: 7px 16px; 
      border-radius: 8px; 
      font-family: inherit; 
      font-size: 12px; 
      font-weight: 600; 
      cursor: pointer; 
      border: none; 
      transition: all .18s; 
    }
    .btn-add  { 
      background: #be0000; 
      color: #fff; 
    }
    .btn-add:hover  { 
      background: #950000; 
      transform: translateY(-1px); 
      box-shadow: 0 4px 10px rgba(190,0,0,.3); 
    }
    .btn-edit { 
      background: #eef0f7; 
      color: #3a4060; 
    }
    .btn-edit:hover { 
      background: #e0e3f0; 
      transform: translateY(-1px); 
    }
    .btn-del  { 
      background: #fff0f0; 
      color: #c62828; 
    }
    .btn-del:hover  { 
      background: #ffe0e0; 
      transform: translateY(-1px); 
    }
    .btn-new  { 
      background: transparent; 
      color: #6b7280; 
      border: 1px solid #e5e7eb; 
    }
    .btn-new:hover  { 
      background: #f9fafb; 
    }
    .btn-exit { 
      background: transparent; 
      color: #6b7280; 
      border: 1px solid #e5e7eb; 
    }
    .btn-exit:hover { background: #f9fafb; }
    .summary-box { 
      margin-left: auto; 
      background: #fff7ed; 
      border: 1px solid #fed7aa; 
      border-radius: 8px; 
      padding: 5px 18px; 
      text-align: center; 
    }
    .summary-box span { 
      font-size: 10px; 
      color: #9a3412; 
      display: block; 
    }
    .summary-box strong { 
      font-size: 15px; 
      font-weight: 800; 
      color: #ea580c; 
    }
    .grid-wrapper { 
      height: calc(100vh - 460px); 
      min-height: 240px; 
      overflow: auto; 
    }
    table { 
      width: 100%; 
      border-collapse: collapse; 
      min-width: 900px; 
    }
    thead tr { background: #f8f9fc; }
    th { 
      padding: 10px 14px; 
      position: sticky; 
      top: 0; 
      background: #f8f9fc; 
      z-index: 10; 
      font-size: 10.5px; 
      font-weight: 700; 
      text-transform: uppercase; 
      letter-spacing: .6px; 
      color: #8892a4; 
      border-bottom: 2px solid #eef0f7; 
      border-right: 1px solid #f3f4f8; 
      white-space: nowrap; 
      text-align: center; 
    }
    tbody tr { 
      border-bottom: 1px solid #f3f4f8; 
      transition: background .1s; 
      cursor: pointer; 
    }
    tbody tr:last-child { border-bottom: none; }
    tbody tr:hover { background: #fdf5f5; }
    tbody tr.selected { background: #fff5f5 !important; }
    tbody tr.selected td { color: #1a1f36; }
    td { 
      padding: 10px 14px; 
      font-size: 12px; 
      color: #374151; 
      border-right: 1px solid #f3f4f8; 
      text-align: center; 
      white-space: nowrap; 
    }
    .td-left { text-align: left !important; }
    .ct-badge { 
      display: inline-block; 
      padding: 2px 8px; 
      border-radius: 5px; 
      background: #f3f4f6; 
      color: #6b7280; 
      font-size: 11px; 
      font-weight: 600; 
      font-family: monospace; 
    }
    .loai-badge { 
      display: inline-flex; 
      align-items: center; 
      gap: 4px; 
      padding: 2px 9px; 
      border-radius: 20px; 
      font-size: 11px; 
      font-weight: 600; 
    }
    .loai-vp  { 
      background: #dbeafe; 
      color: #1e40af; 
    }
    .loai-lnv { 
      background: #ede9fe; 
      color: #5b21b6; 
    }
    .loai-dn  { 
      background: #fef9c3; 
      color: #713f12; 
    }
    .loai-it  { 
      background: #d1fae5; 
      color: #065f46; 
    }
    .loai-mk  { 
      background: #fce7f3; 
      color: #9d174d; 
    }
    .loai-mb  { 
      background: #fff7ed; 
      color: #9a3412; 
    }
    .loai-thue{ 
      background: #e0e7ff; 
      color: #3730a3; 
    }
    .loai-kh  { 
      background: #f3f4f6; 
      color: #374151; 
    }
    .money { 
      text-align: right !important; 
      font-weight: 700; 
      color: #c62828; 
    }
    .empty { 
      text-align: center; 
      padding: 48px; 
      color: #9ca3af; 
      }
</style>
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="page-wrapper">
    <div class="breadcrumb">
        <a href="index.php">Trang chủ</a> <span>›</span>
        <span>Quản lý chi phí</span> <span>›</span>
        <span>Chi phí chung</span>
    </div>
    <div class="card">
        <div class="card-header">
            <h1>📊 Chi phí chung</h1>
            <p>Ghi nhận các khoản chi phí vận hành, văn phòng và quản lý</p>
        </div>
        <form id="mainForm" method="POST" action="save_chi_phi_chung.php">
        <input type="hidden" name="action" id="form_action" value="add">
        <input type="hidden" name="edit_id" id="form_edit_id" value="">
        <div class="form-panel">
            <div class="form-col">
                <div class="col-title">📋 Thông tin chứng từ</div>
                <div class="field-row"><label>Ngày:</label><input type="date" name="ngay_chi" id="ngay_chi" value="<?=date('Y-m-d')?>"></div>
                <div class="field-row"><label>Số chứng từ:</label><input type="text" name="so_chung_tu" id="so_chung_tu" placeholder="VD: CT-001, PC-2026..."></div>
                <div class="field-row">
                    <label>Loại chi phí:</label>
                    <select name="loai_chi_phi" id="loai_chi_phi">
                        <option value="Văn phòng">🏢 Văn phòng</option>
                        <option value="Lương nhân viên">👤 Lương nhân viên</option>
                        <option value="Điện nước">💡 Điện nước</option>
                        <option value="Internet / Điện thoại">📱 Internet / Điện thoại</option>
                        <option value="Marketing / Quảng cáo">📢 Marketing / Quảng cáo</option>
                        <option value="Thuê mặt bằng">🏠 Thuê mặt bằng</option>
                        <option value="Thuế / Phí">📋 Thuế / Phí</option>
                        <option value="Khác">📌 Khác</option>
                    </select>
                </div>
                <div class="field-row"><label>Mô tả:</label><input type="text" name="mo_ta" id="mo_ta" placeholder="Mô tả chi tiết khoản chi..."></div>
            </div>
            <div class="form-col">
                <div class="col-title">💰 Tài chính</div>
                <div class="field-row"><label>Số tiền:</label><input type="text" name="so_tien" id="so_tien" onkeyup="formatNum(this)" placeholder="0" style="font-weight:700;color:#c62828;font-size:13px;"></div>
                <div class="field-row"><label>Người chi:</label><input type="text" name="nguoi_chi" id="nguoi_chi" placeholder="Tên người chi tiền"></div>
                <div class="field-row"><label>Ghi chú:</label><input type="text" name="ghi_chu" id="ghi_chu"></div>
            </div>
        </div>
        <div class="filter-bar">
            <label>🔍 Lọc:</label>
            <label>Tháng:</label><input type="month" id="f_thang" onchange="filterTable()">
            <label>Loại CP:</label>
            <select id="f_loai" onchange="filterTable()">
                <option value="">-- Tất cả --</option>
                <option>Văn phòng</option><option>Lương nhân viên</option><option>Điện nước</option>
                <option>Internet / Điện thoại</option><option>Marketing / Quảng cáo</option>
                <option>Thuê mặt bằng</option><option>Thuế / Phí</option><option>Khác</option>
            </select>
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
            <div class="summary-box">
                <span>Tổng chi phí chung</span>
                <strong><span id="sum_total">0</span> đ</strong>
            </div>
        </div>
        </form>
        <div class="grid-wrapper">
        <table id="mainTable">
        <thead><tr>
            <th width="40">Stt</th><th width="40">Chọn</th><th width="90">Ngày</th>
            <th width="120">Số CT</th><th width="160">Loại chi phí</th>
            <th>Mô tả</th><th width="120">Số tiền</th>
            <th width="130">Người chi</th><th width="150">Ghi chú</th>
        </tr></thead>
        <tbody id="tableBody">
        <?php
        $loaiBadge = ['Văn phòng'=>'loai-vp','Lương nhân viên'=>'loai-lnv','Điện nước'=>'loai-dn','Internet / Điện thoại'=>'loai-it','Marketing / Quảng cáo'=>'loai-mk','Thuê mặt bằng'=>'loai-mb','Thuế / Phí'=>'loai-thue'];
        $stt=1; while($r=$res->fetch_assoc()):
            $lc = $loaiBadge[$r['loai_chi_phi']] ?? 'loai-kh';
        ?>
        <tr onclick="selectRow(this)"
            data-id="<?=$r['id']?>" data-ngay="<?=$r['ngay_chi']?>"
            data-sct="<?=htmlspecialchars($r['so_chung_tu']??'')?>"
            data-loai="<?=htmlspecialchars($r['loai_chi_phi']??'')?>"
            data-mota="<?=htmlspecialchars($r['mo_ta']??'')?>"
            data-tien="<?=$r['so_tien']?>"
            data-nguoi="<?=htmlspecialchars($r['nguoi_chi']??'')?>"
            data-ghichu="<?=htmlspecialchars($r['ghi_chu']??'')?>">
            <td><?=$stt++?></td>
            <td><input type="radio" name="row_select" value="<?=$r['id']?>" onclick="event.stopPropagation();"></td>
            <td><?=date('d/m/Y',strtotime($r['ngay_chi']))?></td>
            <td><span class="ct-badge"><?=htmlspecialchars($r['so_chung_tu']??'')?></span></td>
            <td><span class="loai-badge <?=$lc?>"><?=htmlspecialchars($r['loai_chi_phi']??'')?></span></td>
            <td class="td-left"><?=htmlspecialchars($r['mo_ta']??'')?></td>
            <td class="money"><?=number_format($r['so_tien'])?></td>
            <td class="td-left"><?=htmlspecialchars($r['nguoi_chi']??'')?></td>
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
function selectRow(row){
    document.querySelectorAll('#mainTable tbody tr').forEach(r=>r.classList.remove('selected'));
    row.classList.add('selected'); selectedRow=row;
    row.querySelector('input[type="radio"]').checked=true;
    const d=row.dataset;
    document.getElementById('ngay_chi').value=d.ngay;
    document.getElementById('so_chung_tu').value=d.sct;
    setSelect('loai_chi_phi',d.loai);
    document.getElementById('mo_ta').value=d.mota;
    document.getElementById('so_tien').value=fmtStr(d.tien);
    document.getElementById('nguoi_chi').value=d.nguoi;
    document.getElementById('ghi_chu').value=d.ghichu;
    document.getElementById('form_edit_id').value=d.id;
}
function setSelect(id,val){
  const s=document.getElementById(id);
  for(let i=0;i<s.options.length;i++)
    if(s.options[i].value==val){
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
  if(!selectedRow){alert('Chọn dòng để sửa!');
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
  if(confirm('Xác nhận xóa?'))window.location.href='xoa_chi_phi_chung.php?id='+r.value;
}
function clearForm(){
  document.getElementById('mainForm').reset();
  document.getElementById('ngay_chi').value=new Date().toISOString().split('T')[0];
  document.getElementById('form_edit_id').value='';
  document.getElementById('form_action').value='add';
  document.querySelectorAll('#mainTable tbody tr').forEach(r=>r.classList.remove('selected'));
  selectedRow=null;
}
function filterTable(){
    const ft=document.getElementById('f_thang').value;
    const fl=document.getElementById('f_loai').value.toUpperCase();
    let total=0;
    document.querySelectorAll('#tableBody tr').forEach(row=>{
        const cells=row.getElementsByTagName('td');let show=true;
        if(ft){
          const p=cells[2]?.innerText.trim().split('/');
          if(p&&p.length===3){
            const ym=p[2]+'-'+p[1];
            if(ym!==ft)show=false;
          }
        }
        if(fl&&!cells[4]?.innerText.toUpperCase().includes(fl))show=false;
        row.style.display=show?'':'none';
        if(show)total+=parseNum(cells[6]?.innerText||'0');
    });
    document.getElementById('sum_total').innerText=fmtStr(total);
}
function resetFilter(){
  document.getElementById('f_thang').value='';
  document.getElementById('f_loai').value='';filterTable();
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