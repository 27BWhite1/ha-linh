<?php 
include 'conn.php';
if(isset($_GET['id'])){
    $id = intval($_GET['id']);
    
    $s = $conn->prepare("DELETE FROM so_chi_tra_chu_xe WHERE id=?");
    $s->bind_param("i", $id); 
    $s->execute();
    $s->close();
}
header("Location: so_chi_tra_chu_xe.php");
exit();
?>