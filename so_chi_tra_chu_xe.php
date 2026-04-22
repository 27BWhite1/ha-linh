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
    `ngay_tra` date DEFAULT NULL,
    `ghi_chu` text DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");
$conn->query("ALTER TABLE so_chi_tra_chu_xe ADD COLUMN IF NOT EXISTS `so_chung_tu` varchar(50) DEFAULT NULL");

$res = $conn->query("SELECT * FROM so_chi_tra_chu_xe ORDER BY nam DESC, thang DESC, id DESC");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Số chi trả chủ xe - Hà Linh</title>
<style>
*{box-sizing:border-box;}
body{font-family:Tahoma,sans-serif;font-size:12px;background:#f0f0f0;margin:0;}
.main-container{margin:8px;border:2px solid #999;background:#fff;padding:10px;}
.title{color:#be0000;text-align:center;font-weight:bold;font-size:16px;margin:0 0 10px;border-bottom:2px double #be0000;padding-bottom:5px;}
.form-panel{display:grid;grid-template-columns:1fr 1fr;gap:10px;border:1px solid #ccc;padding:10px;margin-bottom:8px;background:#fafafa;}
.field-row{display:flex;align-items:center;margin-bottom:6px;}
.field-row label{width:130px;flex-shrink:0;font-weight:bold;font-size:11px;}
.field-row input,.field-row select{flex:1;border:1px solid #7f9db9;padding:3px 5px;height:22px;font-size:11px;font-family:Tahoma,sans-serif;}
.toolbar{background:#e1e1e1;border:1px solid #bbb;padding:5px;margin-bottom:8px;display:flex;gap:5px;align-items:center;flex-wrap:wrap;}
.btn{border:1px solid #888;padding:4px 14px;cursor:pointer;font-weight:bold;font-size:12px;display:flex;align-items:center;gap:4px;background:#eee;}
.btn:hover{background:#d0d0d0;}
.summary-group{margin-left:auto;display:flex;gap:10px;}
.sbox{background:#fff3e0;border:1px solid #ffb74d;padding:3px 10px;text-align:center;font-size:11px;}
.sbox span{display:block;font-weight:bold;font-size:13px;color:#e65100;}
.sbox.green{background:#e8f5e9;border-color:#a5d6a7;} .sbox.green span{color:#1b5e20;}
.sbox.red{background:#ffebee;border-color:#ef9a9a;} .sbox.red span{color:#c62828;}
.filter-bar{background:#f5f5f5;border:1px solid #ddd;padding:5px 10px;margin-bottom:5px;display:flex;gap:10px;align-items:center;flex-wrap:wrap;}
.filter-bar label{font-weight:bold;font-size:11px;}
.filter-bar input,.filter-bar select{border:1px solid #ccc;padding:2px 5px;height:22px;font-size:11px;}
.grid-wrapper{height:320px;overflow:auto;border:1px solid #999;}
table{width:100%;border-collapse:collapse;min-width:900px;}
th{background:#5d4037;color:#fff;border:1px solid #795548;padding:5px;position:sticky;top:0;font-weight:normal;font-size:11px;}
td{border:1px solid #ccc;padding:3px 6px;font-size:11px;}
tr:hover{background:#fff8e1;cursor:pointer;}
tr.selected{background:#5d4037!important;color:#fff;}
tr.selected td{color:#fff;}
.col-money{text-align:right;font-weight:bold;}
.col-center{text-align:center;}
.chua-tra{color:#c62828;font-weight:bold;}
.da-tra{color:#1b5e20;font-weight:bold;}
</style>
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="main-container">
<div class="title">🏢 SỐ CHI TRẢ CHỦ XE</div>
<form id="mainForm" method="POST" action="save_chi_chu_xe.php">
<input type="hidden" name="action" id="form_action" value="add">
<input type="hidden" name="edit_id" id="form_edit_id" value="">
<div class="form-panel">
  <div>
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
    <div class="field-row"><label>Ngày trả:</label><input type="date" name="ngay_tra" id="ngay_tra"></div>
  </div>
  <div>
    <div class="field-row"><label>Số tiền phải trả:</label><input type="text" name="so_tien_phai_tra" id="so_tien_phai_tra" onkeyup="formatNum(this);tinhCon();" placeholder="0" style="font-weight:bold;color:#c62828;font-size:13px;"></div>
    <div class="field-row"><label>Số tiền đã trả:</label><input type="text" name="so_tien_da_tra" id="so_tien_da_tra" onkeyup="formatNum(this);tinhCon();" placeholder="0" style="font-weight:bold;color:#1b5e20;font-size:13px;"></div>
    <div class="field-row"><label>Còn nợ:</label><input type="text" id="con_no_display" readonly style="flex:1;border:1px solid #ef9a9a;background:#ffebee;color:#c62828;font-weight:bold;font-size:13px;padding:3px 5px;"></div>
    <div class="field-row"><label>Ghi chú:</label><input type="text" name="ghi_chu" id="ghi_chu"></div>
  </div>
</div>
<div class="filter-bar">
  <label>🔍 Lọc:</label>
  <label>Chủ xe:</label><input type="text" id="f_chu" oninput="filterTable()" placeholder="Tên chủ xe..." style="width:150px;">
  <label>Số CT:</label><input type="text" id="f_sct" oninput="filterTable()" placeholder="Số chứng từ..." style="width:130px;">
  <label><input type="checkbox" id="f_chuatra" onchange="filterTable()"> Chỉ còn nợ</label>
  <button type="button" onclick="resetFilter()" class="btn" style="padding:2px 8px;font-size:11px;">↩ Bỏ lọc</button>
</div>
<div class="toolbar">
  <button type="button" class="btn" style="color:green" onclick="submitAdd()">✚ Thêm (Alt+A)</button>
  <button type="button" class="btn" style="color:blue"  onclick="submitEdit()">💾 Sửa (Alt+E)</button>
  <button type="button" class="btn" style="color:red"   onclick="deleteRow()">✖ Xóa (Alt+D)</button>
  <button type="button" class="btn"                     onclick="clearForm()">🔄 Làm mới</button>
  <button type="button" class="btn"                     onclick="window.location.href='index.php'">🚪 Thoát</button>
  <div class="summary-group">
    <div class="sbox">Phải trả<span id="sum_phai">0</span>đ</div>
    <div class="sbox green">Đã trả<span id="sum_da">0</span>đ</div>
    <div class="sbox red">Còn nợ<span id="sum_con">0</span>đ</div>
  </div>
</div>
</form>
<div class="grid-wrapper">
<table id="mainTable">
<thead><tr>
  <th width="35">Stt</th><th width="35">Chọn</th>
  <th width="150">Chủ xe</th><th width="110">Số CT</th>
  <th width="200">Diễn giải</th>
  <th width="90">Ngày trả</th><th width="120">Phải trả</th>
  <th width="120">Đã trả</th><th width="110">Còn nợ</th><th>Ghi chú</th>
</tr></thead>
<tbody id="tableBody">
<?php $stt=1; while($r=$res->fetch_assoc()):
  $con=$r['so_tien_phai_tra']-$r['so_tien_da_tra'];
  $cc=$con>0?'chua-tra':'da-tra';
?>
<tr onclick="selectRow(this)"
  data-id="<?=$r['id']?>"
  data-chu="<?=htmlspecialchars($r['chu_xe']??'')?>"
  data-sct="<?=htmlspecialchars($r['so_chung_tu']??'')?>"
  data-dg="<?=htmlspecialchars($r['dien_giai']??'')?>"
  data-ngaytra="<?=$r['ngay_tra']??''?>" data-phai="<?=$r['so_tien_phai_tra']?>"
  data-da="<?=$r['so_tien_da_tra']?>" data-ghichu="<?=htmlspecialchars($r['ghi_chu']??'')?>">
  <td class="col-center"><?=$stt++?></td>
  <td class="col-center"><input type="radio" name="row_select" value="<?=$r['id']?>" onclick="event.stopPropagation();"></td>
  <td><?=htmlspecialchars($r['chu_xe']??'')?></td>
  <td class="col-center"><b><?=htmlspecialchars($r['so_chung_tu']??'')?></b></td>
  <td><?=htmlspecialchars($r['dien_giai']??'')?></td>
  <td class="col-center"><?=$r['ngay_tra']?date('d/m/Y',strtotime($r['ngay_tra'])):''?></td>
  <td class="col-money chua-tra"><?=number_format($r['so_tien_phai_tra'])?></td>
  <td class="col-money da-tra"><?=number_format($r['so_tien_da_tra'])?></td>
  <td class="col-money <?=$cc?>"><?=number_format($con)?></td>
  <td><?=htmlspecialchars($r['ghi_chu']??'')?></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>
</div>
<script>
let selectedRow=null;
function tinhCon(){
  const p=parseNum(document.getElementById('so_tien_phai_tra').value),d=parseNum(document.getElementById('so_tien_da_tra').value),el=document.getElementById('con_no_display'),c=p-d;el.value=fmtStr(c);el.style.color=c>0?'#c62828':'#1b5e20';
}
function selectRow(row){
  document.querySelectorAll('#mainTable tbody tr').forEach(r=>r.classList.remove('selected'));
  row.classList.add('selected');
  selectedRow=row;row.querySelector('input[type="radio"]').checked=true;
  const d=row.dataset;
  setSelect('chu_xe',d.chu);
  document.getElementById('so_chung_tu').value=d.sct;
  document.getElementById('dien_giai').value=d.dg;
  document.getElementById('ngay_tra').value=d.ngaytra;
  document.getElementById('so_tien_phai_tra').value=fmtStr(d.phai);
  document.getElementById('so_tien_da_tra').value=fmtStr(d.da);
  document.getElementById('ghi_chu').value=d.ghichu;
  document.getElementById('form_edit_id').value=d.id;tinhCon();
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
    const cells=row.getElementsByTagName('td');
    let show=true;
    if(fc&&!cells[2]?.innerText.toUpperCase().includes(fc))show=false;
    if(fs&&!cells[3]?.innerText.toUpperCase().includes(fs))show=false;
    if(fct&&parseNum(cells[8]?.innerText||'0')<=0)show=false;
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