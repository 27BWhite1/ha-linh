<?php
include 'conn.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $act      = $_POST['action']  ?? 'add';
    $id       = intval($_POST['edit_id'] ?? 0);

    $so_phieu      = $_POST['so_phieu'];
    $ngay_tam_ung  = $_POST['ngay_tam_ung'] ?: null;
    $thang_khau    = intval($_POST['thang_khau'] ?? date('n'));
    $nam_khau      = intval($_POST['nam_khau']   ?? date('Y'));
    $doi_tuong     = $_POST['doi_tuong'];
    $nguoi_tam_ung = $_POST['nguoi_tam_ung'];
    $chuc_vu       = $_POST['chuc_vu'];
    $ly_do         = $_POST['ly_do'];
    $so_tien       = intval(str_replace('.', '', $_POST['so_tien'] ?? 0));
    $nguoi_duyet   = $_POST['nguoi_duyet'];
    $trang_thai    = $_POST['trang_thai'];
    $ngay_hoan_tra = (!empty($_POST['ngay_hoan_tra'])) ? $_POST['ngay_hoan_tra'] : null;
    $ghi_chu       = $_POST['ghi_chu'];
    $nguoi_tao     = $_SESSION['fullname'] ?? '';

    if ($act == 'add') {
        $stmt = $conn->prepare("INSERT INTO tam_ung_luong
            (so_phieu, ngay_tam_ung, thang_khau, nam_khau, doi_tuong, nguoi_tam_ung,
             chuc_vu, ly_do, so_tien, nguoi_duyet, trang_thai, ngay_hoan_tra, ghi_chu, nguoi_tao)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        
        $stmt->bind_param("ssiissssisssss",
            $so_phieu, $ngay_tam_ung, $thang_khau, $nam_khau, $doi_tuong, $nguoi_tam_ung,
            $chuc_vu, $ly_do, $so_tien, $nguoi_duyet, $trang_thai, $ngay_hoan_tra, $ghi_chu, $nguoi_tao
        );
    } else {
        $stmt = $conn->prepare("UPDATE tam_ung_luong SET
            so_phieu=?, ngay_tam_ung=?, thang_khau=?, nam_khau=?, doi_tuong=?, nguoi_tam_ung=?,
            chuc_vu=?, ly_do=?, so_tien=?, nguoi_duyet=?, trang_thai=?, ngay_hoan_tra=?, ghi_chu=?
            WHERE id=?");
            
        $stmt->bind_param("ssiissssissssi",
            $so_phieu, $ngay_tam_ung, $thang_khau, $nam_khau, $doi_tuong, $nguoi_tam_ung,
            $chuc_vu, $ly_do, $so_tien, $nguoi_duyet, $trang_thai, $ngay_hoan_tra, $ghi_chu, $id
        );
    }

    if ($stmt->execute()) {
        if ($trang_thai == 'Đã khấu trừ' && $nguoi_tam_ung) {
            $ck = $conn->prepare("SELECT id, khau_tru, tong_luong, luong_trach_nhiem, tong_luong_chuyen_cn, tong_luong_chuyen_dl, phu_cap FROM luong_lai_xe WHERE thang=? AND nam=? AND ten_lai_xe=?");
            $ck->bind_param("iis", $thang_khau, $nam_khau, $nguoi_tam_ung);
            $ck->execute();
            $lr = $ck->get_result()->fetch_assoc();
            $ck->close();
            if ($lr) {
                $new_khau = $lr['khau_tru'] + $so_tien;
                $new_tl   = $lr['luong_trach_nhiem'] + $lr['tong_luong_chuyen_cn'] + $lr['tong_luong_chuyen_dl'] + $lr['phu_cap'] - $new_khau;
                $upd = $conn->prepare("UPDATE luong_lai_xe SET khau_tru=?, tong_luong=? WHERE id=?");
                $upd->bind_param("iii", $new_khau, $new_tl, $lr['id']);
                $upd->execute();
                $upd->close();
            }
        }
        header("Location: tam_ung_luong.php");
        exit();
    } else {
        echo "Lỗi: " . $stmt->error;
    }
    $stmt->close();
} else {
    header("Location: tam_ung_luong.php");
    exit();
}
?>