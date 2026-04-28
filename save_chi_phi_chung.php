<?php
include 'conn.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $act = ($_POST['action'] ?? 'add');
    $id  = intval($_POST['edit_id'] ?? 0);

    $ngay_chi   = $_POST['ngay_chi'] ?? date('Y-m-d');
    $so_ct      = $_POST['so_chung_tu'] ?? '';
    $loai       = $_POST['loai_chi_phi'] ?? '';
    $mota       = $_POST['mo_ta'] ?? '';
    $tien       = intval(str_replace('.', '', $_POST['so_tien'] ?? 0));
    $nguoi      = $_POST['nguoi_chi'] ?? '';
    $ghi        = $_POST['ghi_chu'] ?? '';

    $thang = intval(date('n', strtotime($ngay_chi)));
    $nam   = intval(date('Y', strtotime($ngay_chi)));

    if ($act == 'add') {
        $s = $conn->prepare("INSERT INTO chi_phi_chung
            (ngay_chi, so_chung_tu, thang, nam, loai_chi_phi, mo_ta, so_tien, nguoi_chi, ghi_chu)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $s->bind_param("ssiississ", $ngay_chi, $so_ct, $thang, $nam, $loai, $mota, $tien, $nguoi, $ghi);
    } else {
        $s = $conn->prepare("UPDATE chi_phi_chung SET
            ngay_chi=?, so_chung_tu=?, thang=?, nam=?, loai_chi_phi=?, mo_ta=?, so_tien=?, nguoi_chi=?, ghi_chu=?
            WHERE id=?");
        $s->bind_param("ssiississi", $ngay_chi, $so_ct, $thang, $nam, $loai, $mota, $tien, $nguoi, $ghi, $id);
    }
    $s->execute();
    $s->close();
}
header("Location: chi_phi_chung.php");
exit();