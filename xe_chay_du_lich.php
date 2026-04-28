<?php
include 'conn.php';
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: dn.php"); exit(); }
$conn->query("CREATE TABLE IF NOT EXISTS `xe_chay_du_lich` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `ngay_di` date DEFAULT NULL,
    `ngay_ve` date DEFAULT NULL,
    `hanh_trinh` varchar(500) DEFAULT NULL,
    `khach_hang` varchar(255) DEFAULT NULL,
    `bien_so` varchar(20) DEFAULT NULL,
    `loai_xe` varchar(50) DEFAULT NULL,
    `lai_xe` varchar(100) DEFAULT NULL,
    `ten_thue_lai` varchar(100) DEFAULT NULL,
    `thue_lai` tinyint(1) DEFAULT 0,
    `cuoc_xe` decimal(15,0) DEFAULT 0,
    `luong_lai_xe` decimal(15,0) DEFAULT 0,
    `phan_tram_luong` int(11) DEFAULT 0,
    `tien_thue_xe` decimal(15,0) DEFAULT 0,
    `tien_thue_lai` decimal(15,0) DEFAULT 0,
    `lai_xe_thu` decimal(15,0) DEFAULT 0,
    `lai_xe_nop` decimal(15,0) DEFAULT 0,
    `lai_xe_chi` decimal(15,0) DEFAULT 0,
    `chu_xe` varchar(100) DEFAULT NULL,
    `loai_hinh` varchar(50) DEFAULT 'Xe nhà',
    `ghi_chu` text DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");
// Tự động migrate
$conn->query("ALTER TABLE xe_chay_du_lich ADD COLUMN IF NOT EXISTS `hanh_trinh` varchar(500) DEFAULT NULL");
$conn->query("ALTER TABLE xe_chay_du_lich ADD COLUMN IF NOT EXISTS `cuoc_xe` decimal(15,0) DEFAULT 0");
$conn->query("ALTER TABLE xe_chay_du_lich ADD COLUMN IF NOT EXISTS `phan_tram_luong` int(11) DEFAULT 0");
$conn->query("ALTER TABLE xe_chay_du_lich ADD COLUMN IF NOT EXISTS `tien_thue_xe` decimal(15,0) DEFAULT 0");
$conn->query("ALTER TABLE xe_chay_du_lich ADD COLUMN IF NOT EXISTS `tien_thue_lai` decimal(15,0) DEFAULT 0");
$conn->query("ALTER TABLE xe_chay_du_lich ADD COLUMN IF NOT EXISTS `ten_thue_lai` varchar(100) DEFAULT NULL");
$conn->query("ALTER TABLE xe_chay_du_lich ADD COLUMN IF NOT EXISTS `thue_lai` tinyint(1) DEFAULT 0");
$conn->query("ALTER TABLE xe_chay_du_lich ADD COLUMN IF NOT EXISTS `lai_xe_thu` decimal(15,0) DEFAULT 0");
$conn->query("ALTER TABLE xe_chay_du_lich ADD COLUMN IF NOT EXISTS `lai_xe_nop` decimal(15,0) DEFAULT 0");
$conn->query("ALTER TABLE xe_chay_du_lich ADD COLUMN IF NOT EXISTS `lai_xe_chi` decimal(15,0) DEFAULT 0");
$conn->query("ALTER TABLE xe_chay_du_lich DROP COLUMN IF EXISTS `ten_doan`");
$conn->query("ALTER TABLE xe_chay_du_lich DROP COLUMN IF EXISTS `diem_don`");
$conn->query("ALTER TABLE xe_chay_du_lich DROP COLUMN IF EXISTS `diem_tra`");
$conn->query("ALTER TABLE xe_chay_du_lich DROP COLUMN IF EXISTS `so_khach`");
$conn->query("ALTER TABLE xe_chay_du_lich DROP COLUMN IF EXISTS `huong_dan_vien`");
$conn->query("ALTER TABLE xe_chay_du_lich DROP COLUMN IF EXISTS `tien_hdv`");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý xe chạy Du lịch – Hà Linh</title>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Be Vietnam Pro', sans-serif; background: #f1f3f8; color: #1a1f36; font-size: 12px; }

        /* ── Page ── */
        .page-wrapper { margin: 20px; padding-bottom: 32px; }
        .breadcrumb { display: flex; align-items: center; gap: 6px; margin-bottom: 14px; font-size: 12px; color: #9ca3af; }
        .breadcrumb a { color: #be0000; text-decoration: none; font-weight: 500; }

        /* ── Card ── */
        .main-container { background: #fff; border-radius: 16px; box-shadow: 0 4px 24px rgba(0,0,0,.07); overflow: hidden; margin-bottom: 12px; }

        /* ── Card header ── */
        .title {
            background: linear-gradient(135deg, #be0000 0%, #7b0000 100%);
            padding: 18px 28px 14px; position: relative; overflow: hidden;
            color: #fff; font-size: 18px; font-weight: 700; letter-spacing: .3px;
        }
        .title::after { content: ''; position: absolute; right: -40px; top: -40px; width: 180px; height: 180px; border-radius: 50%; background: rgba(255,255,255,.07); }
        .title small { display: block; color: rgba(255,255,255,.6); font-size: 11px; font-weight: 400; margin-top: 3px; }

        /* ── Form panel ── */
        .form-panel {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 0;
            border-bottom: 1px solid #f0f0f5;
        }
        .form-col { padding: 14px 18px; border-right: 1px solid #f0f0f5; }
        .form-col:last-child { border-right: none; }
        .col-title {
            font-size: 10px; font-weight: 700; text-transform: uppercase;
            letter-spacing: 1px; color: #9ca3af; margin-bottom: 12px;
        }

        .field-row { display: flex; align-items: center; margin-bottom: 7px; }
        .field-row label { width: 115px; flex-shrink: 0; font-size: 12px; font-weight: 600; color: #6b7280; }
        .field-row input[type="text"],
        .field-row input[type="date"],
        .field-row input[type="number"],
        .field-row select {
            flex: 1; border: 1px solid #e5e7eb; border-radius: 6px;
            padding: 4px 8px; height: 28px; font-size: 12px;
            font-family: inherit; color: #1a1f36; background: #fff;
            transition: border-color .15s, box-shadow .15s;
        }
        .field-row input:focus, .field-row select:focus { outline: none; border-color: #be0000; box-shadow: 0 0 0 3px rgba(190,0,0,.07); }
        .field-row input[readonly] { background: #f8f9fc; color: #9ca3af; }
        .field-row .inline-group { display: flex; gap: 5px; flex: 1; align-items: center; }
        .field-row .inline-group label { width: auto; }

        /* Lợi nhuận special */
        .field-row input.profit-field {
            background: #f0fdf4; border-color: #bbf7d0; color: #166534; font-weight: 700; font-size: 13px;
        }
        /* Lái xe thu/nộp/chi highlight */
        .field-row input.driver-money {
            background: #eff6ff; border-color: #bfdbfe; color: #1e40af;
        }

        /* ── Filter bar ── */
        .filter-bar {
            background: #f8f9fc; border-bottom: 1px solid #f0f0f5;
            padding: 8px 20px; display: flex; gap: 10px;
            align-items: center; flex-wrap: wrap;
        }
        .filter-bar label { font-size: 11px; font-weight: 600; color: #6b7280; }
        .filter-bar input, .filter-bar select {
            border: 1px solid #e5e7eb; border-radius: 6px;
            padding: 3px 8px; height: 26px; font-size: 11px;
            font-family: inherit; background: #fff;
        }
        .filter-bar input:focus, .filter-bar select:focus { outline: none; border-color: #be0000; }
        .btn-filter-reset {
            padding: 4px 12px; border-radius: 6px; border: 1px solid #e5e7eb;
            background: #fff; color: #6b7280; font-size: 11px; font-weight: 600;
            cursor: pointer; font-family: inherit;
        }
        .btn-filter-reset:hover { background: #f3f4f6; }

        /* ── Toolbar ── */
        .toolbar {
            display: flex; gap: 7px; padding: 10px 18px;
            border-bottom: 1px solid #f0f0f5; flex-wrap: wrap; align-items: center;
            background: #fff;
        }
        .btn {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 7px 15px; border-radius: 8px; font-family: inherit;
            font-size: 12px; font-weight: 600; cursor: pointer; border: none;
            transition: all .18s;
        }
        .btn-add  { background: #be0000; color: #fff; }
        .btn-add:hover  { background: #950000; transform: translateY(-1px); box-shadow: 0 4px 10px rgba(190,0,0,.3); }
        .btn-edit { background: #eef0f7; color: #3a4060; }
        .btn-edit:hover { background: #e0e3f0; transform: translateY(-1px); }
        .btn-del  { background: #fff0f0; color: #c62828; }
        .btn-del:hover  { background: #ffe0e0; transform: translateY(-1px); }
        .btn-exit { background: transparent; color: #6b7280; border: 1px solid #e5e7eb; }
        .btn-exit:hover { background: #f9fafb; }
        .btn-new  { background: transparent; color: #6b7280; border: 1px solid #e5e7eb; }
        .btn-new:hover  { background: #f9fafb; }

        /* Summary bar */
        .summary-bar { margin-left: auto; display: flex; gap: 8px; }
        .sbox { background: #f8f9fc; border: 1px solid #eef0f7; border-radius: 8px; padding: 4px 12px; text-align: center; font-size: 10px; color: #9ca3af; }
        .sbox strong { display: block; font-size: 12px; font-weight: 700; color: #1a1f36; margin-top: 1px; }
        .sbox.green { background: #f0fdf4; border-color: #bbf7d0; }
        .sbox.green strong { color: #166534; }
        .sbox.red { background: #fff5f5; border-color: #fecaca; }
        .sbox.red strong { color: #c62828; }
        .summary-value { font-weight: bold; color: #000080; font-size: 13px; }

        /* ── Table ── */
        .table-wrap { background: #fff; border-radius: 0 0 16px 16px; }
        .grid-wrapper { height: calc(100vh - 500px); min-height: 240px; overflow: auto; }
        #mainTable { width: 100%; border-collapse: collapse; min-width: 1800px; }
        #mainTable th {
            background: #f8f9fc; color: #8892a4;
            border-bottom: 2px solid #eef0f7; border-right: 1px solid #f3f4f8;
            padding: 9px 10px; position: sticky; top: 0;
            font-size: 10.5px; font-weight: 700; text-transform: uppercase;
            letter-spacing: .5px; white-space: nowrap; text-align: center; z-index: 10;
        }
        #mainTable td {
            border-bottom: 1px solid #f3f4f8; border-right: 1px solid #f3f4f8;
            padding: 9px 10px; white-space: nowrap; font-size: 12px;
            color: #374151; text-align: center;
        }
        #mainTable tbody tr:hover { background: #fdf5f5; cursor: pointer; }
        #mainTable tbody tr:last-child td { border-bottom: none; }
        #mainTable tr.row-selected { background: #fff5f5 !important; }
        #mainTable tr.row-selected td { color: #1a1f36; }

        .col-money { text-align: right !important; font-weight: 700; color: #1a1f36; }
        .col-center { text-align: center !important; }
        .col-profit-pos { text-align: right !important; font-weight: 700; color: #166534; }
        .col-profit-neg { text-align: right !important; font-weight: 700; color: #c62828; }

        /* Plate */
        .plate { display: inline-block; background: #1a1f36; color: #fff; padding: 2px 7px; border-radius: 4px; font-size: 11px; font-weight: 800; font-family: monospace; letter-spacing: 1px; }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="page-wrapper">
    <div class="breadcrumb">
        <a href="index.php">Trang chủ</a> <span>›</span>
        <span>Quản lý xe chạy</span> <span>›</span>
        <span>Xe chạy Du lịch</span>
    </div>

<div class="main-container">
    <div class="title">🌴 QUẢN LÝ XE CHẠY DU LỊCH
        <small>Nhập, sửa và theo dõi các chuyến xe du lịch</small>
    </div>

    <form id="mainForm" method="POST" action="save_xe_du_lich.php">
        <input type="hidden" name="action" id="form_action" value="add">
        <input type="hidden" name="edit_id" id="form_edit_id" value="">

        <div class="form-panel">
            <div class="form-col">
                <div class="col-title">📅 THÔNG TIN CHUYẾN</div>
                <div class="field-row">
                    <label>Ngày đi:</label>
                    <input type="date" name="ngay_di" id="ngay_di" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="field-row">
                    <label>Ngày về:</label>
                    <input type="date" name="ngay_ve" id="ngay_ve" value="<?= date('Y-m-d') ?>">
                </div>
                <div class="field-row">
                    <label>Hành trình:</label>
                    <input type="text" name="hanh_trinh" id="hanh_trinh" placeholder="VD: Hải Phòng - Hạ Long - Hải Phòng">
                </div>
            </div>
            <div class="form-col">
                <div class="col-title">🚌 XE & LÁI XE</div>
                <div class="field-row">
                    <label>Khách hàng:</label>
                    <input type="text" name="khach_hang" id="khach_hang" placeholder="Tên khách / đơn vị đặt xe">
                </div>
                <div class="field-row">
                    <label>Loại hình xe:</label>
                    <select name="loai_hinh" id="loai_hinh">
                        <option value="Xe nhà">Xe nhà</option>
                        <option value="Xe thuê">Xe thuê</option>
                    </select>
                </div>
                <div class="field-row">
                    <label>Biển số xe:</label>
                    <select name="bien_so" id="bien_so" onchange="loadXeDuLich(this.value)">
                        <option value="">-- Chọn xe --</option>
                        <?php
                        $xe_list = $conn->query("SELECT bien_so, loai_xe FROM danh_muc_xe ORDER BY bien_so ASC");
                        while($xe = $xe_list->fetch_assoc())
                            echo "<option value='{$xe['bien_so']}' data-loai='{$xe['loai_xe']}'>{$xe['bien_so']} ({$xe['loai_xe']})</option>";
                        ?>
                    </select>
                </div>
                <div class="field-row">
                    <label>Loại xe:</label>
                    <input type="text" name="loai_xe" id="loai_xe" readonly>
                </div>
                <div class="field-row">
                    <label>Lái xe:</label>
                    <input type="text" name="lai_xe" id="lai_xe" placeholder="Tên lái xe">
                </div>
                <div class="field-row">
                    <label>Thuê lái:</label>
                    <div style="display:flex; align-items:center; gap:8px; flex:1;">
                        <input type="checkbox" id="thue_lai_cb" style="width:16px;height:16px;cursor:pointer;" onchange="toggleThueLai(this)">
                        <input type="hidden" name="thue_lai" id="thue_lai" value="0">
                        <input type="text" name="ten_thue_lai" id="ten_thue_lai" placeholder="Tên lái xe thuê (nếu có)" style="flex:1; display:none;" id="ten_thue_lai_wrap">
                    </div>
                </div>
                <div class="field-row">
                    <label>Chủ xe:</label>
                    <select name="chu_xe" id="chu_xe">
                        <option value="">-- Chọn chủ xe --</option>
                        <?php
                        $chu_list = $conn->query("SELECT ten_hang FROM danh_muc_hang_van_tai ORDER BY ten_hang ASC");
                        while($c = $chu_list->fetch_assoc())
                            echo "<option value='{$c['ten_hang']}'>{$c['ten_hang']}</option>";
                        ?>
                    </select>
                </div>
            </div>
            <div class="form-col">
                <div class="col-title">💰 TÀI CHÍNH & GHI CHÚ</div>
                <div class="field-row">
                    <label>Cước xe:</label>
                    <input type="text" name="cuoc_xe" id="cuoc_xe" onkeyup="formatNum(this); tinhLuong(); calcLoiNhuan();" placeholder="0">
                </div>
                <div class="field-row">
                    <label>% lương lái:</label>
                    <input type="number" name="phan_tram_luong" id="phan_tram_luong" value="0" onkeyup="tinhLuong(); calcLoiNhuan();">
                </div>
                <div class="field-row">
                    <label>Lương lái xe:</label>
                    <input type="text" name="luong_lai_xe" id="luong_lai_xe" readonly style="background:#f0f0f0;" placeholder="0">
                </div>
                <div class="field-row">
                    <label>Tiền thuê xe:</label>
                    <input type="text" name="tien_thue_xe" id="tien_thue_xe" onkeyup="formatNum(this); calcLoiNhuan();" placeholder="0">
                </div>
                <div class="field-row">
                    <label>Tiền thuê lái:</label>
                    <input type="text" name="tien_thue_lai" id="tien_thue_lai" onkeyup="formatNum(this); calcLoiNhuan();" placeholder="0">
                </div>
                <div class="field-row" style="background:#e3f2fd; border:1px solid #90caf9; padding:3px 6px; border-radius:3px; margin-bottom:5px;">
                    <label style="color:#0d47a1;">Lái xe thu:</label>
                    <input type="text" name="lai_xe_thu" id="lai_xe_thu" onkeyup="formatNum(this); calcLoiNhuan();" placeholder="0" style="background:#e3f2fd;">
                </div>
                <div class="field-row" style="background:#e3f2fd; border:1px solid #90caf9; padding:3px 6px; border-radius:3px; margin-bottom:5px;">
                    <label style="color:#0d47a1;">Lái xe nộp:</label>
                    <input type="text" name="lai_xe_nop" id="lai_xe_nop" onkeyup="formatNum(this); calcLoiNhuan();" placeholder="0" style="background:#e3f2fd;">
                </div>
                <div class="field-row" style="background:#e3f2fd; border:1px solid #90caf9; padding:3px 6px; border-radius:3px; margin-bottom:5px;">
                    <label style="color:#0d47a1;">Lái xe chi:</label>
                    <input type="text" name="lai_xe_chi" id="lai_xe_chi" onkeyup="formatNum(this); calcLoiNhuan();" placeholder="0" style="background:#e3f2fd;">
                </div>
                <div class="field-row" style="margin-top:4px;">
                    <label>Lợi nhuận:</label>
                    <input type="text" id="loi_nhuan_display" readonly
                        style="flex:1; background:#e8f5e9; color:#1b5e20; font-weight:bold; font-size:13px; border:1px solid #a5d6a7; padding:3px 5px;">
                </div>
                <div class="field-row" style="margin-top:6px;">
                    <label>Ghi chú:</label>
                    <input type="text" name="ghi_chu" id="ghi_chu" placeholder="Ghi chú thêm...">
                </div>
            </div>

        </div>
        <div class="filter-bar">
            <label>🔍 Lọc nhanh:</label>
            <label>Ngày đi:</label>
            <input type="date" id="f_ngay" onchange="filterTable()">
            <label>Khách hàng:</label>
            <input type="text" id="f_kh" oninput="filterTable()" placeholder="Nhập tên KH..." style="width:160px;">
            <label>Biển số:</label>
            <input type="text" id="f_bien" oninput="filterTable()" placeholder="Nhập biển số..." style="width:120px;">
            <label>Hành trình:</label>
            <input type="text" id="f_hanh_trinh" oninput="filterTable()" placeholder="Hành trình..." style="width:160px;">
            <button type="button" onclick="resetFilter()" class="btn" style="padding:2px 10px; font-size:11px;">↩ Bỏ lọc</button>
        </div>
        <div class="toolbar">
            <button type="button" class="btn btn-add"  onclick="submitAdd()">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                Thêm (Alt+A)
            </button>
            <button type="button" class="btn btn-edit" onclick="submitEdit()">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                Sửa (Alt+E)
            </button>
            <button type="button" class="btn btn-del"  onclick="deleteRow()">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                Xóa (Alt+D)
            </button>
            <button type="button" class="btn btn-new" onclick="clearForm()">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
                Làm mới
            </button>
            <button type="button" class="btn btn-exit" onclick="window.location.href='index.php'">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                Thoát (Esc)
            </button>
            <div class="summary-bar">
                <div class="sbox">Tổng chuyến<strong id="sum_count">0</strong></div>
                <div class="sbox">Tổng doanh thu<strong id="sum_dongia">0</strong></div>
                <div class="sbox red">Tổng chi phí<strong id="sum_chiphi">0</strong></div>
                <div class="sbox green">Lợi nhuận<strong id="sum_loinhuan">0</strong></div>
            </div>
        </div>
    </form>
    <div class="grid-wrapper">
        <table id="mainTable">
            <thead>
                <tr>
                    <th width="35">Stt</th>
                    <th width="35">Chọn</th>
                    <th width="80">Ngày đi</th>
                    <th width="75">Ngày về</th>
                    <th width="130">Khách hàng</th>
                    <th width="170">Hành trình</th>
                    <th width="45" title="Xe nhà">Xe nhà</th>
                    <th width="120">Hãng v.tải</th>
                    <th width="100">Biển số</th>
                    <th width="120">Lái xe</th>
                    <th width="45" title="Thuê lái">Thuê lái</th>
                    <th width="120">Tên thuê lái</th>
                    <th width="100">T.Thuê lái</th>
                    <th width="100">Cước xe</th>
                    <th width="100">Lương lái xe</th>
                    <th width="100">T.Thuê xe</th>
                    <th width="100">Lái xe thu</th>
                    <th width="100">Lái xe nộp</th>
                    <th width="100">Lái xe chi</th>
                    <th width="110">Lợi nhuận</th>
                    <th>Ghi chú</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                <?php
                $res = $conn->query("SELECT * FROM xe_chay_du_lich ORDER BY ngay_di DESC, id DESC");
                $stt = 1;
                while ($r = $res->fetch_assoc()):
                    $loi_nhuan = $r['cuoc_xe'] - $r['luong_lai_xe'] - $r['tien_thue_xe'] - $r['tien_thue_lai'];
                    $loi_nhuan_class = $loi_nhuan >= 0 ? 'style="color:green;"' : 'style="color:red;"';
                ?>
                <tr onclick="selectRow(this)"
                    data-id="<?= $r['id'] ?>"
                    data-ngaydi="<?= $r['ngay_di'] ?>"
                    data-ngayve="<?= $r['ngay_ve'] ?>"
                    data-hanhtrinh="<?= htmlspecialchars($r['hanh_trinh'] ?? '') ?>"
                    data-kh="<?= htmlspecialchars($r['khach_hang'] ?? '') ?>"
                    data-bienso="<?= htmlspecialchars($r['bien_so'] ?? '') ?>"
                    data-loaixe="<?= htmlspecialchars($r['loai_xe'] ?? '') ?>"
                    data-laixe="<?= htmlspecialchars($r['lai_xe'] ?? '') ?>"
                    data-tenthuuelai="<?= htmlspecialchars($r['ten_thue_lai'] ?? '') ?>"
                    data-thueLai="<?= $r['thue_lai'] ?? 0 ?>"
                    data-chuxe="<?= htmlspecialchars($r['chu_xe'] ?? '') ?>"
                    data-loaihinh="<?= htmlspecialchars($r['loai_hinh'] ?? 'Xe nhà') ?>"
                    data-dongia="<?= $r['don_gia'] ?? 0 ?>"
                    data-cuocxe="<?= $r['cuoc_xe'] ?>"
                    data-luong="<?= $r['luong_lai_xe'] ?>"
                    data-phantramluong="<?= $r['phan_tram_luong'] ?>"
                    data-tienthuexe="<?= $r['tien_thue_xe'] ?>"
                    data-tienthuuelai="<?= $r['tien_thue_lai'] ?>"
                    data-laixethu="<?= $r['lai_xe_thu'] ?>"
                    data-laixenop="<?= $r['lai_xe_nop'] ?>"
                    data-laixechi="<?= $r['lai_xe_chi'] ?>"
                    data-ghichu="<?= htmlspecialchars($r['ghi_chu'] ?? '') ?>">
                    <td class="col-center"><?= $stt++ ?></td>
                    <td class="col-center"><input type="radio" name="row_select" value="<?= $r['id'] ?>" onclick="event.stopPropagation();"></td>
                    <td class="col-center"><?= $r['ngay_di'] ? date('d/m/Y', strtotime($r['ngay_di'])) : '' ?></td>
                    <td class="col-center"><?= $r['ngay_ve'] ? date('d/m/Y', strtotime($r['ngay_ve'])) : '' ?></td>
                    <td><?= htmlspecialchars($r['khach_hang'] ?? '') ?></td>
                    <td><?= htmlspecialchars($r['hanh_trinh'] ?? '') ?></td>
                    <td class="col-center"><?= ($r['loai_hinh'] === 'Xe nhà') ? '<input type="checkbox" checked disabled style="width:14px;height:14px;">' : '<input type="checkbox" disabled style="width:14px;height:14px;">' ?></td>
                    <td><?= htmlspecialchars($r['chu_xe'] ?? '') ?></td>
                    <td class="col-center"><b><?= htmlspecialchars($r['bien_so'] ?? '') ?></b></td>
                    <td><?= htmlspecialchars($r['lai_xe'] ?? '') ?></td>
                    <td class="col-center"><?= ($r['thue_lai'] ?? 0) ? '<input type="checkbox" checked disabled style="width:14px;height:14px;">' : '<input type="checkbox" disabled style="width:14px;height:14px;">' ?></td>
                    <td><?= htmlspecialchars($r['ten_thue_lai'] ?? '') ?></td>
                    <td class="col-money"><?= number_format($r['tien_thue_lai']) ?></td>
                    <td class="col-money"><?= number_format($r['cuoc_xe']) ?></td>
                    <td class="col-money"><?= number_format($r['luong_lai_xe']) ?></td>
                    <td class="col-money"><?= number_format($r['tien_thue_xe']) ?></td>
                    <td class="col-money"><?= number_format($r['lai_xe_thu']) ?></td>
                    <td class="col-money"><?= number_format($r['lai_xe_nop']) ?></td>
                    <td class="col-money"><?= number_format($r['lai_xe_chi']) ?></td>
                    <td class="col-money" <?= $loi_nhuan_class ?>><?= number_format($loi_nhuan) ?></td>
                    <td><?= htmlspecialchars($r['ghi_chu'] ?? '') ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div><!-- grid-wrapper -->
</div><!-- main-container -->
</div><!-- page-wrapper -->

<script>
let selectedRow = null;
function selectRow(row) {
    document.querySelectorAll('#mainTable tbody tr').forEach(r => r.classList.remove('row-selected'));
    row.classList.add('row-selected');
    selectedRow = row;
    row.querySelector('input[type="radio"]').checked = true;
    loadRowToForm(row);
}
function toggleThueLai(cb) {
    const inp = document.getElementById('thue_lai');
    const ten = document.getElementById('ten_thue_lai');
    if (cb.checked) {
        inp.value = '1';
        ten.style.display = '';
        ten.focus();
    } else {
        inp.value = '0';
        ten.style.display = 'none';
        ten.value = '';
    }
    // Cập nhật checkbox trong dòng đang chọn trên bảng
    if (selectedRow) {
        const cells = selectedRow.getElementsByTagName('td');
        // cột Thuê lái = index 10 (0-based: Stt,Chọn,NgàyĐi,NgàyVề,HànhTrinh,XeNhà,KH,BiênSố,LoạiXe,LáiXe,ThuêLái)
        const cbCell = cells[10]?.querySelector('input[type="checkbox"]');
        if (cbCell) cbCell.checked = cb.checked;
    }
}
function loadRowToForm(row) {
    const d = row.dataset;
    setValue('ngay_di', d.ngaydi);
    setValue('ngay_ve', d.ngayve);
    setValue('hanh_trinh', d.hanhtrinh);
    setValue('khach_hang', d.kh);
    setValue('lai_xe', d.laixe);
    setValue('loai_xe', d.loaixe);
    setValue('ghi_chu', d.ghichu);
    setValue('form_edit_id', d.id);
    setSelectValue('bien_so', d.bienso);
    setSelectValue('loai_hinh', d.loaihinh);
    setSelectValue('chu_xe', d.chuxe);
    // Checkbox thuê lái
    const thueLaiVal = parseInt(d.thueLai) || 0;
    const cb = document.getElementById('thue_lai_cb');
    cb.checked = thueLaiVal === 1;
    document.getElementById('thue_lai').value = thueLaiVal;
    const tenField = document.getElementById('ten_thue_lai');
    tenField.style.display = thueLaiVal ? '' : 'none';
    tenField.value = d.tenthuuelai || '';
    document.getElementById('cuoc_xe').value         = formatNumStr(d.cuocxe);
    document.getElementById('luong_lai_xe').value    = formatNumStr(d.luong);
    document.getElementById('phan_tram_luong').value = d.phantramluong || 0;
    document.getElementById('tien_thue_xe').value    = formatNumStr(d.tienthuexe);
    document.getElementById('tien_thue_lai').value   = formatNumStr(d.tienthuuelai);
    document.getElementById('lai_xe_thu').value      = formatNumStr(d.laixethu);
    document.getElementById('lai_xe_nop').value      = formatNumStr(d.laixenop);
    document.getElementById('lai_xe_chi').value      = formatNumStr(d.laixechi);
    calcLoiNhuan();
}
function setValue(id, val) {
    const el = document.getElementById(id);
    if (el) el.value = val || '';
}
function setSelectValue(id, val) {
    const sel = document.getElementById(id);
    if (!sel) return;
    for (let i = 0; i < sel.options.length; i++) {
        if (sel.options[i].value == val) { sel.selectedIndex = i; return; }
    }
}
function loadXeDuLich(bien_so) {
    if (!bien_so) return;
    const sel = document.getElementById('bien_so');
    const opt = sel.options[sel.selectedIndex];
    document.getElementById('loai_xe').value = opt.dataset.loai || '';

    fetch('xuly_ajax.php?type=get_xe&bs=' + encodeURIComponent(bien_so))
    .then(r => r.json()).then(d => {
        if (d) document.getElementById('lai_xe').value = d.ten_lai_xe || '';
    }).catch(() => {});
}
function calcLoiNhuan() {
    const cuoc = parseNum(document.getElementById('cuoc_xe').value);
    const ll   = parseNum(document.getElementById('luong_lai_xe').value);
    const ttx  = parseNum(document.getElementById('tien_thue_xe').value);
    const ttl  = parseNum(document.getElementById('tien_thue_lai').value);

    const ln = cuoc - ll - ttx - ttl;

    const el = document.getElementById('loi_nhuan_display');
    el.value = formatNumStr(ln);
    el.style.color = ln >= 0 ? '#1b5e20' : '#c62828';
}
['cuoc_xe','phan_tram_luong','tien_thue_xe','tien_thue_lai','lai_xe_thu','lai_xe_nop','lai_xe_chi'].forEach(id => {
    document.getElementById(id).addEventListener('input', calcLoiNhuan);
});
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
    if (confirm('Bạn có chắc muốn xóa chuyến xe này không?')) {
        window.location.href = 'xoa_xe_du_lich.php?id=' + radio.value;
    }
}
function clearForm() {
    document.getElementById('mainForm').reset();
    document.getElementById('ngay_di').value  = new Date().toISOString().split('T')[0];
    document.getElementById('ngay_ve').value  = new Date().toISOString().split('T')[0];
    document.getElementById('form_edit_id').value = '';
    document.getElementById('form_action').value = 'add';
    document.getElementById('loi_nhuan_display').value = '';
    // Reset checkbox thuê lái
    document.getElementById('thue_lai_cb').checked = false;
    document.getElementById('thue_lai').value = '0';
    document.getElementById('ten_thue_lai').style.display = 'none';
    document.querySelectorAll('#mainTable tbody tr').forEach(r => r.classList.remove('row-selected'));
    selectedRow = null;
}
function filterTable() {
    const fNgay  = document.getElementById('f_ngay').value;
    const fKH    = document.getElementById('f_kh').value.toUpperCase();
    const fBien  = document.getElementById('f_bien').value.toUpperCase();
    const fHT    = document.getElementById('f_hanh_trinh').value.toUpperCase();

    let count = 0, sumDg = 0, sumChi = 0;

    document.querySelectorAll('#tableBody tr').forEach(row => {
        const cells = row.getElementsByTagName('td');
        let show = true;
        if (fNgay) {
            const parts = cells[2]?.innerText.trim().split('/');
            if (parts && parts.length === 3) {
                const iso = parts[2] + '-' + parts[1] + '-' + parts[0];
                if (iso !== fNgay) show = false;
            }
        }
        if (fKH   && !cells[4]?.innerText.toUpperCase().includes(fKH))   show = false;
        if (fBien && !cells[8]?.innerText.toUpperCase().includes(fBien))  show = false;
        if (fHT   && !cells[5]?.innerText.toUpperCase().includes(fHT))   show = false;

        row.style.display = show ? '' : 'none';
        if (show) {
            count++;
            sumDg  += parseNum(cells[13]?.innerText || '0');
            sumChi += parseNum(cells[14]?.innerText || '0') + parseNum(cells[15]?.innerText || '0') + parseNum(cells[12]?.innerText || '0');
        }
    });
    document.getElementById('sum_count').innerText    = count;
    document.getElementById('sum_dongia').innerText   = formatNumStr(sumDg);
    document.getElementById('sum_chiphi').innerText   = formatNumStr(sumChi);
    const ln = sumDg - sumChi;
    const el = document.getElementById('sum_loinhuan');
    el.innerText = formatNumStr(ln);
    el.style.color = ln >= 0 ? 'green' : 'red';
}
function resetFilter() {
    ['f_ngay','f_kh','f_bien','f_hanh_trinh'].forEach(id => document.getElementById(id).value = '');
    filterTable();
}
function parseNum(s) { return parseInt(String(s).replace(/\./g, '').replace(/,/g, '')) || 0; }
function formatNumStr(n) { return Math.round(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.'); }
function formatNum(input) {
    let v = input.value.replace(/\D/g, '');
    input.value = v.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}
function tinhLuong() {
    let cuoc = parseNum(document.getElementById('cuoc_xe').value);
    let pt   = parseInt(document.getElementById('phan_tram_luong').value) || 0;

    let luong = cuoc * pt / 100;

    document.getElementById('luong_lai_xe').value = formatNumStr(luong);
}
document.getElementById('loai_hinh').addEventListener('change', function() {
    // Cập nhật checkbox ở dòng đang được chọn trong bảng
    if (selectedRow) {
        const cb = selectedRow.querySelector('td:nth-child(6) input[type="checkbox"]');
        if (cb) cb.checked = (this.value === 'Xe nhà');
    }
});
document.addEventListener('keydown', function(e) {
    if (e.altKey) {
        if (e.key.toLowerCase() === 'a') { e.preventDefault(); submitAdd(); }
        if (e.key.toLowerCase() === 'e') { e.preventDefault(); submitEdit(); }
        if (e.key.toLowerCase() === 'd') { e.preventDefault(); deleteRow(); }
    }
    if (e.key === 'Escape') window.location.href = 'index.php';
});
window.addEventListener('load', () => filterTable());
</script>
</body>
</html>