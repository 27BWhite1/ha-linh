<?php
include 'conn.php';
$thang=intval($_POST['thang']); $nam=intval($_POST['nam']);
$d1="$nam-".str_pad($thang,2,'0',STR_PAD_LEFT)."-01";
$d2=date('Y-m-t',strtotime($d1));

$lxs=$conn->query("SELECT id,ten_lai_xe,luong_trach_nhiem FROM danh_muc_lai_xe WHERE da_nghi=0 ORDER BY ten_lai_xe");
while($lx=$lxs->fetch_assoc()){

    $r1=$conn->query("SELECT SUM(luong_lai_xe) as tong FROM xe_chay_cong_nhan WHERE lai_xe='{$lx['ten_lai_xe']}' AND ngay_chay BETWEEN '$d1' AND '$d2'");
    $lcn=($r1->fetch_assoc()['tong']??0);

    $r2=$conn->query("SELECT SUM(luong_lai_xe) as tong FROM xe_chay_du_lich WHERE lai_xe='{$lx['ten_lai_xe']}' AND ngay_di BETWEEN '$d1' AND '$d2'");
    $ldl=($r2->fetch_assoc()['tong']??0);
    $tl=$lx['luong_trach_nhiem']+$lcn+$ldl;

    $ck=$conn->query("SELECT id FROM luong_lai_xe WHERE thang=$thang AND nam=$nam AND lai_xe_id={$lx['id']}");
    if($ck->num_rows==0){
        $s=$conn->prepare("INSERT INTO luong_lai_xe (thang,nam,lai_xe_id,ten_lai_xe,luong_trach_nhiem,tong_luong_chuyen_cn,tong_luong_chuyen_dl,tong_luong) VALUES(?,?,?,?,?,?,?,?)");
        $s->bind_param("iiisiiii",$thang,$nam,$lx['id'],$lx['ten_lai_xe'],$lx['luong_trach_nhiem'],$lcn,$ldl,$tl);
        $s->execute(); 
        $s->close();
    }
}
header("Location: quan_li_luong_lai_xe.php?thang=$thang&nam=$nam"); 
exit();
?>