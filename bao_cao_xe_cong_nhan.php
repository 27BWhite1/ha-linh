<?php
include 'conn.php';
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: dn.php"); exit(); }

$thang = intval($_GET['thang'] ?? date('n'));
$nam   = intval($_GET['nam']   ?? date('Y'));
$d1    = "$nam-".str_pad($thang,2,'0',STR_PAD_LEFT)."-01";
$d2    = date('Y-m-t', strtotime($d1));

$f_kh   = $_GET['kh']   ?? '';
$f_bien = $_GET['bien'] ?? '';

$where = "WHERE ngay_chay BETWEEN '$d1' AND '$d2'";
if ($f_kh)   $where .= " AND khach_hang LIKE '%".addslashes($f_kh)."%'";
if ($f_bien) $where .= " AND bien_so LIKE '%".addslashes($f_bien)."%'";

$res = $conn->query("SELECT * FROM xe_chay_cong_nhan $where ORDER BY ngay_chay ASC, id ASC");

$sum = $conn->query("SELECT COUNT(*) as so_chuyen, SUM(cuoc_xe) as tong_cuoc, SUM(luong_lai_xe) as tong_luong FROM xe_chay_cong_nhan $where")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Báo cáo xe chạy công nhân</title>
<style>
*{box-sizing:border-box;}
body{font-family:Tahoma,sans-serif;font-size:12px;background:#f0f0f0;margin:0;}
.main-container{margin:8px;border:2px solid #999;background:#fff;padding:10px;}
.title{color:#be0000;text-align:center;font-weight:bold;font-size:16px;margin:0 0 8px;border-bottom:2px double #be0000;padding-bottom:5px;}
.filter-bar{background:#e3f2fd;border:1px solid #90caf9;padding:8px 12px;margin-bottom:8px;display:flex;gap:10px;align-items:center;flex-wrap:wrap;}
.filter-bar label{font-weight:bold;font-size:11px;color:#1565c0;}
.filter-bar input,.filter-bar select{border:1px solid #90caf9;padding:3px 6px;height:24px;font-size:11px;}
.btn{border:1px solid #888;padding:4px 14px;cursor:pointer;font-weight:bold;font-size:12px;background:#eee;display:inline-flex;align-items:center;gap:4px;}
.btn:hover{background:#d0d0d0;}
.btn-print{background:#1565c0;color:#fff;border-color:#0d47a1;}
.btn-print:hover{background:#0d47a1;}
.summary-row{display:flex;gap:10px;margin-bottom:8px;flex-wrap:wrap;}
.sbox{flex:1;min-width:140px;background:#fff;border:2px solid #90caf9;border-radius:4px;padding:8px 12px;text-align:center;}
.sbox .val{font-size:18px;font-weight:bold;color:#1565c0;}
.sbox .lbl{font-size:11px;color:#666;margin-top:3px;}
.sbox.green{border-color:#a5d6a7;} .sbox.green .val{color:#2e7d32;}
.sbox.red{border-color:#ef9a9a;} .sbox.red .val{color:#c62828;}
.sbox.orange{border-color:#ffb74d;} .sbox.orange .val{color:#e65100;}
.grid-wrapper{height:420px;overflow:auto;border:1px solid #999;}
table{width:100%;border-collapse:collapse;min-width:1000px;}
th{background:#000080;color:#fff;border:1px solid #1a1aaa;padding:5px 4px;position:sticky;top:0;font-weight:normal;font-size:11px;white-space:nowrap;}
td{border:1px solid #ddd;padding:3px 6px;font-size:11px;}
tr:nth-child(even){background:#f5f8ff;}
tr:hover{background:#fffde0;}
.col-money{text-align:right;font-weight:bold;color:#1565c0;}
.col-center{text-align:center;}
tfoot td{background:#e8eaf6;font-weight:bold;border-top:2px solid #000080;}
@media print{
  body{background:#fff;} .no-print{display:none!important;}
  .main-container{border:none;margin:0;padding:0;}
  .grid-wrapper{height:auto;overflow:visible;}
  table{min-width:100%;}
}
</style>
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="main-container">
<div class="title">🚌 BÁO CÁO XE CHẠY CÔNG NHÂN</div>

<div class="filter-bar no-print">
  <form method="GET" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
    <label>Tháng:</label>
    <select name="thang"><?php for($m=1;$m<=12;$m++) echo "<option value='$m' ".($m==$thang?'selected':'').">Tháng $m</option>"; ?></select>
    <label>Năm:</label>
    <input type="number" name="nam" value="<?=$nam?>" style="width:75px;">
    <label>Khách hàng:</label>
    <input type="text" name="kh" value="<?=htmlspecialchars($f_kh)?>" placeholder="Lọc theo KH..." style="width:140px;">
    <label>Biển số:</label>
    <input type="text" name="bien" value="<?=htmlspecialchars($f_bien)?>" placeholder="Lọc biển số..." style="width:110px;">
    <button type="submit" class="btn" style="color:#1565c0;">🔍 Xem báo cáo</button>
    <button type="button" class="btn btn-print" onclick="window.print()">🖨️ In / PDF</button>
    <a href="index.php" class="btn">🚪 Thoát</a>
  </form>
</div>

<div style="display:none;" class="print-header">
  <h2 style="text-align:center;color:#be0000;margin:0;">CÔNG TY TNHH VẬN TẢI VÀ DU LỊCH HÀ LINH</h2>
  <h3 style="text-align:center;margin:5px 0;">BÁO CÁO XE CHẠY CÔNG NHÂN - THÁNG <?=$thang?>/<?=$nam?></h3>
  <?php if($f_kh) echo "<p style='text-align:center;'>Khách hàng: <b>$f_kh</b></p>"; ?>
</div>

<div class="summary-row">
  <div class="sbox"><div class="val"><?=number_format($sum['so_chuyen'])?></div><div class="lbl">Tổng số chuyến</div></div>
  <div class="sbox green"><div class="val"><?=number_format($sum['tong_cuoc'])?> đ</div><div class="lbl">Tổng cước xe (doanh thu)</div></div>
  <div class="sbox red"><div class="val"><?=number_format($sum['tong_luong'])?> đ</div><div class="lbl">Tổng lương lái xe</div></div>
  <div class="sbox orange"><div class="val"><?=number_format($sum['tong_cuoc']-$sum['tong_luong'])?> đ</div><div class="lbl">Lợi nhuận gộp</div></div>
</div>

<div class="grid-wrapper">
<table>
<thead><tr>
  <th width="40">Stt</th><th width="85">Ngày chạy</th>
  <th width="130">Khách hàng</th><th width="45">Xe nhà</th>
  <th width="90">Biển số</th><th width="130">Tuyến đường</th>
  <th width="110">Lái xe</th><th width="60">Giờ đón</th>
  <th width="55">Giờ về</th><th width="45">Ca nối</th>
  <th width="50">Thuê lái</th><th width="100">Cước xe</th>
  <th width="110">Lương lái xe</th><th>Ghi chú</th>
</tr></thead>
<tbody>
<?php $stt=1; $tcuoc=0; $tluong=0; while($r=$res->fetch_assoc()):
  $tcuoc+=$r['cuoc_xe']; $tluong+=$r['luong_lai_xe'];
?>
<tr>
  <td class="col-center"><?=$stt++?></td>
  <td class="col-center"><?=date('d/m/Y',strtotime($r['ngay_chay']))?></td>
  <td><?=htmlspecialchars($r['khach_hang']??'')?></td>
  <td class="col-center"><?=$r['loai_hinh_xe']=='Xe nhà'?'✔':''?></td>
  <td class="col-center"><b><?=htmlspecialchars($r['bien_so']??'')?></b></td>
  <td><?=htmlspecialchars($r['ma_tuyen']??'')?></td>
  <td><?=htmlspecialchars($r['lai_xe']??'')?></td>
  <td class="col-center"><?=$r['gio_don']?></td>
  <td class="col-center" <?=$r['ca_noi']?'style="background:#ffcdd2;"':''?>><?=$r['gio_ve']?></td>
  <td class="col-center"><?=$r['ca_noi']?'✔':''?></td>
  <td class="col-center"><?=$r['thue_lai']?'✔':''?></td>
  <td class="col-money"><?=number_format($r['cuoc_xe'])?></td>
  <td class="col-money"><?=number_format($r['luong_lai_xe'])?></td>
  <td><?=htmlspecialchars($r['ghi_chu']??'')?></td>
</tr>
<?php endwhile; ?>
</tbody>
<tfoot><tr>
  <td colspan="11" style="text-align:right;">TỔNG CỘNG:</td>
  <td class="col-money" style="color:#2e7d32;"><?=number_format($tcuoc)?></td>
  <td class="col-money" style="color:#c62828;"><?=number_format($tluong)?></td>
  <td></td>
</tr></tfoot>
</table>
</div>
</div>
<style>@media print{.print-header{display:block!important;}}</style>
</body>
</html>