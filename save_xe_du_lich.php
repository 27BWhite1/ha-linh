<?php
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action  = $_POST['action'] ?? 'add';
    $edit_id = intval($_POST['edit_id'] ?? 0);

    $ngay_di       = $_POST['ngay_di'] ?: null;
    $ngay_ve       = $_POST['ngay_ve'] ?: null;
    $hanh_trinh    = $_POST['hanh_trinh'] ?? '';
    $khach_hang    = $_POST['khach_hang'] ?? '';
    $bien_so       = $_POST['bien_so'] ?? '';
    $loai_xe       = $_POST['loai_xe'] ?? '';
    $lai_xe        = $_POST['lai_xe'] ?? '';
    $thue_lai      = intval($_POST['thue_lai'] ?? 0);          // 0 hoặc 1
    $ten_thue_lai  = $_POST['ten_thue_lai'] ?? '';
    $chu_xe        = $_POST['chu_xe'] ?? '';
    $loai_hinh     = $_POST['loai_hinh'] ?? 'Xe nhà';
    $ghi_chu       = $_POST['ghi_chu'] ?? '';

    $cuoc_xe       = intval(str_replace('.', '', $_POST['cuoc_xe']       ?? 0));
    $phan_tram     = intval($_POST['phan_tram_luong'] ?? 0);
    $luong_lai_xe  = intval(($cuoc_xe * $phan_tram) / 100);
    $tien_thue_xe  = intval(str_replace('.', '', $_POST['tien_thue_xe']  ?? 0));
    $tien_thue_lai = intval(str_replace('.', '', $_POST['tien_thue_lai'] ?? 0));
    $lai_xe_thu    = intval(str_replace('.', '', $_POST['lai_xe_thu']    ?? 0));
    $lai_xe_nop    = intval(str_replace('.', '', $_POST['lai_xe_nop']    ?? 0));
    $lai_xe_chi    = intval(str_replace('.', '', $_POST['lai_xe_chi']    ?? 0));

    if ($action == 'add') {
        $stmt = $conn->prepare("INSERT INTO xe_chay_du_lich
            (ngay_di, ngay_ve, hanh_trinh, khach_hang,
             bien_so, loai_xe, lai_xe, ten_thue_lai, chu_xe, loai_hinh,
             thue_lai, cuoc_xe, phan_tram_luong, luong_lai_xe,
             tien_thue_xe, tien_thue_lai,
             lai_xe_thu, lai_xe_nop, lai_xe_chi, ghi_chu)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        // 10s + 9i + 1s = 20
        $stmt->bind_param("ssssssssssiiiiiiiiis",
            $ngay_di, $ngay_ve, $hanh_trinh, $khach_hang,
            $bien_so, $loai_xe, $lai_xe, $ten_thue_lai, $chu_xe, $loai_hinh,
            $thue_lai, $cuoc_xe, $phan_tram, $luong_lai_xe,
            $tien_thue_xe, $tien_thue_lai,
            $lai_xe_thu, $lai_xe_nop, $lai_xe_chi, $ghi_chu
        );

    } else {
        $stmt = $conn->prepare("UPDATE xe_chay_du_lich SET
            ngay_di=?, ngay_ve=?, hanh_trinh=?, khach_hang=?,
            bien_so=?, loai_xe=?, lai_xe=?, ten_thue_lai=?, chu_xe=?, loai_hinh=?,
            thue_lai=?, cuoc_xe=?, phan_tram_luong=?, luong_lai_xe=?,
            tien_thue_xe=?, tien_thue_lai=?,
            lai_xe_thu=?, lai_xe_nop=?, lai_xe_chi=?, ghi_chu=?
            WHERE id=?");

        // 10s + 9i + 1s + 1i(WHERE) = 21
        $stmt->bind_param("ssssssssssiiiiiiiiisi",
            $ngay_di, $ngay_ve, $hanh_trinh, $khach_hang,
            $bien_so, $loai_xe, $lai_xe, $ten_thue_lai, $chu_xe, $loai_hinh,
            $thue_lai, $cuoc_xe, $phan_tram, $luong_lai_xe,
            $tien_thue_xe, $tien_thue_lai,
            $lai_xe_thu, $lai_xe_nop, $lai_xe_chi, $ghi_chu,
            $edit_id
        );
    }

    if ($stmt->execute()) {
        header("Location: xe_chay_du_lich.php");
        exit();
    } else {
        echo "Lỗi: " . $stmt->error;
    }
    $stmt->close();

} else {
    header("Location: xe_chay_du_lich.php");
    exit();
}
?>