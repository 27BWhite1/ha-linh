<?php
include 'conn.php';
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: dn.php"); exit(); }

$nam = intval($_GET['nam'] ?? date('Y'));

$data = [];
for ($m = 1; $m <= 12; $m++) {
    $d1 = "$nam-".str_pad($m,2,'0',STR_PAD_LEFT)."-01";
    $d2 = date('Y-m-t', strtotime($d1));

    $r = $conn->query("SELECT SUM(cuoc_xe) as v FROM xe_chay_cong_nhan WHERE ngay_chay BETWEEN '$d1' AND '$d2'")->fetch_assoc();
    $dt_cn = floatval($r['v'] ?? 0);

    $r = $conn->query("SELECT SUM(don_gia) as v FROM xe_chay_du_lich WHERE ngay_di BETWEEN '$d1' AND '$d2'")->fetch_assoc();
    $dt_dl = floatval($r['v'] ?? 0);

    $r = $conn->query("SELECT SUM(tong_luong) as v FROM luong_lai_xe WHERE thang=$m AND nam=$nam")->fetch_assoc();
    $cp_luong = floatval($r['v'] ?? 0);

    $r = $conn->query("SELECT SUM(so_tien) as v FROM chi_phi_xe WHERE YEAR(ngay)=$nam AND MONTH(ngay)=$m")->fetch_assoc();
    $cp_xe = floatval($r['v'] ?? 0);

    $r = $conn->query("SELECT SUM(so_tien) as v FROM chi_phi_chung WHERE thang=$m AND nam=$nam")->fetch_assoc();
    $cp_chung = floatval($r['v'] ?? 0);

    $dt_tong   = $dt_cn + $dt_dl;
    $cp_tong   = $cp_luong + $cp_xe + $cp_chung;
    $loi_nhuan = $dt_tong - $cp_tong;

    $data[$m] = compact('dt_cn','dt_dl','dt_tong','cp_luong','cp_xe','cp_chung','cp_tong','loi_nhuan');
}
$tong = ['dt_cn'=>0,'dt_dl'=>0,'dt_tong'=>0,'cp_luong'=>0,'cp_xe'=>0,'cp_chung'=>0,'cp_tong'=>0,'loi_nhuan'=>0];
foreach($data as $row) foreach($tong as $k=>$v) $tong[$k]+=$row[$k];

$chart_labels = json_encode(array_map(fn($m)=>"T$m", range(1,12)));
$chart_dt   = json_encode(array_map(fn($m)=>round($data[$m]['dt_tong']/1000000,1), range(1,12)));
$chart_cp   = json_encode(array_map(fn($m)=>round($data[$m]['cp_tong']/1000000,1), range(1,12)));
$chart_ln   = json_encode(array_map(fn($m)=>round($data[$m]['loi_nhuan']/1000000,1), range(1,12)));
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Bảng lợi nhuận theo tháng</title>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<style>
*{box-sizing:border-box;}
body{font-family:Tahoma,sans-serif;font-size:12px;background:#f0f0f0;margin:0;}
.main-container{margin:8px;border:2px solid #999;background:#fff;padding:10px;}
.title{color:#be0000;text-align:center;font-weight:bold;font-size:16px;margin:0 0 8px;border-bottom:2px double #be0000;padding-bottom:5px;}
.filter-bar{background:#f3e5f5;border:1px solid #ce93d8;padding:8px 12px;margin-bottom:8px;display:flex;gap:10px;align-items:center;flex-wrap:wrap;}
.filter-bar label{font-weight:bold;font-size:11px;color:#4a148c;}
.filter-bar input{border:1px solid #ce93d8;padding:3px 6px;height:24px;font-size:11px;}
.btn{border:1px solid #888;padding:4px 14px;cursor:pointer;font-weight:bold;font-size:12px;background:#eee;display:inline-flex;align-items:center;gap:4px;}
.btn:hover{background:#d0d0d0;}
.btn-print{background:#4a148c;color:#fff;}
/* KPI boxes */
.kpi-row{display:flex;gap:10px;margin-bottom:12px;flex-wrap:wrap;}
.kpi{flex:1;min-width:140px;border-radius:6px;padding:10px 12px;text-align:center;border:2px solid #ccc;}
.kpi .val{font-size:16px;font-weight:bold;}
.kpi .lbl{font-size:10px;color:#777;margin-top:3px;}
.kpi.dt{background:#e8f5e9;border-color:#a5d6a7;}.kpi.dt .val{color:#2e7d32;}
.kpi.cp{background:#ffebee;border-color:#ef9a9a;}.kpi.cp .val{color:#c62828;}
.kpi.ln{background:#e8eaf6;border-color:#9fa8da;}.kpi.ln .val{color:#283593;}
.kpi.lnp{background:#fff8e1;border-color:#ffe082;}.kpi.lnp .val{color:#e65100;}

.chart-wrap{margin-bottom:12px;background:#fafafa;border:1px solid #ddd;padding:10px;border-radius:4px;}

table{width:100%;border-collapse:collapse;}
th{background:#4a148c;color:#fff;border:1px solid #6a1b9a;padding:6px 5px;font-weight:normal;font-size:11px;white-space:nowrap;}
td{border:1px solid #ddd;padding:5px 8px;font-size:11px;}
tr:nth-child(even){background:#f8f0ff;}
tr:hover{background:#f3e5f5;}
.col-money{text-align:right;font-weight:bold;}
.col-center{text-align:center;}
.positive{color:#2e7d32;font-weight:bold;}
.negative{color:#c62828;font-weight:bold;}
tfoot td{background:#f3e5f5;font-weight:bold;border-top:2px solid #4a148c;font-size:12px;}
@media print{
  body{background:#fff;}.no-print{display:none!important;}
  .main-container{border:none;margin:0;padding:0;}
  .chart-wrap{display:none;}
}
</style>
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="main-container">
<div class="title">📈 BẢNG LỢI NHUẬN THEO THÁNG</div>

<div class="filter-bar no-print">
  <form method="GET" style="display:flex;gap:8px;align-items:center;">
    <label>Năm:</label><input type="number" name="nam" value="<?=$nam?>" style="width:85px;">
    <button type="submit" class="btn" style="color:#4a148c;">🔍 Xem báo cáo</button>
    <button type="button" class="btn btn-print" onclick="window.print()">🖨️ In / PDF</button>
    <a href="index.php" class="btn">🚪 Thoát</a>
  </form>
</div>

<div class="print-header" style="display:none;">
  <h2 style="text-align:center;color:#be0000;margin:0;">CÔNG TY TNHH VẬN TẢI VÀ DU LỊCH HÀ LINH</h2>
  <h3 style="text-align:center;">BẢNG LỢI NHUẬN THEO THÁNG - NĂM <?=$nam?></h3>
</div>

<div class="kpi-row">
  <div class="kpi dt">
    <div class="val"><?=number_format($tong['dt_tong'])?> đ</div>
    <div class="lbl">Tổng doanh thu năm <?=$nam?></div>
  </div>
  <div class="kpi cp">
    <div class="val"><?=number_format($tong['cp_tong'])?> đ</div>
    <div class="lbl">Tổng chi phí năm <?=$nam?></div>
  </div>
  <div class="kpi ln">
    <div class="val" style="color:<?=$tong['loi_nhuan']>=0?'#283593':'#c62828'?>;"><?=number_format($tong['loi_nhuan'])?> đ</div>
    <div class="lbl">Lợi nhuận ròng năm <?=$nam?></div>
  </div>
  <div class="kpi lnp">
    <div class="val"><?=$tong['dt_tong']>0?round($tong['loi_nhuan']/$tong['dt_tong']*100,1).'%':'0%'?></div>
    <div class="lbl">Tỷ suất lợi nhuận</div>
  </div>
</div>

<div class="chart-wrap no-print">
  <canvas id="profitChart" height="80"></canvas>
</div>

<table>
<thead><tr>
  <th width="55">Tháng</th>
  <th width="120">DT xe CN</th><th width="120">DT xe DL</th>
  <th width="120">Tổng doanh thu</th>
  <th width="110">Chi lương LX</th><th width="110">Chi phí xe</th>
  <th width="110">Chi phí chung</th><th width="120">Tổng chi phí</th>
  <th width="130">LỢI NHUẬN</th><th width="70">Tỷ suất</th>
</tr></thead>
<tbody>
<?php foreach($data as $m=>$row):
  $lnp = $row['dt_tong']>0 ? round($row['loi_nhuan']/$row['dt_tong']*100,1) : 0;
  $ln_class = $row['loi_nhuan']>=0?'positive':'negative';
  // Làm nổi tháng hiện tại
  $is_cur = ($m==date('n') && $nam==date('Y'));
?>
<tr <?=$is_cur?'style="background:#fff8e1;font-weight:bold;"':''?>>
  <td class="col-center"><b>Tháng <?=$m?></b></td>
  <td class="col-money"><?=$row['dt_cn']>0?number_format($row['dt_cn']):'-'?></td>
  <td class="col-money"><?=$row['dt_dl']>0?number_format($row['dt_dl']):'-'?></td>
  <td class="col-money" style="color:#2e7d32;"><?=$row['dt_tong']>0?number_format($row['dt_tong']):'-'?></td>
  <td class="col-money"><?=$row['cp_luong']>0?number_format($row['cp_luong']):'-'?></td>
  <td class="col-money"><?=$row['cp_xe']>0?number_format($row['cp_xe']):'-'?></td>
  <td class="col-money"><?=$row['cp_chung']>0?number_format($row['cp_chung']):'-'?></td>
  <td class="col-money" style="color:#c62828;"><?=$row['cp_tong']>0?number_format($row['cp_tong']):'-'?></td>
  <td class="col-money <?=$ln_class?>" style="font-size:12px;"><?=$row['dt_tong']>0||$row['cp_tong']>0?number_format($row['loi_nhuan']):'-'?></td>
  <td class="col-center <?=$ln_class?>"><?=$row['dt_tong']>0?$lnp.'%':'-'?></td>
</tr>
<?php endforeach; ?>
</tbody>
<tfoot><tr>
  <td style="text-align:center;"><b>TỔNG</b></td>
  <td class="col-money"><?=number_format($tong['dt_cn'])?></td>
  <td class="col-money"><?=number_format($tong['dt_dl'])?></td>
  <td class="col-money" style="color:#2e7d32;"><?=number_format($tong['dt_tong'])?></td>
  <td class="col-money"><?=number_format($tong['cp_luong'])?></td>
  <td class="col-money"><?=number_format($tong['cp_xe'])?></td>
  <td class="col-money"><?=number_format($tong['cp_chung'])?></td>
  <td class="col-money" style="color:#c62828;"><?=number_format($tong['cp_tong'])?></td>
  <td class="col-money <?=$tong['loi_nhuan']>=0?'positive':'negative'?>" style="font-size:13px;"><?=number_format($tong['loi_nhuan'])?></td>
  <td class="col-center"><?=$tong['dt_tong']>0?round($tong['loi_nhuan']/$tong['dt_tong']*100,1).'%':'-'?></td>
</tr></tfoot>
</table>
</div>

<script>
const ctx = document.getElementById('profitChart').getContext('2d');
new Chart(ctx, {
  type: 'bar',
  data: {
    labels: <?=$chart_labels?>,
    datasets: [
      { label: 'Doanh thu (tr.đ)', data: <?=$chart_dt?>, backgroundColor: 'rgba(46,125,50,0.7)', borderColor: '#2e7d32', borderWidth: 1 },
      { label: 'Chi phí (tr.đ)',   data: <?=$chart_cp?>, backgroundColor: 'rgba(198,40,40,0.7)', borderColor: '#c62828', borderWidth: 1 },
      { label: 'Lợi nhuận (tr.đ)', data: <?=$chart_ln?>, type: 'line', borderColor: '#4a148c', backgroundColor: 'rgba(74,20,140,0.1)', borderWidth: 2, pointRadius: 4, fill: true, tension: 0.3 }
    ]
  },
  options: {
    responsive: true,
    plugins: {
      legend: { position: 'top' },
      title: { display: true, text: 'Doanh thu - Chi phí - Lợi nhuận năm <?=$nam?> (đơn vị: triệu đồng)', font: { size: 13 } }
    },
    scales: { y: { beginAtZero: true, ticks: { callback: v => v + ' tr' } } }
  }
});
</script>
<style>@media print{.print-header{display:block!important;}}</style>
</body>
</html>