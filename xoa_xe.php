<?php
include 'conn.php';
if(isset($_GET['id'])) {
    $conn->query("DELETE FROM danh_muc_xe WHERE id = " . $_GET['id']);
}
header("Location: danh_muc_xe.php");
?>