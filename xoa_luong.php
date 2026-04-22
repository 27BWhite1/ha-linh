<?php 
include 'conn.php';

$thang = $_GET['thang'] ?? date('n'); 
$nam = $_GET['nam'] ?? date('Y');

if(isset($_GET['id'])){
    $id = intval($_GET['id']);
    
    $s = $conn->prepare("DELETE FROM luong_lai_xe WHERE id=?");
    $s->bind_param("i", $id); 
    $s->execute();
    $s->close();
}
header("Location: quan_li_luong_lai_xe.php?thang=$thang&nam=$nam");
exit();
?>