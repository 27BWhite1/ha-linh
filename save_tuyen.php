<?php
include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? 'add';
    $id = intval($_POST['id'] ?? 0);

    $ma_tuyen   = $_POST['ma_tuyen'];
    $ten_ca     = $_POST['ten_ca'];
    $ten_tuyen  = $_POST['ten_tuyen'];
    $khach_hang = $_POST['khach_hang'];
    $gio_don    = $_POST['gio_don'];
    $gio_ve     = $_POST['gio_ve'];

    function clean_num($val) {
        return str_replace('.', '', $val);
    }
    $dg04 = clean_num($_POST['don_gia_04']); $dg07 = clean_num($_POST['don_gia_07']);
    $dg16 = clean_num($_POST['don_gia_16']); $dg29 = clean_num($_POST['don_gia_29']);
    $dg34 = clean_num($_POST['don_gia_34']); $dg45 = clean_num($_POST['don_gia_45']);

    $l04 = clean_num($_POST['luong_xe_04']); $l07 = clean_num($_POST['luong_xe_07']);
    $l16 = clean_num($_POST['luong_xe_16']); $l29 = clean_num($_POST['luong_xe_29']);
    $l34 = clean_num($_POST['luong_xe_34']); $l45 = clean_num($_POST['luong_xe_45']);

    if ($action == 'add') {
        $sql = "INSERT INTO danh_muc_tuyen_duong (
                    ma_tuyen, ten_ca, ten_tuyen, khach_hang, gio_don, gio_ve,
                    don_gia_04, don_gia_07, don_gia_16, don_gia_29, don_gia_34, don_gia_45,
                    luong_xe_04, luong_xe_07, luong_xe_16, luong_xe_29, luong_xe_34, luong_xe_45
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssdddddddddddd",
            $ma_tuyen, $ten_ca, $ten_tuyen, $khach_hang, $gio_don, $gio_ve,
            $dg04, $dg07, $dg16, $dg29, $dg34, $dg45,
            $l04, $l07, $l16, $l29, $l34, $l45
        );
    } else {
        $sql = "UPDATE danh_muc_tuyen_duong SET
                    ten_ca=?, ten_tuyen=?, khach_hang=?, gio_don=?, gio_ve=?,
                    don_gia_04=?, don_gia_07=?, don_gia_16=?, don_gia_29=?, don_gia_34=?, don_gia_45=?,
                    luong_xe_04=?, luong_xe_07=?, luong_xe_16=?, luong_xe_29=?, luong_xe_34=?, luong_xe_45=?
                WHERE id=?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssddddddddddddi",
            $ten_ca, $ten_tuyen, $khach_hang, $gio_don, $gio_ve,
            $dg04, $dg07, $dg16, $dg29, $dg34, $dg45,
            $l04, $l07, $l16, $l29, $l34, $l45, $id
        );
    }
    if ($stmt->execute()) {
        echo "<script>alert('Lưu thành công!'); window.parent.closeModal();</script>";
    } else {
        echo "Lỗi truy vấn: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
}
?>