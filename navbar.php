<style>
@import url('https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800&display=swap');
*, *::before, *::after { box-sizing: border-box; }
.hl-navbar {
    font-family: 'Be Vietnam Pro', sans-serif;
    background: #fff;
    box-shadow: 0 1px 0 #f0f0f5, 0 4px 20px rgba(0,0,0,.06);
    position: sticky;
    top: 0;
    z-index: 500;
}
.hl-topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 28px;
    height: 64px;
    border-bottom: 1px solid #f3f4f8;
}
.hl-logo {
    display: flex;
    align-items: center;
    gap: 12px;
    text-decoration: none;
    flex-shrink: 0;
}
.hl-logo img {
    height: 40px;
    width: auto;
}
.hl-logo-text {
    display: flex;
    flex-direction: column;
    line-height: 1.1;
}
.hl-logo-text strong {
    font-size: 18px;
    font-weight: 800;
    color: #be0000;
    letter-spacing: -.3px;
}
.hl-logo-text span {
    font-size: 10px;
    font-weight: 500;
    color: #9ca3af;
    letter-spacing: 1.5px;
    text-transform: uppercase;
}
.hl-user {
    display: flex;
    align-items: center;
    gap: 16px;
}
.hl-user-info {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 6px 14px;
    background: #f8f9fc;
    border-radius: 40px;
    border: 1px solid #eef0f7;
}
.hl-avatar-sm {
    width: 30px; height: 30px;
    border-radius: 50%;
    background: linear-gradient(135deg, #be0000, #7b0000);
    color: #fff;
    font-size: 12px;
    font-weight: 700;
    display: flex; align-items: center; 
    justify-content: center;
    flex-shrink: 0;
}
.hl-user-name {
    font-size: 13px;
    font-weight: 600;
    color: #1a1f36;
}
.hl-user-role {
    font-size: 11px;
    color: #9ca3af;
    font-weight: 400;
}
.hl-logout {
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 7px 14px;
    background: #fff5f5;
    color: #be0000;
    border: 1px solid #fecaca;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    text-decoration: none;
    transition: all .15s;
}
.hl-logout:hover {
    background: #be0000;
    color: #fff;
    border-color: #be0000;
}
.hl-menubar {
    display: flex;
    align-items: center;
    padding: 0 24px;
    height: 44px;
    gap: 2px;
}
.hl-menu-item {
    position: relative;
}
.hl-menu-link {
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 0 14px;
    height: 44px;
    font-size: 13px;
    font-weight: 600;
    color: #4b5263;
    text-decoration: none;
    border-radius: 8px;
    white-space: nowrap;
    transition: color .15s, background .15s;
    position: relative;
}
.hl-menu-link:hover {
    color: #be0000;
    background: #fff5f5;
}
.hl-menu-link.active {
    color: #be0000;
}
.hl-menu-link.active::after {
    content: '';
    position: absolute;
    bottom: 0; 
    left: 14px; 
    right: 14px;
    height: 2px;
    background: #be0000;
    border-radius: 2px 2px 0 0;
}
.hl-chevron {
    width: 14px; 
    height: 14px;
    opacity: .5;
    transition: transform .2s;
    flex-shrink: 0;
}
.hl-menu-item:hover .hl-chevron {
    transform: rotate(180deg);
    opacity: 1;
}
.hl-dropdown {
    display: none;
    position: absolute;
    top: calc(100% + 4px);
    left: 0;
    min-width: 260px;
    background: #fff;
    border: 1px solid #eef0f7;
    border-radius: 12px;
    box-shadow: 0 8px 32px rgba(0,0,0,.12);
    padding: 6px;
    z-index: 9999;
    animation: dropIn .15s ease;
}
@keyframes dropIn {
    from { opacity: 0; transform: translateY(-6px); }
    to   { opacity: 1; transform: translateY(0); }
}
.hl-menu-item:hover .hl-dropdown { display: block; }
.hl-dropdown-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px 12px;
    border-radius: 8px;
    font-size: 13px;
    color: #374151;
    text-decoration: none;
    font-weight: 500;
    transition: background .12s, color .12s;
}
.hl-dropdown-item:hover {
    background: #fff5f5;
    color: #be0000;
}
.hl-dropdown-item .icon {
    width: 26px; 
    height: 26px;
    background: #f3f4f6;
    border-radius: 6px;
    display: flex; 
    align-items: center; 
    justify-content: center;
    font-size: 14px;
    flex-shrink: 0;
    transition: background .12s;
}
.hl-dropdown-item:hover .icon { background: #fef2f2; }
.hl-dropdown-sep {
    height: 1px;
    background: #f3f4f8;
    margin: 4px 6px;
}
</style>

<header class="hl-navbar">
    <div class="hl-topbar">
        <a href="index.php" class="hl-logo">
            <img src="logo_ha_linh.png" alt="Hà Linh" onerror="this.style.display='none'">
            <div class="hl-logo-text">
                <strong>HÀ LINH TRANSPORT</strong>
                <span>Hệ thống quản lý vận tải</span>
            </div>
        </a>

        <div class="hl-user">
            <?php if (isset($_SESSION['fullname'])): ?>
                <div class="hl-user-info">
                    <div class="hl-avatar-sm">
                        <?= strtoupper(mb_substr($_SESSION['fullname'], 0, 1)) ?>
                    </div>
                    <div>
                        <div class="hl-user-name"><?= htmlspecialchars($_SESSION['fullname']) ?></div>
                        <div class="hl-user-role"><?= htmlspecialchars($_SESSION['role']) ?></div>
                    </div>
                </div>
                <a href="logout.php" class="hl-logout">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                        <polyline points="16 17 21 12 16 7"/>
                        <line x1="21" y1="12" x2="9" y2="12"/>
                    </svg>
                    Đăng xuất
                </a>
            <?php else: ?>
                <a href="dn.php" class="hl-logout">Đăng nhập</a>
            <?php endif; ?>
        </div>
    </div>

    <nav class="hl-menubar">

        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'Quản lý'): ?>
        <div class="hl-menu-item">
            <a href="quan_li_nguoi_dung.php" class="hl-menu-link">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
                Người dùng
            </a>
        </div>
        <?php endif; ?>

        <div class="hl-menu-item">
            <a href="#" class="hl-menu-link">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
                Khai báo danh mục
                <svg class="hl-chevron" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
            </a>
            <div class="hl-dropdown">
                <a href="danh-muc-khachhang.php" class="hl-dropdown-item"><span class="icon">👥</span> Khách hàng</a>
                <a href="danh_muc_hang_van_tai.php" class="hl-dropdown-item"><span class="icon">🏢</span> Hãng vận tải (Chủ xe)</a>
                <a href="danh_muc_lai_xe.php" class="hl-dropdown-item"><span class="icon">🧑‍✈️</span> Lái xe</a>
                <div class="hl-dropdown-sep"></div>
                <a href="danh_muc_tuyen_duong.php" class="hl-dropdown-item"><span class="icon">🗺️</span> Tuyến đường</a>
                <a href="danh_muc_loai_xe.php" class="hl-dropdown-item"><span class="icon">📋</span> Loại xe</a>
                <a href="danh_muc_xe.php" class="hl-dropdown-item"><span class="icon">🚗</span> Xe</a>
            </div>
        </div>

        <div class="hl-menu-item">
            <a href="#" class="hl-menu-link">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h5l2 4v4h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                Quản lý xe chạy
                <svg class="hl-chevron" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
            </a>
            <div class="hl-dropdown">
                <a href="xe_chay_cong_nhan.php" class="hl-dropdown-item"><span class="icon">🚌</span> Xe chạy công nhân</a>
                <a href="xe_chay_du_lich.php" class="hl-dropdown-item"><span class="icon">🌴</span> Xe chạy du lịch</a>
            </div>
        </div>

        <div class="hl-menu-item">
            <a href="#" class="hl-menu-link">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/><path d="M12 6v6l4 2"/></svg>
                Quản lý chi phí
                <svg class="hl-chevron" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
            </a>
            <div class="hl-dropdown">
                <a href="chi_phi_xe.php" class="hl-dropdown-item"><span class="icon">🔧</span> Chi phí xe</a>
                <a href="so_thu_khach_hang.php" class="hl-dropdown-item"><span class="icon">💰</span> Số thu tiền khách hàng</a>
                <a href="so_chi_tra_chu_xe.php" class="hl-dropdown-item"><span class="icon">🏢</span> Số chi trả chủ xe</a>
                <a href="chi_phi_chung.php" class="hl-dropdown-item"><span class="icon">📦</span> Chi phí chung</a>
                <div class="hl-dropdown-sep"></div>
                <a href="quan_li_luong_lai_xe.php" class="hl-dropdown-item"><span class="icon">💵</span> Quản lý lương lái xe</a>
                <a href="tam_ung_luong.php" class="hl-dropdown-item"><span class="icon">📄</span> Phiếu tạm ứng lương</a>
            </div>
        </div>

        <div class="hl-menu-item">
            <a href="#" class="hl-menu-link">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 3h18v18H3z"/><path d="M9 9h6M9 12h6M9 15h4"/></svg>
                Báo cáo thống kê
                <svg class="hl-chevron" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg>
            </a>
            <div class="hl-dropdown" style="left:auto; right:0;">
                <a href="bao_cao_xe_cong_nhan.php" class="hl-dropdown-item"><span class="icon">📊</span> Báo cáo xe công nhân</a>
                <a href="bao_cao_xe_du_lich.php" class="hl-dropdown-item"><span class="icon">📈</span> Báo cáo xe du lịch</a>
                <a href="bang_luong_lai_xe.php" class="hl-dropdown-item"><span class="icon">📋</span> Bảng lương lái xe</a>
                <div class="hl-dropdown-sep"></div>
                <a href="thong_ke_cong_no_khach_hang.php" class="hl-dropdown-item"><span class="icon">👥</span> Công nợ khách hàng</a>
                <a href="thong_ke_cong_no_chu_xe.php" class="hl-dropdown-item"><span class="icon">🏢</span> Công nợ chủ xe</a>
                <a href="bang_loi_nhuan_theo_thang.php" class="hl-dropdown-item"><span class="icon">💹</span> Lợi nhuận theo tháng</a>
            </div>
        </div>
    </nav>
</header>