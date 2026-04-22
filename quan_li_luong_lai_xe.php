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
<title>Quản lý lương lái xe - Hà Linh</title>
<style>
*{box-sizing:border-box;}
body{font-family:Tahoma,sans-serif;font-size:12px;background:#f0f0f0;margin:0;}
.main-container{margin:8px;border:2px solid #999;background:#fff;padding:10px;}
.title{color:#be0000;text-align:center;font-weight:bold;font-size:16px;margin:0 0 10px;border-bottom:2px double #be0000;padding-bottom:5px;}
.period-bar{display:flex;align-items:center;gap:10px;background:#e3f2fd;border:1px solid #90caf9;padding:8px 12px;margin-bottom:8px;}
.period-bar label{font-weight:bold;font-size:12px;color:#1565c0;}
.period-bar select,.period-bar input{border:1px solid #90caf9;padding:3px 6px;height:26px;font-size:12px;}
.btn-period{background:#1565c0;color:#fff;border:none;padding:5px 15px;cursor:pointer;font-weight:bold;font-size:12px;}
.btn-period:hover{background:#0d47a1;}
.btn-gen{background:#2e7d32;color:#fff;border:none;padding:5px 15px;cursor:pointer;font-weight:bold;font-size:12px;}
.btn-gen:hover{background:#1b5e20;}
.form-panel{display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;border:1px solid #ccc;padding:10px;margin-bottom:8px;background:#fafafa;}
.form-col-title{font-weight:bold;color:#1565c0;background:#e3f2fd;padding:3px 6px;margin:-10px -10px 8px;font-size:11px;border-bottom:1px solid #ccc;}
.field-row{display:flex;align-items:center;margin-bottom:5px;}
.field-row label{width:130px;flex-shrink:0;font-weight:bold;font-size:11px;}
.field-row input,.field-row select{flex:1;border:1px solid #7f9db9;padding:3px 5px;height:22px;font-size:11px;font-family:Tahoma,sans-serif;}
.field-row input[readonly]{background:#f0f0f0;font-weight:bold;color:#1565c0;}
.toolbar{background:#e1e1e1;border:1px solid #bbb;padding:5px;margin-bottom:8px;display:flex;gap:5px;align-items:center;flex-wrap:wrap;}
.btn{border:1px solid #888;padding:4px 14px;cursor:pointer;font-weight:bold;font-size:12px;display:flex;align-items:center;gap:4px;background:#eee;}
.btn:hover{background:#d0d0d0;}
.summary-group{margin-left:auto;display:flex;gap:10px;}
.sbox{padding:3px 12px;text-align:center;font-size:11px;border:1px solid #90caf9;background:#e3f2fd;}
.sbox span{display:block;font-weight:bold;font-size:13px;color:#1565c0;}
.sbox.green{background:#e8f5e9;border-color:#a5d6a7;} .sbox.green span{color:#1b5e20;}
.grid-wrapper{height:330px;overflow:auto;border:1px solid #999;}
table{width:100%;border-collapse:collapse;min-width:1100px;}
th{background:#1565c0;color:#fff;border:1px solid #1976d2;padding:5px;position:sticky;top:0;font-weight:normal;font-size:11px;white-space:nowrap;}
td{border:1px solid #ccc;padding:3px 6px;font-size:11px;}
tr:hover{background:#e3f2fd;cursor:pointer;}
tr.selected{background:#1565c0!important;color:#fff;}
tr.selected td{color:#fff;}
.col-money{text-align:right;font-weight:bold;color:#1565c0;}
.col-center{text-align:center;}
.paid{color:#1b5e20;font-weight:bold;}
.unpaid{color:#c62828;font-weight:bold;}
</style>
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="main-container">
<div class="title">👨‍✈️ QUẢN LÝ LƯƠNG LÁI XE</div>
<div class="period-bar">
  <label>📅 Kỳ lương:</label>
  <form method="GET" style="display:flex;gap:8px;align-items:center;">
    <select name="thang">
      <?php for($m=1;$m<=12;$m++) echo "<option value='$m' ".($m==$thang_loc?'selected':'').">Tháng $m</option>"; ?>
    </select>
    <input type="number" name="nam" value="<?=$nam_loc?>" style="width:75px;">
    <button type="submit" class="btn-period">📋 Xem</button>
  </form>
  <form method="POST" action="gen_luong.php" style="display:flex;gap:8px;align-items:center;">
    <input type="hidden" name="thang" value="<?=$thang_loc?>">
    <input type="hidden" name="nam" value="<?=$nam_loc?>">
    <button type="submit" class="btn-gen" onclick="return confirm('Tạo bảng lương tháng <?=$thang_loc?>/<?=$nam_loc?>?\n(Sẽ tổng hợp lương từ các chuyến đã chạy)')">
      ⚡ Tạo bảng lương T<?=$thang_loc?>/<?=$nam_loc?>
    </button>
  </form>
</div>

<form id="mainForm" method="POST" action="save_luong.php">
<input type="hidden" name="action" id="form_action" value="add">
<input type="hidden" name="edit_id" id="form_edit_id" value="">
<input type="hidden" name="thang_ky" value="<?=$thang_loc?>">
<input type="hidden" name="nam_ky" value="<?=$nam_loc?>">
<div class="form-panel">
  <div style="padding:10px;">
    <div class="form-col-title">👤 THÔNG TIN LÁI XE</div>
    <div class="field-row">
      <label>Lái xe:</label>
      <select name="lai_xe_id" id="lai_xe_id" onchange="loadLaiXe(this)">
        <option value="">-- Chọn lái xe --</option>
        <?php $lxl=$conn->query("SELECT id,ten_lai_xe,luong_trach_nhiem FROM danh_muc_lai_xe WHERE da_nghi=0 ORDER BY ten_lai_xe");
        while($lx=$lxl->fetch_assoc()) echo "<option value='{$lx['id']}' data-ten='{$lx['ten_lai_xe']}' data-luong='{$lx['luong_trach_nhiem']}'>{$lx['ten_lai_xe']}</option>"; ?>
      </select>
    </div>
    <div class="field-row"><label>Tên lái xe:</label><input type="text" name="ten_lai_xe" id="ten_lai_xe" readonly></div>
    <div class="field-row"><label>Lương TN:</label><input type="text" name="luong_trach_nhiem" id="luong_trach_nhiem" onkeyup="formatNum(this);tinhTong();" style="color:#c62828;font-weight:bold;"></div>
  </div>
  <div style="padding:10px;">
    <div class="form-col-title">💰 LƯƠNG CHUYẾN</div>
    <div class="field-row"><label>Lương CN (chuyến):</label><input type="text" name="tong_luong_chuyen_cn" id="tong_luong_chuyen_cn" onkeyup="formatNum(this);tinhTong();" placeholder="Tổng lương chuyến CN"></div>
    <div class="field-row"><label>Lương DL (chuyến):</label><input type="text" name="tong_luong_chuyen_dl" id="tong_luong_chuyen_dl" onkeyup="formatNum(this);tinhTong();" placeholder="Tổng lương chuyến DL"></div>
    <div class="field-row"><label>Phụ cấp:</label><input type="text" name="phu_cap" id="phu_cap" onkeyup="formatNum(this);tinhTong();" placeholder="0"></div>
    <div class="field-row"><label>Khấu trừ:</label><input type="text" name="khau_tru" id="khau_tru" onkeyup="formatNum(this);tinhTong();" placeholder="0" style="color:#c62828;"></div>
  </div>
  <div style="padding:10px;">
    <div class="form-col-title">📊 KẾT QUẢ</div>
    <div class="field-row"><label>TỔNG LƯƠNG:</label><input type="text" id="tong_luong_display" readonly style="font-weight:bold;font-size:14px;color:#1565c0;background:#e3f2fd;border-color:#90caf9;"></div>
    <div class="field-row">
      <label>Đã thanh toán:</label>
      <input type="checkbox" name="da_thanh_toan" id="da_thanh_toan" style="width:18px;height:18px;flex:none;" onchange="toggleNgayTT()">
    </div>
    <div class="field-row"><label>Ngày TT:</label><input type="date" name="ngay_thanh_toan" id="ngay_thanh_toan"></div>
    <div class="field-row"><label>Ghi chú:</label><input type="text" name="ghi_chu" id="ghi_chu"></div>
  </div>
</div>

<div class="toolbar">
  <button type="button" class="btn" style="color:green" onclick="submitAdd()">✚ Thêm (Alt+A)</button>
  <button type="button" class="btn" style="color:blue"  onclick="submitEdit()">💾 Sửa (Alt+E)</button>
  <button type="button" class="btn" style="color:red"   onclick="deleteRow()">✖ Xóa (Alt+D)</button>
  <button type="button" class="btn"                     onclick="clearForm()">🔄 Làm mới</button>
  <button type="button" class="btn"                     onclick="window.location.href='index.php'">🚪 Thoát</button>
  <div class="summary-group">
    <div class="sbox">Tổng lương tháng<span id="sum_luong">0</span>đ</div>
    <div class="sbox green">Đã thanh toán<span id="sum_da">0</span>đ</div>
    <div class="sbox" style="background:#ffebee;border-color:#ef9a9a;"><span style="color:#c62828;">Chưa TT</span><span id="sum_chua" style="color:#c62828;">0</span>đ</div>
  </div>
</div>
</form>

<div class="grid-wrapper">
<table id="mainTable">
<thead><tr>
  <th width="35">Stt</th><th width="35">Chọn</th>
  <th width="150">Tên lái xe</th>
  <th width="110">Lương TN</th><th width="110">Lương CN</th>
  <th width="110">Lương DL</th><th width="90">Phụ cấp</th>
  <th width="90">Khấu trừ</th><th width="120">TỔNG LƯƠNG</th>
  <th width="80">Đã TT</th><th width="90">Ngày TT</th><th>Ghi chú</th>
</tr></thead>
<tbody id="tableBody">
<?php $stt=1; while($r=$res->fetch_assoc()): ?>
<tr onclick="selectRow(this)"
  data-id="<?=$r['id']?>" data-lxid="<?=$r['lai_xe_id']?>"
  data-ten="<?=htmlspecialchars($r['ten_lai_xe']??'')?>"
  data-ltn="<?=$r['luong_trach_nhiem']?>" data-lcn="<?=$r['tong_luong_chuyen_cn']?>"
  data-ldl="<?=$r['tong_luong_chuyen_dl']?>" data-pc="<?=$r['phu_cap']?>"
  data-kt="<?=$r['khau_tru']?>" data-tl="<?=$r['tong_luong']?>"
  data-datt="<?=$r['da_thanh_toan']?>" data-ngaytt="<?=$r['ngay_thanh_toan']??''?>"
  data-ghichu="<?=htmlspecialchars($r['ghi_chu']??'')?>">
  <td class="col-center"><?=$stt++?></td>
  <td class="col-center"><input type="radio" name="row_select" value="<?=$r['id']?>" onclick="event.stopPropagation();"></td>
  <td><b><?=htmlspecialchars($r['ten_lai_xe']??'')?></b></td>
  <td class="col-money"><?=number_format($r['luong_trach_nhiem'])?></td>
  <td class="col-money"><?=number_format($r['tong_luong_chuyen_cn'])?></td>
  <td class="col-money"><?=number_format($r['tong_luong_chuyen_dl'])?></td>
  <td class="col-money"><?=number_format($r['phu_cap'])?></td>
  <td class="col-money" style="color:#c62828;"><?=number_format($r['khau_tru'])?></td>
  <td class="col-money" style="font-size:13px;"><?=number_format($r['tong_luong'])?></td>
  <td class="col-center <?=$r['da_thanh_toan']?'paid':'unpaid'?>"><?=$r['da_thanh_toan']?'✔ Đã TT':'✖ Chưa'?></td>
  <td class="col-center"><?=$r['ngay_thanh_toan']?date('d/m/Y',strtotime($r['ngay_thanh_toan'])):''?></td>
  <td><?=htmlspecialchars($r['ghi_chu']??'')?></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
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
  const tl=ltn+lcn+ldl+pc-kt;
  document.getElementById('tong_luong_display').value=fmtStr(tl);
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
  row.classList.add('selected');selectedRow=row;
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
function setSelect(id,val){
  const s=document.getElementById(id);
  for(let i=0;i<s.options.length;i++)
    if(s.options[i].value==val){
      s.selectedIndex=i;return;
    }
  }
function toggleNgayTT(){
  document.getElementById('ngay_thanh_toan').value=document.getElementById('da_thanh_toan').checked?new Date().toISOString().split('T')[0]:'';
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
  if(confirm('Xác nhận xóa?'))window.location.href='xoa_luong.php?id='+r.value+'&thang=<?=$thang_loc?>&nam=<?=$nam_loc?>';
}
function clearForm(){
  document.getElementById('mainForm').reset();
  document.getElementById('form_edit_id').value='';
  document.getElementById('form_action').value='add';
  document.getElementById('tong_luong_display').value='';
  document.querySelectorAll('#mainTable tbody tr').forEach(r=>r.classList.remove('selected'));
  selectedRow=null;
}
function calcSummary(){
  let tl=0,da=0;document.querySelectorAll('#tableBody tr').forEach(row=>{
    const cells=row.getElementsByTagName('td');
    if(row.style.display==='none')return;
    tl+=parseNum(cells[8]?.innerText||'0');
    if(cells[9]?.innerText.includes('✔'))da+=parseNum(cells[8]?.innerText||'0');
  });
  document.getElementById('sum_luong').innerText=fmtStr(tl);
  document.getElementById('sum_da').innerText=fmtStr(da);
  document.getElementById('sum_chua').innerText=fmtStr(tl-da);
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
window.addEventListener('load',()=>calcSummary());
</script>
</body>
</html>