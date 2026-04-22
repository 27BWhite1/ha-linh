<?php
include 'conn.php';
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: dn.php"); exit(); }

$thang = intval($_GET['thang'] ?? date('n'));
$nam   = intval($_GET['nam']   ?? date('Y'));

$res = $conn->query("SELECT * FROM luong_lai_xe WHERE thang=$thang AND nam=$nam ORDER BY ten_lai_xe ASC");
$sum = $conn->query("SELECT SUM(luong_trach_nhiem) as ltn, SUM(tong_luong_chuyen_cn) as lcn, SUM(tong_luong_chuyen_dl) as ldl, SUM(phu_cap) as pc, SUM(khau_tru) as kt, SUM(tong_luong) as tl FROM luong_lai_xe WHERE thang=$thang AND nam=$nam")->fetch_assoc();
$sum_da = $conn->query("SELECT SUM(tong_luong) as da FROM luong_lai_xe WHERE thang=$thang AND nam=$nam AND da_thanh_toan=1")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Bảng lương lái xe</title>
<style>
*{box-sizing:border-box;}
body{font-family:Tahoma,sans-serif;font-size:12px;background:#f0f0f0;margin:0;}
.main-container{margin:8px;border:2px solid #999;background:#fff;padding:10px;}
.print-title{text-align:center;margin-bottom:10px;}
.print-title h2{color:#be0000;margin:0;font-size:17px;}
.print-title h3{margin:3px 0;font-size:14px;}
.print-title p{margin:2px 0;font-size:12px;color:#555;}
.filter-bar{background:#fff8e1;border:1px solid #ffe082;padding:8px 12px;margin-bottom:8px;display:flex;gap:10px;align-items:center;flex-wrap:wrap;}
.filter-bar label{font-weight:bold;font-size:11px;color:#e65100;}
.filter-bar select,.filter-bar input{border:1px solid #ffe082;padding:3px 6px;height:24px;font-size:11px;}
.btn{border:1px solid #888;padding:4px 14px;cursor:pointer;font-weight:bold;font-size:12px;background:#eee;display:inline-flex;align-items:center;gap:4px;}
.btn:hover{background:#d0d0d0;}
.btn-print{background:#e65100;color:#fff;border-color:#bf360c;}
.summary-row{display:flex;gap:10px;margin-bottom:8px;flex-wrap:wrap;}
.sbox{flex:1;min-width:130px;border:2px solid #ffe082;border-radius:4px;padding:8px;text-align:center;background:#fffde7;}
.sbox .val{font-size:16px;font-weight:bold;color:#e65100;}
.sbox .lbl{font-size:10px;color:#888;margin-top:2px;}
.sbox.green{border-color:#a5d6a7;background:#f1f8e9;} .sbox.green .val{color:#2e7d32;}
.sbox.red{border-color:#ef9a9a;background:#ffebee;} .sbox.red .val{color:#c62828;}
table{width:100%;border-collapse:collapse;}
th{background:#e65100;color:#fff;border:1px solid #bf360c;padding:6px 5px;font-weight:normal;font-size:11px;white-space:nowrap;}
td{border:1px solid #ddd;padding:4px 6px;font-size:11px;}
tr:nth-child(even){background:#fffde7;}
.col-money{text-align:right;font-weight:bold;color:#1565c0;}
.col-center{text-align:center;}
.paid{color:#2e7d32;font-weight:bold;}
.unpaid{color:#c62828;font-weight:bold;}
tfoot td{background:#fff3e0;font-weight:bold;border-top:2px solid #e65100;}
.sign-row{display:flex;justify-content:space-around;margin-top:30px;text-align:center;}
.sign-row div{width:200px;}
.sign-row p{font-weight:bold;margin:0 0 60px;}
@media print{body{background:#fff;}.no-print{display:none!important;}.main-container{border:none;margin:0;padding:0;}}
</style>
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="main-container">

<div class="print-title">
  <h2>CÔNG TY TNHH VẬN TẢI VÀ DU LỊCH HÀ LINH</h2>
  <h3>BẢNG LƯƠNG LÁI XE - THÁNG <?=$thang?>/<?=$nam?></h3>
  <p>Ngày in: <?=date('d/m/Y')?></p>
</div>

<div class="filter-bar no-print">
  <form method="GET" style="display:flex;gap:8px;align-items:center;">
    <label>Tháng:</label>
    <select name="thang"><?php for($m=1;$m<=12;$m++) echo "<option value='$m' ".($m==$thang?'selected':'').">Tháng $m</option>"; ?></select>
    <label>Năm:</label><input type="number" name="nam" value="<?=$nam?>" style="width:75px;">
    <button type="submit" class="btn" style="color:#e65100;">🔍 Xem</button>
    <button type="button" class="btn btn-print" onclick="window.print()">🖨️ In bảng lương</button>
    <a href="quan_li_luong_lai_xe.php?thang=<?=$thang?>&nam=<?=$nam?>" class="btn" style="color:#1565c0;">✏️ Chỉnh sửa</a>
    <a href="index.php" class="btn">🚪 Thoát</a>
  </form>
</div>

<div class="summary-row no-print">
  <div class="sbox"><div class="val"><?=$res->num_rows?></div><div class="lbl">Số lái xe</div></div>
  <div class="sbox"><div class="val"><?=number_format($sum['ltn'])?> đ</div><div class="lbl">Tổng lương TN</div></div>
  <div class="sbox"><div class="val"><?=number_format($sum['lcn']+$sum['ldl'])?> đ</div><div class="lbl">Tổng lương chuyến</div></div>
  <div class="sbox green"><div class="val"><?=number_format($sum['tl'])?> đ</div><div class="lbl">Tổng lương phải trả</div></div>
  <div class="sbox red"><div class="val"><?=number_format($sum['tl']-$sum_da['da'])?> đ</div><div class="lbl">Chưa thanh toán</div></div>
</div>

<table>
<thead><tr>
  <th width="35">Stt</th><th width="150">Họ tên lái xe</th>
  <th width="110">Lương TN</th><th width="110">Lương CN</th>
  <th width="110">Lương DL</th><th width="90">Phụ cấp</th>
  <th width="90">Khấu trừ</th><th width="130">TỔNG LƯƠNG</th>
  <th width="80">Đã TT</th><th width="90">Ngày TT</th>
  <th width="120">Ký nhận</th><th>Ghi chú</th>
</tr></thead>
<tbody>
<?php $stt=1; $res->data_seek(0); while($r=$res->fetch_assoc()): ?>
<tr>
  <td class="col-center"><?=$stt++?></td>
  <td><b><?=htmlspecialchars($r['ten_lai_xe']??'')?></b></td>
  <td class="col-money"><?=number_format($r['luong_trach_nhiem'])?></td>
  <td class="col-money"><?=number_format($r['tong_luong_chuyen_cn'])?></td>
  <td class="col-money"><?=number_format($r['tong_luong_chuyen_dl'])?></td>
  <td class="col-money"><?=number_format($r['phu_cap'])?></td>
  <td class="col-money" style="color:#c62828;"><?=number_format($r['khau_tru'])?></td>
  <td class="col-money" style="font-size:13px;color:#e65100;"><?=number_format($r['tong_luong'])?></td>
  <td class="col-center <?=$r['da_thanh_toan']?'paid':'unpaid'?>"><?=$r['da_thanh_toan']?'✔':'✖'?></td>
  <td class="col-center"><?=$r['ngay_thanh_toan']?date('d/m/Y',strtotime($r['ngay_thanh_toan'])):''?></td>
  <td style="border-bottom:1px solid #999;"></td>
  <td><?=htmlspecialchars($r['ghi_chu']??'')?></td>
</tr>
<?php endwhile; ?>
</tbody>
<tfoot><tr>
  <td colspan="2" style="text-align:right;">TỔNG:</td>
  <td class="col-money"><?=number_format($sum['ltn'])?></td>
  <td class="col-money"><?=number_format($sum['lcn'])?></td>
  <td class="col-money"><?=number_format($sum['ldl'])?></td>
  <td class="col-money"><?=number_format($sum['pc'])?></td>
  <td class="col-money"><?=number_format($sum['kt'])?></td>
  <td class="col-money" style="color:#e65100;font-size:13px;"><?=number_format($sum['tl'])?></td>
  <td colspan="4"></td>
</tr></tfoot>
</table>

<div class="sign-row">
  <div><p>Kế toán</p>(Ký, họ tên)</div>
  <div><p>Người lập bảng</p>(Ký, họ tên)</div>
  <div><p>Giám đốc</p>(Ký, họ tên, đóng dấu)</div>
</div>
</div>
</body>
</html>