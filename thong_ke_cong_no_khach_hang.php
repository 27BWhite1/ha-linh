<?php
include 'conn.php';
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: dn.php"); exit(); }

$nam  = intval($_GET['nam'] ?? date('Y'));
$f_kh = $_GET['kh'] ?? '';
$where = "WHERE YEAR(ngay_thu) = $nam";
if ($f_kh) $where .= " AND khach_hang LIKE '%".addslashes($f_kh)."%'";

$res = $conn->query("
    SELECT khach_hang,
        SUM(so_tien_phai_thu) as tong_phai,
        SUM(so_tien_da_thu)   as tong_da,
        SUM(so_tien_phai_thu - so_tien_da_thu) as tong_con,
        COUNT(*) as so_hd
    FROM so_thu_khach_hang $where
    GROUP BY khach_hang
    ORDER BY tong_con DESC
");
$no_cu_map = [];
$nc = $conn->query("SELECT ten_khachhang, no_cu FROM danh_muc_khach_hang");
while($r=$nc->fetch_assoc()) $no_cu_map[$r['ten_khachhang']] = $r['no_cu'];

$sum = $conn->query("SELECT SUM(so_tien_phai_thu) as tp, SUM(so_tien_da_thu) as td FROM so_thu_khach_hang $where")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Thống kê công nợ khách hàng</title>
<style>
*{box-sizing:border-box;}
body{font-family:Tahoma,sans-serif;font-size:12px;background:#f0f0f0;margin:0;}
.main-container{margin:8px;border:2px solid #999;background:#fff;padding:10px;}
.title{color:#be0000;text-align:center;font-weight:bold;font-size:16px;margin:0 0 8px;border-bottom:2px double #be0000;padding-bottom:5px;}
.filter-bar{background:#fce4ec;border:1px solid #f48fb1;padding:8px 12px;margin-bottom:8px;display:flex;gap:10px;align-items:center;flex-wrap:wrap;}
.filter-bar label{font-weight:bold;font-size:11px;color:#880e4f;}
.filter-bar input,.filter-bar select{border:1px solid #f48fb1;padding:3px 6px;height:24px;font-size:11px;}
.btn{border:1px solid #888;padding:4px 14px;cursor:pointer;font-weight:bold;font-size:12px;background:#eee;display:inline-flex;align-items:center;gap:4px;}
.btn:hover{background:#d0d0d0;}
.btn-print{background:#880e4f;color:#fff;}
.summary-row{display:flex;gap:10px;margin-bottom:8px;}
.sbox{flex:1;border:2px solid #f48fb1;border-radius:4px;padding:8px;text-align:center;background:#fce4ec;}
.sbox .val{font-size:17px;font-weight:bold;color:#880e4f;}
.sbox .lbl{font-size:11px;color:#888;margin-top:2px;}
.sbox.green{border-color:#a5d6a7;background:#f1f8e9;}.sbox.green .val{color:#2e7d32;}
.sbox.red{border-color:#ef9a9a;background:#ffebee;}.sbox.red .val{color:#c62828;}
table{width:100%;border-collapse:collapse;}
th{background:#880e4f;color:#fff;border:1px solid #ad1457;padding:6px 5px;font-weight:normal;font-size:11px;}
td{border:1px solid #ddd;padding:5px 8px;font-size:12px;}
tr:nth-child(even){background:#fce4ec22;}
tr:hover{background:#fce4ec;}
.col-money{text-align:right;font-weight:bold;}
.col-center{text-align:center;}
.no-trang{color:#2e7d32;font-weight:bold;}
.co-no{color:#c62828;font-weight:bold;}
tfoot td{background:#fce4ec;font-weight:bold;border-top:2px solid #880e4f;}
/* Chi tiết accordion */
.detail-row{display:none;background:#fff8f8;}
.detail-table{width:100%;border-collapse:collapse;font-size:11px;}
.detail-table th{background:#f8bbd0;color:#333;padding:3px 6px;border:1px solid #f48fb1;}
.detail-table td{border:1px solid #f8bbd0;padding:3px 6px;}
@media print{body{background:#fff;}.no-print{display:none!important;}.main-container{border:none;margin:0;padding:0;}}
</style>
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="main-container">
<div class="title">👥 THỐNG KÊ CÔNG NỢ KHÁCH HÀNG</div>

<div class="filter-bar no-print">
  <form method="GET" style="display:flex;gap:8px;align-items:center;">
    <label>Năm:</label><input type="number" name="nam" value="<?=$nam?>" style="width:80px;">
    <label>Khách hàng:</label><input type="text" name="kh" value="<?=htmlspecialchars($f_kh)?>" placeholder="Lọc tên KH..." style="width:180px;">
    <button type="submit" class="btn" style="color:#880e4f;">🔍 Xem</button>
    <button type="button" class="btn btn-print" onclick="window.print()">🖨️ In / PDF</button>
    <a href="index.php" class="btn">🚪 Thoát</a>
  </form>
</div>

<div class="print-header" style="display:none;">
  <h2 style="text-align:center;color:#be0000;margin:0;">CÔNG TY TNHH VẬN TẢI VÀ DU LỊCH HÀ LINH</h2>
  <h3 style="text-align:center;">THỐNG KÊ CÔNG NỢ KHÁCH HÀNG - NĂM <?=$nam?></h3>
</div>

<div class="summary-row">
  <div class="sbox"><div class="val"><?=number_format($sum['tp'])?> đ</div><div class="lbl">Tổng phải thu</div></div>
  <div class="sbox green"><div class="val"><?=number_format($sum['td'])?> đ</div><div class="lbl">Đã thu được</div></div>
  <div class="sbox red"><div class="val"><?=number_format($sum['tp']-$sum['td'])?> đ</div><div class="lbl">Còn phải thu</div></div>
</div>

<table id="mainTable">
<thead><tr>
  <th width="35">Stt</th><th>Khách hàng</th>
  <th width="60">Nợ cũ</th><th width="50">Số HĐ</th>
  <th width="130">Tổng phải thu</th><th width="130">Đã thu</th>
  <th width="130">Còn phải thu</th><th width="80">Trạng thái</th>
  <th width="60" class="no-print">Chi tiết</th>
</tr></thead>
<tbody>
<?php $stt=1; $rows=[]; while($r=$res->fetch_assoc()): $rows[]=$r;
  $no_cu = $no_cu_map[$r['khach_hang']] ?? 0;
  $tong_con_thuc = $r['tong_con'] + $no_cu;
  $status_class = $tong_con_thuc > 0 ? 'co-no' : 'no-trang';
  $status_text  = $tong_con_thuc > 0 ? '⚠ Còn nợ' : '✔ Đã thu';
?>
<tr>
  <td class="col-center"><?=$stt++?></td>
  <td><b><?=htmlspecialchars($r['khach_hang']??'')?></b></td>
  <td class="col-money" style="color:#c62828;"><?=$no_cu>0?number_format($no_cu):'-'?></td>
  <td class="col-center"><?=$r['so_hd']?></td>
  <td class="col-money"><?=number_format($r['tong_phai'])?></td>
  <td class="col-money" style="color:#2e7d32;"><?=number_format($r['tong_da'])?></td>
  <td class="col-money <?=$status_class?>"><?=number_format($tong_con_thuc)?></td>
  <td class="col-center <?=$status_class?>"><?=$status_text?></td>
  <td class="col-center no-print">
    <button class="btn" style="padding:2px 8px;font-size:11px;" onclick="toggleDetail('d<?=$stt-1?>')">📋</button>
  </td>
</tr>
<tr id="d<?=$stt-1?>" class="detail-row no-print">
  <td colspan="9" style="padding:0;">
    <?php
    $det=$conn->query("SELECT * FROM so_thu_khach_hang WHERE khach_hang='".addslashes($r['khach_hang'])."' AND YEAR(ngay_thu)=$nam ORDER BY ngay_thu ASC");
    echo '<table class="detail-table"><thead><tr><th>Ngày</th><th>Loại xe</th><th>Mã CT</th><th>Diễn giải</th><th>Phải thu</th><th>Đã thu</th><th>Còn lại</th></tr></thead><tbody>';
    while($d=$det->fetch_assoc()){
      $cl=$d['so_tien_phai_thu']-$d['so_tien_da_thu'];
      echo "<tr><td>".date('d/m/Y',strtotime($d['ngay_thu']))."</td><td>{$d['loai_xe_chay']}</td><td>{$d['ma_chung_tu']}</td><td>{$d['dien_giai']}</td><td style='text-align:right;font-weight:bold;'>".number_format($d['so_tien_phai_thu'])."</td><td style='text-align:right;color:#2e7d32;font-weight:bold;'>".number_format($d['so_tien_da_thu'])."</td><td style='text-align:right;color:".($cl>0?'#c62828':'#2e7d32').";font-weight:bold;'>".number_format($cl)."</td></tr>";
    }
    echo '</tbody></table>';
    ?>
  </td>
</tr>
<?php endwhile; ?>
</tbody>
<tfoot><tr>
  <td colspan="4" style="text-align:right;">TỔNG:</td>
  <td class="col-money"><?=number_format($sum['tp'])?></td>
  <td class="col-money" style="color:#2e7d32;"><?=number_format($sum['td'])?></td>
  <td class="col-money" style="color:#c62828;"><?=number_format($sum['tp']-$sum['td'])?></td>
  <td colspan="2"></td>
</tr></tfoot>
</table>
</div>
<script>
function toggleDetail(id){const r=document.getElementById(id);r.style.display=r.style.display==='table-row'?'none':'table-row';}
</script>
<style>@media print{.print-header{display:block!important;}}</style>
</body>
</html>