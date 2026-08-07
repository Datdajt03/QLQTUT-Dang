-- ============================================================
-- Database: quan_ly_ket_nap_dang
-- Hệ thống Quản lý Quần chúng Ưu tú Phục vụ Kết nạp Đảng
-- ============================================================

CREATE DATABASE IF NOT EXISTS quan_ly_ket_nap_dang 
    CHARACTER SET utf8mb4 
    COLLATE utf8mb4_unicode_ci;

USE quan_ly_ket_nap_dang;

-- Bảng chi bộ
CREATE TABLE IF NOT EXISTS chi_bo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ten_chi_bo VARCHAR(255) NOT NULL,
    ma_chi_bo VARCHAR(50),
    dang_uy VARCHAR(255),
    tinh_uy VARCHAR(255),
    ghi_chu TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng đảng viên (giúp đỡ / hướng dẫn)
CREATE TABLE IF NOT EXISTS dang_vien (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ho_ten VARCHAR(255) NOT NULL,
    ma_dang_vien VARCHAR(50),
    chuc_vu VARCHAR(255),
    chi_bo_id INT,
    sdt VARCHAR(20),
    email VARCHAR(255),
    ghi_chu TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (chi_bo_id) REFERENCES chi_bo(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng chính: đối tượng quần chúng kết nạp đảng
CREATE TABLE IF NOT EXISTS doi_tuong (
    id INT AUTO_INCREMENT PRIMARY KEY,

    -- Thông tin cá nhân
    ma_gvsv VARCHAR(100),
    ho_ten VARCHAR(255) NOT NULL,
    sdt VARCHAR(20),
    email VARCHAR(255),
    gioi_tinh ENUM('Nam','Nữ','Khác') DEFAULT NULL,
    ngay_sinh DATE DEFAULT NULL,
    dan_toc VARCHAR(100),
    que_quan VARCHAR(500),
    chuc_vu VARCHAR(255),
    lop VARCHAR(255),

    -- Chi bộ & Cảm tình Đảng
    chi_bo_cong_nhan VARCHAR(255),
    so_bc_cam_tinh VARCHAR(100),
    ngay_hop_cam_tinh DATE DEFAULT NULL,

    -- Đảng viên giúp đỡ
    dang_vien_giup_do VARCHAR(255),
    ngay_phan_cong_giup_do DATE DEFAULT NULL,

    -- Lớp bồi dưỡng nhận thức
    so_qd_mo_lop VARCHAR(100),
    ngay_qd_mo_lop DATE DEFAULT NULL,
    tg_lop_boi_duong VARCHAR(255),
    ngay_cap_cc DATE DEFAULT NULL,
    so_qd_cc VARCHAR(100),
    don_vi_cap_cc VARCHAR(255),
    ten_dv_congtac_khi_cap_cc VARCHAR(500),
    ten_chibo_khi_cap_cc VARCHAR(255),
    ten_danguy_khi_cap_cc VARCHAR(255),
    ten_tinhuy_khi_cap_cc VARCHAR(255),

    -- Kết nạp Đảng
    ma_so VARCHAR(50),
    ket_nap_dang VARCHAR(255),
    ngay_quyet_dinh DATE DEFAULT NULL,
    so_qd_ket_nap VARCHAR(100),
    ngay_ket_nap DATE DEFAULT NULL,
    dang_vien_huong_dan VARCHAR(255),

    -- Chuyển sinh hoạt
    ngay_chuyen_sinh_hoat DATE DEFAULT NULL,
    noi_chuyen_toi VARCHAR(500),

    -- Meta
    ghi_chu TEXT,
    trang_thai ENUM('Đang theo dõi','Đã kết nạp','Đã chuyển','Tạm dừng') DEFAULT 'Đang theo dõi',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng đăng ký đối tượng trực tuyến của sinh viên (chờ duyệt)
CREATE TABLE IF NOT EXISTS dang_ky_doi_tuong (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ma_gvsv VARCHAR(100),
    ho_ten VARCHAR(255) NOT NULL,
    sdt VARCHAR(20),
    email VARCHAR(255),
    gioi_tinh ENUM('Nam','Nữ','Khác') DEFAULT NULL,
    ngay_sinh DATE DEFAULT NULL,
    dan_toc VARCHAR(100),
    que_quan VARCHAR(500),
    chuc_vu VARCHAR(255),
    lop VARCHAR(255),
    chi_bo_cong_nhan VARCHAR(255),
    ghi_chu TEXT,
    ly_do_tu_choi TEXT,
    trang_thai ENUM('Chờ duyệt','Đã duyệt','Đã từ chối') DEFAULT 'Chờ duyệt',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng lịch sử chỉnh sửa
CREATE TABLE IF NOT EXISTS lich_su (
    id INT AUTO_INCREMENT PRIMARY KEY,
    doi_tuong_id INT,
    hanh_dong VARCHAR(100),
    mo_ta TEXT,
    nguoi_thuc_hien VARCHAR(255) DEFAULT 'Admin',
    thoi_gian TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (doi_tuong_id) REFERENCES doi_tuong(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng cài đặt hệ thống
CREATE TABLE IF NOT EXISTS cai_dat (
    id INT AUTO_INCREMENT PRIMARY KEY,
    khoa VARCHAR(100) UNIQUE NOT NULL,
    gia_tri TEXT,
    mo_ta VARCHAR(500)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dữ liệu mặc định
INSERT INTO cai_dat (khoa, gia_tri, mo_ta) VALUES
('ten_truong', 'Trường Đại học Tây Bắc', 'Tên trường/đơn vị'),
('ten_dang_uy', 'Đảng uỷ trường ĐH Tây Bắc', 'Tên Đảng uỷ'),
('nam_hoc', '2024-2025', 'Năm học hiện tại'),
('admin_email', 'admin@example.com', 'Email quản trị'),
('admin_pass', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mật khẩu admin (password)');

-- Dữ liệu chi bộ mẫu
INSERT INTO chi_bo (ten_chi_bo, ma_chi_bo, dang_uy) VALUES
('Chi bộ Khoa học Tự nhiên - Công nghệ', 'KHTN-CN', 'Đảng uỷ trường ĐH Tây Bắc'),
('Chi bộ Sư phạm Tự nhiên', 'SPTN', 'Đảng uỷ trường ĐH Tây Bắc'),
('Chi bộ Kinh tế - Quản trị', 'KTQT', 'Đảng uỷ trường ĐH Tây Bắc');

-- Dữ liệu đảng viên giúp đỡ mẫu
INSERT INTO dang_vien (ho_ten, chi_bo_id, chuc_vu) VALUES
('Hoàng Thị Thanh', 1, 'Bí thư Chi bộ'),
('Hoàng Việt Anh', 1, 'Phó Bí thư'),
('Đặng Thị Vân Chi', 1, 'Đảng viên'),
('Nguyễn Hữu Cường', 1, 'Đảng viên'),
('Vũ Tiến Thành', 1, 'Đảng viên');
