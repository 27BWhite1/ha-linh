<?php include 'conn.php';
if($_SERVER['REQUEST_METHOD']=='POST'){
    $act=($_POST['action']??'add'); $id=intval($_POST['edit_id']??0);
    $thang=intval($_POST['thang_ky']); $nam=intval($_POST['nam_ky']);
    $lxid=intval($_POST['lai_xe_id']); $ten=$_POST['ten_lai_xe'];
    $ltn=intval(str_replace('.','',$_POST['luong_trach_nhiem']??0));
    $lcn=intval(str_replace('.','',$_POST['tong_luong_chuyen_cn']??0));
    $ldl=intval(str_replace('.','',$_POST['tong_luong_chuyen_dl']??0));
    $pc=intval(str_replace('.','',$_POST['phu_cap']??0));
    $kt=intval(str_replace('.','',$_POST['khau_tru']??0));
    $tl=$ltn+$lcn+$ldl+$pc-$kt;
    $datt=isset($_POST['da_thanh_toan'])?1:0;
    $ngaytt=(!empty($_POST['ngay_thanh_toan']))?$_POST['ngay_thanh_toan']:null;
    $ghi=$_POST['ghi_chu'];
    if($act=='add'){
        $s=$conn->prepare("INSERT INTO luong_lai_xe (thang,nam,lai_xe_id,ten_lai_xe,luong_trach_nhiem,tong_luong_chuyen_cn,tong_luong_chuyen_dl,phu_cap,khau_tru,tong_luong,da_thanh_toan,ngay_thanh_toan,ghi_chu) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $s->bind_param("iiisiiiiiiiis",$thang,$nam,$lxid,$ten,$ltn,$lcn,$ldl,$pc,$kt,$tl,$datt,$ngaytt,$ghi);
    } else {
        $s=$conn->prepare("UPDATE luong_lai_xe SET thang=?,nam=?,lai_xe_id=?,ten_lai_xe=?,luong_trach_nhiem=?,tong_luong_chuyen_cn=?,tong_luong_chuyen_dl=?,phu_cap=?,khau_tru=?,tong_luong=?,da_thanh_toan=?,ngay_thanh_toan=?,ghi_chu=? WHERE id=?");
        $s->bind_param("iiisiiiiiiiisi",$thang,$nam,$lxid,$ten,$ltn,$lcn,$ldl,$pc,$kt,$tl,$datt,$ngaytt,$ghi,$id);
    }
    $s->execute(); $s->close();
}
header("Location: quan_li_luong_lai_xe.php?thang=$thang&nam=$nam"); exit();