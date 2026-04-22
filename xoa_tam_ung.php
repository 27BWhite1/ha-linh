<?php
include 'conn.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); 
    
    $s = $conn->prepare("DELETE FROM tam_ung_luong WHERE id=?");
    $s->bind_param("i", $id); 
    $s->execute();
    $s->close();
}

header("Location: tam_ung_luong.php");
exit();
?>