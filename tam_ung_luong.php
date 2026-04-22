<?php
include 'conn.php';
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: dn.php"); exit(); }

$conn->query("CREATE TABLE IF NOT EXISTS `tam_ung_luong` (
    `id`            int(11)       NOT NULL AUTO_INCREMENT,
    `so_phieu`      varchar(20)   DEFAULT NULL COMMENT 'Mã phiếu tự động TU-YYYYMM-XXX',
    `ngay_tam_ung`  date          DEFAULT NULL,
    `thang_khau`    int(2)        DEFAULT NULL COMMENT 'Tháng khấu trừ lương',
    `nam_khau`      int(4)        DEFAULT NULL,
    `doi_tuong`     varchar(20)   DEFAULT 'Lái xe' COMMENT 'Lái xe / Nhân viên',
    `nguoi_tam_ung` varchar(100)  DEFAULT NULL,
    `chuc_vu`       varchar(100)  DEFAULT NULL,
    `ly_do`         varchar(255)  DEFAULT NULL,
    `so_tien`       decimal(15,0) DEFAULT 0,
    `nguoi_duyet`   varchar(100)  DEFAULT NULL,
    `trang_thai`    varchar(30)   DEFAULT 'Chưa hoàn trả' COMMENT 'Chưa hoàn trả / Đã khấu trừ / Đã hoàn trả',
    `ngay_hoan_tra` date          DEFAULT NULL,
    `ghi_chu`       text          DEFAULT NULL,
    `nguoi_tao`     varchar(100)  DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");

$f_thang  = $_GET['thang']  ?? '';
$f_nam    = intval($_GET['nam'] ?? date('Y'));
$f_dt     = $_GET['dt']     ?? '';
$f_tt     = $_GET['tt']     ?? '';

$where = "WHERE YEAR(ngay_tam_ung) = $f_nam";
if ($f_thang) $where .= " AND MONTH(ngay_tam_ung) = ".intval($f_thang);
if ($f_dt)    $where .= " AND doi_tuong = '".addslashes($f_dt)."'";
if ($f_tt)    $where .= " AND trang_thai = '".addslashes($f_tt)."'";

$res  = $conn->query("SELECT * FROM tam_ung_luong $where ORDER BY ngay_tam_ung DESC, id DESC");
$sum  = $conn->query("SELECT SUM(so_tien) as tong, COUNT(*) as cnt FROM tam_ung_luong $where")->fetch_assoc();
$sumCT = $conn->query("SELECT SUM(so_tien) as tong FROM tam_ung_luong $where AND trang_thai='Chưa hoàn trả'")->fetch_assoc();

$next_so = '';
$r = $conn->query("SELECT MAX(id) as mid FROM tam_ung_luong");
$next_id = ($r->fetch_assoc()['mid'] ?? 0) + 1;
$next_so = 'TU-' . date('Ym') . '-' . str_pad($next_id, 3, '0', STR_PAD_LEFT);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Tạm ứng lương - Hà Linh</title>
<style>
*{box-sizing:border-box;}
body{font-family:Tahoma,sans-serif;font-size:12px;background:#f0f0f0;margin:0;}
.main-container{margin:8px;border:2px solid #999;background:#fff;padding:10px;}
.title{color:#be0000;text-align:center;font-weight:bold;font-size:16px;margin:0 0 10px;border-bottom:2px double #be0000;padding-bottom:5px;}

.form-panel{display:grid;grid-template-columns:1fr 1fr 1fr;border:1px solid #ccc;margin-bottom:8px;background:#fafafa;}
.form-col{padding:10px 12px;border-right:1px solid #ccc;}
.form-col:last-child{border-right:none;}
.col-title{font-weight:bold;color:#fff;background:#8b0000;padding:4px 8px;margin:-10px -12px 10px;font-size:11px;}
.field-row{display:flex;align-items:center;margin-bottom:6px;}
.field-row label{width:115px;flex-shrink:0;font-weight:bold;font-size:11px;color:#444;}
.field-row input,.field-row select,.field-row textarea{flex:1;border:1px solid #aaa;padding:3px 5px;height:22px;font-size:11px;font-family:Tahoma,sans-serif;}
.field-row textarea{height:36px;resize:none;}
.field-row input[readonly]{background:#f0f0f0;color:#be0000;font-weight:bold;}

.toolbar{background:#e1e1e1;border:1px solid #bbb;padding:5px 8px;margin-bottom:8px;display:flex;gap:5px;align-items:center;flex-wrap:wrap;}
.btn{border:1px solid #888;padding:4px 14px;cursor:pointer;font-weight:bold;font-size:12px;display:flex;align-items:center;gap:4px;background:#eee;font-family:Tahoma,sans-serif;}
.btn:hover{background:#d5d5d5;}
.btn-print{background:#8b0000;color:#fff;border-color:#6b0000;}
.btn-print:hover{background:#6b0000;}

.summary-group{margin-left:auto;display:flex;gap:8px;}
.sbox{padding:4px 12px;text-align:center;font-size:11px;border-radius:4px;}
.sbox span{display:block;font-weight:bold;font-size:13px;}
.sbox.total{background:#fff3e0;border:1px solid #ffb74d;}.sbox.total span{color:#e65100;}
.sbox.red{background:#ffebee;border:1px solid #ef9a9a;}.sbox.red span{color:#c62828;}
.sbox.green{background:#e8f5e9;border:1px solid #a5d6a7;}.sbox.green span{color:#2e7d32;}

.filter-bar{background:#f5f5f5;border:1px solid #ddd;padding:5px 10px;margin-bottom:5px;display:flex;gap:10px;align-items:center;flex-wrap:wrap;}
.filter-bar label{font-weight:bold;font-size:11px;}
.filter-bar input,.filter-bar select{border:1px solid #ccc;padding:2px 5px;height:22px;font-size:11px;}

.grid-wrapper{height:300px;overflow:auto;border:1px solid #999;}
table{width:100%;border-collapse:collapse;min-width:1050px;}
th{background:#8b0000;color:#fff;border:1px solid #aa2222;padding:5px 4px;position:sticky;top:0;font-weight:normal;font-size:11px;white-space:nowrap;}
td{border:1px solid #ddd;padding:3px 6px;font-size:11px;}
tr:hover{background:#fff8e1;cursor:pointer;}
tr.selected{background:#8b0000!important;color:#fff;}
tr.selected td{color:#fff;}
.col-money{text-align:right;font-weight:bold;color:#8b0000;}
.col-center{text-align:center;}
.tt-chua{background:#fff3cd;color:#856404;padding:2px 8px;border-radius:10px;font-size:10px;font-weight:700;white-space:nowrap;}
.tt-khau{background:#cfe2ff;color:#084298;padding:2px 8px;border-radius:10px;font-size:10px;font-weight:700;white-space:nowrap;}
.tt-hoan{background:#d1e7dd;color:#0f5132;padding:2px 8px;border-radius:10px;font-size:10px;font-weight:700;white-space:nowrap;}

@media print {
    body * { visibility: hidden; }
    #print-area, #print-area * { visibility: visible; }
    #print-area { position: fixed; left: 0; top: 0; width: 100%; padding: 20px; }
}
#print-area { display: none; }
</style>
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="main-container">
<div class="title">📄 QUẢN LÝ PHIẾU TẠM ỨNG LƯƠNG</div>

<form id="mainForm" method="POST" action="save_tam_ung.php">
<input type="hidden" name="action"  id="form_action"  value="add">
<input type="hidden" name="edit_id" id="form_edit_id" value="">

<div class="form-panel">

  <div class="form-col">
    <div class="col-title">📋 THÔNG TIN PHIẾU</div>
    <div class="field-row">
      <label>Số phiếu:</label>
      <input type="text" name="so_phieu" id="so_phieu" value="<?=$next_so?>" readonly>
    </div>
    <div class="field-row">
      <label>Ngày tạm ứng:</label>
      <input type="date" name="ngay_tam_ung" id="ngay_tam_ung" value="<?=date('Y-m-d')?>" required>
    </div>
    <div class="field-row">
      <label>Đối tượng:</label>
      <select name="doi_tuong" id="doi_tuong" onchange="loadDanhSach(this.value)">
        <option value="Lái xe">👨‍✈️ Lái xe</option>
        <option value="Nhân viên">👤 Nhân viên</option>
      </select>
    </div>
    <div class="field-row">
      <label>Người tạm ứng:</label>
      <select name="nguoi_tam_ung" id="nguoi_tam_ung" onchange="loadChucVu(this)">
        <option value="">-- Chọn --</option>
        <?php
        $lx = $conn->query("SELECT ten_lai_xe FROM danh_muc_lai_xe WHERE da_nghi=0 ORDER BY ten_lai_xe");
        while($r=$lx->fetch_assoc())
            echo "<option value='{$r['ten_lai_xe']}' data-cv='Lái xe'>{$r['ten_lai_xe']}</option>";
        ?>
      </select>
    </div>
    <div class="field-row">
      <label>Chức vụ:</label>
      <input type="text" name="chuc_vu" id="chuc_vu" readonly style="background:#f9f9f9;">
    </div>
  </div>

  <div class="form-col">
    <div class="col-title">💰 NỘI DUNG TẠM ỨNG</div>
    <div class="field-row">
      <label>Số tiền tạm ứng:</label>
      <input type="text" name="so_tien" id="so_tien" onkeyup="formatNum(this)"
        placeholder="0" style="font-weight:bold;color:#c62828;font-size:14px;">
    </div>
    <div class="field-row">
      <label>Lý do tạm ứng:</label>
      <input type="text" name="ly_do" id="ly_do" placeholder="VD: Tạm ứng lương tháng 4...">
    </div>
    <div class="field-row">
      <label>Khấu trừ tháng:</label>
      <select name="thang_khau" id="thang_khau" style="width:70px;flex:none;margin-right:5px;">
        <?php for($m=1;$m<=12;$m++) echo "<option value='$m' ".($m==date('n')?'selected':'').">T$m</option>"; ?>
      </select>
      <input type="number" name="nam_khau" id="nam_khau" value="<?=date('Y')?>" style="width:65px;flex:none;">
    </div>
    <div class="field-row">
      <label>Người duyệt:</label>
      <input type="text" name="nguoi_duyet" id="nguoi_duyet"
        value="<?=htmlspecialchars($_SESSION['fullname']??'')?>" placeholder="Tên người duyệt">
    </div>
    <div class="field-row">
      <label>Ghi chú:</label>
      <input type="text" name="ghi_chu" id="ghi_chu">
    </div>
  </div>

  <div class="form-col">
    <div class="col-title">📊 TRẠNG THÁI & XỬ LÝ</div>
    <div class="field-row">
      <label>Trạng thái:</label>
      <select name="trang_thai" id="trang_thai">
        <option value="Chưa hoàn trả">⏳ Chưa hoàn trả</option>
        <option value="Đã khấu trừ">💳 Đã khấu trừ vào lương</option>
        <option value="Đã hoàn trả">✅ Đã hoàn trả</option>
      </select>
    </div>
    <div class="field-row">
      <label>Ngày hoàn trả:</label>
      <input type="date" name="ngay_hoan_tra" id="ngay_hoan_tra">
    </div>

    <div style="margin-top:12px;background:#fff3e0;border:1px solid #ffb74d;padding:8px 10px;border-radius:4px;">
      <div style="font-size:11px;font-weight:bold;color:#e65100;margin-bottom:4px;">💰 Tổng đang tạm ứng chưa hoàn trả:</div>
      <div id="du_no_display" style="font-size:16px;font-weight:bold;color:#c62828;">-- Chọn người trước --</div>
    </div>

    <div style="margin-top:8px;">
      <button type="button" class="btn btn-print" onclick="printPhieu()" style="width:100%;justify-content:center;">
        🖨️ In phiếu tạm ứng
      </button>
    </div>
  </div>

</div>

<div class="filter-bar">
  <label>🔍 Lọc:</label>
  <form method="GET" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
    <label>Năm:</label>
    <input type="number" name="nam" value="<?=$f_nam?>" style="width:70px;">
    <label>Tháng:</label>
    <select name="thang">
      <option value="">-- Tất cả --</option>
      <?php for($m=1;$m<=12;$m++) echo "<option value='$m' ".($m==$f_thang?'selected':'').">T$m</option>"; ?>
    </select>
    <label>Đối tượng:</label>
    <select name="dt">
      <option value="">-- Tất cả --</option>
      <option value="Lái xe" <?=$f_dt=='Lái xe'?'selected':''?>>Lái xe</option>
      <option value="Nhân viên" <?=$f_dt=='Nhân viên'?'selected':''?>>Nhân viên</option>
    </select>
    <label>Trạng thái:</label>
    <select name="tt">
      <option value="">-- Tất cả --</option>
      <option value="Chưa hoàn trả" <?=$f_tt=='Chưa hoàn trả'?'selected':''?>>Chưa hoàn trả</option>
      <option value="Đã khấu trừ" <?=$f_tt=='Đã khấu trừ'?'selected':''?>>Đã khấu trừ</option>
      <option value="Đã hoàn trả" <?=$f_tt=='Đã hoàn trả'?'selected':''?>>Đã hoàn trả</option>
    </select>
    <button type="submit" class="btn" style="color:#8b0000;padding:3px 10px;font-size:11px;">🔍 Lọc</button>
    <a href="tam_ung_luong.php" class="btn" style="padding:3px 10px;font-size:11px;">↩ Bỏ lọc</a>
  </form>
</div>

<div class="toolbar">
  <button type="button" class="btn" style="color:green" onclick="submitAdd()">✚ Thêm (Alt+A)</button>
  <button type="button" class="btn" style="color:blue"  onclick="submitEdit()">💾 Sửa (Alt+E)</button>
  <button type="button" class="btn" style="color:red"   onclick="deleteRow()">✖ Xóa (Alt+D)</button>
  <button type="button" class="btn"                     onclick="clearForm()">🔄 Làm mới</button>
  <button type="button" class="btn"                     onclick="window.location.href='index.php'">🚪 Thoát</button>
  <div class="summary-group">
    <div class="sbox total">Tổng phiếu<span id="s_cnt"><?=$sum['cnt']??0?></span></div>
    <div class="sbox total">Tổng tạm ứng<span id="s_tong"><?=number_format($sum['tong']??0)?> đ</span></div>
    <div class="sbox red">Chưa hoàn trả<span id="s_chua"><?=number_format($sumCT['tong']??0)?> đ</span></div>
  </div>
</div>
</form>

<div class="grid-wrapper">
<table id="mainTable">
<thead><tr>
  <th width="35">Stt</th>
  <th width="35">Chọn</th>
  <th width="110">Số phiếu</th>
  <th width="85">Ngày TU</th>
  <th width="75">Đối tượng</th>
  <th width="140">Người tạm ứng</th>
  <th width="100">Chức vụ</th>
  <th width="140">Lý do</th>
  <th width="110">Số tiền TU</th>
  <th width="80">Khấu tháng</th>
  <th width="110">Người duyệt</th>
  <th width="110">Trạng thái</th>
  <th width="85">Ngày HT</th>
  <th>Ghi chú</th>
</tr></thead>
<tbody id="tableBody">
<?php $stt=1; while($r=$res->fetch_assoc()):
  $tt_class = match($r['trang_thai']) {
    'Đã khấu trừ' => 'tt-khau',
    'Đã hoàn trả' => 'tt-hoan',
    default        => 'tt-chua'
  };
?>
<tr onclick="selectRow(this)"
  data-id="<?=$r['id']?>"
  data-sophieu="<?=htmlspecialchars($r['so_phieu']??'')?>"
  data-ngay="<?=$r['ngay_tam_ung']?>"
  data-dt="<?=htmlspecialchars($r['doi_tuong']??'')?>"
  data-nguoi="<?=htmlspecialchars($r['nguoi_tam_ung']??'')?>"
  data-cv="<?=htmlspecialchars($r['chuc_vu']??'')?>"
  data-lydo="<?=htmlspecialchars($r['ly_do']??'')?>"
  data-tien="<?=$r['so_tien']?>"
  data-thang="<?=$r['thang_khau']?>"
  data-nam="<?=$r['nam_khau']?>"
  data-duyet="<?=htmlspecialchars($r['nguoi_duyet']??'')?>"
  data-tt="<?=htmlspecialchars($r['trang_thai']??'')?>"
  data-ngayht="<?=$r['ngay_hoan_tra']??''?>"
  data-ghichu="<?=htmlspecialchars($r['ghi_chu']??'')?>">
  <td class="col-center"><?=$stt++?></td>
  <td class="col-center"><input type="radio" name="row_select" value="<?=$r['id']?>" onclick="event.stopPropagation();"></td>
  <td class="col-center"><b style="color:#8b0000;"><?=htmlspecialchars($r['so_phieu']??'')?></b></td>
  <td class="col-center"><?=date('d/m/Y',strtotime($r['ngay_tam_ung']))?></td>
  <td class="col-center"><?=htmlspecialchars($r['doi_tuong']??'')?></td>
  <td><b><?=htmlspecialchars($r['nguoi_tam_ung']??'')?></b></td>
  <td><?=htmlspecialchars($r['chuc_vu']??'')?></td>
  <td><?=htmlspecialchars($r['ly_do']??'')?></td>
  <td class="col-money"><?=number_format($r['so_tien'])?></td>
  <td class="col-center">T<?=$r['thang_khau']?>/<?=$r['nam_khau']?></td>
  <td><?=htmlspecialchars($r['nguoi_duyet']??'')?></td>
  <td class="col-center"><span class="<?=$tt_class?>"><?=$r['trang_thai']?></span></td>
  <td class="col-center"><?=$r['ngay_hoan_tra']?date('d/m/Y',strtotime($r['ngay_hoan_tra'])):''?></td>
  <td><?=htmlspecialchars($r['ghi_chu']??'')?></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>

</div>

<div id="print-area">
  <div style="text-align:center;margin-bottom:15px;">
    <div style="font-size:14px;font-weight:bold;text-transform:uppercase;">Công ty TNHH Vận tải và Du lịch Hà Linh</div>
    <div style="font-size:11px;color:#555;">Số 33/23 Chợ Hàng, P. Dư Hàng Kênh, Q. Lê Chân, TP. Hải Phòng | ĐT: 0983.188.003</div>
    <div style="height:1px;background:#333;margin:8px 0;"></div>
    <div style="font-size:18px;font-weight:bold;letter-spacing:2px;margin:10px 0;">PHIẾU TẠM ỨNG LƯƠNG</div>
    <div style="font-size:12px;" id="p_sophieu_title"></div>
  </div>

  <table style="width:100%;border-collapse:collapse;font-size:13px;margin-bottom:15px;">
    <tr>
      <td style="width:50%;padding:6px 0;vertical-align:top;">
        <table style="width:100%;">
          <tr><td style="width:140px;font-weight:bold;">Số phiếu:</td><td id="p_sophieu" style="color:#8b0000;font-weight:bold;"></td></tr>
          <tr><td style="font-weight:bold;">Ngày tạm ứng:</td><td id="p_ngay"></td></tr>
          <tr><td style="font-weight:bold;">Người tạm ứng:</td><td id="p_nguoi" style="font-weight:bold;font-size:14px;"></td></tr>
          <tr><td style="font-weight:bold;">Chức vụ:</td><td id="p_cv"></td></tr>
        </table>
      </td>
      <td style="width:50%;padding:6px 0;vertical-align:top;">
        <table style="width:100%;">
          <tr><td style="width:140px;font-weight:bold;">Số tiền tạm ứng:</td><td id="p_tien" style="color:#c62828;font-weight:bold;font-size:15px;"></td></tr>
          <tr><td style="font-weight:bold;">Bằng chữ:</td><td id="p_bangchu" style="font-style:italic;"></td></tr>
          <tr><td style="font-weight:bold;">Khấu trừ tháng:</td><td id="p_khau"></td></tr>
          <tr><td style="font-weight:bold;">Người duyệt:</td><td id="p_duyet"></td></tr>
        </table>
      </td>
    </tr>
  </table>

  <div style="background:#fff8f0;border:1px solid #f0c070;padding:10px 14px;border-radius:4px;margin-bottom:20px;">
    <b>Lý do tạm ứng:</b> <span id="p_lydo"></span>
  </div>

  <div style="display:flex;justify-content:space-between;margin-top:30px;text-align:center;">
    <div style="width:22%;">
      <div style="font-weight:bold;margin-bottom:55px;">Người tạm ứng</div>
      <div style="border-top:1px solid #333;">(Ký, họ tên)</div>
    </div>
    <div style="width:22%;">
      <div style="font-weight:bold;margin-bottom:55px;">Kế toán</div>
      <div style="border-top:1px solid #333;">(Ký, họ tên)</div>
    </div>
    <div style="width:22%;">
      <div style="font-weight:bold;margin-bottom:55px;">Người duyệt</div>
      <div style="border-top:1px solid #333;">(Ký, họ tên)</div>
    </div>
    <div style="width:22%;">
      <div style="font-weight:bold;margin-bottom:35px;">Giám đốc</div>
      <div style="font-size:11px;color:#666;">(Ký, họ tên, đóng dấu)</div>
      <div style="border-top:1px solid #333;margin-top:20px;"></div>
    </div>
  </div>

  <div style="margin-top:25px;border-top:2px dashed #ccc;padding-top:12px;font-size:11px;color:#666;text-align:center;">
    Phiếu in ngày <?=date('d/m/Y H:i')?> — Hệ thống Hà Linh Transport
  </div>
</div>
<script>
let selectedRow = null;

function loadDanhSach(dt) {
    fetch('xuly_ajax.php?type=get_nv&dt=' + encodeURIComponent(dt))
    .then(r => r.json()).then(arr => {
        const sel = document.getElementById('nguoi_tam_ung');
        sel.innerHTML = '<option value="">-- Chọn --</option>';
        arr.forEach(item => {
            sel.innerHTML += `<option value="${item.ten}" data-cv="${item.cv}">${item.ten}</option>`;
        });
        document.getElementById('chuc_vu').value = '';
        document.getElementById('du_no_display').innerText = '-- Chọn người trước --';
    }).catch(() => {});
}
function loadChucVu(sel) {
    const opt = sel.options[sel.selectedIndex];
    document.getElementById('chuc_vu').value = opt.dataset.cv || '';
    const nguoi = sel.value;
    if (nguoi) loadDuNo(nguoi);
}
function loadDuNo(nguoi) {
    fetch('xuly_ajax.php?type=get_du_no_tamung&nguoi=' + encodeURIComponent(nguoi))
    .then(r => r.json()).then(d => {
        const el = document.getElementById('du_no_display');
        el.innerText = fmtStr(d.tong || 0) + ' đ';
        el.style.color = (d.tong || 0) > 0 ? '#c62828' : '#2e7d32';
    }).catch(() => {});
}
function selectRow(row) {
    document.querySelectorAll('#mainTable tbody tr').forEach(r => r.classList.remove('selected'));
    row.classList.add('selected'); selectedRow = row;
    row.querySelector('input[type="radio"]').checked = true;
    const d = row.dataset;
    document.getElementById('so_phieu').value       = d.sophieu;
    document.getElementById('ngay_tam_ung').value   = d.ngay;
    document.getElementById('ly_do').value          = d.lydo;
    document.getElementById('so_tien').value        = fmtStr(d.tien);
    document.getElementById('nguoi_duyet').value    = d.duyet;
    document.getElementById('ghi_chu').value        = d.ghichu;
    document.getElementById('ngay_hoan_tra').value  = d.ngayht;
    document.getElementById('form_edit_id').value   = d.id;
    setSelect('doi_tuong', d.dt);
    setSelect('thang_khau', d.thang);
    setSelect('trang_thai', d.tt);
    document.getElementById('nam_khau').value = d.nam;
    document.getElementById('chuc_vu').value  = d.cv;
    loadDanhSach(d.dt);
    setTimeout(() => {
        setSelect('nguoi_tam_ung', d.nguoi);
        loadDuNo(d.nguoi);
    }, 300);
}
function setSelect(id, val) {
    const s = document.getElementById(id);
    if (!s) return;
    for (let i = 0; i < s.options.length; i++)
        if (s.options[i].value == val) { s.selectedIndex = i; return; }
}
function submitAdd() {
    document.getElementById('form_action').value = 'add';
    document.getElementById('form_edit_id').value = '';
    document.getElementById('mainForm').submit();
}
function submitEdit() {
    if (!selectedRow) { alert('Vui lòng chọn một dòng để sửa!'); return; }
    document.getElementById('form_action').value = 'edit';
    document.getElementById('mainForm').submit();
}
function deleteRow() {
    const r = document.querySelector('input[name="row_select"]:checked');
    if (!r) { alert('Vui lòng chọn một dòng để xóa!'); return; }
    if (confirm('Xác nhận xóa phiếu tạm ứng này?'))
        window.location.href = 'xoa_tam_ung.php?id=' + r.value;
}
function clearForm() {
    document.getElementById('mainForm').reset();
    document.getElementById('ngay_tam_ung').value = new Date().toISOString().split('T')[0];
    document.getElementById('form_edit_id').value = '';
    document.getElementById('form_action').value = 'add';
    document.getElementById('du_no_display').innerText = '-- Chọn người trước --';
    document.querySelectorAll('#mainTable tbody tr').forEach(r => r.classList.remove('selected'));
    selectedRow = null;
}
function printPhieu() {
    const nguoi  = document.getElementById('nguoi_tam_ung').value;
    const sophieu= document.getElementById('so_phieu').value;
    const ngay   = document.getElementById('ngay_tam_ung').value;
    const tien   = parseNum(document.getElementById('so_tien').value);
    const lydo   = document.getElementById('ly_do').value;
    const cv     = document.getElementById('chuc_vu').value;
    const duyet  = document.getElementById('nguoi_duyet').value;
    const thang  = document.getElementById('thang_khau').value;
    const nam    = document.getElementById('nam_khau').value;

    if (!nguoi || !tien) { alert('Vui lòng nhập người tạm ứng và số tiền trước khi in!'); return; }

    document.getElementById('p_sophieu').innerText       = sophieu;
    document.getElementById('p_sophieu_title').innerText = 'Số phiếu: ' + sophieu;
    document.getElementById('p_nguoi').innerText         = nguoi;
    document.getElementById('p_cv').innerText            = cv;
    document.getElementById('p_ngay').innerText          = formatDate(ngay);
    document.getElementById('p_tien').innerText          = fmtStr(tien) + ' đồng';
    document.getElementById('p_bangchu').innerText       = docTien(tien);
    document.getElementById('p_lydo').innerText          = lydo || '(Không có lý do)';
    document.getElementById('p_duyet').innerText         = duyet;
    document.getElementById('p_khau').innerText          = 'Tháng ' + thang + '/' + nam;

    document.getElementById('print-area').style.display = 'block';
    window.print();
    document.getElementById('print-area').style.display = 'none';
}
function formatDate(s) {
    if (!s) return '';
    const p = s.split('-');
    return `ngày ${p[2]} tháng ${p[1]} năm ${p[0]}`;
}
function docTien(n) {
    if (!n || n == 0) return 'Không đồng';
    const dvs = ['', 'nghìn', 'triệu', 'tỷ'];
    const chu = ['không','một','hai','ba','bốn','năm','sáu','bảy','tám','chín'];
    function doc3(x) {
        let s = '';
        const h = Math.floor(x/100), t = Math.floor((x%100)/10), dv = x%10;
        if (h) s += chu[h] + ' trăm ';
        if (t == 1) s += 'mười ';
        else if (t > 1) s += chu[t] + ' mươi ';
        if (dv == 5 && t > 1) s += 'lăm ';
        else if (dv == 1 && t > 1) s += 'mốt ';
        else if (dv > 0) s += chu[dv] + ' ';
        return s.trim();
    }
    let groups = [], tmp = Math.round(n);
    while (tmp > 0) { groups.push(tmp % 1000); tmp = Math.floor(tmp / 1000); }
    let result = '';
    for (let i = groups.length - 1; i >= 0; i--) {
        if (groups[i] > 0) result += doc3(groups[i]) + (dvs[i] ? ' ' + dvs[i] : '') + ' ';
    }
    return result.trim().charAt(0).toUpperCase() + result.trim().slice(1) + ' đồng chẵn';
}
function parseNum(s) { 
    return parseInt(String(s).replace(/\./g,''))||0; 
}
function fmtStr(n)   { 
    return Math.round(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g,'.'); 
}
function formatNum(i){ 
    let v=i.value.replace(/\D/g,''); 
    i.value=v.replace(/\B(?=(\d{3})+(?!\d))/g,'.'); 
}
document.addEventListener('keydown', e => {
    if (e.altKey) {
        if (e.key.toLowerCase()==='a') { e.preventDefault(); submitAdd(); }
        if (e.key.toLowerCase()==='e') { e.preventDefault(); submitEdit(); }
        if (e.key.toLowerCase()==='d') { e.preventDefault(); deleteRow(); }
    }
    if (e.key === 'Escape') window.location.href = 'index.php';
});
</script>
</body>
</html>