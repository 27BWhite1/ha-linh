<?php
include 'conn.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$id     = intval($_POST['id'] ?? $_GET['id'] ?? 0);
$ma     = $_POST['ma_khachhang'] ?? '';
$ten    = $_POST['ten_khachhang'] ?? '';
$cccd   = $_POST['so_cccd'] ?? '';
$no     = floatval($_POST['no_cu'] ?? 0);

if ($action == 'add') {
    $stmt = $conn->prepare("INSERT INTO danh_muc_khach_hang (ma_khachhang, ten_khachhang, so_cccd, no_cu) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssd", $ma, $ten, $cccd, $no);
}
elseif ($action == 'edit') {
    $stmt = $conn->prepare("UPDATE danh_muc_khach_hang SET ma_khachhang=?, ten_khachhang=?, so_cccd=?, no_cu=? WHERE id=?");
    $stmt->bind_param("sssdi", $ma, $ten, $cccd, $no, $id);
}
elseif ($action == 'delete') {
    $stmt = $conn->prepare("DELETE FROM danh_muc_khach_hang WHERE id=?");
    $stmt->bind_param("i", $id);
}

if (isset($stmt)) {
    if ($stmt->execute()) {
        header("Location: danh-muc-khachhang.php");
        exit();
    } else {
        echo "Lỗi xử lý: " . $stmt->error;
    }
    $stmt->close();
} else {
    echo "Hành động không hợp lệ.";
}
?>