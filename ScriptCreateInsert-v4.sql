-- =========================================================================
-- KHỞI TẠO CƠ SỞ DỮ LIỆU (DB_HELIOS_LITE)
-- =========================================================================
DROP DATABASE IF EXISTS `db_helios`;
CREATE DATABASE `db_helios` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `db_helios`;

-- =========================================================================
-- PHẦN 1: TẠO CÁC BẢNG ĐỘC LẬP (LEVEL 1)
-- =========================================================================

-- 1. Bảng Người Dùng
CREATE TABLE `NguoiDung` (
    `MaNguoiDung` INT AUTO_INCREMENT PRIMARY KEY,
    `HoTen` VARCHAR(100) NOT NULL,
    `TieuDe` VARCHAR(255) DEFAULT NULL,
    `DiaDiem` VARCHAR(255) DEFAULT NULL,
    `GioiTinh` TINYINT(1), 
    `NgaySinh` DATE,
    `Bio` TEXT,
    `AnhDaiDien` VARCHAR(255),
    `AnhBia` VARCHAR(255),
    `VaiTro` VARCHAR(20) NOT NULL
) ENGINE=InnoDB;

-- 2. Bảng Công Ty
CREATE TABLE `CongTy` (
    `MaCongTy` INT AUTO_INCREMENT PRIMARY KEY,
    `TenCongTy` VARCHAR(255) NOT NULL,
    `MoTa` TEXT,
    `Logo` VARCHAR(255)
) ENGINE=InnoDB;

-- 3. Bảng Kỹ Năng
CREATE TABLE `KyNang` (
    `MaKyNang` INT AUTO_INCREMENT PRIMARY KEY,
    `TenKyNang` VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;


-- =========================================================================
-- PHẦN 2: TẠO CÁC BẢNG CON (LEVEL 2 - Phụ thuộc Level 1)
-- =========================================================================

-- 4. Bảng Tài Khoản
CREATE TABLE `TaiKhoan` (
    `MaTaiKhoan` INT AUTO_INCREMENT PRIMARY KEY,
    `TenDangNhap` VARCHAR(100) NOT NULL UNIQUE,
    `MatKhau` VARCHAR(255) NOT NULL,
    `TrangThai` VARCHAR(50) DEFAULT 'Active',
    `MaNguoiDung` INT NOT NULL UNIQUE,
    CONSTRAINT `fk_taikhoan_nguoidung` FOREIGN KEY (`MaNguoiDung`) REFERENCES `NguoiDung`(`MaNguoiDung`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 5. Bảng Kết Nối (Self-referencing NguoiDung - Kết bạn/Follow)
CREATE TABLE `KetNoi` (
    `NguoiKetNoi` INT NOT NULL,
    `NguoiDuocKetNoi` INT NOT NULL,
    `ThoiGianKetNoi` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`NguoiKetNoi`, `NguoiDuocKetNoi`),
    CONSTRAINT `fk_ketnoi_nguoigui` FOREIGN KEY (`NguoiKetNoi`) REFERENCES `NguoiDung`(`MaNguoiDung`) ON DELETE CASCADE,
    CONSTRAINT `fk_ketnoi_nguoinhan` FOREIGN KEY (`NguoiDuocKetNoi`) REFERENCES `NguoiDung`(`MaNguoiDung`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 6. Bảng Học Vấn
CREATE TABLE `HocVan` (
    `MaHocVan` INT AUTO_INCREMENT PRIMARY KEY,
    `TruongHoc` VARCHAR(255) NOT NULL,
    `ChuyenNganh` VARCHAR(255) NOT NULL,
    `ThoiGianTu` DATE NOT NULL,
    `ThoiGianDen` DATE NULL,
    `MaNguoiDung` INT NOT NULL,
    CONSTRAINT `fk_hocvan_nguoidung` FOREIGN KEY (`MaNguoiDung`) REFERENCES `NguoiDung`(`MaNguoiDung`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 7. Bảng Kinh Nghiệm
CREATE TABLE `KinhNghiem` (
    `MaKinhNghiem` INT AUTO_INCREMENT PRIMARY KEY,
    `CongTy` VARCHAR(255) NOT NULL,
    `ViTri` VARCHAR(255) NOT NULL,
    `MoTa` TEXT,
    `ThoiGianTu` DATE NOT NULL,
    `ThoiGianDen` DATE NULL,
    `MaNguoiDung` INT NOT NULL,
    CONSTRAINT `fk_kinhnghiem_nguoidung` FOREIGN KEY (`MaNguoiDung`) REFERENCES `NguoiDung`(`MaNguoiDung`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 8. Bảng Bài Viết (Đã tích hợp Sự Kiện bằng Enum)
CREATE TABLE `BaiViet` (
    `MaBaiViet` INT AUTO_INCREMENT PRIMARY KEY,
    `NoiDung` TEXT NOT NULL,
    `LoaiBaiViet` ENUM('post','event') DEFAULT 'post', 
    `TenSuKien` VARCHAR(255) NULL,                     
    `DiaDiemSuKien` VARCHAR(255) NULL,                
    `ThoiGianSuKien` DATETIME NULL,                    
    `TrangThai` VARCHAR(50) DEFAULT 'Public',
    `ThoiGianDang` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `MaNguoiDung` INT NOT NULL,
    CONSTRAINT `fk_baiviet_nguoidung` FOREIGN KEY (`MaNguoiDung`) REFERENCES `NguoiDung`(`MaNguoiDung`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 9. Bảng Công Việc (Tuyển dụng)
CREATE TABLE `CongViec` (
    `MaCongViec` INT AUTO_INCREMENT PRIMARY KEY,
    `TieuDe` VARCHAR(255) NOT NULL,
    `MoTa` TEXT NOT NULL,
    `MucLuong` VARCHAR(100),
    `TrangThai` VARCHAR(50) DEFAULT 'Open',
    `HanNop` DATE NOT NULL,
    `MaCongTy` INT NOT NULL,
    CONSTRAINT `fk_congviec_congty` FOREIGN KEY (`MaCongTy`) REFERENCES `CongTy`(`MaCongTy`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 10. Bảng Người Dùng _ Kỹ Năng (Nhiều - Nhiều)
CREATE TABLE `NguoiDung_KyNang` (
    `MaNguoiDung` INT NOT NULL,
    `MaKyNang` INT NOT NULL,
    PRIMARY KEY (`MaNguoiDung`, `MaKyNang`),
    CONSTRAINT `fk_ndkn_nguoidung` FOREIGN KEY (`MaNguoiDung`) REFERENCES `NguoiDung`(`MaNguoiDung`) ON DELETE CASCADE,
    CONSTRAINT `fk_ndkn_kynang` FOREIGN KEY (`MaKyNang`) REFERENCES `KyNang`(`MaKyNang`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 11. Bảng Thông Báo
CREATE TABLE `ThongBao` (
    `MaThongBao` INT AUTO_INCREMENT PRIMARY KEY,
    `NoiDung` TEXT NOT NULL,
    `LoaiThongBao` VARCHAR(50),
    `TrangThaiDoc` TINYINT(1) DEFAULT 0,
    `LienKet` VARCHAR(255),
    `ThoiGianTao` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `MaNguoiDung` INT NOT NULL,
    CONSTRAINT `fk_thongbao_nguoidung` FOREIGN KEY (`MaNguoiDung`) REFERENCES `NguoiDung`(`MaNguoiDung`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 12. Bảng Tin Nhắn (Đã tối ưu cho cấu trúc Chat 1-1)
CREATE TABLE `TinNhan` (
    `MaTinNhan` INT AUTO_INCREMENT PRIMARY KEY,
    `NguoiGui` INT NOT NULL,
    `NguoiNhan` INT NOT NULL,
    `NoiDung` TEXT NOT NULL,
    `TrangThaiDoc` TINYINT(1) DEFAULT 0, -- 0: Chưa đọc, 1: Đã đọc
    `ThoiGianGui` DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_tinnhan_nguoigui` FOREIGN KEY (`NguoiGui`) REFERENCES `NguoiDung`(`MaNguoiDung`) ON DELETE CASCADE,
    CONSTRAINT `fk_tinnhan_nguoinhan` FOREIGN KEY (`NguoiNhan`) REFERENCES `NguoiDung`(`MaNguoiDung`) ON DELETE CASCADE
) ENGINE=InnoDB;


-- =========================================================================
-- PHẦN 3: TẠO CÁC BẢNG CHÁU (LEVEL 3 - Phụ thuộc Level 2)
-- =========================================================================

-- 13. Bảng Hình Ảnh (Đính kèm Bài Viết)
CREATE TABLE `HinhAnh` (
    `MaHinhAnh` INT AUTO_INCREMENT PRIMARY KEY,
    `DuongDanURL` VARCHAR(255) NOT NULL,
    `MaBaiViet` INT NOT NULL,
    CONSTRAINT `fk_hinhanh_baiviet` FOREIGN KEY (`MaBaiViet`) REFERENCES `BaiViet`(`MaBaiViet`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 14. Bảng Bình Luận
CREATE TABLE `BinhLuan` (
    `MaBinhLuan` INT AUTO_INCREMENT PRIMARY KEY,
    `NoiDung` TEXT NOT NULL,
    `ThoiGianDang` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `MaBaiViet` INT NOT NULL,
    `MaNguoiDung` INT NOT NULL,
    CONSTRAINT `fk_binhluan_baiviet` FOREIGN KEY (`MaBaiViet`) REFERENCES `BaiViet`(`MaBaiViet`) ON DELETE CASCADE,
    CONSTRAINT `fk_binhluan_nguoidung` FOREIGN KEY (`MaNguoiDung`) REFERENCES `NguoiDung`(`MaNguoiDung`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 15. Bảng Tương Tác (Like, Thả tim...)
CREATE TABLE `TuongTac` (
    `MaNguoiDung` INT NOT NULL,
    `MaBaiViet` INT NOT NULL,
    `LoaiTuongTac` VARCHAR(20) NOT NULL,
    `ThoiGian` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`MaNguoiDung`, `MaBaiViet`, `LoaiTuongTac`),
    CONSTRAINT `fk_tuongtac_nguoidung` FOREIGN KEY (`MaNguoiDung`) REFERENCES `NguoiDung`(`MaNguoiDung`) ON DELETE CASCADE,
    CONSTRAINT `fk_tuongtac_baiviet` FOREIGN KEY (`MaBaiViet`) REFERENCES `BaiViet`(`MaBaiViet`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 16. Bảng Công Việc _ Kỹ Năng (Nhiều - Nhiều)
CREATE TABLE `CongViec_KyNang` (
    `MaCongViec` INT NOT NULL,
    `MaKyNang` INT NOT NULL,
    PRIMARY KEY (`MaCongViec`, `MaKyNang`),
    CONSTRAINT `fk_cvkn_congviec` FOREIGN KEY (`MaCongViec`) REFERENCES `CongViec`(`MaCongViec`) ON DELETE CASCADE,
    CONSTRAINT `fk_cvkn_kynang` FOREIGN KEY (`MaKyNang`) REFERENCES `KyNang`(`MaKyNang`) ON DELETE CASCADE
) ENGINE=InnoDB;


-- =========================================================================
-- PHẦN 4: THÊM DỮ LIỆU MẪU (INSERT DATA)
-- =========================================================================

-- INSERT 1: NguoiDung 
INSERT INTO `NguoiDung` (`HoTen`, `TieuDe`, `DiaDiem`, `GioiTinh`, `NgaySinh`, `Bio`, `VaiTro`) VALUES 
('Trương Nhật Phương Vy', 'Sinh viên Hệ thống thông tin kinh doanh - UEH', 'Thành phố Hồ Chí Minh, Việt Nam', 0, '2003-01-01', 'Software Engineer yêu thích lập trình Backend', 'User'),
('Trần Thị Bích', 'Chuyên viên Marketing & Quản trị thương hiệu', 'Hà Nội, Việt Nam', 0, '1998-10-20', 'Chuyên viên Marketing & Quản trị thương hiệu', 'User'),
('Lê Hoàng Phong', 'Giám đốc nhân sự, 10 năm kinh nghiệm', 'Đà Nẵng, Việt Nam', 1, '1990-12-05', 'Giám đốc nhân sự, 10 năm kinh nghiệm', 'Admin'),
('Phạm Quỳnh Như', 'Sinh viên IT năm cuối, tìm cơ hội thực tập', 'Thành phố Hồ Chí Minh, Việt Nam', 0, '2000-01-10', 'Sinh viên IT năm cuối, tìm cơ hội thực tập', 'User'),
('Vũ Đức Đam', 'Data Scientist, đam mê AI/ML', 'Hà Nội, Việt Nam', 1, '1988-08-22', 'Data Scientist, đam mê AI/ML', 'User'),
('Hoàng Lan Phương', 'UI/UX Designer, thích cái đẹp', 'Thành phố Hồ Chí Minh, Việt Nam', 0, '1996-03-08', 'UI/UX Designer, thích cái đẹp', 'User');

-- INSERT 2: CongTy 
INSERT INTO `CongTy` (`TenCongTy`, `MoTa`) VALUES 
('Công ty Cổ phần VNG', 'Kỳ lân công nghệ hàng đầu VN'),
('FPT Software', 'Công ty gia công phần mềm'),
('Shopee Việt Nam', 'Nền tảng TMĐT'),
('Momo', 'Ví điện tử hàng đầu');

-- INSERT 3: KyNang 
INSERT INTO `KyNang` (`TenKyNang`) VALUES 
('Lập trình Java'), ('Phân tích dữ liệu (Python)'), ('Digital Marketing'),
('Tuyển dụng (TA)'), ('Lập trình ReactJS'), ('Figma & Design Thinking');

-- INSERT 4: TaiKhoan
INSERT INTO `TaiKhoan` (`TenDangNhap`, `MatKhau`, `MaNguoiDung`) VALUES 
('phuongvy_ueh', 'pass123', 1),
('tranbich', 'pass123', 2),
('admin_phong', 'pass123', 3),
('quynhnhu_it', 'pass123', 4),
('vuducdam', 'pass123', 5);

-- INSERT 5: KetNoi (Kết bạn)
INSERT INTO `KetNoi` (`NguoiKetNoi`, `NguoiDuocKetNoi`) VALUES 
(1, 4), (4, 1), (1, 5), (5, 1), (2, 3), (3, 2);

-- INSERT 6: HocVan
INSERT INTO `HocVan` (`TruongHoc`, `ChuyenNganh`, `ThoiGianTu`, `ThoiGianDen`, `MaNguoiDung`) VALUES 
('Đại học Bách Khoa HN', 'Khoa học máy tính', '2013-09-01', '2017-06-30', 1),
('Đại học Ngoại Thương', 'Quản trị kinh doanh', '2016-09-01', '2020-06-30', 2),
('Đại học KHTN', 'Công nghệ thông tin', '2018-09-01', NULL, 4);

-- INSERT 7: KinhNghiem
INSERT INTO `KinhNghiem` (`CongTy`, `ViTri`, `MoTa`, `ThoiGianTu`, `ThoiGianDen`, `MaNguoiDung`) VALUES 
('FPT Software', 'Junior Backend Developer', 'Làm việc với Spring Boot', '2018-01-01', '2021-12-31', 1),
('VNG', 'Senior Backend Developer', 'Xây dựng hệ thống Microservices', '2022-01-01', NULL, 1),
('Shopee', 'Chuyên viên SEO', 'Tối ưu hóa công cụ tìm kiếm', '2020-03-01', NULL, 2);

-- INSERT 8: BaiViet (Bao gồm Post thường và Event)
INSERT INTO `BaiViet` (`NoiDung`, `LoaiBaiViet`, `TenSuKien`, `DiaDiemSuKien`, `ThoiGianSuKien`, `MaNguoiDung`) VALUES 
('Chào mọi người, mình vừa hoàn thành dự án lớn dùng Spring Boot!', 'post', NULL, NULL, NULL, 1),
('Công ty FPT trân trọng kính mời các bạn sinh viên tham gia Seminar hướng nghiệp.', 'event', 'Seminar Hướng nghiệp IT 2024', 'Đại học Bách Khoa', '2024-05-20 08:30:00', 3),
('Xu hướng Marketing 2024 sẽ tập trung vào Tiktok và AI.', 'post', NULL, NULL, NULL, 2);

-- INSERT 9: CongViec (Tuyển dụng)
INSERT INTO `CongViec` (`TieuDe`, `MoTa`, `MucLuong`, `HanNop`, `MaCongTy`) VALUES 
('Tuyển dụng Backend Developer (Java)', 'Yêu cầu 2 năm kinh nghiệm', '1000$ - 1500$', '2026-12-31', 1),
('Thực tập sinh Frontend (React)', 'Chấp nhận sinh viên năm cuối', 'Trợ cấp 3tr', '2024-06-30', 2),
('Chuyên viên Digital Marketing', 'Kinh nghiệm chạy Ads, SEO', 'Thỏa thuận', '2024-08-15', 3),
('UI/UX Designer', 'Sử dụng thành thạo Figma', '800$ - 1200$', '2024-10-20', 4);

-- INSERT 10: NguoiDung_KyNang
INSERT INTO `NguoiDung_KyNang` (`MaNguoiDung`, `MaKyNang`) VALUES 
(1, 1), (2, 3), (3, 4), (4, 5), (5, 2);

-- INSERT 11: CongViec_KyNang
INSERT INTO `CongViec_KyNang` (`MaCongViec`, `MaKyNang`) VALUES 
(1, 1), (2, 5), (3, 3);

-- INSERT 12: ThongBao
INSERT INTO `thongbao` (`NoiDung`, `LoaiThongBao`, `TrangThaiDoc`, `LienKet`, `ThoiGianTao`, `MaNguoiDung`) VALUES 
('Nguyễn Văn Nam đã bày tỏ cảm xúc về bài viết của bạn.', 'TuongTac', 0, '/post/3', '2026-05-16 20:00:15', 1),
('Công ty TechCorp đã gửi cho bạn một lời mời phỏng vấn.', 'Tuyendung', 0, '/interview/12', '2026-05-16 20:05:30', 2),
('Hệ thống đã cập nhật tính năng bảo mật mới cho tài khoản.', 'HeThong', 1, '/setting/security', '2026-05-16 20:10:00', 1),
('Trần Minh Quang đã nhắc đến bạn trong một bình luận.', 'BinhLuan', 0, '/post/5#comment-88', '2026-05-16 20:12:45', 3),
('Có 5 việc làm Front-end Developer mới phù hợp với bạn.', 'HeThong', 0, '/jobs/suggest', '2026-05-16 20:15:22', 1);


-- INSERT 13: TinNhan (SỬ DỤNG MÔ HÌNH CHAT 1-1)
-- Giải thích: Người 1 nhắn cho Người 4 và ngược lại
INSERT INTO `TinNhan` (`NguoiGui`, `NguoiNhan`, `NoiDung`, `TrangThaiDoc`) VALUES 
(1, 4, 'Chào Như, cậu làm xong module Đăng Nhập chưa?', 1),
(4, 1, 'Mình vừa làm xong, chuẩn bị đẩy code lên Github nè.', 1),
(3, 4, 'Chào Như, anh thấy em đang tìm cơ hội thực tập tại FPT?', 0);

-- INSERT 14: HinhAnh (Của Bài viết)
INSERT INTO `HinhAnh` (`DuongDanURL`, `MaBaiViet`) VALUES 
('https://example.com/img/project.jpg', 1),
('https://example.com/img/fpt_seminar.jpg', 2);

-- INSERT 15: BinhLuan
INSERT INTO `BinhLuan` (`NoiDung`, `MaBaiViet`, `MaNguoiDung`) VALUES 
('Tuyệt vời quá Vy ơi!', 1, 4),
('Sinh viên trường khác tham gia sự kiện được không anh?', 2, 4);

-- INSERT 16: TuongTac
INSERT INTO `TuongTac` (`MaNguoiDung`, `MaBaiViet`, `LoaiTuongTac`) VALUES 
(4, 1, 'Thích'), (5, 1, 'Quan tâm'), (1, 2, 'Hữu ích');

-- =========================================================================
-- KẾT THÚC SCRIPT
-- =========================================================================