<?php
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action     = $_POST['action'] ?? 'add';
    $edit_id    = intval($_POST['edit_id'] ?? 0);

    $ngay_chay  = $_POST['ngay_chay'] ?? null;
    $loai_xe    = $_POST['loai_xe'] ?? '';
    $ma_tuyen   = $_POST['ma_tuyen'] ?? '';
    $bien_so    = $_POST['bien_so'] ?? '';
    $lai_xe     = $_POST['lai_xe'] ?? '';
    $khach_hang = $_POST['khach_hang'] ?? '';
    $gio_don    = $_POST['gio_don'] ?? '';
    $gio_ve     = $_POST['gio_ve'] ?? '';
    $chu_xe     = $_POST['chu_xe'] ?? '';
    $ten_thue_lai  = $_POST['ten_thue_lai'] ?? '';
    $ca_noi     = isset($_POST['ca_noi']) ? 1 : 0;
    $thue_lai   = isset($_POST['thue_lai']) ? 1 : 0;
    $ghi_chu    = $_POST['ghi_chu'] ?? '';

    $cuoc_xe      = intval(str_replace('.', '', $_POST['cuoc_xe'] ?? 0));
    $luong_lai    = intval(str_replace('.', '', $_POST['luong_lai'] ?? 0));
    $tien_thue_lai = intval(str_replace('.', '', $_POST['tien_thue_lai'] ?? 0));

    if ($action == 'add') {
        $stmt = $conn->prepare("INSERT INTO xe_chay_cong_nhan 
            (ngay_chay, loai_hinh_xe, ma_tuyen, bien_so, lai_xe, khach_hang,
             gio_don, gio_ve, chu_xe, ten_thue_lai, cuoc_xe, luong_lai_xe,
             tien_thue_lai, ca_noi, thue_lai, ghi_chu)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        // 10s + 3i + 2i + 1s = ssssssssssiiiiis (16 params)
        $stmt->bind_param("ssssssssssiiiiis",
            $ngay_chay, $loai_xe, $ma_tuyen, $bien_so, $lai_xe, $khach_hang,
            $gio_don, $gio_ve, $chu_xe, $ten_thue_lai,
            $cuoc_xe, $luong_lai, $tien_thue_lai, $ca_noi, $thue_lai, $ghi_chu
        );
    } else {
        $stmt = $conn->prepare("UPDATE xe_chay_cong_nhan SET
            ngay_chay=?, loai_hinh_xe=?, ma_tuyen=?, bien_so=?, lai_xe=?, khach_hang=?,
            gio_don=?, gio_ve=?, chu_xe=?, ten_thue_lai=?,
            cuoc_xe=?, luong_lai_xe=?, tien_thue_lai=?, ca_noi=?, thue_lai=?, ghi_chu=?
            WHERE id=?");
        // 10s + 3i + 2i + 1s + 1i(WHERE) = ssssssssssiiiiiisi (17 params)
        $stmt->bind_param("ssssssssssiiiiisi",
            $ngay_chay, $loai_xe, $ma_tuyen, $bien_so, $lai_xe, $khach_hang,
            $gio_don, $gio_ve, $chu_xe, $ten_thue_lai,
            $cuoc_xe, $luong_lai, $tien_thue_lai, $ca_noi, $thue_lai, $ghi_chu, $edit_id
        );
    }
    if ($stmt->execute()) {
        header("Location: xe_chay_cong_nhan.php");
        exit();
    } else {
        echo "Lỗi lưu dữ liệu: " . $stmt->error;
    }
    $stmt->close();
} else {
    header("Location: xe_chay_cong_nhan.php");
    exit();
}
?>