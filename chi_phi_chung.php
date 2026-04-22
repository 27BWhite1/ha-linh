<?php
include 'conn.php';
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: dn.php"); exit(); }
$conn->query("CREATE TABLE IF NOT EXISTS `chi_phi_chung` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `ngay` date NOT NULL, `thang` int(2) DEFAULT NULL, `nam` int(4) DEFAULT NULL,
    `loai_chi_phi` varchar(100) DEFAULT NULL, `mo_ta` varchar(255) DEFAULT NULL,
    `so_tien` decimal(15,0) DEFAULT 0, `nguoi_chi` varchar(100) DEFAULT NULL,
    `ghi_chu` text DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");
$res = $conn->query("SELECT * FROM chi_phi_chung ORDER BY ngay DESC, id DESC");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Chi phí chung - Hà Linh</title>
<style>
*{box-sizing:border-box;}
body{font-family:Tahoma,sans-serif;font-size:12px;background:#f0f0f0;margin:0;}
.main-container{margin:8px;border:2px solid #999;background:#fff;padding:10px;}
.title{color:#be0000;text-align:center;font-weight:bold;font-size:16px;margin:0 0 10px;border-bottom:2px double #be0000;padding-bottom:5px;}
.form-panel{display:grid;grid-template-columns:1fr 1fr;gap:10px;border:1px solid #ccc;padding:10px;margin-bottom:8px;background:#fafafa;}
.field-row{display:flex;align-items:center;margin-bottom:6px;}
.field-row label{width:120px;flex-shrink:0;font-weight:bold;font-size:11px;}
.field-row input,.field-row select{flex:1;border:1px solid #7f9db9;padding:3px 5px;height:22px;font-size:11px;font-family:Tahoma,sans-serif;}
.toolbar{background:#e1e1e1;border:1px solid #bbb;padding:5px;margin-bottom:8px;display:flex;gap:5px;align-items:center;flex-wrap:wrap;}
.btn{border:1px solid #888;padding:4px 14px;cursor:pointer;font-weight:bold;font-size:12px;display:flex;align-items:center;gap:4px;background:#eee;}
.btn:hover{background:#d0d0d0;}
.summary{margin-left:auto;background:#fff3e0;border:1px solid #ffb74d;padding:4px 15px;font-weight:bold;font-size:13px;color:#e65100;}
.filter-bar{background:#f5f5f5;border:1px solid #ddd;padding:5px 10px;margin-bottom:5px;display:flex;gap:10px;align-items:center;flex-wrap:wrap;}
.filter-bar label{font-weight:bold;font-size:11px;}
.filter-bar input,.filter-bar select{border:1px solid #ccc;padding:2px 5px;height:22px;font-size:11px;}
.grid-wrapper{height:320px;overflow:auto;border:1px solid #999;}
table{width:100%;border-collapse:collapse;min-width:850px;}
th{background:#4a148c;color:#fff;border:1px solid #6a1b9a;padding:5px;position:sticky;top:0;font-weight:normal;font-size:11px;}
td{border:1px solid #ccc;padding:3px 6px;font-size:11px;}
tr:hover{background:#f3e5f5;cursor:pointer;}
tr.selected{background:#4a148c!important;color:#fff;}
tr.selected td{color:#fff;}
.col-money{text-align:right;font-weight:bold;color:#4a148c;}
.col-center{text-align:center;}
</style>
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="main-container">
<div class="title">📊 CHI PHÍ CHUNG</div>
<form id="mainForm" method="POST" action="save_chi_phi_chung.php">
<input type="hidden" name="action" id="form_action" value="add">
<input type="hidden" name="edit_id" id="form_edit_id" value="">
<div class="form-panel">
  <div>
    <div class="field-row"><label>Ngày:</label><input type="date" name="ngay" id="ngay" value="<?=date('Y-m-d')?>"></div>
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
    <div class="field-row"><label>Người chi:</label><input type="text" name="nguoi_chi" id="nguoi_chi"></div>
  </div>
  <div>
    <div class="field-row"><label>Số tiền:</label><input type="text" name="so_tien" id="so_tien" onkeyup="formatNum(this)" placeholder="0" style="font-weight:bold;color:#4a148c;font-size:14px;"></div>
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
  <button type="button" onclick="resetFilter()" class="btn" style="padding:2px 8px;font-size:11px;">↩ Bỏ lọc</button>
</div>
<div class="toolbar">
  <button type="button" class="btn" style="color:green" onclick="submitAdd()">✚ Thêm (Alt+A)</button>
  <button type="button" class="btn" style="color:blue"  onclick="submitEdit()">💾 Sửa (Alt+E)</button>
  <button type="button" class="btn" style="color:red"   onclick="deleteRow()">✖ Xóa (Alt+D)</button>
  <button type="button" class="btn"                     onclick="clearForm()">🔄 Làm mới</button>
  <button type="button" class="btn"                     onclick="window.location.href='index.php'">🚪 Thoát</button>
  <div class="summary">Tổng chi phí chung: <span id="sum_total">0</span> đ</div>
</div>
</form>
<div class="grid-wrapper">
<table id="mainTable">
<thead><tr>
  <th width="35">Stt</th><th width="35">Chọn</th><th width="90">Ngày</th>
  <th width="160">Loại chi phí</th><th width="220">Mô tả</th>
  <th width="120">Số tiền</th><th width="120">Người chi</th><th>Ghi chú</th>
</tr></thead>
<tbody id="tableBody">
<?php $stt=1; while($r=$res->fetch_assoc()): ?>
<tr onclick="selectRow(this)"
  data-id="<?=$r['id']?>" data-ngay="<?=$r['ngay']?>"
  data-loai="<?=htmlspecialchars($r['loai_chi_phi']??'')?>"
  data-mota="<?=htmlspecialchars($r['mo_ta']??'')?>"
  data-tien="<?=$r['so_tien']?>" data-nguoi="<?=htmlspecialchars($r['nguoi_chi']??'')?>"
  data-ghichu="<?=htmlspecialchars($r['ghi_chu']??'')?>">
  <td class="col-center"><?=$stt++?></td>
  <td class="col-center"><input type="radio" name="row_select" value="<?=$r['id']?>" onclick="event.stopPropagation();"></td>
  <td class="col-center"><?=date('d/m/Y',strtotime($r['ngay']))?></td>
  <td><?=htmlspecialchars($r['loai_chi_phi']??'')?></td>
  <td><?=htmlspecialchars($r['mo_ta']??'')?></td>
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
  row.classList.add('selected');
  selectedRow=row;
  row.querySelector('input[type="radio"]').checked=true;
  const d=row.dataset;document.getElementById('ngay').value=d.ngay;setSelect('loai_chi_phi',d.loai);
  document.getElementById('mo_ta').value=d.mota;document.getElementById('so_tien').value=fmtStr(d.tien);
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
  if(confirm('Xác nhận xóa?'))window.location.href='xoa_chi_phi_chung.php?id='+r.value;
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
  const fl=document.getElementById('f_loai').value.toUpperCase();
  let total=0;
  document.querySelectorAll('#tableBody tr').forEach(row=>{
    const cells=row.getElementsByTagName('td');
    let show=true;
    if(ft){const p=cells[2]?.innerText.trim().split('/');
    if(p&&p.length===3){
      const ym=p[2]+'-'+p[1];
      if(ym!==ft)show=false;
    }
  }
    if(fl&&!cells[3]?.innerText.toUpperCase().includes(fl))show=false;
    row.style.display=show?'':'none';
    if(show)total+=parseNum(cells[5]?.innerText||'0');
  });
  document.getElementById('sum_total').innerText=fmtStr(total);
}
function resetFilter(){
  ['f_thang'].forEach(id=>document.getElementById(id).value='');
  document.getElementById('f_loai').value='';filterTable();
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