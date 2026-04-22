<?php
include 'conn.php';
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: dn.php"); exit(); }

$conn->query("CREATE TABLE IF NOT EXISTS `so_thu_khach_hang` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `ngay_thu` date DEFAULT NULL,
    `khach_hang` varchar(255) DEFAULT NULL,
    `loai_xe_chay` varchar(50) DEFAULT 'Công nhân',
    `ma_chung_tu` varchar(50) DEFAULT NULL,
    `dien_giai` varchar(255) DEFAULT NULL,
    `nguoi_tra` varchar(100) DEFAULT NULL,
    `so_tien_phai_thu` decimal(15,0) DEFAULT 0,
    `so_tien_da_thu` decimal(15,0) DEFAULT 0,
    `ghi_chu` text DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");
$conn->query("ALTER TABLE so_thu_khach_hang ADD COLUMN IF NOT EXISTS `nguoi_tra` varchar(100) DEFAULT NULL");

$res = $conn->query("SELECT * FROM so_thu_khach_hang ORDER BY ngay_thu DESC, id DESC");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Số thu tiền khách hàng - Hà Linh</title>
<style>
*{box-sizing:border-box;}
body{font-family:Tahoma,sans-serif;font-size:12px;background:#f0f0f0;margin:0;padding:0;}
.main-container{margin:8px;border:2px solid #999;background:#fff;padding:10px;}
.title{color:#be0000;text-align:center;font-weight:bold;font-size:16px;margin:0 0 10px;border-bottom:2px double #be0000;padding-bottom:5px;}
.form-panel{display:grid;grid-template-columns:1fr 1fr;gap:10px;border:1px solid #ccc;padding:10px;margin-bottom:8px;background:#fafafa;}
.field-row{display:flex;align-items:center;margin-bottom:6px;}
.field-row label{width:130px;flex-shrink:0;font-weight:bold;font-size:11px;}
.field-row input,.field-row select{flex:1;border:1px solid #7f9db9;padding:3px 5px;height:22px;font-size:11px;font-family:Tahoma,sans-serif;}
.field-row .con-lai{flex:1;border:1px solid #a5d6a7;padding:3px 5px;height:22px;font-size:13px;font-weight:bold;background:#e8f5e9;color:#1b5e20;}
.toolbar{background:#e1e1e1;border:1px solid #bbb;padding:5px;margin-bottom:8px;display:flex;gap:5px;align-items:center;flex-wrap:wrap;}
.btn{border:1px solid #888;padding:4px 14px;cursor:pointer;font-weight:bold;font-size:12px;display:flex;align-items:center;gap:4px;background:#eee;font-family:Tahoma,sans-serif;}
.btn:hover{background:#d0d0d0;}
.summary-group{margin-left:auto;display:flex;gap:12px;}
.summary-box{background:#fff3e0;border:1px solid #ffb74d;padding:3px 12px;text-align:center;font-size:11px;}
.summary-box span{display:block;font-weight:bold;font-size:13px;color:#e65100;}
.summary-box.green{background:#e8f5e9;border-color:#a5d6a7;}
.summary-box.green span{color:#1b5e20;}
.summary-box.red{background:#ffebee;border-color:#ef9a9a;}
.summary-box.red span{color:#c62828;}
.filter-bar{background:#f5f5f5;border:1px solid #ddd;padding:5px 10px;margin-bottom:5px;display:flex;gap:10px;align-items:center;flex-wrap:wrap;}
.filter-bar label{font-weight:bold;font-size:11px;}
.filter-bar input,.filter-bar select{border:1px solid #ccc;padding:2px 5px;height:22px;font-size:11px;}
.grid-wrapper{height:320px;overflow:auto;border:1px solid #999;}
table{width:100%;border-collapse:collapse;min-width:1000px;}
th{background:#006400;color:#fff;border:1px solid #228b22;padding:5px;position:sticky;top:0;font-weight:normal;font-size:11px;white-space:nowrap;}
td{border:1px solid #ccc;padding:3px 6px;font-size:11px;}
tr:hover{background:#f1fff1;cursor:pointer;}
tr.selected{background:#006400!important;color:#fff;}
tr.selected td{color:#fff;}
.col-money{text-align:right;font-weight:bold;}
.col-center{text-align:center;}
.chua-thu{color:#c62828;font-weight:bold;}
.da-thu{color:#1b5e20;font-weight:bold;}
</style>
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="main-container">
<div class="title">💰 SỐ THU TIỀN KHÁCH HÀNG</div>

<form id="mainForm" method="POST" action="save_so_thu.php">
<input type="hidden" name="action" id="form_action" value="add">
<input type="hidden" name="edit_id" id="form_edit_id" value="">
<div class="form-panel">
  <div>
    <div class="field-row"><label>Ngày thu:</label><input type="date" name="ngay_thu" id="ngay_thu" value="<?=date('Y-m-d')?>"></div>
    <div class="field-row">
      <label>Khách hàng:</label>
      <select name="khach_hang" id="khach_hang">
        <option value="">-- Chọn khách hàng --</option>
        <?php $kl=$conn->query("SELECT ten_khachhang FROM danh_muc_khach_hang ORDER BY ten_khachhang");
        while($k=$kl->fetch_assoc()) echo "<option value='{$k['ten_khachhang']}'>{$k['ten_khachhang']}</option>"; ?>
      </select>
    </div>
    <div class="field-row"><label>Mã chứng từ:</label><input type="text" name="ma_chung_tu" id="ma_chung_tu" placeholder="VD: HD001, TT202504..."></div>
    <div class="field-row"><label>Diễn giải:</label><input type="text" name="dien_giai" id="dien_giai" placeholder="Mô tả khoản thu..."></div>
    <div class="field-row"><label>Người trả:</label><input type="text" name="nguoi_tra" id="nguoi_tra" placeholder="Tên người trả tiền..."></div>
  </div>
  <div>
    <div class="field-row">
      <label>Số tiền phải thu:</label>
      <input type="text" name="so_tien_phai_thu" id="so_tien_phai_thu" onkeyup="formatNum(this);tinhConLai();" placeholder="0" style="font-weight:bold;color:#c62828;font-size:13px;">
    </div>
    <div class="field-row">
      <label>Số tiền đã thu:</label>
      <input type="text" name="so_tien_da_thu" id="so_tien_da_thu" onkeyup="formatNum(this);tinhConLai();" placeholder="0" style="font-weight:bold;color:#1b5e20;font-size:13px;">
    </div>
    <div class="field-row">
      <label>Còn lại (chưa thu):</label>
      <input type="text" id="con_lai_display" class="con-lai" readonly>
    </div>
    <div class="field-row"><label>Ghi chú:</label><input type="text" name="ghi_chu" id="ghi_chu"></div>
  </div>
</div>

<div class="filter-bar">
  <label>🔍 Lọc:</label>
  <label>Tháng:</label><input type="month" id="f_thang" onchange="filterTable()">
  <label>Khách hàng:</label><input type="text" id="f_kh" oninput="filterTable()" placeholder="Tên KH..." style="width:150px;">
  <label><input type="checkbox" id="f_chuathu" onchange="filterTable()"> Chỉ chưa thu đủ</label>
  <button type="button" onclick="resetFilter()" class="btn" style="padding:2px 8px;font-size:11px;">↩ Bỏ lọc</button>
</div>

<div class="toolbar">
  <button type="button" class="btn" style="color:green" onclick="submitAdd()">✚ Thêm (Alt+A)</button>
  <button type="button" class="btn" style="color:blue"  onclick="submitEdit()">💾 Sửa (Alt+E)</button>
  <button type="button" class="btn" style="color:red"   onclick="deleteRow()">✖ Xóa (Alt+D)</button>
  <button type="button" class="btn"                     onclick="clearForm()">🔄 Làm mới</button>
  <button type="button" class="btn"                     onclick="window.location.href='index.php'">🚪 Thoát</button>
  <div class="summary-group">
    <div class="summary-box">Phải thu<span id="sum_phai">0</span>đ</div>
    <div class="summary-box green">Đã thu<span id="sum_da">0</span>đ</div>
    <div class="summary-box red">Còn lại<span id="sum_con">0</span>đ</div>
  </div>
</div>
</form>

<div class="grid-wrapper">
<table id="mainTable">
<thead><tr>
  <th width="35">Stt</th><th width="35">Chọn</th><th width="90">Ngày thu</th>
  <th width="150">Khách hàng</th>
  <th width="100">Mã CT</th><th width="180">Diễn giải</th>
  <th width="120">Người trả</th>
  <th width="120">Phải thu</th><th width="120">Đã thu</th><th width="110">Còn lại</th><th>Ghi chú</th>
</tr></thead>
<tbody id="tableBody">
<?php $stt=1; while($r=$res->fetch_assoc()):
  $con_lai = $r['so_tien_phai_thu'] - $r['so_tien_da_thu'];
  $con_lai_class = $con_lai > 0 ? 'chua-thu' : 'da-thu';
?>
<tr onclick="selectRow(this)"
  data-id="<?=$r['id']?>" data-ngay="<?=$r['ngay_thu']?>"
  data-kh="<?=htmlspecialchars($r['khach_hang']??'')?>"
  data-mact="<?=htmlspecialchars($r['ma_chung_tu']??'')?>"
  data-dg="<?=htmlspecialchars($r['dien_giai']??'')?>"
  data-nguoitra="<?=htmlspecialchars($r['nguoi_tra']??'')?>"
  data-phai="<?=$r['so_tien_phai_thu']?>" data-da="<?=$r['so_tien_da_thu']?>"
  data-ghichu="<?=htmlspecialchars($r['ghi_chu']??'')?>">
  <td class="col-center"><?=$stt++?></td>
  <td class="col-center"><input type="radio" name="row_select" value="<?=$r['id']?>" onclick="event.stopPropagation();"></td>
  <td class="col-center"><?=date('d/m/Y',strtotime($r['ngay_thu']))?></td>
  <td><?=htmlspecialchars($r['khach_hang']??'')?></td>
  <td class="col-center"><?=htmlspecialchars($r['ma_chung_tu']??'')?></td>
  <td><?=htmlspecialchars($r['dien_giai']??'')?></td>
  <td><?=htmlspecialchars($r['nguoi_tra']??'')?></td>
  <td class="col-money chua-thu"><?=number_format($r['so_tien_phai_thu'])?></td>
  <td class="col-money da-thu"><?=number_format($r['so_tien_da_thu'])?></td>
  <td class="col-money <?=$con_lai_class?>"><?=number_format($con_lai)?></td>
  <td><?=htmlspecialchars($r['ghi_chu']??'')?></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>
</div>
<script>
let selectedRow=null;
function tinhConLai(){
  const p=parseNum(document.getElementById('so_tien_phai_thu').value);
  const d=parseNum(document.getElementById('so_tien_da_thu').value);
  const el=document.getElementById('con_lai_display');
  const cl=p-d; el.value=fmtStr(cl);
  el.style.color=cl>0?'#c62828':'#1b5e20';
}
function selectRow(row){
  document.querySelectorAll('#mainTable tbody tr').forEach(r=>r.classList.remove('selected'));
  row.classList.add('selected'); selectedRow=row;
  row.querySelector('input[type="radio"]').checked=true;
  const d=row.dataset;
  document.getElementById('ngay_thu').value=d.ngay;
  setSelect('khach_hang',d.kh);
  document.getElementById('ma_chung_tu').value=d.mact;
  document.getElementById('dien_giai').value=d.dg;
  document.getElementById('nguoi_tra').value=d.nguoitra;
  document.getElementById('so_tien_phai_thu').value=fmtStr(d.phai);
  document.getElementById('so_tien_da_thu').value=fmtStr(d.da);
  document.getElementById('ghi_chu').value=d.ghichu;
  document.getElementById('form_edit_id').value=d.id;
  tinhConLai();
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
    alert('Chọn dòng để xóa!');
    return;
  }
  if(confirm('Xác nhận xóa?'))window.location.href='xoa_so_thu.php?id='+r.value;
}
function clearForm(){
  document.getElementById('mainForm').reset();
  document.getElementById('ngay_thu').value=new Date().toISOString().split('T')[0];
  document.getElementById('form_edit_id').value='';
  document.getElementById('form_action').value='add';
  document.getElementById('con_lai_display').value='';
  document.querySelectorAll('#mainTable tbody tr').forEach(r=>r.classList.remove('selected'));
  selectedRow=null;
}
function filterTable(){
  const ft=document.getElementById('f_thang').value;
  const fk=document.getElementById('f_kh').value.toUpperCase();
  const fct=document.getElementById('f_chuathu').checked;
  let sp=0,sd=0;
  document.querySelectorAll('#tableBody tr').forEach(row=>{
    const cells=row.getElementsByTagName('td');
    let show=true;
    if(ft){const p=cells[2]?.innerText.trim().split('/');if(p&&p.length===3){const ym=p[2]+'-'+p[1];if(ym!==ft)show=false;}}
    if(fk&&!cells[3]?.innerText.toUpperCase().includes(fk))show=false;
    if(fct&&parseNum(cells[9]?.innerText||'0')<=0)show=false;
    row.style.display=show?'':'none';
    if(show){sp+=parseNum(cells[7]?.innerText||'0');sd+=parseNum(cells[8]?.innerText||'0');}
  });
  document.getElementById('sum_phai').innerText=fmtStr(sp);
  document.getElementById('sum_da').innerText=fmtStr(sd);
  document.getElementById('sum_con').innerText=fmtStr(sp-sd);
}
function resetFilter(){
  ['f_thang','f_kh'].forEach(id=>document.getElementById(id).value='');
  document.getElementById('f_chuathu').checked=false;filterTable();
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