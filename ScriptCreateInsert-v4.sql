-- ===================================================================================
-- KHỞI TẠO CƠ SỞ DỮ LIỆU (DB_HELIOS)
-- ===================================================================================

DROP DATABASE IF EXISTS `db_helios`;
CREATE DATABASE `db_helios` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `db_helios`;

-- ===================================================================================
-- PHẦN 1: TẠO CÁC BẢNG ĐỘC LẬP (LEVEL 1)
-- ===================================================================================

-- 1. Bảng Người Dùng 
CREATE TABLE `NguoiDung` (
  `MaNguoiDung` INT AUTO_INCREMENT PRIMARY KEY,
  `HoTen` VARCHAR(100) NOT NULL,
  `TieuDe` VARCHAR(255) DEFAULT NULL,
  `DiaDiem` VARCHAR(255) DEFAULT NULL,
  `Bio` TEXT,
  `AnhDaiDien` VARCHAR(255) DEFAULT NULL,
  `AnhBia` VARCHAR(255) DEFAULT NULL,
  `LanHoatDongCuoi` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `XacMinh` TINYINT(1) DEFAULT 0
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

-- ===================================================================================
-- PHẦN 2: TẠO CÁC BẢNG CON (LEVEL 2 - Phụ thuộc Level 1)
-- ===================================================================================

-- 4. Bảng Tài Khoản
CREATE TABLE `TaiKhoan` (
  `MaTaiKhoan` INT AUTO_INCREMENT PRIMARY KEY,
  `MaNguoiDung` INT NOT NULL UNIQUE,
  `Email` VARCHAR(100) NOT NULL UNIQUE,
  `MatKhau` VARCHAR(255) NOT NULL,
  `VaiTro` ENUM('User', 'Admin', 'Recruiter') NOT NULL DEFAULT 'User',
  `TrangThai` VARCHAR(50) DEFAULT 'active',
  `NgayTao` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `VerificationToken` VARCHAR(255) NULL DEFAULT NULL,
  `TokenExpiresAt` DATETIME NULL DEFAULT NULL,
  `PasswordResetToken` VARCHAR(255) NULL DEFAULT NULL,
  `ResetTokenExpiresAt` DATETIME NULL DEFAULT NULL,
  
  -- Khóa ngoại liên kết chặt chẽ với bảng NguoiDung
  CONSTRAINT `fk_taikhoan_nguoidung` FOREIGN KEY (`MaNguoiDung`) REFERENCES `NguoiDung` (`MaNguoiDung`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 5. Bảng Kết Nối
CREATE TABLE `KetNoi` (
  `MaKetNoi` INT AUTO_INCREMENT PRIMARY KEY,
  `MaNguoiGui` INT NOT NULL,
  `MaNguoiNhan` INT NOT NULL,
  `TrangThai` ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
  `NgayTao` DATETIME DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_ketnoi_nguoigui` FOREIGN KEY (`MaNguoiGui`) REFERENCES `NguoiDung`(`MaNguoiDung`) ON DELETE CASCADE,
  CONSTRAINT `fk_ketnoi_nguoinhan` FOREIGN KEY (`MaNguoiNhan`) REFERENCES `NguoiDung`(`MaNguoiDung`) ON DELETE CASCADE,
  CONSTRAINT `chk_ketnoi_khac_nhau` CHECK (`MaNguoiGui` <> `MaNguoiNhan`),
  UNIQUE KEY `uq_ketnoi_cap` (`MaNguoiGui`, `MaNguoiNhan`)
) ENGINE=InnoDB;

-- 6. Bảng Học Vấn
CREATE TABLE `HocVan` (
  `MaHocVan` INT AUTO_INCREMENT PRIMARY KEY,
  `TruongHoc` VARCHAR(255) NOT NULL,
  `ChuyenNganh` VARCHAR(255) NOT NULL,
  `ThoiGianTu` DATE NOT NULL,
  `ThoiGianDen` DATE NULL,
  `MaNguoiDung` INT NOT NULL,
  CONSTRAINT `fk_hocvan_nguoidung` FOREIGN KEY (`MaNguoiDung`) REFERENCES `NguoiDung` (`MaNguoiDung`) ON DELETE CASCADE
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
  CONSTRAINT `fk_kinhnghiem_nguoidung` FOREIGN KEY (`MaNguoiDung`) REFERENCES `NguoiDung` (`MaNguoiDung`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 8. Bảng Bài Viết
CREATE TABLE `BaiViet` (
  `MaBaiViet` INT AUTO_INCREMENT PRIMARY KEY,
  `NoiDung` TEXT NOT NULL,
  `LoaiBaiViet` ENUM('post','event') DEFAULT 'post',
  `TenSuKien` VARCHAR(255) NULL,
  `DiaDiemSuKien` VARCHAR(255) NULL,
  `ThoiGianSuKien` DATETIME NULL,
  `TrangThai` ENUM('Public','Private','Friends') DEFAULT 'Public',
  `ThoiGianDang` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `MaNguoiDung` INT NOT NULL,
  CONSTRAINT `fk_baiviet_nguoidung` FOREIGN KEY (`MaNguoiDung`) REFERENCES `NguoiDung` (`MaNguoiDung`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 9. Bảng Công Việc
CREATE TABLE `CongViec` (
  `MaCongViec` INT(11) NOT NULL AUTO_INCREMENT,
  `TieuDe` VARCHAR(255) NOT NULL,
  `MoTa` TEXT NOT NULL,
  `YeuCau` TEXT NOT NULL,
  `QuyenLoi` TEXT NOT NULL,
  `NoiLamViec` VARCHAR(255) NOT NULL,
  `MucLuong` VARCHAR(100) NOT NULL,
  `HanNop` DATE NOT NULL,
  `NgayDang` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `MaCongTy` INT(11) NOT NULL,
  PRIMARY KEY (`MaCongViec`),
  CONSTRAINT `fk_congviec_congty` FOREIGN KEY (`MaCongTy`) REFERENCES `CongTy`(`MaCongTy`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 10. Bảng Người Dùng - Kỹ Năng
CREATE TABLE `NguoiDung_KyNang` (
  `MaNguoiDung` INT NOT NULL,
  `MaKyNang` INT NOT NULL,
  PRIMARY KEY (`MaNguoiDung`, `MaKyNang`),
  CONSTRAINT `fk_ndkn_nguoidung` FOREIGN KEY (`MaNguoiDung`) REFERENCES `NguoiDung` (`MaNguoiDung`) ON DELETE CASCADE,
  CONSTRAINT `fk_ndkn_kynang` FOREIGN KEY (`MaKyNang`) REFERENCES `KyNang` (`MaKyNang`) ON DELETE CASCADE
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
  CONSTRAINT `fk_thongbao_nguoidung` FOREIGN KEY (`MaNguoiDung`) REFERENCES `NguoiDung` (`MaNguoiDung`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 12. Bảng Tin Nhắn
CREATE TABLE `TinNhan` (
  `MaTinNhan` INT AUTO_INCREMENT PRIMARY KEY,
  `NguoiGui` INT NOT NULL,
  `NguoiNhan` INT NOT NULL,
  `NoiDung` TEXT NOT NULL,
  `DuongDanFile` VARCHAR(500) NULL,
  `TrangThaiDoc` TINYINT(1) DEFAULT 0,
  `DaGhim` TINYINT(1) DEFAULT 0,
  `ThoiGianGui` DATETIME DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_tinnhan_nguoigui` FOREIGN KEY (`NguoiGui`) REFERENCES `NguoiDung` (`MaNguoiDung`) ON DELETE CASCADE,
  CONSTRAINT `fk_tinnhan_nguoinhan` FOREIGN KEY (`NguoiNhan`) REFERENCES `NguoiDung` (`MaNguoiDung`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ===================================================================================
-- PHẦN 3: TẠO CÁC BẢNG CHÁU (LEVEL 3 - Phụ thuộc Level 2)
-- ===================================================================================

-- 13. Bảng Hình Ảnh
CREATE TABLE `HinhAnh` (
  `MaHinhAnh` INT AUTO_INCREMENT PRIMARY KEY,
  `DuongDanURL` VARCHAR(255) NOT NULL,
  `MaBaiViet` INT NOT NULL,
  CONSTRAINT `fk_hinhanh_baiviet` FOREIGN KEY (`MaBaiViet`) REFERENCES `BaiViet` (`MaBaiViet`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 14. Bảng Bình Luận
CREATE TABLE `BinhLuan` (
  `MaBinhLuan` INT AUTO_INCREMENT PRIMARY KEY,
  `NoiDung` TEXT NOT NULL,
  `ThoiGianDang` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `MaBaiViet` INT NOT NULL,
  `MaNguoiDung` INT NOT NULL,
  CONSTRAINT `fk_binhluan_baiviet` FOREIGN KEY (`MaBaiViet`) REFERENCES `BaiViet` (`MaBaiViet`) ON DELETE CASCADE,
  CONSTRAINT `fk_binhluan_nguoidung` FOREIGN KEY (`MaNguoiDung`) REFERENCES `NguoiDung` (`MaNguoiDung`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 15. Bảng Tương Tác
CREATE TABLE `TuongTac` (
  `MaNguoiDung` INT NOT NULL,
  `MaBaiViet` INT NOT NULL,
  `LoaiTuongTac` VARCHAR(20) NOT NULL,
  `ThoiGian` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`MaNguoiDung`, `MaBaiViet`),
  CONSTRAINT `fk_tuongtac_nguoidung` FOREIGN KEY (`MaNguoiDung`) REFERENCES `NguoiDung` (`MaNguoiDung`) ON DELETE CASCADE,
  CONSTRAINT `fk_tuongtac_baiviet` FOREIGN KEY (`MaBaiViet`) REFERENCES `BaiViet` (`MaBaiViet`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 16. Bảng Công Việc - Kỹ Năng
CREATE TABLE `CongViec_KyNang` (
  `MaCongViec` INT NOT NULL,
  `MaKyNang` INT NOT NULL,
  PRIMARY KEY (`MaCongViec`, `MaKyNang`),
  CONSTRAINT `fk_cvkn_congviec` FOREIGN KEY (`MaCongViec`) REFERENCES `CongViec` (`MaCongViec`) ON DELETE CASCADE,
  CONSTRAINT `fk_cvkn_kynang` FOREIGN KEY (`MaKyNang`) REFERENCES `KyNang` (`MaKyNang`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ===================================================================================
-- PHẦN 4: THÊM DỮ LIỆU MẪU (INSERT DATA)
-- ===================================================================================

-- INSERT 1: NguoiDung
INSERT INTO `NguoiDung` (`MaNguoiDung`, `HoTen`, `TieuDe`, `DiaDiem`, `Bio`) VALUES
(1, 'Trương Nhật Phương Vy', 'Sinh viên Hệ thống thông tin kinh doanh - UEH', 'Thành phố Hồ Chí Minh, Việt Nam', 'Software Engineer yêu thích lập trình Backend'),
(2, 'Trần Thị Bích', 'Chuyên viên Marketing & Quản trị thương hiệu', 'Hà Nội, Việt Nam', 'Chuyên viên Marketing & Quản trị thương hiệu'),
(3, 'Lê Hoàng Phong', 'Giám đốc nhân sự, 10 năm kinh nghiệm', 'Đà Nẵng, Việt Nam', 'Giám đốc nhân sự, 10 năm kinh nghiệm'),
(4, 'Phạm Quỳnh Như', 'Sinh viên IT năm cuối, tìm cơ hội thực tập', 'Thành phố Hồ Chí Minh, Việt Nam', 'Sinh viên IT năm cuối, tìm cơ hội thực tập'),
(5, 'Vũ Đức Đam', 'Data Scientist, đam mê AI/ML', 'Hà Nội, Việt Nam', 'Data Scientist, đam mê AI/ML'),
(6, 'Hoàng Lan Phương', 'UI/UX Designer, thích cái đẹp', 'Thành phố Hồ Chí Minh, Việt Nam', 'UI/UX Designer, thích cái đẹp'),
(7, 'Nguyễn Văn Nam', 'Freelancer Web', 'Thành phố Hồ Chí Minh, Việt Nam', 'Freelancer Web độc lập, nhận dự án nhỏ');

-- INSERT 2: CongTy
INSERT INTO `CongTy` (`MaCongTy`, `TenCongTy`, `MoTa`, `Logo`) VALUES
(1, 'Công ty Cổ phần VNG', 'Công ty Cổ phần VNG là một trong những công ty công nghệ hàng đầu Việt Nam...', 'vng.png'),
(2, 'FPT Software', 'FPT Software là công ty công nghệ hàng đầu Việt Nam...', 'FPT.png'),
(3, 'Shopee Việt Nam', 'Shopee là nền tảng thương mại điện tử hàng đầu...', 'shopee.png'),
(4, 'Momo', 'MoMo là một trong những ví điện tử và nền tảng thanh toán...', 'momo.png'),
(5, 'MISA', 'MISA là công ty công nghệ hàng đầu Việt Nam chuyên cung cấp phần mềm...', 'misa.jpg'),
(6, 'Bosch', 'Bosch là tập đoàn công nghệ và kỹ thuật đa quốc gia...', 'bosch.jfif'),
(7, 'CMC', 'CMC là tập đoàn công nghệ thông tin hàng đầu Việt Nam...', 'cmc.jpg'),
(8, 'Samsung', 'Samsung là tập đoàn điện tử đa quốc gia hàng đầu...', 'samsung.jpg'),
(9, 'LG', 'LG là tập đoàn điện tử và công nghệ đa quốc gia...', 'LG.png'),
(10, 'Intel', 'Intel là tập đoàn công nghệ bán dẫn hàng đầu thế giới...', 'intel.jpeg'),
(11, 'Base.vn', 'Base.vn là nền tảng quản trị doanh nghiệp toàn diện...', 'base.png'),
(12, 'VNPT', 'VNPT là tập đoàn Bưu chính Viễn thông Việt Nam...', 'vnpt.png');

-- INSERT 3: KyNang
INSERT INTO `KyNang` (`MaKyNang`, `TenKyNang`) VALUES
(1, 'Lập trình Java'), (2, 'Phân tích dữ liệu (Python)'), (3, 'Digital Marketing'),
(4, 'Tuyển dụng (TA)'), (5, 'Lập trình ReactJS'), (6, 'Figma & Design Thinking');

-- INSERT 4: TaiKhoan
INSERT INTO `TaiKhoan` (`MaNguoiDung`, `Email`, `MatKhau`, `VaiTro`, `TrangThai`) VALUES
(1, 'phuong.vy@example.com', 'pass123', 'User', 'active'),
(2, 'bich.tran@example.com', 'pass123', 'User', 'active'),
(3, 'phong.le@example.com', 'pass123', 'Admin', 'active'),
(4, 'nhu.pham@example.com', 'pass123', 'User', 'active'),
(5, 'dam.vu@example.com', 'pass123', 'User', 'active'),
(6, 'phuong.hoang@example.com', 'pass123', 'User', 'active'),
(7, 'nam.nguyen@example.com', 'pass123', 'User', 'active');

-- INSERT 5: KetNoi
INSERT INTO `KetNoi` (`MaNguoiGui`, `MaNguoiNhan`, `TrangThai`) VALUES
(1, 2, 'accepted'), (1, 3, 'pending'), (4, 1, 'rejected');

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

-- INSERT 8: BaiViet
INSERT INTO `BaiViet` (`NoiDung`, `LoaiBaiViet`, `TenSuKien`, `DiaDiemSuKien`, `ThoiGianSuKien`, `MaNguoiDung`) VALUES
('Chào mọi người, mình vừa hoàn thành dự án lớn dùng Spring Boot!', 'post', NULL, NULL, '2024-05-20 08:30:00', 1),
('Công ty FPT trân trọng kính mời các bạn sinh viên tham gia Seminar hướng nghiệp.', 'event', 'Seminar Hướng nghiệp IT 2024', 'Đại học Bách Khoa', '2024-05-20 08:30:00', 3),
('Xu hướng Marketing 2024 sẽ tập trung vào Tiktok và AI.', 'post', NULL, NULL, NULL, 2);

-- INSERT 9: CongViec 
-- (Dữ liệu đầy đủ và phức tạp, giữ nguyên từ script gốc của bạn)
INSERT INTO `CongViec` (`MaCongViec`, `TieuDe`, `MoTa`, `YeuCau`, `QuyenLoi`, `NoiLamViec`, `MucLuong`, `HanNop`, `NgayDang`, `MaCongTy`) VALUES
(1, 'Lập trình viên Java Back-End Developer', '- Phát triển và mở rộng các sản phẩm phần mềm của công ty trên nền tảng framework nội bộ Java Web Application.\n- Thiết kế, xây dựng và vận hành các module backend phục vụ hệ thống ở quy mô lớn, đảm bảo hiệu năng, tính ổn định và khả năng mở rộng.\n- Phân tích yêu cầu nghiệp vụ, thiết kế giải pháp kỹ thuật và tham gia review architecture.\n- Phát triển các module backend mới và bảo trì, mở rộng các module hiện có.\n- Viết code đảm bảo chất lượng: rõ ràng, dễ bảo trì, tuân thủ convention của dự án.\n- Phối hợp với team để thiết kế API, schema database và luồng xử lý dữ liệu.\n- Nếu bạn quan tâm đến vị trí này, vui lòng gửi CV trực tiếp qua email: tuyendung@vng.com.vn', '- Kinh nghiệm: 03 năm phát triển backend Java trong môi trường production.\n- Trình độ: Đại học chuyên ngành Công Nghệ Thông Tin hoặc Toán tin.\n- Nắm vững lập trình hướng đối tượng (OOP) và các nguyên tắc thiết kế phần mềm (Design Patterns: Factory, Singleton, Strategy, Observer...).\n- Thành thạo ngôn ngữ Java; có kinh nghiệm phát triển ứng dụng backend trên nền tảng Java Web Application.\n- Hiểu biết về kiến trúc phân lớp RESTful API và các cơ chế xử lý request/response trong ứng dụng web (HTTP lifecycle, filter, interceptor, serialization).\n- Nắm vững MongoDB: thiết kế schema document, aggregation pipeline, indexing, query optimization trong môi trường production. Có kinh nghiệm làm việc với dữ liệu lớn.\n- Thành thạo truy vấn SQL.', '- Được ăn trưa tại công ty.\n- Được đóng bảo hiểm xã hội, y tế.\n- Có thưởng các ngày lễ tết.\n- Du lịch nghỉ dưỡng hằng năm cùng công ty.\n- Thưởng theo dự án, đi du lịch nhiều nơi.', 'E.21 đường D23 khu nhà ở Phước Long B, Phường Phước Long, Tp. Hồ Chí Minh', '20 - 30 triệu (tùy dự án)', '2026-12-31', '2026-10-01 08:00:00', 1),
(2, 'Thực tập sinh Software Engineer', '- Tùy theo năng lực và định hướng, thực tập sinh sẽ được phân công vào nhóm phù hợp (Backend / Frontend / Mobile / Data - Automation) và làm việc dưới sự hướng dẫn trực tiếp của mentor:\n- Tham gia phát triển, hoàn thiện các tính năng được giao trên hệ thống web và API nội bộ (PHP / Laravel; NodeJS / TypeScript).\n- Xây dựng và chỉnh sửa giao diện người dùng với React / TypeScript / JavaScript.\n- Tham gia phát triển, bảo trì ứng dụng di động bằng React Native / Flutter.\n- Viết script xử lý dữ liệu, tự động hóa quy trình và công cụ nội bộ bằng Python.\n- Tìm và sửa lỗi (bug) trên hệ thống; kiểm thử tính năng trước khi đưa lên.\n- Hỗ trợ tối ưu truy vấn, xử lý dữ liệu trên cơ sở dữ liệu MySQL; làm việc với Redis, hàng đợi và tích hợp API đối tác/nhà vận chuyển.\n- Viết và cập nhật tài liệu kỹ thuật cho tính năng/module được giao.\n- Trao đổi tiến độ, nhận và phản hồi yêu cầu công việc trực tiếp với mentor và các thành viên trong nhóm.\n- Nếu bạn quan tâm đến vị trí này, vui lòng gửi CV trực tiếp qua email: tuyendung@fpt.com.vn', '- Sinh viên năm 2 - 4 hoặc mới tốt nghiệp ngành CNTT, Khoa học máy tính, Kỹ thuật phần mềm hoặc tương đương.\n- Nắm vững kiến thức nền tảng: cấu trúc dữ liệu & giải thuật, lập trình hướng đối tượng (OOP), Git.\n- Hiểu cơ bản về cơ sở dữ liệu quan hệ (SQL / MySQL) và mô hình HTTP / RESTful API.\n- Có kiến thức ít nhất một trong các stack: JavaScript / TypeScript, NodeJS, React, React Native, PHP / Laravel, hoặc Python.\n- Đọc hiểu được tài liệu kỹ thuật bằng tiếng Anh.\n- Tư duy logic tốt, ham học hỏi, chủ động, có trách nhiệm, kỷ luật và làm việc nhóm tốt.', '- Có hỗ trợ chi phí thực tập (mức cụ thể trao đổi khi phỏng vấn).\n- Được hướng dẫn bởi mentor và tham gia vào dự án thực tế của công ty.\n- Được làm quen với quy trình phát triển phần mềm chuyên nghiệp và các công nghệ đang sử dụng tại SuperShip.\n- Cấp giấy xác nhận thực tập, đóng dấu và hỗ trợ hồ sơ tốt nghiệp.\n- Có cơ hội được xem xét trở thành nhân viên chính thức (Junior Developer) nếu kết quả thực tập tốt.', '32 Thân Nhân Trung, Phường 13, Quận Tân Bình, Tp. Hồ Chí Minh', 'Thỏa thuận', '2026-09-30', '2026-07-01 08:00:00', 2),
(3, 'Nhân Viên Telemarketing', '- Sáng tạo nội dung Fanpage để thu hút khách hàng tiềm năng.\n- Tương tác, chăm sóc khách hàng Fanpage công ty để tạo dữ liệu khách hàng.\n- Tham gia các sự kiện lái thử của showroom vào cuối tuần, cũng như các sự kiện lớn của công ty để hỗ trợ khách hàng.\n- Thực hiện các cuộc gọi với các khách hàng tiềm năng để hẹn khách hàng đến Showroom lái thử xe ô tô Subaru.\n- Thực hiện các cuộc gọi/khảo sát đặc biệt theo yêu cầu kinh doanh.\n- Gọi điện định kỳ hàng tháng để theo dõi độ tiềm năng dẫn đến nhu cầu mua xe của khách hàng.\n- Chuyển các khiếu nại từ khách hàng đến Giám sát để xử lý.\n- Lưu trữ thông tin khách hàng, thực hiện các báo cáo hàng tuần.\n- Nếu bạn quan tâm đến vị trí này, vui lòng gửi CV trực tiếp qua email: careers@shopee.vn', '- Nữ, độ tuổi 2000 trở lên, có kinh nghiệm mảng Marketing.\n- Ưu tiên tốt nghiệp ngành Marketing.\n- Sử dụng thành thạo vi tính văn phòng (Microsoft Office).\n- Khả năng giao tiếp tốt, thuyết phục khách hàng tốt.\n- Giọng nói dễ nghe, không nói ngọng, nói lắp, nói nặng giọng địa phương.\n- Có thể giao tiếp bằng tiếng Anh là một lợi thế.', '- Thử việc 100% lương.\n- Các chế độ BHXH, BHYT, BHTN theo quy định của nhà nước.\n- Ngoài ra được cấp thêm Bảo hiểm tai nạn 24/7, thẻ Bảo hiểm sức khỏe.\n- Quà tặng Lễ Tết hàng năm theo quy định của Công ty.\n- Khám sức khỏe tổng quát hằng năm.\n- Môi trường làm việc chuyên nghiệp, cơ hội thăng tiến cao.', 'Showroom Subaru Gò Vấp - 819 Quang Trung, An Hội Tây, Hồ Chí Minh', 'Thỏa thuận', '2026-09-30', '2026-07-01 08:00:00', 3),
(4, 'UI/UX Designer', '- Thiết kế giao diện người dùng (UI) và trải nghiệm người dùng (UX) cho các ứng dụng quản lý doanh nghiệp, đặc biệt là CRM, HRM, Sales Apps...\n- Tiến hành nghiên cứu người dùng (user research), làm việc trực tiếp với end-user để hiểu nhu cầu, hành vi của người dùng cuối.\n- Sử dụng Figma để tạo wireframes, mockups, prototypes tương tác và design systems.\n- Thực hiện user testing và usability testing để xác thực các thiết kế.\n- Hợp tác chặt chẽ với các developer, product manager và các stakeholder khác.\n- Phát triển và duy trì design system và style guide.\n- Nếu bạn quan tâm đến vị trí này, vui lòng gửi CV trực tiếp qua email: luuvanE@momovietnam.com', '- Tốt nghiệp đại học trở lên - chuyên ngành thiết kế hoặc các ngành liên quan.\n- Ít nhất 3 năm kinh nghiệm làm việc ở vị trí tương đương.\n- Thành thạo Figma.\n- Kinh nghiệm trong user research, prototyping và usability testing.\n- Hiểu biết vững chắc về các nguyên tắc thiết kế UX/UI và responsive design.\n- Kinh nghiệm thiết kế cho các hệ thống enterprise (CRM, HRM, ERP, Sales Apps).\n- Hồ sơ đính kèm Portfolio các mẫu thiết kế đã thực hiện.', '- Tham gia BHXH, BHYT, BHTN theo quy định pháp luật.\n- Lương, thưởng các dịp lễ, tết và phúc lợi khác theo chế độ Tập đoàn.\n- Phép năm: 12 ngày/năm, tăng theo thâm niên.\n- Phụ cấp cơm trưa: 25k/ngày.\n- Giảm giá 30%-50% khi sử dụng dịch vụ tại chuỗi nhà hàng Café Rita Võ.', 'Số 5A đường số 3, Phường An Khánh, Thành phố Thủ Đức', '12 - 17 triệu', '2026-10-20', '2026-08-01 08:00:00', 4),
(5, 'Trưởng Nhóm Frontend Developer', '- Phát triển ứng dụng frontend cho OTT, TV app, mobile/web platform, game portal.\n- Xây dựng UI sử dụng ReactJS / NextJS (SSR, SSG, App Router).\n- Tối ưu performance trên thiết bị yếu (Smart TV, mobile).\n- Tích hợp video streaming (HLS/DASH), player customization.\n- Xây dựng realtime features (chat, notification, live event).\n- Thiết kế kiến trúc frontend scalable, reusable component system.\n- Phối hợp với backend (.NET/NodeJS/Go) và team sản phẩm.\n- Nếu bạn quan tâm đến vị trí này, vui lòng gửi CV trực tiếp qua email: tuyendung@misa.com.vn', '- Từ 3 năm kinh nghiệm Frontend.\n- Thành thạo: ReactJS (hooks, lifecycle, optimization).\n- Kinh nghiệm: State management (Redux / Zustand / React Query).\n- Hiểu sâu: Performance optimization (code splitting, memo, lazy load), Browser rendering / event loop.\n- Có kinh nghiệm làm sản phẩm thực tế (ưu tiên media, video, OTT).', '- Được làm việc trong công ty đứng đầu về mảng Giải trí số.\n- Hưởng đầy đủ các chế độ theo luật (BHXH, BHYT, nghỉ lễ, nghỉ phép, thai sản...).\n- Bảo hiểm sức khỏe toàn diện 24/7, khám sức khỏe thường niên.\n- Lộ trình thăng tiến rõ ràng, môi trường trẻ trung năng động.', '3 ngõ 84 Ngọc Khánh, Phường Giảng Võ, Hà Nội', 'Thoả thuận', '2026-12-31', '2026-10-01 08:00:00', 5),
(6, 'IT Securities Developer', '- Trực tiếp lập trình, xây dựng và bảo trì các công cụ, ứng dụng nội bộ phục vụ vận hành của MarTech Team.\n- Thiết kế kiến trúc hệ thống, database và API cho các sản phẩm nội bộ.\n- Cài đặt, cấu hình và quản trị hạ tầng server, hệ thống máy tính và thiết bị CNTT cho toàn công ty.\n- Xây dựng các luồng tự động hóa quy trình vận hành (CI/CD pipeline, automated monitoring, scheduled tasks).\n- Viết script (Python, Bash, PowerShell) để tự động hóa giám sát hệ thống.\n- Nếu bạn quan tâm đến vị trí này, vui lòng gửi CV trực tiếp qua email: hr@bosch-vietnam.com.vn', '- Tốt nghiệp Đại học chuyên ngành Công nghệ Thông tin, Khoa học Máy tính, Kỹ thuật Phần mềm hoặc các ngành liên quan.\n- Có kinh nghiệm thực tế ở vị trí Backend/Fullstack Developer, System/DevOps Engineer hoặc IT Infrastructure.\n- Có kiến thức nền tảng về an toàn thông tin, quản trị hệ thống mạng và xử lý sự cố bảo mật.', '- Môi trường làm việc năng động, được trao quyền quyết định về giải pháp và kiến trúc bảo mật.\n- Hỗ trợ chi phí di chuyển bằng xe công nghệ khi đi gặp khách hàng.\n- Thưởng lễ, Tết, lương tháng 13.\n- Du lịch, teambuilding cùng công ty hằng năm.\n- 16 ngày phép/năm. Happy hour mỗi thứ 6 hằng tuần.', 'Phan Văn Hân, Phường Gia Định, Thành phố Hồ Chí Minh', '10 - 13 triệu', '2026-11-30', '2026-09-01 08:00:00', 6),
(7, 'Data Analyst', '- Xây dựng báo cáo và Dashboard trực quan hỗ trợ ra quyết định cho Ban lãnh đạo và các phòng ban.\n- Thiết kế, quản lý và tối ưu hóa hệ thống báo cáo KPI theo yêu cầu quản trị.\n- Phân tích hành vi khách hàng, xu hướng thị trường và hiệu quả hoạt động để đưa ra insight và khuyến nghị hành động.\n- Hợp tác với các phòng ban nghiệp vụ để hiểu yêu cầu kinh doanh.\n- Triển khai, tùy chỉnh và vận hành Dashboard trên Apache Superset/Power BI/Tableau.\n- Nếu bạn quan tâm đến vị trí này, vui lòng gửi CV trực tiếp qua email: careers@cmcglobal.vn', '- Tốt nghiệp Đại học chuyên ngành CNTT, Toán - Thống kê, Kinh tế hoặc các ngành liên quan.\n- Tối thiểu 3 năm kinh nghiệm trong lĩnh vực Data Analyst/BI.\n- Thành thạo SQL, DAX, MDX để khai thác dữ liệu.\n- Có kinh nghiệm với công cụ BI: Power BI, Tableau, Google Data Studio, Qlik hoặc Apache Superset.', '- Thưởng Lễ Tết, thưởng hiệu quả kinh doanh.\n- Bảo hiểm sức khỏe và Chương trình Khám sức khỏe định kỳ hàng năm chất lượng cao.\n- Du lịch, team building 2-3 lần/năm.\n- Các chế độ theo quy định của pháp luật (BHYT, BHXH, BHTN).\n- Môi trường làm việc chuyên nghiệp, lắng nghe, tôn trọng.', 'Số O17, tầng 1 tòa B, Phường Xuân La, Quận Tây Hồ, Thành phố Hà Nội', '20 - 27 triệu', '2026-10-15', '2026-08-01 08:00:00', 7),
(8, 'Product Manager Mobile App', '- Xây dựng và quản lý kế hoạch phát triển sản phẩm (Product Roadmap) kết hợp với kế hoạch triển khai dự án.\n- Đảm nhận vai trò Product Owner để xác định yêu cầu sản phẩm, xây dựng backlog, xác định các ưu tiên tính năng.\n- Lập kế hoạch dự án, đề xuất và xác minh giải pháp với toàn team (PO, UI/UX, Dev, Tester).\n- Hỗ trợ các thành viên khác trong đội dự án để bảo đảm tiến độ, chất lượng công việc.\n- Quản lý, điều phối và thúc đẩy các hoạt động của nhóm dự án.\n- Nếu bạn quan tâm đến vị trí này, vui lòng gửi CV trực tiếp qua email: recruitment@samsung-vietnam.com', '- Tối thiểu 3 năm kinh nghiệm làm Project Manager hoặc Product Owner, ưu tiên kinh nghiệm phát triển Mobile App dòng IAP.\n- Có khả năng nghiên cứu nhanh và nắm bắt kiến thức về hệ thống cũng như các điểm kỹ thuật/công nghệ.\n- Am hiểu về Google Play Store/Apple App Store.\n- Có mindset tốt về sản phẩm, trải nghiệm người dùng.\n- Có khả năng ứng dụng AI vào công việc.', '- Thưởng các mức theo milestone dự án, năng suất tiến độ hoặc sáng kiến cải tiến mới.\n- Đồng nghiệp trẻ trung, năng động, luôn hỗ trợ và giúp đỡ nhau.\n- Môi trường làm việc văn minh, công bằng, tinh thần Startup năng động.\n- Luyện kỹ năng trong hệ sinh thái 10 triệu người dùng hàng tháng trên toàn cầu.', '60B Nguyễn Huy Tưởng, Phường Thanh Xuân, Hà Nội', '30 - 50 triệu', '2026-09-20', '2026-07-01 08:00:00', 8),
(9, 'Kỹ Sư Chất Lượng QC Công Trình', '- Thiết lập hệ thống quản lý chất lượng và kiểm tra hiện trường.\n- Thiết lập hệ thống đảm bảo chất lượng cho dự án bao gồm: hệ thống biểu mẫu cần thiết, chỉ dẫn kỹ thuật thi công, hướng dẫn nghiệm thu công việc cho đội ngũ Kỹ sư hiện trường.\n- Kiểm tra tiêu chuẩn kỹ thuật của dự án, đảm bảo vật tư, thiết bị mang đến dự án đảm bảo yêu cầu kỹ thuật.\n- Hướng dẫn cách lập bảng biểu ghi chép nhật ký và các biểu nghiệm thu công việc.\n- Kiểm soát và đánh giá chất lượng.\n- Nếu bạn quan tâm đến vị trí này, vui lòng gửi CV trực tiếp qua email: hr@lg-vietnam.com.vn', '- Đại học các ngành xây dựng công trình, địa kỹ thuật.\n- Hiểu sâu các tiêu chuẩn kỹ thuật liên quan đến sản phẩm dịch vụ của Công ty.\n- Sử dụng thành thạo 7 công cụ QC.', '- Đóng bảo hiểm xã hội, thu nhập cạnh tranh.\n- Thưởng kinh doanh dự án, trợ cấp dự án.\n- Thưởng hiệu suất công việc.\n- Thưởng lễ/tết.', 'Tầng 15, Tháp CEO, Đường Phạm Hùng, Phường Mễ Trì, Quận Nam Từ Liêm, Hà Nội', '25 - 28 triệu', '2026-08-01', '2026-05-01 08:00:00', 9),
(10, 'Web Developer Junior', '- Tham gia phát triển các sản phẩm Web trong môi trường Agile/Scrum.\n- Làm việc cùng Tech Lead và mentor để xây dựng tính năng mới.\n- Viết code, unit-test, sửa lỗi và tối ưu hiệu năng hệ thống.\n- Phối hợp với các thành viên trong team.\n- Tham gia họp sprint và báo cáo tiến độ.\n- Chủ động tìm hiểu và áp dụng công nghệ mới theo định hướng dự án.\n- Nếu bạn quan tâm đến vị trí này, vui lòng gửi CV trực tiếp qua email: jobs@intel-vietnam.com', '- Tốt nghiệp đại học chuyên ngành IT hoặc các chuyên ngành liên quan.\n- Tối thiểu 1 năm kinh nghiệm thực tế phát triển Web.\n- Có kinh nghiệm với ít nhất một ngôn ngữ lập trình phổ biến (Java / .NET / NodeJS / PHP / Python / React / Angular / Vue...).\n- Thành thạo sử dụng Git trong làm việc nhóm.\n- Tiếng Anh: đọc hiểu tài liệu chuyên ngành.', '- Thưởng lương tháng 13, thưởng các dịp lễ, Tết.\n- Tham gia BHXH, BHYT, BHTN theo đúng quy định của Nhà nước.\n- Được đào tạo bài bản, hướng dẫn trực tiếp bởi Mentor giàu kinh nghiệm.\n- Được cấp máy tính và đầy đủ thiết bị làm việc.', '28/180 Thái Thịnh, Đống Đa, Hà Nội', '10 - 16 triệu', '2026-07-15', '2026-05-01 08:00:00', 10),
(11, 'Thực tập sinh Content Marketing', '- Lên ý tưởng và viết nội dung cho các kênh truyền thông: Facebook, Website, TikTok, Zalo,...\n- Biên soạn nội dung quảng cáo, bài đăng truyền thông theo định hướng thương hiệu.\n- Phối hợp cùng team Design/Marketing để triển khai chiến dịch.\n- Theo dõi xu hướng mới, cập nhật trend để ứng dụng vào nội dung.\n- Hỗ trợ xây dựng kế hoạch content theo tuần/tháng.\n- Nếu bạn quan tâm đến vị trí này, vui lòng gửi CV trực tiếp qua email: tuyendung@base.vn', '- Sinh viên năm cuối hoặc mới tốt nghiệp các ngành Marketing, Truyền thông, Báo chí,...\n- Có khả năng viết lách, sáng tạo nội dung.\n- Có hiểu biết về Facebook ads, Google ads.\n- Chủ động, có tinh thần học hỏi và trách nhiệm trong công việc.\n- Có laptop cá nhân.', '- Hỗ trợ dấu xác nhận thực tập.\n- Cơ hội trở thành nhân viên chính thức sau 2-3 tháng thực tập.\n- Nhân viên chính thức được review lương 2 lần/năm và hưởng đầy đủ phúc lợi.\n- Môi trường năng động, chuyên nghiệp, thân thiện.', 'Số 17 Đường số 2, KDC CityLand Park Hills, P10, Q. Gò Vấp, TP.HCM', '1 - 2 triệu', '2026-06-30', '2026-05-01 08:00:00', 11),
(12, 'HR Executive', '- Phụ trách thực hiện công tác tuyển dụng: đăng tin, sàng lọc hồ sơ, tổ chức phỏng vấn, tiếp nhận nhân viên mới.\n- Quản lý chấm công, tính phúc lợi cho nhân viên hàng tháng.\n- Quản lý và xử lý chứng từ, hồ sơ và văn bản.\n- Quản lý điện nước, cơ sở vật chất.\n- Phụ trách tổ chức các hoạt động nội bộ như team building, sinh nhật, sự kiện nội bộ.\n- Nếu bạn quan tâm đến vị trí này, vui lòng gửi CV trực tiếp qua email: nhansu@vnpt.vn', '- Tốt nghiệp cao đẳng/đại học, ưu tiên ngành Quản trị nhân sự, Văn thư và các ngành liên quan.\n- Sử dụng thành thạo tin học văn phòng, đặc biệt là Excel.\n- Khả năng chịu áp lực tốt, trung thực, cẩn trọng, làm việc tỉ mỉ.\n- Có laptop cá nhân.', '- Được tham gia BHYT, BHXH, BHTN theo luật hiện hành.\n- Chế độ nghỉ phép 12 ngày/năm.\n- Lương tháng 13, được xem xét review lương hàng năm.\n- Được tham gia Team Building, Year End Party.\n- Làm việc trong môi trường trẻ trung năng động.', 'Số 74 Cửa Bắc, Phường Ba Đình, Hà Nội', '7 - 9 triệu', '2026-05-31', '2026-03-01 08:00:00', 12);

-- INSERT 10: NguoiDung_KyNang
INSERT INTO `NguoiDung_KyNang` (`MaNguoiDung`, `MaKyNang`) VALUES
(1, 1), (2, 3), (3, 4), (4, 5), (5, 2);

-- INSERT 11: CongViec_KyNang
INSERT INTO `CongViec_KyNang` (`MaCongViec`, `MaKyNang`) VALUES
(1, 1), (2, 5), (3, 3);

-- INSERT 12: ThongBao
INSERT INTO `ThongBao` (`NoiDung`, `LoaiThongBao`, `LienKet`, `MaNguoiDung`) VALUES
('Nguyễn Văn Nam đã bày tỏ cảm xúc về bài viết của bạn.', 'TuongTac', '/post/3', 1),
('Công ty TechCorp đã gửi cho bạn một lời mời phỏng vấn.', 'Tuyendung', '/interview/12', 2),
('Trần Minh Quang đã nhắc đến bạn trong một bình luận.', 'BinhLuan', '/post/5#comment-88', 3);

-- INSERT 13: TinNhan
INSERT INTO `TinNhan` (`NguoiGui`, `NguoiNhan`, `NoiDung`, `ThoiGianGui`, `TrangThaiDoc`) VALUES
(3, 1, 'Chào Phương Vy, công ty anh đang tuyển vị trí Backend Developer, em có quan tâm không?', DATE_SUB(NOW(), INTERVAL 5 DAY), 1),
(1, 3, 'Dạ em quan tâm ạ. Cho em hỏi yêu cầu và mức lương như thế nào?', DATE_SUB(NOW(), INTERVAL 4 DAY), 1),
(3, 1, 'Yêu cầu 1 năm kinh nghiệm PHP/Java, lương 18-25tr. Em gửi CV qua email anh nhé', DATE_SUB(NOW(), INTERVAL 3 DAY), 1),
(1, 3, 'Dạ vâng ạ. Em sẽ gửi CV trong hôm nay, cảm ơn anh!', DATE_SUB(NOW(), INTERVAL 2 DAY), 0),
(1, 4, 'Như ơi, cậu làm xong bài tập môn Web chưa?', DATE_SUB(NOW(), INTERVAL 2 DAY), 1),
(4, 1, 'Mình làm xong giao diện rồi, còn phần đăng nhập với database', DATE_SUB(NOW(), INTERVAL 2 DAY), 1),
(1, 4, 'Phần đăng nhập mình làm rồi, tối nay share code cho cậu nhé', DATE_SUB(NOW(), INTERVAL 1 DAY), 1),
(4, 1, 'Cảm ơn cậu nhiều! Khi nào rảnh mời cậu đi ăn', DATE_SUB(NOW(), INTERVAL 12 HOUR), 0),
(1, 4, 'Ok cậu, hẹn cuối tuần nhé!', DATE_SUB(NOW(), INTERVAL 6 HOUR), 0),
(1, 5, 'Anh Đam ơi, em đang làm project Data Analysis, anh có thể hướng dẫn em được không ạ?', DATE_SUB(NOW(), INTERVAL 3 DAY), 1),
(5, 1, 'Có, em muốn hỏi gì về Data Analysis?', DATE_SUB(NOW(), INTERVAL 3 DAY), 1),
(1, 5, 'Dạ em muốn học cách xử lý dữ liệu lớn với Python ạ', DATE_SUB(NOW(), INTERVAL 2 DAY), 1),
(5, 1, 'Ok, cuối tuần này anh rảnh, anh sẽ hướng dẫn em qua team call', DATE_SUB(NOW(), INTERVAL 1 DAY), 1),
(1, 5, 'Dạ cảm ơn anh nhiều ạ!', DATE_SUB(NOW(), INTERVAL 12 HOUR), 0),
(2, 1, 'Phương Vy, bạn có biết ai đang tìm việc mảng Marketing không? Bên mình tuyển gấp', DATE_SUB(NOW(), INTERVAL 4 DAY), 1),
(1, 2, 'Để mình hỏi bạn bè xem. Bên bạn yêu cầu gì thế?', DATE_SUB(NOW(), INTERVAL 4 DAY), 1),
(2, 1, 'Cần 2 năm kinh nghiệm, ưu tiên biết chạy Facebook Ads', DATE_SUB(NOW(), INTERVAL 3 DAY), 1),
(1, 2, 'Mình có bạn học chuyên ngành Marketing, để mình giới thiệu', DATE_SUB(NOW(), INTERVAL 2 DAY), 0),
(6, 1, 'Phương Vy ơi, chị thấy em đăng bài về Figma, em có thể dạy chị được không?', DATE_SUB(NOW(), INTERVAL 1 DAY), 1),
(1, 6, 'Dạ được chị ơi. Chị muốn học phần nào trước ạ?', DATE_SUB(NOW(), INTERVAL 23 HOUR), 1),
(6, 1, 'Chị muốn học cách tạo prototype cho web', DATE_SUB(NOW(), INTERVAL 22 HOUR), 1),
(1, 6, 'Dạ cuối tuần này em rảnh, em sẽ hướng dẫn chị qua Zoom nhé', DATE_SUB(NOW(), INTERVAL 20 HOUR), 1),
(6, 1, 'Cảm ơn em nhiều, chị sẽ cố gắng học', DATE_SUB(NOW(), INTERVAL 18 HOUR), 0),
(4, 3, 'Anh Phong ơi, công ty mình còn nhận thực tập sinh không ạ?', DATE_SUB(NOW(), INTERVAL 1 DAY), 1),
(3, 4, 'Còn em, bên IT đang cần 2 bạn thực tập Frontend', DATE_SUB(NOW(), INTERVAL 23 HOUR), 1),
(4, 3, 'Dạ cho em xin thông tin với ạ', DATE_SUB(NOW(), INTERVAL 22 HOUR), 1),
(3, 4, 'Em gửi CV qua email anh nhé, anh sẽ chuyển cho team kỹ thuật', DATE_SUB(NOW(), INTERVAL 20 HOUR), 1),
(4, 3, 'Dạ vâng ạ, cảm ơn anh nhiều!', DATE_SUB(NOW(), INTERVAL 18 HOUR), 0),
(7, 1, 'Chào Phương Vy, mình có job web nhỏ, bạn có nhận không?', DATE_SUB(NOW(), INTERVAL 2 DAY), 1),
(1, 7, 'Cảm ơn bạn, job gì vậy ạ?', DATE_SUB(NOW(), INTERVAL 2 DAY), 1),
(7, 1, 'Làm landing page cho quán cà phê, thù lao 5tr', DATE_SUB(NOW(), INTERVAL 1 DAY), 1),
(1, 7, 'Mình nhận được, khi nào cần bàn giao ạ?', DATE_SUB(NOW(), INTERVAL 1 DAY), 1),
(7, 1, '1 tuần nữa bạn nhé, ok không?', DATE_SUB(NOW(), INTERVAL 12 HOUR), 1),
(1, 7, 'Ok bạn, mình sẽ cố gắng', DATE_SUB(NOW(), INTERVAL 6 HOUR), 0);

-- INSERT 14: HinhAnh
INSERT INTO `HinhAnh` (`DuongDanURL`, `MaBaiViet`) VALUES
('https://example.com/img/project.jpg', 1),
('https://example.com/img/fpt_seminar.jpg', 2);

-- INSERT 15: BinhLuan
INSERT INTO `BinhLuan` (`NoiDung`, `MaBaiViet`, `MaNguoiDung`) VALUES
('Tuyệt vời quá Vy ơi!', 1, 4),
('Sinh viên trường khác tham gia sự kiện được không anh?', 2, 4);

-- INSERT 16: TuongTac
INSERT INTO `TuongTac` (`MaNguoiDung`, `MaBaiViet`, `LoaiTuongTac`) VALUES
(4, 1, 'Thích'),
(5, 1, 'Quan tâm'),
(1, 2, 'Hữu ích');

-- ===================================================================================
-- KẾT THÚC SCRIPT
-- ===================================================================================
