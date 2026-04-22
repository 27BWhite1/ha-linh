<?php
include 'conn.php';
if($_SERVER['REQUEST_METHOD']=='POST'){
    $action=($_POST['action']??'add'); $id=intval($_POST['edit_id']??0);
    $sct=$_POST['so_chung_tu']??'';
    $ngay=$_POST['ngay']; $bien=$_POST['bien_so']; $loai=$_POST['loai_chi_phi'];
    $tien=intval(str_replace('.','',$_POST['so_tien']??0));
    $nguoi=$_POST['nguoi_chi']; $ghi=$_POST['ghi_chu'];
    if($action=='add'){
        $s=$conn->prepare("INSERT INTO chi_phi_xe (so_chung_tu,ngay,bien_so,loai_chi_phi,so_tien,nguoi_chi,ghi_chu) VALUES(?,?,?,?,?,?,?)");
        $s->bind_param("ssssiis",$sct,$ngay,$bien,$loai,$tien,$nguoi,$ghi);
    } else {
        $s=$conn->prepare("UPDATE chi_phi_xe SET so_chung_tu=?,ngay=?,bien_so=?,loai_chi_phi=?,so_tien=?,nguoi_chi=?,ghi_chu=? WHERE id=?");
        $s->bind_param("ssssissi",$sct,$ngay,$bien,$loai,$tien,$nguoi,$ghi,$id);
    }
    $s->execute(); $s->close();
}
header("Location: chi_phi_xe.php"); exit();