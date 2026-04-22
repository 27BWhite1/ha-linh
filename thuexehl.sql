-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th4 17, 2026 lúc 01:55 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `thuexehl`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chi_phi_chung`
--

CREATE TABLE `chi_phi_chung` (
  `id` int(11) NOT NULL,
  `ngay` date NOT NULL,
  `thang` int(2) DEFAULT NULL,
  `nam` int(4) DEFAULT NULL,
  `loai_chi_phi` varchar(100) DEFAULT NULL,
  `mo_ta` varchar(255) DEFAULT NULL,
  `so_tien` decimal(15,0) DEFAULT 0,
  `nguoi_chi` varchar(100) DEFAULT NULL,
  `ghi_chu` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chi_phi_xe`
--

CREATE TABLE `chi_phi_xe` (
  `id` int(11) NOT NULL,
  `ngay` date NOT NULL,
  `bien_so` varchar(20) DEFAULT NULL,
  `loai_chi_phi` varchar(100) DEFAULT NULL,
  `mo_ta` varchar(255) DEFAULT NULL,
  `so_tien` decimal(15,0) DEFAULT 0,
  `nguoi_chi` varchar(100) DEFAULT NULL,
  `ghi_chu` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `danh_muc_hang_van_tai`
--

CREATE TABLE `danh_muc_hang_van_tai` (
  `id` int(11) NOT NULL,
  `ma_hang` varchar(50) NOT NULL,
  `ten_hang` varchar(255) NOT NULL,
  `so_cccd` varchar(20) NOT NULL,
  `no_cu` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `danh_muc_hang_van_tai`
--

INSERT INTO `danh_muc_hang_van_tai` (`id`, `ma_hang`, `ten_hang`, `so_cccd`, `no_cu`) VALUES
(1, 'Toan', 'Ngô Hữu Toàn', '', 0.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `danh_muc_khach_hang`
--

CREATE TABLE `danh_muc_khach_hang` (
  `id` int(11) NOT NULL,
  `ma_khachhang` varchar(100) NOT NULL,
  `ten_khachhang` varchar(255) NOT NULL,
  `so_cccd` varchar(50) NOT NULL,
  `no_cu` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `danh_muc_khach_hang`
--

INSERT INTO `danh_muc_khach_hang` (`id`, `ma_khachhang`, `ten_khachhang`, `so_cccd`, `no_cu`) VALUES
(4, 'AMI', 'AMI', '', 0.00),
(6, 'ALUMINIUM', 'ALUMINIUM', '', 0.00),
(7, 'FPT', 'FPT', '', 0.00),
(8, 'Haiya', 'Haiya', '', 0.00),
(9, 'Sagemcom', 'Sagemcom', '', 0.00),
(10, 'YUANLI', 'YUANLI', '', 0.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `danh_muc_lai_xe`
--

CREATE TABLE `danh_muc_lai_xe` (
  `id` int(11) NOT NULL,
  `ma_lai_xe` varchar(20) NOT NULL,
  `ten_lai_xe` varchar(100) NOT NULL,
  `so_cccd` varchar(20) DEFAULT NULL,
  `luong_trach_nhiem` decimal(15,2) DEFAULT 0.00,
  `da_nghi` tinyint(1) DEFAULT 0,
  `ngay_nghi` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `danh_muc_lai_xe`
--

INSERT INTO `danh_muc_lai_xe` (`id`, `ma_lai_xe`, `ten_lai_xe`, `so_cccd`, `luong_trach_nhiem`, `da_nghi`, `ngay_nghi`) VALUES
(4, '1', 'Bùi Văn Huynh', '', 500000.00, 0, NULL),
(5, '2', 'Nguyễn Danh Chiến', '', 800000.00, 0, NULL),
(6, '3', 'Nguyễn Khắc Thẩm ', '', 500000.00, 0, NULL),
(7, '4', 'Vũ Quang', '', 500000.00, 0, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `danh_muc_loai_xe`
--

CREATE TABLE `danh_muc_loai_xe` (
  `id` int(11) NOT NULL,
  `ma_loai_xe` varchar(50) NOT NULL,
  `phan_tram_luong_lai_xe` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `danh_muc_loai_xe`
--

INSERT INTO `danh_muc_loai_xe` (`id`, `ma_loai_xe`, `phan_tram_luong_lai_xe`) VALUES
(1, 'Xe04', 20),
(2, 'Xe07', 20),
(3, 'Xe16', 17),
(5, 'Xe29', 15),
(6, 'Xe34', 15),
(7, 'Xe45', 15);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `danh_muc_tuyen_duong`
--

CREATE TABLE `danh_muc_tuyen_duong` (
  `id` int(11) NOT NULL,
  `ma_tuyen` varchar(20) DEFAULT NULL,
  `ten_ca` varchar(100) DEFAULT NULL,
  `ten_tuyen` varchar(255) DEFAULT NULL,
  `khach_hang` varchar(255) DEFAULT NULL,
  `gio_don` time DEFAULT NULL,
  `gio_ve` time DEFAULT NULL,
  `don_gia_04` decimal(15,2) DEFAULT 0.00,
  `don_gia_07` decimal(15,2) DEFAULT 0.00,
  `don_gia_16` decimal(15,2) DEFAULT 0.00,
  `don_gia_29` decimal(15,2) DEFAULT 0.00,
  `don_gia_34` decimal(15,2) DEFAULT 0.00,
  `don_gia_45` decimal(15,2) DEFAULT 0.00,
  `luong_xe_04` decimal(15,2) DEFAULT 0.00,
  `luong_xe_07` decimal(15,2) DEFAULT 0.00,
  `luong_xe_16` decimal(15,2) DEFAULT 0.00,
  `luong_xe_29` decimal(15,2) DEFAULT 0.00,
  `luong_xe_34` decimal(15,2) DEFAULT 0.00,
  `luong_xe_45` decimal(15,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `danh_muc_tuyen_duong`
--

INSERT INTO `danh_muc_tuyen_duong` (`id`, `ma_tuyen`, `ten_ca`, `ten_tuyen`, `khach_hang`, `gio_don`, `gio_ve`, `don_gia_04`, `don_gia_07`, `don_gia_16`, `don_gia_29`, `don_gia_34`, `don_gia_45`, `luong_xe_04`, `luong_xe_07`, `luong_xe_16`, `luong_xe_29`, `luong_xe_34`, `luong_xe_45`) VALUES
(3, '1', 'Kiến An - Ca 2', 'Kiến An - Sgc', 'Sagemcom', '17:45:00', '00:00:00', 0.00, 0.00, 460000.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 100000.00, 125000.00, 0.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `danh_muc_xe`
--

CREATE TABLE `danh_muc_xe` (
  `id` int(11) NOT NULL,
  `bien_so` varchar(20) NOT NULL,
  `loai_xe` varchar(50) DEFAULT NULL,
  `ma_lai_xe` int(11) DEFAULT NULL,
  `ten_lai_xe` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `danh_muc_xe`
--

INSERT INTO `danh_muc_xe` (`id`, `bien_so`, `loai_xe`, `ma_lai_xe`, `ten_lai_xe`) VALUES
(2, '15E-01827', 'Xe16', 3, 'Nguyễn Khắc Thẩm '),
(3, '15K-45753', 'Xe07', 1, 'Bùi Văn Huynh');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `luong_lai_xe`
--

CREATE TABLE `luong_lai_xe` (
  `id` int(11) NOT NULL,
  `thang` int(2) NOT NULL,
  `nam` int(4) NOT NULL,
  `lai_xe_id` int(11) DEFAULT NULL,
  `ten_lai_xe` varchar(100) DEFAULT NULL,
  `luong_trach_nhiem` decimal(15,0) DEFAULT 0,
  `tong_luong_chuyen_cn` decimal(15,0) DEFAULT 0,
  `tong_luong_chuyen_dl` decimal(15,0) DEFAULT 0,
  `phu_cap` decimal(15,0) DEFAULT 0,
  `khau_tru` decimal(15,0) DEFAULT 0,
  `tong_luong` decimal(15,0) DEFAULT 0,
  `da_thanh_toan` tinyint(1) DEFAULT 0,
  `ngay_thanh_toan` date DEFAULT NULL,
  `ghi_chu` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `so_chi_tra_chu_xe`
--

CREATE TABLE `so_chi_tra_chu_xe` (
  `id` int(11) NOT NULL,
  `thang` int(2) DEFAULT NULL,
  `nam` int(4) DEFAULT NULL,
  `chu_xe` varchar(255) DEFAULT NULL,
  `dien_giai` varchar(255) DEFAULT NULL,
  `so_tien_phai_tra` decimal(15,0) DEFAULT 0,
  `so_tien_da_tra` decimal(15,0) DEFAULT 0,
  `ngay_tra` date DEFAULT NULL,
  `ghi_chu` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `so_thu_khach_hang`
--

CREATE TABLE `so_thu_khach_hang` (
  `id` int(11) NOT NULL,
  `ngay_thu` date DEFAULT NULL,
  `khach_hang` varchar(255) DEFAULT NULL,
  `loai_xe_chay` varchar(50) DEFAULT 'Công nhân',
  `ma_chung_tu` varchar(50) DEFAULT NULL,
  `dien_giai` varchar(255) DEFAULT NULL,
  `so_tien_phai_thu` decimal(15,0) DEFAULT 0,
  `so_tien_da_thu` decimal(15,0) DEFAULT 0,
  `ghi_chu` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fullname` varchar(100) DEFAULT NULL,
  `role` varchar(50) DEFAULT 'Nhân Viên',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `fullname`, `role`, `created_at`) VALUES
(1, 'nhatquang', '2345', 'quang', 'Nhân Viên', '2026-04-03 07:53:55'),
(2, 'Trungduc', '2345', 'Đức', 'Kế Toán', '2026-04-03 07:58:37'),
(3, 'baominh', '4567', NULL, 'Quản lý', '2026-04-03 08:05:57'),
(4, '123', '1234', 'BAOMINH', 'Quản lý', '2026-04-14 14:16:08');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `xe_chay_cong_nhan`
--

CREATE TABLE `xe_chay_cong_nhan` (
  `id` int(11) NOT NULL,
  `ngay_chay` date DEFAULT NULL,
  `loai_hinh_xe` varchar(50) DEFAULT NULL,
  `ma_tuyen` varchar(50) DEFAULT NULL,
  `bien_so` varchar(20) DEFAULT NULL,
  `lai_xe` varchar(100) DEFAULT NULL,
  `khach_hang` varchar(100) DEFAULT NULL,
  `gio_don` varchar(10) DEFAULT NULL,
  `gio_ve` varchar(10) DEFAULT NULL,
  `cuoc_xe` decimal(15,0) DEFAULT 0,
  `luong_lai_xe` decimal(15,0) DEFAULT 0,
  `chu_xe` varchar(100) DEFAULT NULL,
  `ca_noi` tinyint(1) DEFAULT 0,
  `thue_lai` tinyint(1) DEFAULT 0,
  `ten_lai_thue` varchar(100) DEFAULT NULL,
  `tien_thue_xe` decimal(15,0) DEFAULT 0,
  `ghi_chu` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `xe_chay_cong_nhan`
--

INSERT INTO `xe_chay_cong_nhan` (`id`, `ngay_chay`, `loai_hinh_xe`, `ma_tuyen`, `bien_so`, `lai_xe`, `khach_hang`, `gio_don`, `gio_ve`, `cuoc_xe`, `luong_lai_xe`, `chu_xe`, `ca_noi`, `thue_lai`, `ten_lai_thue`, `tien_thue_xe`, `ghi_chu`) VALUES
(7, '2025-08-15', 'Xe nhà', '1', '15E-01827', 'Nguyễn Khắc Thẩm ', 'Sagemcom', '6:00:00', '00:00:00', 460000, 100000, NULL, 0, 0, NULL, 0, ''),
(8, '2025-08-17', 'Xe nhà', '1', '15E-01827', 'Nguyễn Khắc Thẩm ', 'Sagemcom', '6:00:00', '00:00:00', 460000, 100000, NULL, 0, 0, NULL, 0, ''),
(9, '2025-08-16', 'Xe nhà', '1', '15E-01827', 'Nguyễn Khắc Thẩm ', 'Sagemcom', '6:00:00', '00:00:00', 460000, 100000, NULL, 0, 0, NULL, 0, ''),
(10, '2025-08-18', 'Xe nhà', '1', '15E-01827', 'Nguyễn Khắc Thẩm ', 'Sagemcom', '6:00:00', '00:00:00', 460000, 100000, NULL, 0, 0, NULL, 0, '');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `xe_chay_du_lich`
--

CREATE TABLE `xe_chay_du_lich` (
  `id` int(11) NOT NULL,
  `ngay_di` date DEFAULT NULL,
  `ngay_ve` date DEFAULT NULL,
  `hanh_trinh` varchar(500) DEFAULT NULL,
  `khach_hang` varchar(255) DEFAULT NULL,
  `bien_so` varchar(20) DEFAULT NULL,
  `loai_xe` varchar(50) DEFAULT NULL,
  `lai_xe` varchar(100) DEFAULT NULL,
  `ten_thue_lai` varchar(100) DEFAULT NULL,
  `gio_xuat_phat` varchar(10) DEFAULT NULL,
  `gio_ve` varchar(10) DEFAULT NULL,
  `don_gia` decimal(15,0) DEFAULT 0,
  `cuoc_xe` decimal(15,0) DEFAULT 0,
  `luong_lai_xe` decimal(15,0) DEFAULT 0,
  `phan_tram_luong` int(11) DEFAULT 0,
  `tien_thue_xe` decimal(15,0) DEFAULT 0,
  `tien_thue_lai` decimal(15,0) DEFAULT 0,
  `lai_xe_thu` decimal(15,0) DEFAULT 0,
  `lai_xe_nop` decimal(15,0) DEFAULT 0,
  `lai_xe_chi` decimal(15,0) DEFAULT 0,
  `chu_xe` varchar(100) DEFAULT NULL,
  `loai_hinh` varchar(50) DEFAULT 'Xe nhà',
  `ghi_chu` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `chi_phi_chung`
--
ALTER TABLE `chi_phi_chung`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `chi_phi_xe`
--
ALTER TABLE `chi_phi_xe`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `danh_muc_hang_van_tai`
--
ALTER TABLE `danh_muc_hang_van_tai`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `danh_muc_khach_hang`
--
ALTER TABLE `danh_muc_khach_hang`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `danh_muc_lai_xe`
--
ALTER TABLE `danh_muc_lai_xe`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `danh_muc_loai_xe`
--
ALTER TABLE `danh_muc_loai_xe`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `danh_muc_tuyen_duong`
--
ALTER TABLE `danh_muc_tuyen_duong`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `danh_muc_xe`
--
ALTER TABLE `danh_muc_xe`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `luong_lai_xe`
--
ALTER TABLE `luong_lai_xe`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `so_chi_tra_chu_xe`
--
ALTER TABLE `so_chi_tra_chu_xe`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `so_thu_khach_hang`
--
ALTER TABLE `so_thu_khach_hang`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `xe_chay_cong_nhan`
--
ALTER TABLE `xe_chay_cong_nhan`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `xe_chay_du_lich`
--
ALTER TABLE `xe_chay_du_lich`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `chi_phi_chung`
--
ALTER TABLE `chi_phi_chung`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `chi_phi_xe`
--
ALTER TABLE `chi_phi_xe`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `danh_muc_hang_van_tai`
--
ALTER TABLE `danh_muc_hang_van_tai`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `danh_muc_khach_hang`
--
ALTER TABLE `danh_muc_khach_hang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `danh_muc_lai_xe`
--
ALTER TABLE `danh_muc_lai_xe`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT cho bảng `danh_muc_loai_xe`
--
ALTER TABLE `danh_muc_loai_xe`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT cho bảng `danh_muc_tuyen_duong`
--
ALTER TABLE `danh_muc_tuyen_duong`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `danh_muc_xe`
--
ALTER TABLE `danh_muc_xe`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `luong_lai_xe`
--
ALTER TABLE `luong_lai_xe`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `so_chi_tra_chu_xe`
--
ALTER TABLE `so_chi_tra_chu_xe`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `so_thu_khach_hang`
--
ALTER TABLE `so_thu_khach_hang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `xe_chay_cong_nhan`
--
ALTER TABLE `xe_chay_cong_nhan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `xe_chay_du_lich`
--
ALTER TABLE `xe_chay_du_lich`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
