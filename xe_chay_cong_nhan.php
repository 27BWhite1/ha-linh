<?php
include 'conn.php';
session_start();
// Tự động thêm cột mới nếu chưa có
$conn->query("ALTER TABLE xe_chay_cong_nhan ADD COLUMN IF NOT EXISTS `chu_xe` varchar(100) DEFAULT NULL");
$conn->query("ALTER TABLE xe_chay_cong_nhan ADD COLUMN IF NOT EXISTS `ten_thue_lai` varchar(100) DEFAULT NULL");
$conn->query("ALTER TABLE xe_chay_cong_nhan ADD COLUMN IF NOT EXISTS `tien_thue_lai` decimal(15,0) DEFAULT 0");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý xe chạy Công nhân – Hà Linh</title>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Be Vietnam Pro', sans-serif; background: #f1f3f8; color: #1a1f36; min-height: 100vh; }

        /* ── Page wrapper ── */
        .page-wrapper { margin: 20px 20px; padding-bottom: 32px; }

        .breadcrumb { display: flex; align-items: center; gap: 6px; margin-bottom: 14px; font-size: 12px; color: #9ca3af; }
        .breadcrumb a { color: #be0000; text-decoration: none; font-weight: 500; }

        /* ── Card ── */
        .card { background: #fff; border-radius: 16px; box-shadow: 0 4px 24px rgba(0,0,0,.07); overflow: hidden; margin-bottom: 16px; }

        /* ── Card header ── */
        .card-header {
            background: linear-gradient(135deg, #be0000 0%, #7b0000 100%);
            padding: 20px 28px 16px; position: relative; overflow: hidden;
        }
        .card-header::after { content: ''; position: absolute; right: -40px; top: -40px; width: 180px; height: 180px; border-radius: 50%; background: rgba(255,255,255,.07); }
        .card-header h1 { color: #fff; font-size: 18px; font-weight: 700; position: relative; }
        .card-header p  { color: rgba(255,255,255,.65); font-size: 11px; margin-top: 3px; position: relative; }

        /* ── Form area ── */
        .form-card { background: #fff; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,.06); overflow: hidden; margin-bottom: 12px; }
        .form-body  { display: flex; gap: 0; }

        /* Form columns */
        .form-col { flex: 1; padding: 16px 20px; border-right: 1px solid #f3f4f8; }
        .form-col:last-child { border-right: none; }
        .col-title { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #9ca3af; margin-bottom: 12px; }

        .field { display: flex; align-items: center; margin-bottom: 8px; }
        .field label { width: 90px; flex-shrink: 0; font-size: 12px; font-weight: 600; color: #6b7280; }

        input[type="text"], input[type="date"], select, textarea {
            flex: 1; border: 1px solid #e5e7eb; border-radius: 6px;
            padding: 5px 9px; height: 28px; font-size: 12px;
            font-family: inherit; color: #1a1f36; background: #fff;
            transition: border-color .15s, box-shadow .15s;
        }
        input:focus, select:focus { outline: none; border-color: #be0000; box-shadow: 0 0 0 3px rgba(190,0,0,.07); }
        input[readonly] { background: #f8f9fc; color: #9ca3af; }

        /* Search panel */
        .search-panel { width: 280px; flex-shrink: 0; background: #f8f9fc; border-left: 1px solid #f0f0f5; padding: 16px; }
        .search-panel-title { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #9ca3af; margin-bottom: 12px; display: flex; align-items: center; gap: 6px; }
        .search-row { display: flex; align-items: center; gap: 6px; margin-bottom: 8px; }
        .search-row input[type="checkbox"] { width: 14px; height: 14px; accent-color: #be0000; flex-shrink: 0; }
        .search-row label { font-size: 11px; color: #6b7280; width: 72px; flex-shrink: 0; font-weight: 500; }
        .search-row select, .search-row input[type="date"] { flex: 1; height: 26px; font-size: 11px; }

        .btn-search {
            width: 100%; margin-top: 8px; height: 32px; cursor: pointer;
            background: linear-gradient(135deg, #be0000, #7b0000);
            color: #fff; border: none; border-radius: 8px;
            font-family: inherit; font-size: 12px; font-weight: 700;
            display: flex; align-items: center; justify-content: center; gap: 5px;
            transition: opacity .15s;
        }
        .btn-search:hover { opacity: .88; }
        .btn-reset {
            width: 100%; margin-top: 5px; height: 28px; cursor: pointer;
            background: #eef0f7; color: #3a4060; border: none; border-radius: 8px;
            font-family: inherit; font-size: 12px; font-weight: 600;
            transition: background .15s;
        }
        .btn-reset:hover { background: #e0e3f0; }

        /* ── Toolbar ── */
        .toolbar { display: flex; gap: 7px; padding: 12px 20px; border-bottom: 1px solid #f0f0f5; flex-wrap: wrap; background: #fff; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,.06); margin-bottom: 12px; }
        .btn {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 7px 16px; border-radius: 8px; font-family: inherit;
            font-size: 12px; font-weight: 600; cursor: pointer; border: none;
            transition: all .18s;
        }
        .btn-add    { background: #be0000; color: #fff; }
        .btn-add:hover    { background: #950000; transform: translateY(-1px); box-shadow: 0 4px 10px rgba(190,0,0,.3); }
        .btn-edit   { background: #eef0f7; color: #3a4060; }
        .btn-edit:hover   { background: #e0e3f0; transform: translateY(-1px); }
        .btn-del    { background: #fff0f0; color: #c62828; }
        .btn-del:hover    { background: #ffe0e0; transform: translateY(-1px); }
        .btn-exit   { background: transparent; color: #6b7280; border: 1px solid #e5e7eb; }
        .btn-exit:hover   { background: #f9fafb; }
        .btn-clear  { background: transparent; color: #6b7280; border: 1px solid #e5e7eb; }
        .btn-clear:hover  { background: #f9fafb; }

        /* Summary row */
        .summary-bar {
            margin-left: auto; display: flex; gap: 8px;
        }
        .sbox { background: #f8f9fc; border: 1px solid #eef0f7; border-radius: 8px; padding: 4px 12px; text-align: center; font-size: 10px; color: #9ca3af; }
        .sbox strong { display: block; font-size: 13px; font-weight: 700; color: #1a1f36; margin-top: 1px; }
        .sbox.green { background: #f0fdf4; border-color: #bbf7d0; }
        .sbox.green strong { color: #166534; }

        /* ── Table ── */
        .table-card { background: #fff; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,.06); overflow: hidden; }
        .grid-wrapper { height: calc(100vh - 520px); min-height: 260px; overflow: auto; }
        table { width: 100%; border-collapse: collapse; min-width: 1600px; }

        thead tr { background: #f8f9fc; }
        th {
            padding: 9px 10px; text-align: center;
            font-size: 10.5px; font-weight: 700; text-transform: uppercase;
            letter-spacing: .5px; color: #8892a4;
            border-bottom: 2px solid #eef0f7; border-right: 1px solid #f3f4f8;
            position: sticky; top: 0; background: #f8f9fc; z-index: 10;
            white-space: nowrap;
        }

        tbody tr { border-bottom: 1px solid #f3f4f8; transition: background .1s; cursor: pointer; }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: #fdf5f5; }
        tbody tr.row-selected { background: #fff5f5 !important; }
        tbody tr.row-selected td { color: #1a1f36; }

        td { padding: 9px 10px; font-size: 12px; color: #374151; text-align: center; white-space: nowrap; border-right: 1px solid #f3f4f8; }
        td.text-left { text-align: left; }

        /* Plate */
        .plate { display: inline-block; background: #1a1f36; color: #fff; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 800; font-family: monospace; letter-spacing: 1px; }

        /* ca nối highlight */
        .ca-noi-time { background: #fee2e2; color: #c62828; font-weight: 700; padding: 2px 6px; border-radius: 4px; font-size: 11px; }

        /* money */
        .money { font-weight: 700; color: #1a1f36; text-align: right !important; }

        .bg-red { background: #fee2e2 !important; }
        .table-footer { padding: 8px 20px; border-top: 1px solid #f3f4f8; font-size: 11px; color: #9ca3af; display: flex; justify-content: space-between; }
        .table-footer strong { color: #374151; }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="page-wrapper">
    <div class="breadcrumb">
        <a href="index.php">Trang chủ</a> <span>›</span>
        <span>Quản lý xe chạy</span> <span>›</span>
        <span>Xe chạy Công nhân</span>
    </div>

    <!-- Form card -->
    <div class="form-card">
        <div class="card-header">
            <h1>🚌 Quản lý xe chạy Công nhân</h1>
            <p>Nhập, sửa và theo dõi các chuyến xe công nhân</p>
        </div>

    <form id="mainForm" method="POST" action="save_xe_cong_nhan.php">
        <input type="hidden" name="action" id="form_action" value="add">
        <input type="hidden" name="edit_id" id="form_edit_id" value="">

        <div class="form-body">
            <!-- Cột 1: Thông tin chuyến -->
            <div class="form-col">
                <div class="col-title">📅 Thông tin chuyến</div>
                <div class="field">
                    <label>Ngày chạy:</label>
                    <input type="date" name="ngay_chay" id="ngay_chay" value="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="field">
                    <label>Loại hình xe:</label>
                    <select name="loai_xe" id="loai_xe">
                        <option value="Xe nhà">Xe nhà</option>
                        <option value="Xe thuê">Xe thuê</option>
                    </select>
                </div>
                <div class="field">
                    <label>Tuyến đường:</label>
                    <select name="ma_tuyen" id="ma_tuyen" onchange="loadTuyen(this.value)">
                        <option value="">--Chọn tuyến--</option>
                        <?php
                        $tuyens = $conn->query("SELECT ma_tuyen, ten_tuyen FROM danh_muc_tuyen_duong ORDER BY ma_tuyen ASC");
                        while($t = $tuyens->fetch_assoc()) {
                            echo "<option value='{$t['ma_tuyen']}'>{$t['ten_tuyen']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="field">
                    <label>Giờ đón:</label>
                    <input type="text" name="gio_don" id="gio_don" placeholder="06:00" style="width:70px; flex:none;">
                    <label style="width:50px; text-align:center;">Giờ về:</label>
                    <input type="text" name="gio_ve" id="gio_ve" placeholder="18:00" style="flex:1;">
                </div>
                <div class="field">
                    <label>Khách hàng:</label>
                    <input type="text" name="khach_hang" id="khach_hang" readonly>
                </div>
            </div>

            <!-- Cột 2: Xe & Lái xe -->
            <div class="form-col">
                <div class="col-title">🚗 Xe & Lái xe</div>
                <div class="field">
                    <label>Xe chạy:</label>
                    <select name="bien_so" id="bien_so" onchange="loadXe(this.value)">
                        <option value="">--Chọn xe--</option>
                        <?php
                        $xes = $conn->query("SELECT bien_so FROM danh_muc_xe ORDER BY bien_so ASC");
                        while($x = $xes->fetch_assoc()) {
                            echo "<option value='{$x['bien_so']}'>{$x['bien_so']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="field">
                    <label>Hãng v.tải:</label>
                    <select name="chu_xe" id="chu_xe">
                        <option value="">-- Chọn hãng --</option>
                        <?php
                        $hang_list = $conn->query("SELECT ten_hang FROM danh_muc_hang_van_tai ORDER BY ten_hang ASC");
                        while($h = $hang_list->fetch_assoc()) echo "<option value='{$h['ten_hang']}'>{$h['ten_hang']}</option>";
                        ?>
                    </select>
                </div>
                <div class="field">
                    <label>Lái xe:</label>
                    <input type="text" name="lai_xe" id="lai_xe" placeholder="Tên lái xe">
                </div>
                <div class="field" style="align-items:center; gap:8px;">
                    <input type="checkbox" name="thue_lai" id="thue_lai" style="width:15px;height:15px;accent-color:#be0000;" onchange="toggleThueLaiForm(this)">
                    <span style="font-size:12px;font-weight:600;color:#374151;">Thuê lái</span>
                </div>
                <div id="wrap_ten_thue_lai" style="display:none;">
                    <div class="field">
                        <label>Tên lái thuê:</label>
                        <input type="text" name="ten_thue_lai" id="ten_thue_lai" placeholder="Tên lái xe thuê">
                    </div>
                </div>
                <div id="wrap_tien_thue_lai" style="display:none;">
                    <div class="field">
                        <label>Tiền thuê lái:</label>
                        <input type="text" name="tien_thue_lai" id="tien_thue_lai" placeholder="0" onkeyup="formatNum(this)">
                    </div>
                </div>
            </div>

            <!-- Cột 3: Tài chính -->
            <div class="form-col">
                <div class="col-title">💰 Tài chính</div>
                <div class="field">
                    <label>Cước xe:</label>
                    <input type="text" name="cuoc_xe" id="cuoc_xe" onkeyup="formatNum(this)" placeholder="0">
                </div>
                <div class="field">
                    <label>Lương lái xe:</label>
                    <input type="text" name="luong_lai" id="luong_lai" onkeyup="formatNum(this)" placeholder="0">
                </div>
                <div class="field">
                    <label>Ghi chú:</label>
                    <input type="text" name="ghi_chu" id="ghi_chu">
                </div>
                <div class="field" style="align-items:center; gap:8px; margin-top:4px;">
                    <input type="checkbox" name="ca_noi" id="ca_noi" style="width:15px;height:15px;accent-color:#be0000;">
                    <span style="font-size:12px;font-weight:600;color:#374151;">Là ca nối</span>
                </div>
            </div>

            <!-- Search panel -->
            <div class="search-panel">
                <div class="search-panel-title">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                    Tìm kiếm
                </div>
                <div class="search-row">
                    <input type="checkbox" id="ck_ngay">
                    <label>Theo ngày:</label>
                    <input type="date" id="f_ngay">
                </div>
                <div class="search-row">
                    <input type="checkbox" id="ck_chu">
                    <label>Theo chủ xe:</label>
                    <select id="f_chu">
                        <option value="">-- Tất cả --</option>
                        <?php
                        $chu_list = $conn->query("SELECT ten_hang FROM danh_muc_hang_van_tai ORDER BY ten_hang ASC");
                        while($c = $chu_list->fetch_assoc()) echo "<option>{$c['ten_hang']}</option>";
                        ?>
                    </select>
                </div>
                <div class="search-row">
                    <input type="checkbox" id="ck_kh">
                    <label>Theo KH:</label>
                    <select id="f_kh">
                        <option value="">-- Tất cả --</option>
                        <?php
                        $kh_list = $conn->query("SELECT ten_khachhang FROM danh_muc_khach_hang ORDER BY ten_khachhang ASC");
                        while($k = $kh_list->fetch_assoc()) echo "<option>{$k['ten_khachhang']}</option>";
                        ?>
                    </select>
                </div>
                <div class="search-row">
                    <input type="checkbox" id="ck_xe">
                    <label>Theo xe:</label>
                    <select id="f_xe">
                        <option value="">-- Tất cả --</option>
                        <?php
                        $xe_list = $conn->query("SELECT bien_so FROM danh_muc_xe ORDER BY bien_so ASC");
                        while($xe = $xe_list->fetch_assoc()) echo "<option>{$xe['bien_so']}</option>";
                        ?>
                    </select>
                </div>
                <button type="button" onclick="doSearch()" class="btn-search">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                    Tìm kiếm
                </button>
                <button type="button" onclick="resetSearch()" class="btn-reset">↩ Xem tất cả</button>
            </div>
        </div>
    </form>
    </div><!-- end form-card -->

    <!-- Toolbar -->
    <div class="toolbar">
        <button type="button" class="btn btn-add" onclick="submitAdd()">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
            Thêm (Alt+A)
        </button>
        <button type="button" class="btn btn-edit" onclick="submitEdit()">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            Sửa (Alt+E)
        </button>
        <button type="button" class="btn btn-del" onclick="deleteRow()">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
            Xóa (Alt+D)
        </button>
        <button type="button" class="btn btn-exit" onclick="window.location.href='index.php'">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            Thoát (Esc)
        </button>
        <button type="button" class="btn btn-clear" onclick="clearForm()">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
            Làm mới
        </button>
        <div class="summary-bar">
            <div class="sbox">Tổng chuyến<strong id="sum_count">0</strong></div>
            <div class="sbox">Tổng cước<strong id="sum_cuoc">0</strong></div>
            <div class="sbox green">Tổng lương<strong id="sum_luong">0</strong></div>
        </div>
    </div>

    <!-- Table card -->
    <div class="table-card">
    </form>
    <div class="grid-wrapper">
        <table id="mainTable">
            <thead>
                <tr>
                    <th width="35">Stt</th>
                    <th width="35">Chọn</th>
                    <th width="80">Ngày chạy</th>
                    <th width="120">Khách hàng</th>
                    <th width="40">Xe nhà</th>
                    <th width="110">Hãng v.tải</th>
                    <th width="100">Biển số</th>
                    <th width="150">Tuyến đường</th>
                    <th width="120">Lái xe</th>
                    <th width="65">Giờ đón</th>
                    <th width="65">Giờ về</th>
                    <th width="45">Ca nối</th>
                    <th width="55">Thuê lái</th>
                    <th width="120">Tên thuê lái</th>
                    <th width="100">T.Thuê lái</th>
                    <th width="90">Cước xe</th>
                    <th width="100">Lương lái xe</th>
                    <th>Ghi chú</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                <?php
                $res = $conn->query("SELECT x.*, COALESCE(t.ten_tuyen, x.ma_tuyen) as ten_tuyen_display 
                    FROM xe_chay_cong_nhan x
                    LEFT JOIN danh_muc_tuyen_duong t ON x.ma_tuyen = t.ma_tuyen
                    ORDER BY x.ngay_chay DESC, x.id DESC");
                $stt = 1;
                while($r = $res->fetch_assoc()):
                ?>
                <tr onclick="selectRow(this)"
                    data-id="<?= $r['id'] ?>"
                    data-ngay="<?= $r['ngay_chay'] ?>"
                    data-loai="<?= htmlspecialchars($r['loai_hinh_xe']) ?>"
                    data-tuyen="<?= htmlspecialchars($r['ma_tuyen']) ?>"
                    data-bien="<?= htmlspecialchars($r['bien_so']) ?>"
                    data-lai="<?= htmlspecialchars($r['lai_xe']) ?>"
                    data-kh="<?= htmlspecialchars($r['khach_hang']) ?>"
                    data-gdon="<?= $r['gio_don'] ?>"
                    data-gve="<?= $r['gio_ve'] ?>"
                    data-canoi="<?= $r['ca_noi'] ?>"
                    data-thuê="<?= $r['thue_lai'] ?>"
                    data-chuxe="<?= htmlspecialchars($r['chu_xe'] ?? '') ?>"
                    data-tenthuuelai="<?= htmlspecialchars($r['ten_thue_lai'] ?? '') ?>"
                    data-tienthuuelai="<?= $r['tien_thue_lai'] ?? 0 ?>"
                    data-cuoc="<?= $r['cuoc_xe'] ?>"
                    data-luong="<?= $r['luong_lai_xe'] ?>"
                    data-ghichu="<?= htmlspecialchars($r['ghi_chu'] ?? '') ?>">
                    <td><?= $stt++ ?></td>
                    <td><input type="radio" name="row_select" value="<?= $r['id'] ?>" onclick="event.stopPropagation();"></td>
                    <td><?= date('d/m/Y', strtotime($r['ngay_chay'])) ?></td>
                    <td style="text-align:left;"><?= htmlspecialchars($r['khach_hang']) ?></td>
                    <td><input type="checkbox" disabled <?= $r['loai_hinh_xe'] == 'Xe nhà' ? 'checked' : '' ?>></td>
                    <td style="text-align:left;"><?= htmlspecialchars($r['chu_xe'] ?? '') ?></td>
                    <td><?= htmlspecialchars($r['bien_so']) ?></td>
                    <td style="text-align:left;"><?= htmlspecialchars($r['ten_tuyen_display']) ?></td>
                    <td style="text-align:left;"><?= htmlspecialchars($r['lai_xe']) ?></td>
                    <td><?= $r['gio_don'] ?></td>
                    <td class="<?= $r['ca_noi'] == 1 ? 'bg-red' : '' ?>"><?= $r['gio_ve'] ?></td>
                    <td><input type="checkbox" disabled <?= $r['ca_noi'] == 1 ? 'checked' : '' ?>></td>
                    <td><input type="checkbox" disabled <?= $r['thue_lai'] == 1 ? 'checked' : '' ?>></td>
                    <td style="text-align:left;"><?= htmlspecialchars($r['ten_thue_lai'] ?? '') ?></td>
                    <td style="text-align:right;"><?= number_format($r['tien_thue_lai'] ?? 0) ?></td>
                    <td style="text-align:right;"><?= number_format($r['cuoc_xe']) ?></td>
                    <td class="money"><?= number_format($r['luong_lai_xe']) ?></td>
                    <td class="text-left"><?= htmlspecialchars($r['ghi_chu'] ?? '') ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div><!-- end grid-wrapper -->
    </div><!-- end table-card -->
</div><!-- end page-wrapper -->
<script>
let selectedRow = null;

function selectRow(row) {
    document.querySelectorAll('#mainTable tr').forEach(r => r.classList.remove('row-selected'));
    row.classList.add('row-selected');
    selectedRow = row;
    row.querySelector('input[type="radio"]').checked = true;
    loadRowToForm(row);
}
function toggleThueLaiForm(cb) {
    const wrapTen  = document.getElementById('wrap_ten_thue_lai');
    const wrapTien = document.getElementById('wrap_tien_thue_lai');
    wrapTen.style.display  = cb.checked ? 'flex' : 'none';
    wrapTien.style.display = cb.checked ? 'flex' : 'none';
    if (!cb.checked) {
        document.getElementById('ten_thue_lai').value  = '';
        document.getElementById('tien_thue_lai').value = '';
    }
}
function loadRowToForm(row) {
    const d = row.dataset;
    document.getElementById('ngay_chay').value   = d.ngay;
    document.getElementById('loai_xe').value     = d.loai;
    document.getElementById('khach_hang').value  = d.kh;
    document.getElementById('gio_don').value     = d.gdon;
    document.getElementById('gio_ve').value      = d.gve;
    document.getElementById('lai_xe').value      = d.lai;
    document.getElementById('ghi_chu').value     = d.ghichu;
    document.getElementById('ca_noi').checked    = d.canoi == '1';
    document.getElementById('form_edit_id').value = d.id;
    document.getElementById('cuoc_xe').value  = parseInt(d.cuoc || 0).toLocaleString('vi-VN').replace(/,/g, '.');
    document.getElementById('luong_lai').value = parseInt(d.luong || 0).toLocaleString('vi-VN').replace(/,/g, '.');
    // Hãng vận tải
    setSelectValue('chu_xe', d.chuxe);
    // Thuê lái
    const thueLai = d.thuê == '1';
    document.getElementById('thue_lai').checked = thueLai;
    document.getElementById('wrap_ten_thue_lai').style.display  = thueLai ? 'flex' : 'none';
    document.getElementById('wrap_tien_thue_lai').style.display = thueLai ? 'flex' : 'none';
    document.getElementById('ten_thue_lai').value  = d.tenthuuelai || '';
    document.getElementById('tien_thue_lai').value = parseInt(d.tienthuuelai || 0).toLocaleString('vi-VN').replace(/,/g, '.');
    setSelectValue('ma_tuyen', d.tuyen);
    setSelectValue('bien_so', d.bien);
}
function setSelectValue(id, val) {
    const sel = document.getElementById(id);
    for (let i = 0; i < sel.options.length; i++) {
        if (sel.options[i].value == val) { sel.selectedIndex = i; break; }
    }
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
    const radio = document.querySelector('input[name="row_select"]:checked');
    if (!radio) { alert('Vui lòng chọn một dòng để xóa!'); return; }
    if (confirm('Bạn có chắc muốn xóa bản ghi này không?')) {
        window.location.href = 'xoa_xe_cong_nhan.php?id=' + radio.value;
    }
}
function clearForm() {
    document.getElementById('mainForm').reset();
    document.getElementById('ngay_chay').value = new Date().toISOString().split('T')[0];
    document.getElementById('form_edit_id').value = '';
    document.getElementById('form_action').value = 'add';
    document.getElementById('wrap_ten_thue_lai').style.display  = 'none';
    document.getElementById('wrap_tien_thue_lai').style.display = 'none';
    document.querySelectorAll('#mainTable tr').forEach(r => r.classList.remove('row-selected'));
    selectedRow = null;
}
function loadTuyen(ma) {
    if (!ma) return;
    fetch('xuly_ajax.php?type=get_tuyen&ma=' + encodeURIComponent(ma))
    .then(res => res.json()).then(d => {
        if (!d) return;
        document.getElementById('khach_hang').value = d.khach_hang || '';
        document.getElementById('gio_don').value    = d.gio_don || '';
        document.getElementById('gio_ve').value     = d.gio_ve || '';
        document.getElementById('cuoc_xe').value    = formatNumStr(d.don_gia_16 || 0);
        document.getElementById('luong_lai').value  = formatNumStr(d.luong_xe_16 || 0);
    }).catch(() => {});
}
function loadXe(bs) {
    if (!bs) return;
    fetch('xuly_ajax.php?type=get_xe&bs=' + encodeURIComponent(bs))
    .then(res => res.json()).then(d => {
        if (!d) return;
        document.getElementById('lai_xe').value = d.ten_lai_xe || '';
    }).catch(() => {});
}
function doSearch() {
    const ckNgay = document.getElementById('ck_ngay').checked;
    const ckChu  = document.getElementById('ck_chu').checked;
    const ckKH   = document.getElementById('ck_kh').checked;
    const ckXe   = document.getElementById('ck_xe').checked;

    const fNgay = document.getElementById('f_ngay').value;
    const fChu  = document.getElementById('f_chu').value.toUpperCase();
    const fKH   = document.getElementById('f_kh').value.toUpperCase();
    const fXe   = document.getElementById('f_xe').value.toUpperCase();

    const rows = document.querySelectorAll('#tableBody tr');
    rows.forEach(row => {
        const cells = row.getElementsByTagName('td');
        let show = true;

        if (ckNgay && fNgay) {
            const ngay = cells[2]?.innerText.trim();
            const parts = ngay.split('/');
            const ngayISO = parts[2] + '-' + parts[1] + '-' + parts[0];
            if (ngayISO !== fNgay) show = false;
        }
        if (ckKH && fKH) {
            if (!cells[3]?.innerText.toUpperCase().includes(fKH)) show = false;
        }
        if (ckXe && fXe) {
            if (!cells[5]?.innerText.toUpperCase().includes(fXe)) show = false;
        }
        row.style.display = show ? '' : 'none';
    });
    updateSummary();
}
function resetSearch() {
    document.querySelectorAll('#tableBody tr').forEach(r => r.style.display = '');
    ['ck_ngay','ck_chu','ck_kh','ck_xe'].forEach(id => document.getElementById(id).checked = false);
    updateSummary();
}
function formatNumStr(n) {
    return parseInt(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}
function toggleThueLaiForm(cb) {
    const wrapTen  = document.getElementById('wrap_ten_thue_lai');
    const wrapTien = document.getElementById('wrap_tien_thue_lai');
    wrapTen.style.display  = cb.checked ? 'block' : 'none';
    wrapTien.style.display = cb.checked ? 'block' : 'none';
    if (!cb.checked) {
        document.getElementById('ten_thue_lai').value  = '';
        document.getElementById('tien_thue_lai').value = '';
    }
}
function updateSummary() {
    let count = 0, cuoc = 0, luong = 0;
    document.querySelectorAll('#tableBody tr').forEach(tr => {
        if (tr.style.display === 'none') return;
        count++;
        const cells = tr.getElementsByTagName('td');
        cuoc  += parseNum(cells[cells.length - 4]?.innerText || '0');
        luong += parseNum(cells[cells.length - 3]?.innerText || '0');
    });
    document.getElementById('sum_count').innerText  = count;
    document.getElementById('sum_cuoc').innerText   = fmtNum(cuoc);
    document.getElementById('sum_luong').innerText  = fmtNum(luong);
}
function parseNum(s){ return parseInt(String(s).replace(/\./g,''))||0; }
function fmtNum(n){ return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g,'.'); }

document.addEventListener('keydown', function(e) {
    if (e.altKey) {
        if (e.key.toLowerCase() === 'a') { e.preventDefault(); submitAdd(); }
        if (e.key.toLowerCase() === 'e') { e.preventDefault(); submitEdit(); }
        if (e.key.toLowerCase() === 'd') { e.preventDefault(); deleteRow(); }
    }
    if (e.key === 'Escape') { window.location.href = 'index.php'; }
});
window.addEventListener('load', updateSummary);
</script>
</body>
</html>