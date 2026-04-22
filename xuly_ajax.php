<?php
include 'conn.php';
header('Content-Type: application/json; charset=utf-8');

$type = $_GET['type'] ?? '';

if ($type == 'get_tuyen') {
    $ma = $_GET['ma'] ?? '';
    $stmt = $conn->prepare("SELECT * FROM danh_muc_tuyen_duong WHERE ma_tuyen = ?");
    $stmt->bind_param("s", $ma);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    if ($row) {
        $row['ten_khachhang'] = $row['khach_hang'];
    }
    echo json_encode($row ?: null); 
    $stmt->close();

} elseif ($type == 'get_xe') {
    $bs = $_GET['bs'] ?? '';
    $stmt = $conn->prepare("SELECT ten_lai_xe FROM danh_muc_xe WHERE bien_so = ?");
    $stmt->bind_param("s", $bs);
    $stmt->execute();
    $res = $stmt->get_result();
    echo json_encode($res->fetch_assoc() ?: null);
    $stmt->close();

} elseif ($type == 'get_luong_chuyen') {
    $lai_xe_id = intval($_GET['lai_xe_id'] ?? 0);
    $thang = intval($_GET['thang'] ?? date('n'));
    $nam   = intval($_GET['nam']   ?? date('Y'));
    
    $d1 = "$nam-".str_pad($thang, 2, '0', STR_PAD_LEFT)."-01";
    $d2 = date('Y-m-t', strtotime($d1));
    
    $stmt0 = $conn->prepare("SELECT ten_lai_xe FROM danh_muc_lai_xe WHERE id = ?");
    $stmt0->bind_param("i", $lai_xe_id);
    $stmt0->execute();
    $r0 = $stmt0->get_result();
    $ten = $r0->fetch_assoc()['ten_lai_xe'] ?? '';
    $stmt0->close();

    $stmt1 = $conn->prepare("SELECT SUM(luong_lai_xe) as tong FROM xe_chay_cong_nhan WHERE lai_xe = ? AND ngay_chay BETWEEN ? AND ?");
    $stmt1->bind_param("sss", $ten, $d1, $d2);
    $stmt1->execute();
    $cn = $stmt1->get_result()->fetch_assoc()['tong'] ?? 0;
    $stmt1->close();

    $stmt2 = $conn->prepare("SELECT SUM(luong_lai_xe) as tong FROM xe_chay_du_lich WHERE lai_xe = ? AND ngay_di BETWEEN ? AND ?");
    $stmt2->bind_param("sss", $ten, $d1, $d2);
    $stmt2->execute();
    $dl = $stmt2->get_result()->fetch_assoc()['tong'] ?? 0;
    $stmt2->close();

    echo json_encode(['cn' => intval($cn), 'dl' => intval($dl)]);

} elseif ($type == 'get_nv') {
    $dt = $_GET['dt'] ?? 'Lái xe';
    $arr = [];
    
    if ($dt == 'Lái xe') {
        $res = $conn->query("SELECT ten_lai_xe as ten, 'Lái xe' as cv FROM danh_muc_lai_xe WHERE da_nghi=0 ORDER BY ten_lai_xe");
        while ($r = $res->fetch_assoc()) {
            $arr[] = $r;
        }
    } else {
        $res = $conn->query("SELECT DISTINCT fullname as ten, role as cv FROM users ORDER BY fullname");
        while ($r = $res->fetch_assoc()) {
            $arr[] = ['ten' => $r['ten'], 'cv' => $r['cv']];
        }
    }
    echo json_encode($arr);

} elseif ($type == 'get_du_no_tamung') {
    $nguoi = $_GET['nguoi'] ?? '';
    $stmt  = $conn->prepare("SELECT SUM(so_tien) as tong FROM tam_ung_luong WHERE nguoi_tam_ung = ? AND trang_thai = 'Chưa hoàn trả'");
    $stmt->bind_param("s", $nguoi);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    echo json_encode(['tong' => intval($row['tong'] ?? 0)]);
}
