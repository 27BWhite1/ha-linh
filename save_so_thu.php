<?php include 'conn.php';
if($_SERVER['REQUEST_METHOD']=='POST'){
    $act=($_POST['action']??'add'); $id=intval($_POST['edit_id']??0);
    $ngay=$_POST['ngay_thu']; $kh=$_POST['khach_hang'];
    $mact=$_POST['ma_chung_tu']; $dg=$_POST['dien_giai'];
    $nguoi_tra=$_POST['nguoi_tra']??'';
    $phai=intval(str_replace('.','',$_POST['so_tien_phai_thu']??0));
    $da=intval(str_replace('.','',$_POST['so_tien_da_thu']??0));
    $ghi=$_POST['ghi_chu'];
    if($act=='add'){
        $s=$conn->prepare("INSERT INTO so_thu_khach_hang (ngay_thu,khach_hang,ma_chung_tu,dien_giai,nguoi_tra,so_tien_phai_thu,so_tien_da_thu,ghi_chu) VALUES(?,?,?,?,?,?,?,?)");
        $s->bind_param("sssssiis",$ngay,$kh,$mact,$dg,$nguoi_tra,$phai,$da,$ghi);
    } else {
        $s=$conn->prepare("UPDATE so_thu_khach_hang SET ngay_thu=?,khach_hang=?,ma_chung_tu=?,dien_giai=?,nguoi_tra=?,so_tien_phai_thu=?,so_tien_da_thu=?,ghi_chu=? WHERE id=?");
        $s->bind_param("sssssiisi",$ngay,$kh,$mact,$dg,$nguoi_tra,$phai,$da,$ghi,$id);
    }
    $s->execute(); $s->close();
}
header("Location: so_thu_khach_hang.php"); exit();