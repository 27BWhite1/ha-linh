<?php
include 'conn.php';
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM danh_muc_tuyen_duong WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        if (isset($_SERVER['HTTP_REFERER'])) {
            header("Location: " . $_SERVER['HTTP_REFERER']);
        } else {
            header("Location: danh_muc_tuyen.php"); 
        }
        exit;
    } else {
        echo "Lỗi khi xóa: " . $conn->error;
    }
    $stmt->close();
}
$conn->close();
?>