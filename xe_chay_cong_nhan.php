<?php
include 'conn.php';
session_start();
// Tự động thêm cột mới nếu chưa có
$conn->query("ALTER TABLE xe_chay_cong_nhan ADD COLUMN IF NOT EXISTS `chu_xe` varchar(100) DEFAULT NULL");
$conn->query("ALTER TABLE xe_chay_cong_nhan ADD COLUMN IF NOT EXISTS `ten_thue_lai` varchar(100) DEFAULT NULL");
$conn->query("ALTER TABLE xe_chay_cong_nhan ADD COLUMN IF NOT EXISTS `tien_thue_lai` decimal(15,0) DEFAULT 0");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Quản lý xe chạy Công nhân</title>
    <style>
        body { font-family: Tahoma, sans-serif; font-size: 11px; background: #f0f0f0; margin: 5px; }
        .main-container { border: 1px solid #999; background: #fff; padding: 10px; }
        .title { color: #be0000; text-align: center; font-weight: bold; font-size: 16px; margin: 0 0 10px 0; }

        .header-flex { display: flex; gap: 10px; align-items: flex-start; }
        .input-area { flex: 3; display: grid; grid-template-columns: repeat(4, 1fr); gap: 5px; }
        .search-area { flex: 1.2; border: 1px solid #bba; padding: 15px 10px 10px 10px; position: relative; border-radius: 4px; background: #f9f9f9; }
        .search-label { position: absolute; top: -10px; left: 10px; background: #f9f9f9; padding: 0 5px; font-weight: bold; font-size: 10px; }

        .field { display: flex; align-items: center; gap: 4px; }
        .field label { width: 85px; flex-shrink: 0; font-weight: bold; }
        input[type="text"], input[type="date"], select { border: 1px solid #7f9db9; padding: 2px 4px; height: 22px; font-size: 11px; }
        input[type="text"], select { width: 100%; }

        .toolbar { background: #e1e1e1; border: 1px solid #ccc; padding: 4px; margin: 10px 0; display: flex; gap: 5px; }
        .btn { border: 1px solid #888; padding: 4px 12px; cursor: pointer; font-weight: bold; display: flex; align-items: center; gap: 4px; background: #eee; font-size: 12px; }
        .btn:hover { background: #d5d5d5; }

        .grid-wrapper { height: 400px; overflow: auto; border: 1px solid #999; }
        table { width: 100%; border-collapse: collapse; min-width: 1200px; }
        th { background: #000080; color: white; border: 1px solid #ccc; padding: 5px 4px; position: sticky; top: 0; font-weight: normal; font-size: 11px; }
        td { border: 1px solid #ccc; padding: 3px 4px; text-align: center; white-space: nowrap; font-size: 11px; }

        .bg-red { background: red !important; color: white; }
        .row-selected { background: #0000ff !important; color: white; }
        .row-selected td { color: white; }
        tr:hover { background: #ffffcc; cursor: pointer; }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="main-container">
    <h1 class="title">XE CHẠY CÔNG NHÂN</h1>

    <form id="mainForm" method="POST" action="save_xe_cong_nhan.php">
        <input type="hidden" name="action" id="form_action" value="add">
        <input type="hidden" name="edit_id" id="form_edit_id" value="">

        <div class="header-flex">
            <div class="input-area">
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
                    <input type="text" name="gio_don" id="gio_don" style="width:50px">
                    <label style="width:auto;">Giờ về:</label>
                    <input type="text" name="gio_ve" id="gio_ve" style="width:50px">
                </div>

                <div class="field">
                    <label>Khách hàng:</label>
                    <input type="text" name="khach_hang" id="khach_hang" readonly style="background:#f0f0f0;">
                </div>
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
                    <input type="text" name="lai_xe" id="lai_xe">
                </div>
                <div class="field">
                    <input type="checkbox" name="thue_lai" id="thue_lai" style="width:15px" onchange="toggleThueLaiForm(this)"> &nbsp;Thuê lái
                </div>
                <div class="field" id="wrap_ten_thue_lai" style="display:none;">
                    <label>Tên lái thuê:</label>
                    <input type="text" name="ten_thue_lai" id="ten_thue_lai" placeholder="Tên lái xe thuê">
                </div>
                <div class="field" id="wrap_tien_thue_lai" style="display:none;">
                    <label>Tiền thuê lái:</label>
                    <input type="text" name="tien_thue_lai" id="tien_thue_lai" placeholder="0" onkeyup="formatNum(this)">
                </div>

                <div class="field">
                    <label>Cước xe:</label>
                    <input type="text" name="cuoc_xe" id="cuoc_xe" onkeyup="formatNum(this)">
                </div>
                <div class="field">
                    <label>Lương lái xe:</label>
                    <input type="text" name="luong_lai" id="luong_lai" onkeyup="formatNum(this)">
                </div>
                <div class="field">
                    <label>Ghi chú:</label>
                    <input type="text" name="ghi_chu" id="ghi_chu">
                </div>
                <div class="field">
                    <input type="checkbox" name="ca_noi" id="ca_noi" style="width:15px"> Là ca nối
                </div>
            </div>

            <div class="search-area">
                <span class="search-label">Tìm kiếm</span>
                <div class="field" style="margin-bottom:5px;">
                    <input type="checkbox" id="ck_ngay" style="width:15px">
                    <span style="width:70px;">Theo ngày:</span>
                    <input type="date" id="f_ngay" style="width:110px">
                </div>
                <div class="field" style="margin-bottom:5px;">
                    <input type="checkbox" id="ck_chu" style="width:15px">
                    <span style="width:70px;">Theo chủ xe:</span>
                    <select id="f_chu" style="flex:1;">
                        <option value="">-- Tất cả --</option>
                        <?php
                        $chu_list = $conn->query("SELECT ten_hang FROM danh_muc_hang_van_tai ORDER BY ten_hang ASC");
                        while($c = $chu_list->fetch_assoc()) echo "<option>{$c['ten_hang']}</option>";
                        ?>
                    </select>
                </div>
                <div class="field" style="margin-bottom:5px;">
                    <input type="checkbox" id="ck_kh" style="width:15px">
                    <span style="width:70px;">Theo KH:</span>
                    <select id="f_kh" style="flex:1;">
                        <option value="">-- Tất cả --</option>
                        <?php
                        $kh_list = $conn->query("SELECT ten_khachhang FROM danh_muc_khach_hang ORDER BY ten_khachhang ASC");
                        while($k = $kh_list->fetch_assoc()) echo "<option>{$k['ten_khachhang']}</option>";
                        ?>
                    </select>
                </div>
                <div class="field" style="margin-bottom:5px;">
                    <input type="checkbox" id="ck_xe" style="width:15px">
                    <span style="width:70px;">Theo xe:</span>
                    <select id="f_xe" style="flex:1;">
                        <option value="">-- Tất cả --</option>
                        <?php
                        $xe_list = $conn->query("SELECT bien_so FROM danh_muc_xe ORDER BY bien_so ASC");
                        while($xe = $xe_list->fetch_assoc()) echo "<option>{$xe['bien_so']}</option>";
                        ?>
                    </select>
                </div>
                <button type="button" onclick="doSearch()" style="width:100%; margin-top:5px; height:24px; cursor:pointer; background:#000080; color:white; border:none; font-weight:bold;">🔍 Tìm kiếm</button>
                <button type="button" onclick="resetSearch()" style="width:100%; margin-top:3px; height:24px; cursor:pointer;">↩ Xem tất cả</button>
            </div>
        </div>
        <div class="toolbar">
            <button type="button" class="btn" style="color:green" onclick="submitAdd()">✚ Thêm (Alt+A)</button>
            <button type="button" class="btn" style="color:blue" onclick="submitEdit()">💾 Sửa (Alt+E)</button>
            <button type="button" class="btn" style="color:red" onclick="deleteRow()">✖ Xóa (Alt+D)</button>
            <button type="button" class="btn" onclick="window.location.href='index.php'">ⓧ Thoát (Esc)</button>
            <button type="button" class="btn" style="color:#555" onclick="clearForm()">🔄 Làm mới</button>
        </div>
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
                    <td style="text-align:right;"><?= number_format($r['luong_lai_xe']) ?></td>
                    <td style="text-align:left;"><?= htmlspecialchars($r['ghi_chu'] ?? '') ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
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
    wrapTen.style.display  = cb.checked ? '' : 'none';
    wrapTien.style.display = cb.checked ? '' : 'none';
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
    document.getElementById('wrap_ten_thue_lai').style.display  = thueLai ? '' : 'none';
    document.getElementById('wrap_tien_thue_lai').style.display = thueLai ? '' : 'none';
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
}
function resetSearch() {
    document.querySelectorAll('#tableBody tr').forEach(r => r.style.display = '');
    ['ck_ngay','ck_chu','ck_kh','ck_xe'].forEach(id => document.getElementById(id).checked = false);
}
function formatNumStr(n) {
    return parseInt(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}
function formatNum(input) {
    let v = input.value.replace(/\D/g, '');
    input.value = v.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}
document.addEventListener('keydown', function(e) {
    if (e.altKey) {
        if (e.key.toLowerCase() === 'a') { e.preventDefault(); submitAdd(); }
        if (e.key.toLowerCase() === 'e') { e.preventDefault(); submitEdit(); }
        if (e.key.toLowerCase() === 'd') { e.preventDefault(); deleteRow(); }
    }
    if (e.key === 'Escape') { 
        window.location.href = 'index.php'; 
    }
});
</script>
</body>
</html>