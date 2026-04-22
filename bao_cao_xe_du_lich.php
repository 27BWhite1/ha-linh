<?php
include 'conn.php';
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: dn.php"); exit(); }

$thang = intval($_GET['thang'] ?? date('n'));
$nam   = intval($_GET['nam']   ?? date('Y'));
$d1    = "$nam-".str_pad($thang,2,'0',STR_PAD_LEFT)."-01";
$d2    = date('Y-m-t', strtotime($d1));
$f_kh  = $_GET['kh'] ?? '';

$where = "WHERE ngay_di BETWEEN '$d1' AND '$d2'";
if ($f_kh) $where .= " AND khach_hang LIKE '%".addslashes($f_kh)."%'";

$res = $conn->query("SELECT * FROM xe_chay_du_lich $where ORDER BY ngay_di ASC, id ASC");
$sum = $conn->query("SELECT COUNT(*) as so_chuyen, SUM(don_gia) as tong_dg, SUM(luong_lai_xe) as tong_luong FROM xe_chay_du_lich $where")->fetch_assoc();
$tong_ln = $sum['tong_dg'] - $sum['tong_luong'];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Báo cáo xe chạy du lịch</title>
<style>
*{box-sizing:border-box;}
body{font-family:Tahoma,sans-serif;font-size:12px;background:#f0f0f0;margin:0;}
.main-container{margin:8px;border:2px solid #999;background:#fff;padding:10px;}
.title{color:#be0000;text-align:center;font-weight:bold;font-size:16px;margin:0 0 8px;border-bottom:2px double #be0000;padding-bottom:5px;}
.filter-bar{background:#e8f5e9;border:1px solid #a5d6a7;padding:8px 12px;margin-bottom:8px;display:flex;gap:10px;align-items:center;flex-wrap:wrap;}
.filter-bar label{font-weight:bold;font-size:11px;color:#2e7d32;}
.filter-bar input,.filter-bar select{border:1px solid #a5d6a7;padding:3px 6px;height:24px;font-size:11px;}
.btn{border:1px solid #888;padding:4px 14px;cursor:pointer;font-weight:bold;font-size:12px;background:#eee;display:inline-flex;align-items:center;gap:4px;}
.btn:hover{background:#d0d0d0;}
.btn-print{background:#2e7d32;color:#fff;border-color:#1b5e20;}
.btn-print:hover{background:#1b5e20;}
.summary-row{display:flex;gap:10px;margin-bottom:8px;flex-wrap:wrap;}
.sbox{flex:1;min-width:130px;background:#fff;border:2px solid #a5d6a7;border-radius:4px;padding:8px 12px;text-align:center;}
.sbox .val{font-size:17px;font-weight:bold;color:#2e7d32;}
.sbox .lbl{font-size:11px;color:#666;margin-top:3px;}
.sbox.red{border-color:#ef9a9a;} .sbox.red .val{color:#c62828;}
.sbox.blue{border-color:#90caf9;} .sbox.blue .val{color:#1565c0;}
.sbox.orange{border-color:#ffb74d;} .sbox.orange .val{color:#e65100;}
.grid-wrapper{height:400px;overflow:auto;border:1px solid #999;}
table{width:100%;border-collapse:collapse;min-width:1200px;}
th{background:#2e7d32;color:#fff;border:1px solid #388e3c;padding:5px 4px;position:sticky;top:0;font-weight:normal;font-size:11px;white-space:nowrap;}
td{border:1px solid #ddd;padding:3px 6px;font-size:11px;}
tr:nth-child(even){background:#f1fff1;}
tr:hover{background:#fffde0;}
.col-money{text-align:right;font-weight:bold;}
.col-center{text-align:center;}
tfoot td{background:#e8f5e9;font-weight:bold;border-top:2px solid #2e7d32;}
@media print{body{background:#fff;}.no-print{display:none!important;}.main-container{border:none;margin:0;padding:0;}.grid-wrapper{height:auto;overflow:visible;}table{min-width:100%;}}
</style>
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="main-container">
<div class="title">🌴 BÁO CÁO XE CHẠY DU LỊCH</div>

<div class="filter-bar no-print">
  <form method="GET" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
    <label>Tháng:</label>
    <select name="thang"><?php for($m=1;$m<=12;$m++) echo "<option value='$m' ".($m==$thang?'selected':'').">Tháng $m</option>"; ?></select>
    <label>Năm:</label><input type="number" name="nam" value="<?=$nam?>" style="width:75px;">
    <label>Khách hàng:</label><input type="text" name="kh" value="<?=htmlspecialchars($f_kh)?>" placeholder="Lọc theo KH..." style="width:150px;">
    <button type="submit" class="btn" style="color:#2e7d32;">🔍 Xem báo cáo</button>
    <button type="button" class="btn btn-print" onclick="window.print()">🖨️ In / PDF</button>
    <a href="index.php" class="btn">🚪 Thoát</a>
  </form>
</div>

<div class="print-header" style="display:none;">
  <h2 style="text-align:center;color:#be0000;margin:0;">CÔNG TY TNHH VẬN TẢI VÀ DU LỊCH HÀ LINH</h2>
  <h3 style="text-align:center;margin:5px 0;">BÁO CÁO XE CHẠY DU LỊCH - THÁNG <?=$thang?>/<?=$nam?></h3>
</div>

<div class="summary-row">
  <div class="sbox"><div class="val"><?=number_format($sum['so_chuyen'])?></div><div class="lbl">Tổng chuyến</div></div>
  <div class="sbox"><div class="val"><?=number_format($sum['tong_dg'])?> đ</div><div class="lbl">Tổng doanh thu</div></div>
  <div class="sbox red"><div class="val"><?=number_format($sum['tong_luong'])?> đ</div><div class="lbl">Tổng lương lái xe</div></div>
  <div class="sbox orange"><div class="val"><?=number_format($tong_ln)?> đ</div><div class="lbl">Lợi nhuận gộp</div></div>
</div>

<div class="grid-wrapper">
<table>
<thead><tr>
  <th width="35">Stt</th><th width="80">Ngày đi</th><th width="75">Ngày về</th>
  <th width="170">Tên đoàn / Tour</th><th width="120">Khách hàng</th>
  <th width="50">Số KH</th><th width="90">Biển số</th>
  <th width="110">Lái xe</th><th width="100">HDV</th>
  <th width="130">Điểm đón</th><th width="130">Điểm trả</th>
  <th width="100">Đơn giá</th><th width="100">Lương LX</th>
  <th width="90">Tiền HDV</th><th width="100">Lợi nhuận</th><th>Ghi chú</th>
</tr></thead>
<tbody>
<?php $stt=1; $tdg=0; $tll=0; $thdv=0;
while($r=$res->fetch_assoc()):
  $ln=$r['don_gia']-$r['luong_lai_xe']-$r['tien_hdv'];
  $tdg+=$r['don_gia']; $tll+=$r['luong_lai_xe']; $thdv+=$r['tien_hdv'];
?>
<tr>
  <td class="col-center"><?=$stt++?></td>
  <td class="col-center"><?=date('d/m/Y',strtotime($r['ngay_di']))?></td>
  <td class="col-center"><?=$r['ngay_ve']?date('d/m/Y',strtotime($r['ngay_ve'])):''?></td>
  <td><?=htmlspecialchars($r['ten_doan']??'')?></td>
  <td><?=htmlspecialchars($r['khach_hang']??'')?></td>
  <td class="col-center"><?=$r['so_khach']?></td>
  <td class="col-center"><b><?=htmlspecialchars($r['bien_so']??'')?></b></td>
  <td><?=htmlspecialchars($r['lai_xe']??'')?></td>
  <td><?=htmlspecialchars($r['huong_dan_vien']??'')?></td>
  <td><?=htmlspecialchars($r['diem_don']??'')?></td>
  <td><?=htmlspecialchars($r['diem_tra']??'')?></td>
  <td class="col-money" style="color:#2e7d32;"><?=number_format($r['don_gia'])?></td>
  <td class="col-money" style="color:#c62828;"><?=number_format($r['luong_lai_xe'])?></td>
  <td class="col-money" style="color:#1565c0;"><?=number_format($r['tien_hdv'])?></td>
  <td class="col-money" style="color:<?=$ln>=0?'#2e7d32':'#c62828'?>;"><?=number_format($ln)?></td>
  <td><?=htmlspecialchars($r['ghi_chu']??'')?></td>
</tr>
<?php endwhile; ?>
</tbody>
<tfoot><tr>
  <td colspan="11" style="text-align:right;">TỔNG CỘNG:</td>
  <td class="col-money" style="color:#2e7d32;"><?=number_format($tdg)?></td>
  <td class="col-money" style="color:#c62828;"><?=number_format($tll)?></td>
  <td class="col-money" style="color:#1565c0;"><?=number_format($thdv)?></td>
  <td class="col-money" style="color:<?=($tdg-$tll-$thdv)>=0?'#2e7d32':'#c62828'?>;"><?=number_format($tdg-$tll-$thdv)?></td>
  <td></td>
</tr></tfoot>
</table>
</div>
</div>
<style>@media print{.print-header{display:block!important;}}</style>
</body>
</html>