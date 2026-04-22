<?php
include 'conn.php';
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: dn.php"); exit(); }

$conn->query("CREATE TABLE IF NOT EXISTS `chi_phi_xe` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `so_chung_tu` varchar(50) DEFAULT NULL,
    `ngay` date NOT NULL,
    `bien_so` varchar(20) DEFAULT NULL,
    `loai_chi_phi` varchar(100) DEFAULT NULL,
    `mo_ta` varchar(255) DEFAULT NULL,
    `so_tien` decimal(15,0) DEFAULT 0,
    `nguoi_chi` varchar(100) DEFAULT NULL,
    `ghi_chu` text DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");
$conn->query("ALTER TABLE chi_phi_xe ADD COLUMN IF NOT EXISTS `so_chung_tu` varchar(50) DEFAULT NULL");

$res = $conn->query("SELECT * FROM chi_phi_xe ORDER BY ngay DESC, id DESC");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Chi phí xe - Hà Linh</title>
<style>
*{box-sizing:border-box;}
body{font-family:Tahoma,sans-serif;font-size:12px;background:#f0f0f0;margin:0;padding:0;}
.main-container{margin:8px;border:2px solid #999;background:#fff;padding:10px;}
.title{color:#be0000;text-align:center;font-weight:bold;font-size:16px;margin:0 0 10px;border-bottom:2px double #be0000;padding-bottom:5px;}
.form-panel{display:grid;grid-template-columns:1fr 1fr;gap:10px;border:1px solid #ccc;padding:10px;margin-bottom:8px;background:#fafafa;}
.field-row{display:flex;align-items:center;margin-bottom:6px;}
.field-row label{width:120px;flex-shrink:0;font-weight:bold;font-size:11px;}
.field-row input,.field-row select,.field-row textarea{flex:1;border:1px solid #7f9db9;padding:3px 5px;height:22px;font-size:11px;font-family:Tahoma,sans-serif;}
.field-row textarea{height:40px;resize:none;}
.toolbar{background:#e1e1e1;border:1px solid #bbb;padding:5px;margin-bottom:8px;display:flex;gap:5px;align-items:center;}
.btn{border:1px solid #888;padding:4px 14px;cursor:pointer;font-weight:bold;font-size:12px;display:flex;align-items:center;gap:4px;background:#eee;font-family:Tahoma,sans-serif;}
.btn:hover{background:#d0d0d0;}
.summary{margin-left:auto;background:#fff3e0;border:1px solid #ffb74d;padding:4px 15px;font-weight:bold;font-size:13px;color:#e65100;}
.filter-bar{background:#f5f5f5;border:1px solid #ddd;padding:5px 10px;margin-bottom:5px;display:flex;gap:10px;align-items:center;flex-wrap:wrap;}
.filter-bar label{font-weight:bold;font-size:11px;}
.filter-bar input,.filter-bar select{border:1px solid #ccc;padding:2px 5px;height:22px;font-size:11px;}
.grid-wrapper{height:350px;overflow:auto;border:1px solid #999;}
table{width:100%;border-collapse:collapse;min-width:900px;}
th{background:#000080;color:#fff;border:1px solid #1111aa;padding:5px;position:sticky;top:0;font-weight:normal;font-size:11px;white-space:nowrap;}
td{border:1px solid #ccc;padding:3px 6px;font-size:11px;white-space:nowrap;}
tr:hover{background:#fffde0;cursor:pointer;}
tr.selected{background:#0000cc!important;color:#fff;}
tr.selected td{color:#fff;}
.col-money{text-align:right;font-weight:bold;color:#000080;}
.col-center{text-align:center;}
</style>
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="main-container">
<div class="title">🔧 CHI PHÍ XE</div>

<form id="mainForm" method="POST" action="save_chi_phi_xe.php">
<input type="hidden" name="action" id="form_action" value="add">
<input type="hidden" name="edit_id" id="form_edit_id" value="">
<div class="form-panel">
  <div>
    <div class="field-row"><label>Số chứng từ:</label><input type="text" name="so_chung_tu" id="so_chung_tu" placeholder="VD: CT-001, HĐ-2025/08..."></div>
    <div class="field-row"><label>Ngày:</label><input type="date" name="ngay" id="ngay" value="<?=date('Y-m-d')?>" required></div>
    <div class="field-row">
      <label>Biển số xe:</label>
      <select name="bien_so" id="bien_so">
        <option value="">-- Chọn xe --</option>
        <?php $xl=$conn->query("SELECT bien_so FROM danh_muc_xe ORDER BY bien_so");
        while($x=$xl->fetch_assoc()) echo "<option value='{$x['bien_so']}'>{$x['bien_so']}</option>"; ?>
      </select>
    </div>
    <div class="field-row">
      <label>Loại chi phí:</label>
      <select name="loai_chi_phi" id="loai_chi_phi">
        <option value="Sửa chữa">🔩 Sửa chữa</option>
        <option value="Bảo dưỡng">🛠️ Bảo dưỡng</option>
        <option value="Xăng dầu">⛽ Xăng dầu</option>
        <option value="Phí đường bộ">🛣️ Phí đường bộ</option>
        <option value="Phí cầu phà">🌉 Phí cầu phà</option>
        <option value="Bảo hiểm">📋 Bảo hiểm</option>
        <option value="Đăng kiểm">✅ Đăng kiểm</option>
        <option value="Khác">📌 Khác</option>
      </select>
    </div>
  </div>
  <div>
    <div class="field-row"><label>Số tiền:</label><input type="text" name="so_tien" id="so_tien" onkeyup="formatNum(this)" placeholder="0" style="font-weight:bold;color:#c62828;font-size:13px;"></div>
    <div class="field-row"><label>Người chi:</label><input type="text" name="nguoi_chi" id="nguoi_chi" placeholder="Tên người chi tiền"></div>
    <div class="field-row"><label>Ghi chú:</label><input type="text" name="ghi_chu" id="ghi_chu"></div>
  </div>
</div>

<div class="filter-bar">
  <label>🔍 Lọc:</label>
  <label>Tháng:</label><input type="month" id="f_thang" onchange="filterTable()">
  <label>Biển số:</label><input type="text" id="f_bien" oninput="filterTable()" placeholder="Biển số..." style="width:110px;">
  <label>Loại CP:</label>
  <select id="f_loai" onchange="filterTable()">
    <option value="">-- Tất cả --</option>
    <option>Sửa chữa</option><option>Bảo dưỡng</option><option>Xăng dầu</option>
    <option>Phí đường bộ</option><option>Phí cầu phà</option><option>Bảo hiểm</option>
    <option>Đăng kiểm</option><option>Khác</option>
  </select>
  <button type="button" onclick="resetFilter()" class="btn" style="padding:2px 8px;font-size:11px;">↩ Bỏ lọc</button>
</div>

<div class="toolbar">
  <button type="button" class="btn" style="color:green" onclick="submitAdd()">✚ Thêm (Alt+A)</button>
  <button type="button" class="btn" style="color:blue"  onclick="submitEdit()">💾 Sửa (Alt+E)</button>
  <button type="button" class="btn" style="color:red"   onclick="deleteRow()">✖ Xóa (Alt+D)</button>
  <button type="button" class="btn"                     onclick="clearForm()">🔄 Làm mới</button>
  <button type="button" class="btn"                     onclick="window.location.href='index.php'">🚪 Thoát</button>
  <div class="summary">Tổng chi phí: <span id="sum_total">0</span> đ</div>
</div>
</form>

<div class="grid-wrapper">
<table id="mainTable">
<thead><tr>
  <th width="35">Stt</th><th width="35">Chọn</th><th width="90">Ngày</th>
  <th width="110">Số CT</th>
  <th width="100">Biển số</th><th width="120">Loại chi phí</th>
  <th width="120">Số tiền</th>
  <th width="120">Người chi</th><th>Ghi chú</th>
</tr></thead>
<tbody id="tableBody">
<?php $stt=1; while($r=$res->fetch_assoc()): ?>
<tr onclick="selectRow(this)"
  data-id="<?=$r['id']?>" data-ngay="<?=$r['ngay']?>" data-sct="<?=htmlspecialchars($r['so_chung_tu']??'')?>"
  data-bien="<?=htmlspecialchars($r['bien_so']??'')?>"
  data-loai="<?=htmlspecialchars($r['loai_chi_phi']??'')?>"
  data-tien="<?=$r['so_tien']?>" data-nguoi="<?=htmlspecialchars($r['nguoi_chi']??'')?>"
  data-ghichu="<?=htmlspecialchars($r['ghi_chu']??'')?>">
  <td class="col-center"><?=$stt++?></td>
  <td class="col-center"><input type="radio" name="row_select" value="<?=$r['id']?>" onclick="event.stopPropagation();"></td>
  <td class="col-center"><?=date('d/m/Y',strtotime($r['ngay']))?></td>
  <td class="col-center"><b><?=htmlspecialchars($r['so_chung_tu']??'')?></b></td>
  <td class="col-center"><b><?=htmlspecialchars($r['bien_so']??'')?></b></td>
  <td><?=htmlspecialchars($r['loai_chi_phi']??'')?></td>
  <td class="col-money"><?=number_format($r['so_tien'])?></td>
  <td><?=htmlspecialchars($r['nguoi_chi']??'')?></td>
  <td><?=htmlspecialchars($r['ghi_chu']??'')?></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>
</div>
<script>
let selectedRow=null;
function selectRow(row){
  document.querySelectorAll('#mainTable tbody tr').forEach(r=>r.classList.remove('selected'));
  row.classList.add('selected'); selectedRow=row;
  row.querySelector('input[type="radio"]').checked=true;
  const d=row.dataset;
  document.getElementById('ngay').value=d.ngay;
  document.getElementById('so_chung_tu').value=d.sct;
  setSelect('bien_so',d.bien); setSelect('loai_chi_phi',d.loai);
  document.getElementById('so_tien').value=fmtStr(d.tien);
  document.getElementById('nguoi_chi').value=d.nguoi;
  document.getElementById('ghi_chu').value=d.ghichu;
  document.getElementById('form_edit_id').value=d.id;
}
function setSelect(id,val){
  const s=document.getElementById(id);
  for(let i=0;i<s.options.length;i++)
    if(s.options[i].value==val){
      s.selectedIndex=i;return;
    }
  }
function submitAdd(){
  document.getElementById('form_action').value='add';
  document.getElementById('form_edit_id').value='';
  document.getElementById('mainForm').submit();
}
function submitEdit(){
  if(!selectedRow){
    alert('Vui lòng chọn dòng để sửa!');
    return;
  }
  document.getElementById('form_action').value='edit';
  document.getElementById('mainForm').submit();
}
function deleteRow(){
  const r=document.querySelector('input[name="row_select"]:checked');
  if(!r){
    alert('Vui lòng chọn dòng để xóa!');
    return;
  }
  if(confirm('Xác nhận xóa?'))window.location.href='xoa_chi_phi_xe.php?id='+r.value;
}
function clearForm(){
  document.getElementById('mainForm').reset();
  document.getElementById('ngay').value=new Date().toISOString().split('T')[0];
  document.getElementById('form_edit_id').value='';
  document.getElementById('form_action').value='add';
  document.querySelectorAll('#mainTable tbody tr').forEach(r=>r.classList.remove('selected'));
  selectedRow=null;
}
function filterTable(){
  const ft=document.getElementById('f_thang').value;
  const fb=document.getElementById('f_bien').value.toUpperCase();
  const fl=document.getElementById('f_loai').value.toUpperCase();
  let total=0;
  document.querySelectorAll('#tableBody tr').forEach(row=>{
    const cells=row.getElementsByTagName('td');
    let show=true;
    if(ft){const p=cells[2]?.innerText.trim().split('/');if(p&&p.length===3){const ym=p[2]+'-'+p[1];if(ym!==ft)show=false;}}
    if(fb&&!cells[4]?.innerText.toUpperCase().includes(fb))show=false;
    if(fl&&!cells[5]?.innerText.toUpperCase().includes(fl))show=false;
    row.style.display=show?'':'none';
    if(show)total+=parseNum(cells[6]?.innerText||'0');
  });
  document.getElementById('sum_total').innerText=fmtStr(total);
}
function resetFilter(){
  ['f_thang','f_bien'].forEach(id=>document.getElementById(id).value='');
  document.getElementById('f_loai').value='';
  filterTable();
}
function parseNum(s){
  return parseInt(String(s).replace(/\./g,'').replace(/,/g,''))||0;
}
function fmtStr(n){
  return Math.round(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g,'.');
}
function formatNum(i){
  let v=i.value.replace(/\D/g,'');
  i.value=v.replace(/\B(?=(\d{3})+(?!\d))/g,'.');
}
document.addEventListener('keydown',e=>{
  if(e.altKey){
    if(e.key.toLowerCase()==='a'){
      e.preventDefault();
      submitAdd();
    }
    if(e.key.toLowerCase()==='e'){
      e.preventDefault();
      submitEdit();
    }
    if(e.key.toLowerCase()==='d'){
      e.preventDefault();
      deleteRow();
    }
  }
});
window.addEventListener('load',()=>filterTable());
</script>
</body>
</html>