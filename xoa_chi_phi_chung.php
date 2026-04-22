<?php 
include 'conn.php';
if(isset($_GET['id'])){
    $id = intval($_GET['id']);
    
    $s = $conn->prepare("DELETE FROM chi_phi_chung WHERE id=?");
    $s->bind_param("i", $id); 
    $s->execute();
    $s->close();
}
header("Location: chi_phi_chung.php");
exit();
?>