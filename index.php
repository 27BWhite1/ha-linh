<?php
session_start();
require_once 'conn.php';

if (!isset($_SESSION['user_id'])) { header("Location: dn.php"); exit(); }

$stats = [
    'total_vehicles'  => 0,
    'total_drivers'   => 0,
    'total_customers' => 0,
    'total_routes'    => 0
];

$r = $conn->query("SELECT COUNT(*) as c FROM danh_muc_xe");
$stats['total_vehicles']  = $r ? $r->fetch_assoc()['c'] : 0;

$r = $conn->query("SELECT COUNT(*) as c FROM danh_muc_lai_xe WHERE da_nghi=0");
$stats['total_drivers']   = $r ? $r->fetch_assoc()['c'] : 0;

$r = $conn->query("SELECT COUNT(*) as c FROM danh_muc_khach_hang");
$stats['total_customers'] = $r ? $r->fetch_assoc()['c'] : 0;

$r = $conn->query("SELECT COUNT(*) as c FROM danh_muc_tuyen_duong");
$stats['total_routes']    = $r ? $r->fetch_assoc()['c'] : 0;

$thang_ht = intval(date('n'));
$nam_ht   = intval(date('Y'));
$chart_labels = $chart_dt = $chart_ln = [];

for ($i = 5; $i >= 0; $i--) {
    $mt  = mktime(0, 0, 0, $thang_ht - $i, 1, $nam_ht);
    $mm  = date('n', $mt);
    $yy  = date('Y', $mt);
    $dd1 = "$yy-" . str_pad($mm, 2, '0', STR_PAD_LEFT) . "-01";
    $dd2 = date('Y-m-t', strtotime($dd1));

    $chart_labels[] = "T$mm/$yy";

    $rc = $conn->query("SELECT SUM(cuoc_xe) as v FROM xe_chay_cong_nhan WHERE ngay_chay BETWEEN '$dd1' AND '$dd2'");
    $rd = $conn->query("SELECT SUM(don_gia) as v FROM xe_chay_du_lich WHERE ngay_di BETWEEN '$dd1' AND '$dd2'");
    $dt = ($rc->fetch_assoc()['v'] ?? 0) + ($rd->fetch_assoc()['v'] ?? 0);

    $rl = $conn->query("SELECT SUM(tong_luong) as v FROM luong_lai_xe WHERE thang=$mm AND nam=$yy");
    $rx = $conn->query("SELECT SUM(so_tien) as v FROM chi_phi_xe WHERE YEAR(ngay)=$yy AND MONTH(ngay)=$mm");
    $rg = $conn->query("SELECT SUM(so_tien) as v FROM chi_phi_chung WHERE thang=$mm AND nam=$yy");
    $cp = ($rl->fetch_assoc()['v'] ?? 0) + ($rx->fetch_assoc()['v'] ?? 0) + ($rg->fetch_assoc()['v'] ?? 0);

    $chart_dt[] = round($dt / 1000000, 1);
    $chart_ln[] = round(($dt - $cp) / 1000000, 1);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ thống Quản lý Vận tải - Hà Linh Transport</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body { margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .main-nav { display: none !important; }
        
        .top-header { position: fixed; top: 0; left: 0; right: 0; z-index: 1001; background: white; border-bottom: 1px solid #e0e0e0; }
        
        .dashboard-layout { display: flex !important; min-height: 100vh; background: #f5f7fa; padding-top: 60px; }
        .dashboard-sidebar { width: 260px; background: white; border-right: 1px solid #e0e0e0; position: fixed; height: calc(100vh - 60px); top: 60px; overflow-y: auto; z-index: 100; }
        .sidebar-header { padding: 25px 20px; border-bottom: 1px solid #e0e0e0; }
        .sidebar-header h3 { margin: 0; font-size: 20px; color: #be0000; font-weight: 700; }
        .sidebar-nav { padding: 15px 0; }
        .nav-item { display: flex; align-items: center; padding: 12px 20px; color: #5f6368; text-decoration: none; transition: all 0.2s; font-size: 14px; font-weight: 500; }
        .nav-item:hover { background: #f5f5f5; color: #be0000; }
        .nav-item.active { background: #fff5f5; color: #be0000; border-left: 3px solid #be0000; }
        .nav-icon { margin-right: 12px; font-size: 18px; }
        .nav-group { margin-top: 20px; }
        .nav-group-title { padding: 8px 20px; font-size: 11px; font-weight: 700; color: #9e9e9e; letter-spacing: 0.5px; }
        
        .dashboard-main { margin-left: 260px; flex: 1; padding: 30px; background: #f5f7fa; margin-top: 0; }
        .dashboard-header { margin-bottom: 30px; }
        .dashboard-header h1 { margin: 0 0 5px; font-size: 28px; font-weight: 700; color: #202124; }
        .dashboard-header p { margin: 0; font-size: 14px; color: #5f6368; }
        
        .stats-row { display: grid !important; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px; }
        .stat-card-compact { background: white !important; padding: 20px; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); position: relative; overflow: hidden; }
        .stat-card-compact::before { content: ''; position: absolute; top: 0; left: 0; width: 4px; height: 100%; }
        .stat-card-compact.blue::before { background: #4285f4; }
        .stat-card-compact.green::before { background: #34a853; }
        .stat-card-compact.orange::before { background: #fbbc04; }
        .stat-card-compact.purple::before { background: #9b59b6; }
        .stat-label { font-size: 13px; color: #5f6368; margin-bottom: 8px; font-weight: 500; }
        .stat-value { font-size: 32px; font-weight: 700; color: #202124; margin-bottom: 5px; }
        .stat-card-compact .stat-icon { position: absolute; right: 15px; top: 50%; transform: translateY(-50%); font-size: 40px; opacity: 0.15; }
        
        .dashboard-grid { display: grid !important; grid-template-columns: 2fr 1fr; gap: 20px; }
        .dashboard-col-left, .dashboard-col-right { display: flex; flex-direction: column; gap: 20px; }
        
        .dashboard-card { background: white !important; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow: hidden; }
        .card-header { padding: 20px; border-bottom: 1px solid #e0e0e0; }
        .card-header h3 { margin: 0; font-size: 16px; font-weight: 600; color: #202124; }
        .card-body { padding: 20px; }
        
        .quick-actions { display: grid !important; grid-template-columns: repeat(2, 1fr); gap: 12px; }
        .action-btn { display: flex; align-items: center; gap: 12px; padding: 15px; background: #f8f9fa; border-radius: 8px; text-decoration: none; color: #202124; font-size: 14px; font-weight: 500; transition: all 0.2s; }
        .action-btn:hover { background: #e8f0fe; color: #be0000; }
        .action-icon { font-size: 24px; }
        
        .report-list { display: flex; flex-direction: column; gap: 10px; }
        .report-item { display: flex; align-items: center; gap: 12px; padding: 15px; background: #f8f9fa; border-radius: 8px; text-decoration: none; color: #202124; transition: all 0.2s; }
        .report-item:hover { background: #e8f0fe; transform: translateX(5px); }
        .report-icon { width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; }
        .report-icon.blue { background: #e8f0fe; }
        .report-icon.green { background: #e6f4ea; }
        .report-icon.orange { background: #fef7e0; }
        .report-icon.purple { background: #f3e8fd; }
        .report-name { flex: 1; font-size: 14px; font-weight: 500; }
        .report-arrow { color: #5f6368; font-size: 18px; }
        
        .activity-list { display: flex; flex-direction: column; gap: 15px; }
        .activity-item { display: flex; gap: 12px; align-items: flex-start; }
        .activity-icon { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; }
        .activity-icon.blue { background: #e8f0fe; }
        .activity-icon.green { background: #e6f4ea; }
        .activity-icon.orange { background: #fef7e0; }
        .activity-content { flex: 1; }
        .activity-title { font-size: 14px; font-weight: 500; color: #202124; margin-bottom: 4px; }
        .activity-time { font-size: 12px; color: #5f6368; }
        
        footer { margin-left: 260px; }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="dashboard-layout">
    <aside class="dashboard-sidebar">
        <div class="sidebar-header">
            <h3>📊 Dashboard</h3>
        </div>
        <nav class="sidebar-nav">
            <a href="index.php" class="nav-item active">
                <span class="nav-icon">🏠</span>
                <span>Trang chủ</span>
            </a>
            <div class="nav-group">
                <div class="nav-group-title">DANH MỤC</div>
                <a href="danh-muc-khachhang.php" class="nav-item">
                    <span class="nav-icon">👥</span>
                    <span>Khách hàng</span>
                </a>
                <a href="danh_muc_hang_van_tai.php" class="nav-item">
                    <span class="nav-icon">🏢</span>
                    <span>Hãng vận tải</span>
                </a>
                <a href="danh_muc_lai_xe.php" class="nav-item">
                    <span class="nav-icon">👨‍✈️</span>
                    <span>Lái xe</span>
                </a>
                <a href="danh_muc_tuyen_duong.php" class="nav-item">
                    <span class="nav-icon">🗺️</span>
                    <span>Tuyến đường</span>
                </a>
                <a href="danh_muc_loai_xe.php" class="nav-item">
                    <span class="nav-icon">📋</span>
                    <span>Loại xe</span>
                </a>
                <a href="danh_muc_xe.php" class="nav-item">
                    <span class="nav-icon"></span>
                    <span>Xe</span>
                </a>
            </div>
            <div class="nav-group">
                <div class="nav-group-title">VẬN HÀNH</div>
                <a href="xe_chay_cong_nhan.php" class="nav-item">
                    <span class="nav-icon">🚌</span>
                    <span>Xe công nhân</span>
                </a>
                <a href="xe_chay_du_lich.php" class="nav-item">
                    <span class="nav-icon">🌴</span>
                    <span>Xe du lịch</span>
                </a>
            </div>
            <div class="nav-group">
                <div class="nav-group-title">QUẢN LÝ CHI PHÍ</div>
                <a href="chi_phi_xe.php" class="nav-item">
                    <span class="nav-icon">💵</span>
                    <span>Chi phí xe</span>
                </a>
                <a href="so_thu_khach_hang.php" class="nav-item">
                    <span class="nav-icon">💰</span>
                    <span>Số thu khách hàng</span>
                </a>
                <a href="so_chi_tra_chu_xe.php" class="nav-item">
                    <span class="nav-icon">💸</span>
                    <span>Số chi trả chủ xe</span>
                </a>
                <a href="chi_phi_chung.php" class="nav-item">
                    <span class="nav-icon">📝</span>
                    <span>Chi phí chung</span>
                </a>
                <a href="quan_li_luong_lai_xe.php" class="nav-item">
                    <span class="nav-icon">💼</span>
                    <span>Lương lái xe</span>
                </a>
                <a href="tam_ung_luong.php" class="nav-item">
                    <span class="nav-icon">📄</span>
                     <span>Phiếu tạm ứng lương</span>
                </a>
            </div>
            <div class="nav-group">
                <div class="nav-group-title">BÁO CÁO & THỐNG KÊ</div>
                <a href="bao_cao_xe_cong_nhan.php" class="nav-item">
                    <span class="nav-icon">📊</span>
                    <span>BC Xe công nhân</span>
                </a>
                <a href="bao_cao_xe_du_lich.php" class="nav-item">
                    <span class="nav-icon">📈</span>
                    <span>BC Xe du lịch</span>
                </a>
                <a href="bang_luong_lai_xe.php" class="nav-item">
                    <span class="nav-icon">💵</span>
                    <span>Bảng lương lái xe</span>
                </a>
                <a href="thong_ke_cong_no_khach_hang.php" class="nav-item">
                    <span class="nav-icon">📋</span>
                    <span>Công nợ KH</span>
                </a>
                <a href="thong_ke_cong_no_chu_xe.php" class="nav-item">
                    <span class="nav-icon">📄</span>
                    <span>Công nợ chủ xe</span>
                </a>
                <a href="bang_loi_nhuan_theo_thang.php" class="nav-item">
                    <span class="nav-icon">💰</span>
                    <span>Lợi nhuận tháng</span>
                </a>
            </div>
            <div class="nav-group">
                <div class="nav-group-title">HỆ THỐNG</div>
                <a href="quan_li_nguoi_dung.php" class="nav-item">
                    <span class="nav-icon">👤</span>
                    <span>Quản lý người dùng</span>
                </a>
            </div>
        </nav>
    </aside>

    <main class="dashboard-main">
        <div class="dashboard-header">
            <div>
                <h1>Dashboard Quản Lý Vận Tải</h1>
                <p>Tổng quan hoạt động và thống kê hệ thống Hà Linh Transport</p>
            </div>
        </div>

        <div class="stats-row">
            <div class="stat-card-compact blue">
                <div class="stat-label">Tổng số xe</div>
                <div class="stat-value"><?php echo $stats['total_vehicles']; ?></div>
                <div class="stat-icon">🚗</div>
            </div>
            <div class="stat-card-compact green">
                <div class="stat-label">Lái xe</div>
                <div class="stat-value"><?php echo $stats['total_drivers']; ?></div>
                <div class="stat-icon">👨‍✈️</div>
            </div>
            <div class="stat-card-compact orange">
                <div class="stat-label">Khách hàng</div>
                <div class="stat-value"><?php echo $stats['total_customers']; ?></div>
                <div class="stat-icon">👥</div>
            </div>
            <div class="stat-card-compact purple">
                <div class="stat-label">Tuyến đường</div>
                <div class="stat-value"><?php echo $stats['total_routes']; ?></div>
                <div class="stat-icon">🗺️</div>
            </div>
        </div>

        <div class="dashboard-grid">
            <div class="dashboard-col-left">
                <div class="dashboard-card">
                    <div class="card-header">
                        <h3>Truy Cập Nhanh</h3>
                    </div>
                    <div class="card-body">
                        <div class="quick-actions">
                            <a href="danh-muc-khachhang.php" class="action-btn">
                                <span class="action-icon">👥</span>
                                <span>Khách hàng</span>
                            </a>
                            <a href="danh_muc_hang_van_tai.php" class="action-btn">
                                <span class="action-icon">🏢</span>
                                <span>Hãng vận tải</span>
                            </a>
                            <a href="danh_muc_lai_xe.php" class="action-btn">
                                <span class="action-icon">👨‍✈️</span>
                                <span>Lái xe</span>
                            </a>
                            <a href="danh_muc_xe.php" class="action-btn">
                                <span class="action-icon">🚗</span>
                                <span>Danh mục xe</span>
                            </a>
                            <a href="xe_chay_cong_nhan.php" class="action-btn">
                                <span class="action-icon">🚌</span>
                                <span>Xe công nhân</span>
                            </a>
                            <a href="xe_chay_du_lich.php" class="action-btn">
                                <span class="action-icon">🌴</span>
                                <span>Xe du lịch</span>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="dashboard-card">
                    <div class="card-header">
                        <h3>Báo Cáo Thống Kê</h3>
                    </div>
                    <div class="card-body">
                        <div class="report-list">
                            <a href="bao_cao_xe_cong_nhan.php" class="report-item">
                                <span class="report-icon blue">📊</span>
                                <span class="report-name">Báo cáo xe công nhân</span>
                                <span class="report-arrow">→</span>
                            </a>
                            <a href="bao_cao_xe_du_lich.php" class="report-item">
                                <span class="report-icon green">📈</span>
                                <span class="report-name">Báo cáo xe du lịch</span>
                                <span class="report-arrow">→</span>
                            </a>
                            <a href="bang_luong_lai_xe.php" class="report-item">
                                <span class="report-icon orange">💰</span>
                                <span class="report-name">Bảng lương lái xe</span>
                                <span class="report-arrow">→</span>
                            </a>
                            <a href="bang_loi_nhuan_theo_thang.php" class="report-item">
                                <span class="report-icon purple">📉</span>
                                <span class="report-name">Lợi nhuận theo tháng</span>
                                <span class="report-arrow">→</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="dashboard-col-right">
                <div class="dashboard-card">
                    <div class="card-header">
                        <h3>Hoạt Động Gần Đây</h3>
                    </div>
                    <div class="card-body">
                        <div class="activity-list">
                            <div class="activity-item">
                                <div class="activity-icon blue">🚗</div>
                                <div class="activity-content">
                                    <div class="activity-title">Xe mới được thêm</div>
                                    <div class="activity-time">Hôm nay</div>
                                </div>
                            </div>
                            <div class="activity-item">
                                <div class="activity-icon green">👨‍✈️</div>
                                <div class="activity-content">
                                    <div class="activity-title">Lái xe đăng ký</div>
                                    <div class="activity-time">Hôm qua</div>
                                </div>
                            </div>
                            <div class="activity-item">
                                <div class="activity-icon orange">📊</div>
                                <div class="activity-content">
                                    <div class="activity-title">Báo cáo tháng mới</div>
                                    <div class="activity-time">2 ngày trước</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard-card" style="margin-top: 20px;">
            <div class="card-header">
                <h3>📈 Biểu Đồ Doanh Thu & Lợi Nhuận 6 Tháng Gần Đây</h3>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" style="max-height: 300px;"></canvas>
            </div>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?=json_encode($chart_labels)?>,
            datasets: [{
                label: 'Doanh Thu (triệu VNĐ)',
                data: <?=json_encode($chart_dt)?>,
                borderColor: '#be0000',
                backgroundColor: 'rgba(190, 0, 0, 0.08)',
                tension: 0.4,
                fill: true,
                borderWidth: 2,
                pointRadius: 4,
                pointBackgroundColor: '#be0000'
            }, {
                label: 'Lợi Nhuận (triệu VNĐ)',
                data: <?=json_encode($chart_ln)?>,
                borderColor: '#34a853',
                backgroundColor: 'rgba(52, 168, 83, 0.08)',
                tension: 0.4,
                fill: true,
                borderWidth: 2,
                pointRadius: 4,
                pointBackgroundColor: '#34a853'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value + 'M';
                        }
                    }
                }
            }
        }
    });
</script>
</body>
</html>