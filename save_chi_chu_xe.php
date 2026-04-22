<?php 
include 'conn.php';
if($_SERVER['REQUEST_METHOD']=='POST'){
    $act=($_POST['action']??'add'); 
    $id=intval($_POST['edit_id']??0);
    $chu=$_POST['chu_xe'];
    $sct=$_POST['so_chung_tu']??'';
    $dg=$_POST['dien_giai']; 
    $ngay_tra=(!empty($_POST['ngay_tra']))?$_POST['ngay_tra']:null;
    $phai=intval(str_replace('.','',$_POST['so_tien_phai_tra']??0));
    $da=intval(str_replace('.','',$_POST['so_tien_da_tra']??0));
    $ghi=$_POST['ghi_chu'];

    if($act=='add'){
        $s=$conn->prepare("INSERT INTO so_chi_tra_chu_xe (chu_xe,so_chung_tu,dien_giai,so_tien_phai_tra,so_tien_da_tra,ngay_tra,ghi_chu) VALUES(?,?,?,?,?,?,?)");
        $s->bind_param("sssiiis",$chu,$sct,$dg,$phai,$da,$ngay_tra,$ghi);
    } else {
        $s=$conn->prepare("UPDATE so_chi_tra_chu_xe SET chu_xe=?,so_chung_tu=?,dien_giai=?,so_tien_phai_tra=?,so_tien_da_tra=?,ngay_tra=?,ghi_chu=? WHERE id=?");
        $s->bind_param("sssiissi",$chu,$sct,$dg,$phai,$da,$ngay_tra,$ghi,$id);
    }
    $s->execute(); 
    $s->close();
}
header("Location: so_chi_tra_chu_xe.php"); 
exit();
?>