<?php
include 'conn.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "DELETE FROM danh_muc_lai_xe WHERE id = $id";
    
    if ($conn->query($sql)) {
        header("Location: danh_muc_lai_xe.php");
        exit();
    } else {
        echo "Lỗi khi xóa dữ liệu: " . $conn->error;
    }
} else {
    echo "Không tìm thấy ID cần xóa.";
}
?>