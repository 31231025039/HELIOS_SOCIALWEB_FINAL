-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th5 31, 2026 lúc 04:58 PM
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
-- Cơ sở dữ liệu: `db_helios`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `baiviet`
--

CREATE TABLE `baiviet` (
  `MaBaiViet` int(11) NOT NULL,
  `NoiDung` text NOT NULL,
  `LoaiBaiViet` enum('post','event') DEFAULT 'post',
  `TenSuKien` varchar(255) DEFAULT NULL,
  `DiaDiemSuKien` varchar(255) DEFAULT NULL,
  `ThoiGianSuKien` datetime DEFAULT NULL,
  `TrangThai` enum('Public','Private','Friends') DEFAULT 'Public',
  `ThoiGianDang` datetime DEFAULT current_timestamp(),
  `MaNguoiDung` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `baiviet`
--

INSERT INTO `baiviet` (`MaBaiViet`, `NoiDung`, `LoaiBaiViet`, `TenSuKien`, `DiaDiemSuKien`, `ThoiGianSuKien`, `TrangThai`, `ThoiGianDang`, `MaNguoiDung`) VALUES
(1, 'Chào mọi người, mình vừa hoàn thành dự án lớn dùng Spring Boot!', 'post', NULL, NULL, NULL, 'Public', '2026-05-27 09:18:45', 1),
(2, 'Công ty FPT trân trọng kính mời các bạn sinh viên tham gia Seminar hướng nghiệp.', 'event', 'Seminar Hướng nghiệp IT 2024', 'Đại học Bách Khoa', '2026-06-05 15:00:00', 'Public', '2026-05-27 09:18:45', 3),
(3, 'Xu hướng Marketing 2024 sẽ tập trung vào Tiktok và AI.', 'post', NULL, NULL, NULL, 'Public', '2026-05-27 09:18:45', 2),
(6, 'Chào mừng đến Career Fair Helios', 'event', 'Chào mừng đến Career Fair', 'Hồ Chí Minh', '2026-05-30 10:00:00', 'Public', '2026-05-27 10:48:36', 8),
(14, 'Mình vừa hoàn thành thiết kế Figma cho trang đăng kí/đăng nhập Hệ thống trung tâm Anh ngữ', 'post', NULL, NULL, NULL, 'Public', '2026-05-30 02:48:35', 11);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `binhluan`
--

CREATE TABLE `binhluan` (
  `MaBinhLuan` int(11) NOT NULL,
  `NoiDung` text NOT NULL,
  `ThoiGianDang` datetime DEFAULT current_timestamp(),
  `MaBaiViet` int(11) NOT NULL,
  `MaNguoiDung` int(11) NOT NULL,
  `MaBinhLuanCha` int(11) DEFAULT NULL,
  `TrangThaiBinhLuan` varchar(20) NOT NULL DEFAULT 'Hien',
  `AnBoiNguoiDung` int(11) DEFAULT NULL,
  `ThoiGianBiAn` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `binhluan`
--

INSERT INTO `binhluan` (`MaBinhLuan`, `NoiDung`, `ThoiGianDang`, `MaBaiViet`, `MaNguoiDung`, `MaBinhLuanCha`, `TrangThaiBinhLuan`, `AnBoiNguoiDung`, `ThoiGianBiAn`) VALUES
(1, 'Tuyệt vời quá Vy ơi!', '2026-05-27 09:18:45', 1, 4, NULL, 'Hien', NULL, NULL),
(2, 'Sinh viên trường khác tham gia sự kiện được không anh?', '2026-05-27 09:18:45', 2, 4, NULL, 'Hien', NULL, NULL),
(3, 'Mình nghĩ được á bạn', '2026-05-27 11:44:18', 2, 9, 2, 'Hien', NULL, NULL),
(4, 'chào bạn', '2026-05-30 02:48:53', 14, 1, NULL, 'Hien', NULL, NULL),
(5, 'muốn xem thêm những trang khác thì nhắn mình', '2026-05-30 12:43:21', 14, 11, 4, 'Hien', NULL, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `congty`
--

CREATE TABLE `congty` (
  `MaCongTy` int(11) NOT NULL,
  `TenCongTy` varchar(255) NOT NULL,
  `MoTa` text DEFAULT NULL,
  `Logo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `congty`
--

INSERT INTO `congty` (`MaCongTy`, `TenCongTy`, `MoTa`, `Logo`) VALUES
(1, 'Công ty Cổ phần VNG', 'Công ty Cổ phần VNG là một trong những công ty công nghệ hàng đầu Việt Nam...', '/uploads/logos/logo_6a16caa86b3b9.png'),
(2, 'FPT Software', 'FPT Software là công ty công nghệ hàng đầu Việt Nam...', '/uploads/logos/logo_6a16ca9a53b2d.png'),
(3, 'Shopee Việt Nam', 'Shopee là nền tảng thương mại điện tử hàng đầu...', '/uploads/logos/logo_6a16ca8d3810c.png'),
(4, 'Momo', 'MoMo là một trong những ví điện tử và nền tảng thanh toán...', '/uploads/logos/logo_6a16ca7fc9c5b.png'),
(5, 'MISA', 'MISA là công ty công nghệ hàng đầu Việt Nam chuyên cung cấp phần mềm...', '/uploads/logos/logo_6a16ca722d89a.jpg'),
(6, 'Bosch', 'Bosch là tập đoàn công nghệ và kỹ thuật đa quốc gia...', '/uploads/logos/logo_6a16ca627a29a.jfif'),
(7, 'CMC', 'CMC là tập đoàn công nghệ thông tin hàng đầu Việt Nam...', '/uploads/logos/logo_6a16ca4e098db.jpg'),
(8, 'Samsung', 'Samsung là tập đoàn điện tử đa quốc gia hàng đầu', '/uploads/logos/logo_6a16ca41487c1.jpg'),
(9, 'LG', 'LG là tập đoàn điện tử và công nghệ đa quốc gia...', '/uploads/logos/logo_6a16ca2f6a731.png'),
(10, 'Intel', 'Intel là tập đoàn công nghệ bán dẫn hàng đầu thế giới', '/uploads/logos/logo_6a16ca236077f.jpeg'),
(11, 'Base.vn', 'Base.vn là nền tảng quản trị doanh nghiệp toàn diện', '/uploads/logos/logo_6a16ca0420e66.png'),
(12, 'VNPT', 'VNPT là tập đoàn Bưu chính Viễn thông Việt Nam', '/uploads/logos/logo_6a16ca12ede5c.png');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `congviec`
--

CREATE TABLE `congviec` (
  `MaCongViec` int(11) NOT NULL,
  `TieuDe` varchar(255) NOT NULL,
  `MoTa` text NOT NULL,
  `YeuCau` text NOT NULL,
  `QuyenLoi` text NOT NULL,
  `NoiLamViec` varchar(255) NOT NULL,
  `MucLuong` varchar(100) NOT NULL,
  `HanNop` date NOT NULL,
  `NgayDang` datetime DEFAULT current_timestamp(),
  `MaCongTy` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `congviec`
--

INSERT INTO `congviec` (`MaCongViec`, `TieuDe`, `MoTa`, `YeuCau`, `QuyenLoi`, `NoiLamViec`, `MucLuong`, `HanNop`, `NgayDang`, `MaCongTy`) VALUES
(1, 'Lập trình viên Java Back-End Developer', '- Phát triển và mở rộng các sản phẩm phần mềm của công ty trên nền tảng framework nội bộ Java Web Application.\n- Thiết kế, xây dựng và vận hành các module backend phục vụ hệ thống ở quy mô lớn, đảm bảo hiệu năng, tính ổn định và khả năng mở rộng.\n- Phân tích yêu cầu nghiệp vụ, thiết kế giải pháp kỹ thuật và tham gia review architecture.\n- Phát triển các module backend mới và bảo trì, mở rộng các module hiện có.\n- Viết code đảm bảo chất lượng: rõ ràng, dễ bảo trì, tuân thủ convention của dự án.\n- Phối hợp với team để thiết kế API, schema database và luồng xử lý dữ liệu.\n- Nếu bạn quan tâm đến vị trí này, vui lòng gửi CV trực tiếp qua email: tuyendung@vng.com.vn', '- Kinh nghiệm: 03 năm phát triển backend Java trong môi trường production.\n- Trình độ: Đại học chuyên ngành Công Nghệ Thông Tin hoặc Toán tin.\n- Nắm vững lập trình hướng đối tượng (OOP) và các nguyên tắc thiết kế phần mềm (Design Patterns: Factory, Singleton, Strategy, Observer...).\n- Thành thạo ngôn ngữ Java; có kinh nghiệm phát triển ứng dụng backend trên nền tảng Java Web Application.\n- Hiểu biết về kiến trúc phân lớp RESTful API và các cơ chế xử lý request/response trong ứng dụng web (HTTP lifecycle, filter, interceptor, serialization).\n- Nắm vững MongoDB: thiết kế schema document, aggregation pipeline, indexing, query optimization trong môi trường production. Có kinh nghiệm làm việc với dữ liệu lớn.\n- Thành thạo truy vấn SQL.', '- Được ăn trưa tại công ty.\n- Được đóng bảo hiểm xã hội, y tế.\n- Có thưởng các ngày lễ tết.\n- Du lịch nghỉ dưỡng hằng năm cùng công ty.\n- Thưởng theo dự án, đi du lịch nhiều nơi.', 'E.21 đường D23 khu nhà ở Phước Long B, Phường Phước Long, Tp. Hồ Chí Minh', '20 - 30 triệu (tùy dự án)', '2026-12-31', '2026-05-24 08:40:00', 1),
(2, 'Thực tập sinh Software Engineer', '- Tùy theo năng lực và định hướng, thực tập sinh sẽ được phân công vào nhóm phù hợp (Backend / Frontend / Mobile / Data - Automation) và làm việc dưới sự hướng dẫn trực tiếp của mentor:\n- Tham gia phát triển, hoàn thiện các tính năng được giao trên hệ thống web và API nội bộ (PHP / Laravel; NodeJS / TypeScript).\n- Xây dựng và chỉnh sửa giao diện người dùng với React / TypeScript / JavaScript.\n- Tham gia phát triển, bảo trì ứng dụng di động bằng React Native / Flutter.\n- Viết script xử lý dữ liệu, tự động hóa quy trình và công cụ nội bộ bằng Python.\n- Tìm và sửa lỗi (bug) trên hệ thống; kiểm thử tính năng trước khi đưa lên.\n- Hỗ trợ tối ưu truy vấn, xử lý dữ liệu trên cơ sở dữ liệu MySQL; làm việc với Redis, hàng đợi và tích hợp API đối tác/nhà vận chuyển.\n- Viết và cập nhật tài liệu kỹ thuật cho tính năng/module được giao.\n- Trao đổi tiến độ, nhận và phản hồi yêu cầu công việc trực tiếp với mentor và các thành viên trong nhóm.\n- Nếu bạn quan tâm đến vị trí này, vui lòng gửi CV trực tiếp qua email: tuyendung@fpt.com.vn', '- Sinh viên năm 2 - 4 hoặc mới tốt nghiệp ngành CNTT, Khoa học máy tính, Kỹ thuật phần mềm hoặc tương đương.\n- Nắm vững kiến thức nền tảng: cấu trúc dữ liệu & giải thuật, lập trình hướng đối tượng (OOP), Git.\n- Hiểu cơ bản về cơ sở dữ liệu quan hệ (SQL / MySQL) và mô hình HTTP / RESTful API.\n- Có kiến thức ít nhất một trong các stack: JavaScript / TypeScript, NodeJS, React, React Native, PHP / Laravel, hoặc Python.\n- Đọc hiểu được tài liệu kỹ thuật bằng tiếng Anh.\n- Tư duy logic tốt, ham học hỏi, chủ động, có trách nhiệm, kỷ luật và làm việc nhóm tốt.', '- Có hỗ trợ chi phí thực tập (mức cụ thể trao đổi khi phỏng vấn).\n- Được hướng dẫn bởi mentor và tham gia vào dự án thực tế của công ty.\n- Được làm quen với quy trình phát triển phần mềm chuyên nghiệp và các công nghệ đang sử dụng tại SuperShip.\n- Cấp giấy xác nhận thực tập, đóng dấu và hỗ trợ hồ sơ tốt nghiệp.\n- Có cơ hội được xem xét trở thành nhân viên chính thức (Junior Developer) nếu kết quả thực tập tốt.', '32 Thân Nhân Trung, Phường 13, Quận Tân Bình, Tp. Hồ Chí Minh', 'Thỏa thuận', '2026-09-30', '2026-05-12 10:05:00', 2),
(3, 'Nhân Viên Telemarketing', '- Sáng tạo nội dung Fanpage để thu hút khách hàng tiềm năng.\n- Tương tác, chăm sóc khách hàng Fanpage công ty để tạo dữ liệu khách hàng.\n- Tham gia các sự kiện lái thử của showroom vào cuối tuần, cũng như các sự kiện lớn của công ty để hỗ trợ khách hàng.\n- Thực hiện các cuộc gọi với các khách hàng tiềm năng để hẹn khách hàng đến Showroom lái thử xe ô tô Subaru.\n- Thực hiện các cuộc gọi/khảo sát đặc biệt theo yêu cầu kinh doanh.\n- Gọi điện định kỳ hàng tháng để theo dõi độ tiềm năng dẫn đến nhu cầu mua xe của khách hàng.\n- Chuyển các khiếu nại từ khách hàng đến Giám sát để xử lý.\n- Lưu trữ thông tin khách hàng, thực hiện các báo cáo hàng tuần.\n- Nếu bạn quan tâm đến vị trí này, vui lòng gửi CV trực tiếp qua email: careers@shopee.vn', '- Nữ, độ tuổi 2000 trở lên, có kinh nghiệm mảng Marketing.\n- Ưu tiên tốt nghiệp ngành Marketing.\n- Sử dụng thành thạo vi tính văn phòng (Microsoft Office).\n- Khả năng giao tiếp tốt, thuyết phục khách hàng tốt.\n- Giọng nói dễ nghe, không nói ngọng, nói lắp, nói nặng giọng địa phương.\n- Có thể giao tiếp bằng tiếng Anh là một lợi thế.', '- Thử việc 100% lương.\n- Các chế độ BHXH, BHYT, BHTN theo quy định của nhà nước.\n- Ngoài ra được cấp thêm Bảo hiểm tai nạn 24/7, thẻ Bảo hiểm sức khỏe.\n- Quà tặng Lễ Tết hàng năm theo quy định của Công ty.\n- Khám sức khỏe tổng quát hằng năm.\n- Môi trường làm việc chuyên nghiệp, cơ hội thăng tiến cao.', 'Showroom Subaru Gò Vấp - 819 Quang Trung, An Hội Tây, Hồ Chí Minh', 'Thỏa thuận', '2026-09-30', '2026-05-14 11:48:00', 3),
(4, 'UI/UX Designer', '- Thiết kế giao diện người dùng (UI) và trải nghiệm người dùng (UX) cho các ứng dụng quản lý doanh nghiệp, đặc biệt là CRM, HRM, Sales Apps...\n- Tiến hành nghiên cứu người dùng (user research), làm việc trực tiếp với end-user để hiểu nhu cầu, hành vi của người dùng cuối.\n- Sử dụng Figma để tạo wireframes, mockups, prototypes tương tác và design systems.\n- Thực hiện user testing và usability testing để xác thực các thiết kế.\n- Hợp tác chặt chẽ với các developer, product manager và các stakeholder khác.\n- Phát triển và duy trì design system và style guide.\n- Nếu bạn quan tâm đến vị trí này, vui lòng gửi CV trực tiếp qua email: luuvanE@momovietnam.com', '- Tốt nghiệp đại học trở lên - chuyên ngành thiết kế hoặc các ngành liên quan.\n- Ít nhất 3 năm kinh nghiệm làm việc ở vị trí tương đương.\n- Thành thạo Figma.\n- Kinh nghiệm trong user research, prototyping và usability testing.\n- Hiểu biết vững chắc về các nguyên tắc thiết kế UX/UI và responsive design.\n- Kinh nghiệm thiết kế cho các hệ thống enterprise (CRM, HRM, ERP, Sales Apps).\n- Hồ sơ đính kèm Portfolio các mẫu thiết kế đã thực hiện.', '- Tham gia BHXH, BHYT, BHTN theo quy định pháp luật.\n- Lương, thưởng các dịp lễ, tết và phúc lợi khác theo chế độ Tập đoàn.\n- Phép năm: 12 ngày/năm, tăng theo thâm niên.\n- Phụ cấp cơm trưa: 25k/ngày.\n- Giảm giá 30%-50% khi sử dụng dịch vụ tại chuỗi nhà hàng Café Rita Võ.', 'Số 5A đường số 3, Phường An Khánh, Thành phố Thủ Đức', '12 - 17 triệu', '2026-10-20', '2026-05-18 09:55:00', 4),
(5, 'Trưởng Nhóm Frontend Developer', '- Phát triển ứng dụng frontend cho OTT, TV app, mobile/web platform, game portal.\n- Xây dựng UI sử dụng ReactJS / NextJS (SSR, SSG, App Router).\n- Tối ưu performance trên thiết bị yếu (Smart TV, mobile).\n- Tích hợp video streaming (HLS/DASH), player customization.\n- Xây dựng realtime features (chat, notification, live event).\n- Thiết kế kiến trúc frontend scalable, reusable component system.\n- Phối hợp với backend (.NET/NodeJS/Go) và team sản phẩm.\n- Nếu bạn quan tâm đến vị trí này, vui lòng gửi CV trực tiếp qua email: tuyendung@misa.com.vn', '- Từ 3 năm kinh nghiệm Frontend.\n- Thành thạo: ReactJS (hooks, lifecycle, optimization).\n- Kinh nghiệm: State management (Redux / Zustand / React Query).\n- Hiểu sâu: Performance optimization (code splitting, memo, lazy load), Browser rendering / event loop.\n- Có kinh nghiệm làm sản phẩm thực tế (ưu tiên media, video, OTT).', '- Được làm việc trong công ty đứng đầu về mảng Giải trí số.\n- Hưởng đầy đủ các chế độ theo luật (BHXH, BHYT, nghỉ lễ, nghỉ phép, thai sản...).\n- Bảo hiểm sức khỏe toàn diện 24/7, khám sức khỏe thường niên.\n- Lộ trình thăng tiến rõ ràng, môi trường trẻ trung năng động.', '3 ngõ 84 Ngọc Khánh, Phường Giảng Võ, Hà Nội', 'Thoả thuận', '2026-12-31', '2026-05-28 12:27:00', 5),
(6, 'IT Securities Developer', '- Trực tiếp lập trình, xây dựng và bảo trì các công cụ, ứng dụng nội bộ phục vụ vận hành của MarTech Team.\n- Thiết kế kiến trúc hệ thống, database và API cho các sản phẩm nội bộ.\n- Cài đặt, cấu hình và quản trị hạ tầng server, hệ thống máy tính và thiết bị CNTT cho toàn công ty.\n- Xây dựng các luồng tự động hóa quy trình vận hành (CI/CD pipeline, automated monitoring, scheduled tasks).\n- Viết script (Python, Bash, PowerShell) để tự động hóa giám sát hệ thống.\n- Nếu bạn quan tâm đến vị trí này, vui lòng gửi CV trực tiếp qua email: hr@bosch-vietnam.com.vn', '- Tốt nghiệp Đại học chuyên ngành Công nghệ Thông tin, Khoa học Máy tính, Kỹ thuật Phần mềm hoặc các ngành liên quan.\n- Có kinh nghiệm thực tế ở vị trí Backend/Fullstack Developer, System/DevOps Engineer hoặc IT Infrastructure.\n- Có kiến thức nền tảng về an toàn thông tin, quản trị hệ thống mạng và xử lý sự cố bảo mật.', '- Môi trường làm việc năng động, được trao quyền quyết định về giải pháp và kiến trúc bảo mật.\n- Hỗ trợ chi phí di chuyển bằng xe công nghệ khi đi gặp khách hàng.\n- Thưởng lễ, Tết, lương tháng 13.\n- Du lịch, teambuilding cùng công ty hằng năm.\n- 16 ngày phép/năm. Happy hour mỗi thứ 6 hằng tuần.', 'Phan Văn Hân, Phường Gia Định, Thành phố Hồ Chí Minh', '10 - 13 triệu', '2026-11-30', '2026-05-22 17:03:00', 6),
(7, 'Data Analyst', '- Xây dựng báo cáo và Dashboard trực quan hỗ trợ ra quyết định cho Ban lãnh đạo và các phòng ban.\n- Thiết kế, quản lý và tối ưu hóa hệ thống báo cáo KPI theo yêu cầu quản trị.\n- Phân tích hành vi khách hàng, xu hướng thị trường và hiệu quả hoạt động để đưa ra insight và khuyến nghị hành động.\n- Hợp tác với các phòng ban nghiệp vụ để hiểu yêu cầu kinh doanh.\n- Triển khai, tùy chỉnh và vận hành Dashboard trên Apache Superset/Power BI/Tableau.\n- Nếu bạn quan tâm đến vị trí này, vui lòng gửi CV trực tiếp qua email: careers@cmcglobal.vn', '- Tốt nghiệp Đại học chuyên ngành CNTT, Toán - Thống kê, Kinh tế hoặc các ngành liên quan.\n- Tối thiểu 3 năm kinh nghiệm trong lĩnh vực Data Analyst/BI.\n- Thành thạo SQL, DAX, MDX để khai thác dữ liệu.\n- Có kinh nghiệm với công cụ BI: Power BI, Tableau, Google Data Studio, Qlik hoặc Apache Superset.', '- Thưởng Lễ Tết, thưởng hiệu quả kinh doanh.\n- Bảo hiểm sức khỏe và Chương trình Khám sức khỏe định kỳ hàng năm chất lượng cao.\n- Du lịch, team building 2-3 lần/năm.\n- Các chế độ theo quy định của pháp luật (BHYT, BHXH, BHTN).\n- Môi trường làm việc chuyên nghiệp, lắng nghe, tôn trọng.', 'Số O17, tầng 1 tòa B, Phường Xuân La, Quận Tây Hồ, Thành phố Hà Nội', '20 - 27 triệu', '2026-10-15', '2026-05-20 13:10:00', 7),
(8, 'Product Manager Mobile App', '- Xây dựng và quản lý kế hoạch phát triển sản phẩm (Product Roadmap) kết hợp với kế hoạch triển khai dự án.\n- Đảm nhận vai trò Product Owner để xác định yêu cầu sản phẩm, xây dựng backlog, xác định các ưu tiên tính năng.\n- Lập kế hoạch dự án, đề xuất và xác minh giải pháp với toàn team (PO, UI/UX, Dev, Tester).\n- Hỗ trợ các thành viên khác trong đội dự án để bảo đảm tiến độ, chất lượng công việc.\n- Quản lý, điều phối và thúc đẩy các hoạt động của nhóm dự án.\n- Nếu bạn quan tâm đến vị trí này, vui lòng gửi CV trực tiếp qua email: recruitment@samsung-vietnam.com', '- Tối thiểu 3 năm kinh nghiệm làm Project Manager hoặc Product Owner, ưu tiên kinh nghiệm phát triển Mobile App dòng IAP.\n- Có khả năng nghiên cứu nhanh và nắm bắt kiến thức về hệ thống cũng như các điểm kỹ thuật/công nghệ.\n- Am hiểu về Google Play Store/Apple App Store.\n- Có mindset tốt về sản phẩm, trải nghiệm người dùng.\n- Có khả năng ứng dụng AI vào công việc.', '- Thưởng các mức theo milestone dự án, năng suất tiến độ hoặc sáng kiến cải tiến mới.\n- Đồng nghiệp trẻ trung, năng động, luôn hỗ trợ và giúp đỡ nhau.\n- Môi trường làm việc văn minh, công bằng, tinh thần Startup năng động.\n- Luyện kỹ năng trong hệ sinh thái 10 triệu người dùng hàng tháng trên toàn cầu.', '60B Nguyễn Huy Tưởng, Phường Thanh Xuân, Hà Nội', '30 - 50 triệu', '2026-09-20', '2026-05-16 15:20:00', 8),
(9, 'Kỹ Sư Chất Lượng QC Công Trình', '- Thiết lập hệ thống quản lý chất lượng và kiểm tra hiện trường.\n- Thiết lập hệ thống đảm bảo chất lượng cho dự án bao gồm: hệ thống biểu mẫu cần thiết, chỉ dẫn kỹ thuật thi công, hướng dẫn nghiệm thu công việc cho đội ngũ Kỹ sư hiện trường.\n- Kiểm tra tiêu chuẩn kỹ thuật của dự án, đảm bảo vật tư, thiết bị mang đến dự án đảm bảo yêu cầu kỹ thuật.\n- Hướng dẫn cách lập bảng biểu ghi chép nhật ký và các biểu nghiệm thu công việc.\n- Kiểm soát và đánh giá chất lượng.\n- Nếu bạn quan tâm đến vị trí này, vui lòng gửi CV trực tiếp qua email: hr@lg-vietnam.com.vn', '- Đại học các ngành xây dựng công trình, địa kỹ thuật.\n- Hiểu sâu các tiêu chuẩn kỹ thuật liên quan đến sản phẩm dịch vụ của Công ty.\n- Sử dụng thành thạo 7 công cụ QC.', '- Đóng bảo hiểm xã hội, thu nhập cạnh tranh.\n- Thưởng kinh doanh dự án, trợ cấp dự án.\n- Thưởng hiệu suất công việc.\n- Thưởng lễ/tết.', 'Tầng 15, Tháp CEO, Đường Phạm Hùng, Phường Mễ Trì, Quận Nam Từ Liêm, Hà Nội', '25 - 28 triệu', '2026-08-01', '2026-05-06 14:47:00', 9),
(10, 'Web Developer Junior', '- Tham gia phát triển các sản phẩm Web trong môi trường Agile/Scrum.\n- Làm việc cùng Tech Lead và mentor để xây dựng tính năng mới.\n- Viết code, unit-test, sửa lỗi và tối ưu hiệu năng hệ thống.\n- Phối hợp với các thành viên trong team.\n- Tham gia họp sprint và báo cáo tiến độ.\n- Chủ động tìm hiểu và áp dụng công nghệ mới theo định hướng dự án.\n- Nếu bạn quan tâm đến vị trí này, vui lòng gửi CV trực tiếp qua email: jobs@intel-vietnam.com', '- Tốt nghiệp đại học chuyên ngành IT hoặc các chuyên ngành liên quan.\n- Tối thiểu 1 năm kinh nghiệm thực tế phát triển Web.\n- Có kinh nghiệm với ít nhất một ngôn ngữ lập trình phổ biến (Java / .NET / NodeJS / PHP / Python / React / Angular / Vue...).\n- Thành thạo sử dụng Git trong làm việc nhóm.\n- Tiếng Anh: đọc hiểu tài liệu chuyên ngành.', '- Thưởng lương tháng 13, thưởng các dịp lễ, Tết.\n- Tham gia BHXH, BHYT, BHTN theo đúng quy định của Nhà nước.\n- Được đào tạo bài bản, hướng dẫn trực tiếp bởi Mentor giàu kinh nghiệm.\n- Được cấp máy tính và đầy đủ thiết bị làm việc.', '28/180 Thái Thịnh, Đống Đa, Hà Nội', '10 - 16 triệu', '2026-07-15', '2026-05-08 08:15:00', 10),
(11, 'Thực tập sinh Content Marketing', '- Lên ý tưởng và viết nội dung cho các kênh truyền thông: Facebook, Website, TikTok, Zalo,...\n- Biên soạn nội dung quảng cáo, bài đăng truyền thông theo định hướng thương hiệu.\n- Phối hợp cùng team Design/Marketing để triển khai chiến dịch.\n- Theo dõi xu hướng mới, cập nhật trend để ứng dụng vào nội dung.\n- Hỗ trợ xây dựng kế hoạch content theo tuần/tháng.\n- Nếu bạn quan tâm đến vị trí này, vui lòng gửi CV trực tiếp qua email: tuyendung@base.vn', '- Sinh viên năm cuối hoặc mới tốt nghiệp các ngành Marketing, Truyền thông, Báo chí,...\n- Có khả năng viết lách, sáng tạo nội dung.\n- Có hiểu biết về Facebook ads, Google ads.\n- Chủ động, có tinh thần học hỏi và trách nhiệm trong công việc.\n- Có laptop cá nhân.', '- Hỗ trợ dấu xác nhận thực tập.\n- Cơ hội trở thành nhân viên chính thức sau 2-3 tháng thực tập.\n- Nhân viên chính thức được review lương 2 lần/năm và hưởng đầy đủ phúc lợi.\n- Môi trường năng động, chuyên nghiệp, thân thiện.', 'Số 17 Đường số 2, KDC CityLand Park Hills, P10, Q. Gò Vấp, TP.HCM', '1 - 2 triệu', '2026-06-30', '2026-05-10 16:32:00', 11),
(12, 'HR Executive', '- Phụ trách thực hiện công tác tuyển dụng: đăng tin, sàng lọc hồ sơ, tổ chức phỏng vấn, tiếp nhận nhân viên mới.\r\n- Quản lý chấm công, tính phúc lợi cho nhân viên hàng tháng.\r\n- Quản lý và xử lý chứng từ, hồ sơ và văn bản.\r\n- Quản lý điện nước, cơ sở vật chất.\r\n- Phụ trách tổ chức các hoạt động nội bộ như team building, sinh nhật, sự kiện nội bộ.\r\n- Nếu bạn quan tâm đến vị trí này, vui lòng gửi CV trực tiếp qua email: nhansu@vnpt.vn', '- Tốt nghiệp cao đẳng/đại học, ưu tiên ngành Quản trị nhân sự, Văn thư và các ngành liên quan.\r\n- Sử dụng thành thạo tin học văn phòng, đặc biệt là Excel.\r\n- Khả năng chịu áp lực tốt, trung thực, cẩn trọng, làm việc tỉ mỉ.\r\n- Có laptop cá nhân.', '- Được tham gia BHYT, BHXH, BHTN theo luật hiện hành.\r\n- Chế độ nghỉ phép 12 ngày/năm.\r\n- Lương tháng 13, được xem xét review lương hàng năm.\r\n- Được tham gia Team Building, Year End Party.\r\n- Làm việc trong môi trường trẻ trung năng động.', 'Số 74 Cửa Bắc, Phường Ba Đình, Hà Nội', '7 - 9 triệu', '2026-07-10', '2026-05-04 09:23:00', 12);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `congviec_kynang`
--

CREATE TABLE `congviec_kynang` (
  `MaCongViec` int(11) NOT NULL,
  `MaKyNang` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `congviec_kynang`
--

INSERT INTO `congviec_kynang` (`MaCongViec`, `MaKyNang`) VALUES
(1, 1),
(2, 5),
(3, 3),
(12, 3),
(12, 4);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `hinhanh`
--

CREATE TABLE `hinhanh` (
  `MaHinhAnh` int(11) NOT NULL,
  `DuongDanURL` varchar(255) NOT NULL,
  `MaBaiViet` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `hinhanh`
--

INSERT INTO `hinhanh` (`MaHinhAnh`, `DuongDanURL`, `MaBaiViet`) VALUES
(2, 'https://example.com/img/fpt_seminar.jpg', 2),
(3, '/helios/public/uploads/posts/post_1_6a1668e3c3bb2.webp', 1),
(6, '/helios/public/uploads/posts/post_3_6a19db63d0a56.jpg', 3),
(7, '/helios/public/uploads/1780119725_6a1a78ad19d90_Giao diện đăng ký bằng email.png', 14),
(9, '/helios/public/uploads/1780119725_6a1a78ad1a92e_Giao diện đăng nhập.png', 14);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `hocvan`
--

CREATE TABLE `hocvan` (
  `MaHocVan` int(11) NOT NULL,
  `TruongHoc` varchar(255) NOT NULL,
  `ChuyenNganh` varchar(255) NOT NULL,
  `ThoiGianTu` date NOT NULL,
  `ThoiGianDen` date DEFAULT NULL,
  `MaNguoiDung` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `hocvan`
--

INSERT INTO `hocvan` (`MaHocVan`, `TruongHoc`, `ChuyenNganh`, `ThoiGianTu`, `ThoiGianDen`, `MaNguoiDung`) VALUES
(1, 'Đại học Bách Khoa HN', 'Khoa học máy tính', '2013-09-01', '2017-06-30', 1),
(2, 'Đại học Ngoại Thương', 'Quản trị kinh doanh', '2016-09-01', '2020-06-30', 2),
(3, 'Đại học KHTN', 'Công nghệ thông tin', '2018-09-01', NULL, 4),
(4, 'Trường Công nghệ và Thiết kế - Đại học UEH', 'Hệ thống thông tin kinh doanh', '2023-09-18', '2026-12-31', 11);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `ketnoi`
--

CREATE TABLE `ketnoi` (
  `MaKetNoi` int(11) NOT NULL,
  `MaNguoiGui` int(11) NOT NULL,
  `MaNguoiNhan` int(11) NOT NULL,
  `TrangThai` enum('pending','accepted','rejected') DEFAULT 'pending',
  `NgayTao` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `ketnoi`
--

INSERT INTO `ketnoi` (`MaKetNoi`, `MaNguoiGui`, `MaNguoiNhan`, `TrangThai`, `NgayTao`) VALUES
(1, 1, 2, 'accepted', '2026-05-27 09:18:45'),
(2, 1, 3, 'pending', '2026-05-27 09:18:45'),
(3, 4, 1, 'rejected', '2026-05-27 09:18:45'),
(4, 9, 4, 'pending', '2026-05-27 17:32:26'),
(5, 9, 7, 'pending', '2026-05-27 17:32:29'),
(6, 9, 5, 'pending', '2026-05-27 21:44:57'),
(8, 9, 1, 'pending', '2026-05-28 18:00:45'),
(10, 9, 2, 'pending', '2026-05-28 18:23:30'),
(23, 11, 13, 'accepted', '2026-05-29 20:40:00'),
(26, 11, 10, 'accepted', '2026-05-30 01:43:10'),
(27, 9, 11, 'pending', '2026-05-30 01:45:03'),
(28, 12, 11, 'pending', '2026-05-30 01:47:23');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `kinhnghiem`
--

CREATE TABLE `kinhnghiem` (
  `MaKinhNghiem` int(11) NOT NULL,
  `CongTy` varchar(255) NOT NULL,
  `ViTri` varchar(255) NOT NULL,
  `MoTa` text DEFAULT NULL,
  `ThoiGianTu` date NOT NULL,
  `ThoiGianDen` date DEFAULT NULL,
  `MaNguoiDung` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `kinhnghiem`
--

INSERT INTO `kinhnghiem` (`MaKinhNghiem`, `CongTy`, `ViTri`, `MoTa`, `ThoiGianTu`, `ThoiGianDen`, `MaNguoiDung`) VALUES
(1, 'FPT Software', 'Junior Backend Developer', 'Làm việc với Spring Boot', '2018-01-01', '2021-12-31', 1),
(2, 'VNG', 'Senior Backend Developer', 'Xây dựng hệ thống Microservices', '2022-01-01', NULL, 1),
(3, 'Shopee', 'Chuyên viên SEO', 'Tối ưu hóa công cụ tìm kiếm', '2020-03-01', NULL, 2),
(4, 'Survival Tech Solutions ', 'Business Analyst Intern', '', '2025-10-25', NULL, 11);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `kynang`
--

CREATE TABLE `kynang` (
  `MaKyNang` int(11) NOT NULL,
  `TenKyNang` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `kynang`
--

INSERT INTO `kynang` (`MaKyNang`, `TenKyNang`) VALUES
(3, 'Digital Marketing'),
(6, 'Figma & Design Thinking'),
(1, 'Lập trình Java'),
(5, 'Lập trình ReactJS'),
(2, 'Phân tích dữ liệu (Python)'),
(4, 'Tuyển dụng (TA)');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nguoidung`
--

CREATE TABLE `nguoidung` (
  `MaNguoiDung` int(11) NOT NULL,
  `HoTen` varchar(100) NOT NULL,
  `TieuDe` varchar(255) DEFAULT NULL,
  `DiaDiem` varchar(255) DEFAULT NULL,
  `Bio` text DEFAULT NULL,
  `AnhDaiDien` varchar(255) DEFAULT NULL,
  `AnhBia` varchar(255) DEFAULT NULL,
  `LanHoatDongCuoi` datetime DEFAULT current_timestamp(),
  `XacMinh` tinyint(1) DEFAULT 0,
  `NhanThongBao` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `nguoidung`
--

INSERT INTO `nguoidung` (`MaNguoiDung`, `HoTen`, `TieuDe`, `DiaDiem`, `Bio`, `AnhDaiDien`, `AnhBia`, `LanHoatDongCuoi`, `XacMinh`, `NhanThongBao`) VALUES
(1, 'Vy Trương', 'Sinh viên Hệ thống thông tin kinh doanh - UEH', 'Thành phố Hồ Chí Minh, Việt Nam', 'Software Engineer yêu thích lập trình Backend', NULL, NULL, '2026-05-27 09:18:44', 0, 1),
(2, 'Trần Thị Bích', 'Chuyên viên Marketing & Quản trị thương hiệu', 'Hà Nội, Việt Nam', 'Chuyên viên Marketing & Quản trị thương hiệu', NULL, NULL, '2026-05-27 09:18:44', 0, 1),
(3, 'Lê Hoàng Phong', 'Giám đốc nhân sự, 10 năm kinh nghiệm', 'Đà Nẵng, Việt Nam', 'Giám đốc nhân sự, 10 năm kinh nghiệm', NULL, NULL, '2026-05-27 09:18:44', 0, 1),
(4, 'Phạm Quỳnh Như', 'Sinh viên IT năm cuối, tìm cơ hội thực tập', 'Thành phố Hồ Chí Minh, Việt Nam', 'Sinh viên IT năm cuối, tìm cơ hội thực tập', NULL, NULL, '2026-05-27 09:18:44', 0, 1),
(5, 'Vũ Đức Đam', 'Data Scientist, đam mê AI/ML', 'Hà Nội, Việt Nam', 'Data Scientist, đam mê AI/ML', NULL, NULL, '2026-05-27 09:18:44', 0, 1),
(6, 'Hoàng Lan Phương', 'UI/UX Designer, thích cái đẹp', 'Thành phố Hồ Chí Minh, Việt Nam', 'UI/UX Designer, thích cái đẹp', '', NULL, '2026-05-27 09:18:44', 0, 1),
(7, 'Nguyễn Văn Nam', 'Freelancer Web', 'Thành phố Hồ Chí Minh, Việt Nam', 'Freelancer Web độc lập, nhận dự án nhỏ', NULL, NULL, '2026-05-27 09:18:44', 0, 1),
(8, 'Hoàng An Nguyên', 'Admin trang Helios Network', '', NULL, '/uploads/profiles/6a1793928cdc2_1779930002.jpg', NULL, '2026-05-27 09:53:03', 0, 1),
(9, 'Phương Vy Misu', 'Sinh viên năm 3 ngành BIS-UEH', 'Hồ Chí Minh', NULL, '/uploads/profiles/6a1789a0015a6_1779927456.jpg', NULL, '2026-05-27 11:16:38', 0, 1),
(10, 'Nhật Phương Uyên', NULL, NULL, NULL, NULL, NULL, '2026-05-28 18:55:11', 0, 1),
(11, 'Nguyễn Thanh Thảo', 'Sinh viên Hệ thống thông tin kinh doanh - UEH', 'Thành phố Hồ Chí Minh, Việt Nam', 'Mình là Thanh Thảo, sinh viên ngành Hệ thống thông tin kinh doanh tại UEH và hiện là Thực tập sinh Business Analyst tại Survival Tech Solutions.\r\nVới thế mạnh kết hợp giữa tư duy kinh doanh và kiến thức hệ thống, mình đam mê việc khơi thông dòng chảy dữ liệu, làm cầu nối ngôn ngữ giữa khối Kinh doanh (Business) và khối Kỹ thuật (Technical). Mình luôn hướng đến việc tối ưu hóa quy trình doanh nghiệp và tìm kiếm ý nghĩa thực sự thông qua các giải pháp công nghệ có tính ứng dụng cao.', '/uploads/profiles/6a18628a4f9bd_1779982986.jpg', NULL, '2026-05-28 21:55:54', 0, 1),
(12, 'Hao2', NULL, NULL, NULL, NULL, NULL, '2026-05-28 22:48:18', 0, 1),
(13, 'Hải Yến Nguyễn', NULL, NULL, NULL, '/uploads/profiles/6a19ea1ca3e7c_1780083228.jpg', NULL, '2026-05-29 17:49:58', 0, 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nguoidung_kynang`
--

CREATE TABLE `nguoidung_kynang` (
  `MaNguoiDung` int(11) NOT NULL,
  `MaKyNang` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `nguoidung_kynang`
--

INSERT INTO `nguoidung_kynang` (`MaNguoiDung`, `MaKyNang`) VALUES
(1, 1),
(2, 3),
(3, 4),
(4, 5),
(5, 2),
(11, 1),
(11, 2);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `taikhoan`
--

CREATE TABLE `taikhoan` (
  `MaTaiKhoan` int(11) NOT NULL,
  `MaNguoiDung` int(11) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `MatKhau` varchar(255) NOT NULL,
  `VaiTro` enum('User','Admin') NOT NULL DEFAULT 'User',
  `TrangThai` varchar(50) DEFAULT 'active',
  `NgayTao` datetime DEFAULT current_timestamp(),
  `VerificationToken` varchar(255) DEFAULT NULL,
  `TokenExpiresAt` datetime DEFAULT NULL,
  `PasswordResetToken` varchar(255) DEFAULT NULL,
  `ResetTokenExpiresAt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `taikhoan`
--

INSERT INTO `taikhoan` (`MaTaiKhoan`, `MaNguoiDung`, `Email`, `MatKhau`, `VaiTro`, `TrangThai`, `NgayTao`, `VerificationToken`, `TokenExpiresAt`, `PasswordResetToken`, `ResetTokenExpiresAt`) VALUES
(1, 1, 'vytruong.31231025039@st.ueh.edu.vn', '$2y$10$BMF8eljRGtLvv5jk0cE31.9LOFNwkSOOmNV/3Rj/24k47XTLBb1Ca', 'User', 'active', '2026-05-23 09:18:45', NULL, NULL, NULL, NULL),
(2, 2, 'bich.tran@example.com', 'pass123', 'User', 'active', '2026-05-24 10:18:45', NULL, NULL, NULL, NULL),
(3, 3, 'phong.le@example.com', 'pass123', 'User', 'active', '2026-05-24 21:20:45', NULL, NULL, NULL, NULL),
(4, 4, 'nhu.pham@example.com', 'pass123', 'User', 'active', '2026-05-27 09:18:45', NULL, NULL, NULL, NULL),
(5, 5, 'dam.vu@example.com', 'pass123', 'User', 'active', '2026-05-27 09:18:45', NULL, NULL, NULL, NULL),
(6, 6, 'phuong.hoang@example.com', 'pass123', 'User', 'active', '2026-05-27 09:18:45', NULL, NULL, NULL, NULL),
(7, 7, 'nam.nguyen@example.com', 'pass123', 'User', 'active', '2026-05-27 09:18:45', NULL, NULL, NULL, NULL),
(8, 8, 'hnannguyen0408@gmail.com', '$2y$10$tZ2LOFtTo6z7y2txkC5/BOAfl4nUvM8VPU0eKinOrOR0BeRqguh4G', 'Admin', 'active', '2026-05-27 09:53:03', NULL, NULL, NULL, NULL),
(9, 9, 'tnphuongvy2005@gmail.com', '$2y$10$8mZRyubAAlj5Jkc.CdAmc.viOg8s1yhAQP2UzD0eJzuliJYOuccoa', 'User', 'active', '2026-05-27 11:16:38', NULL, NULL, '3efbe51bd5145b1ab72cbb7b7f0e9dc5edcaa343e4021e2b868d964fec07416d', '2026-05-28 05:27:54'),
(10, 10, 'tnphuonguyen2013@gmail.com', '$2y$10$5KXq8MKcmvEtWXaGyDmq7uWYxADRYZGGV30qwjeIIEf5VWzBj1K9.', 'User', 'active', '2026-05-28 18:55:11', NULL, NULL, NULL, NULL),
(11, 11, 'nguyenthanhthao0728@gmail.com', '$2y$10$MS9IXVfBn1zmtr9V6tO4ve0lMyZVFA6/yrZuy.JX4KiHA4pxM0yye', 'User', 'active', '2026-05-28 21:55:54', NULL, NULL, NULL, NULL),
(12, 12, 'tranhung.ksmy12@gmail.com', '$2y$10$UMYWpvFyHRNVknHcscmoiu8H0dD8kYwUyh9NYsv9S.367kiUSAHeu', 'User', 'active', '2026-05-28 22:48:18', '3edb2a822b75a4daf57b4289421afc1219655b69e6c2dcc9e8269ac4727944cd', '2026-05-28 18:48:18', NULL, NULL),
(13, 13, 'jangvian4@gmail.com', '$2y$10$83jTvHtGxpD8xP.kAtG2deDu10Lp76RUqqYiKMtjHxoxyg.2w63fq', 'User', 'locked', '2026-05-29 17:49:58', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `thongbao`
--

CREATE TABLE `thongbao` (
  `MaThongBao` int(11) NOT NULL,
  `NoiDung` text NOT NULL,
  `LoaiThongBao` varchar(50) DEFAULT NULL,
  `TrangThaiDoc` tinyint(1) DEFAULT 0,
  `LienKet` varchar(255) DEFAULT NULL,
  `ThoiGianTao` datetime DEFAULT current_timestamp(),
  `MaNguoiDung` int(11) NOT NULL,
  `NguoiKhoiTao` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `thongbao`
--

INSERT INTO `thongbao` (`MaThongBao`, `NoiDung`, `LoaiThongBao`, `TrangThaiDoc`, `LienKet`, `ThoiGianTao`, `MaNguoiDung`, `NguoiKhoiTao`) VALUES
(1, 'Nguyễn Văn Nam đã bày tỏ cảm xúc về bài viết của bạn.', 'TuongTac', 0, '/post/3', '2026-05-27 09:18:45', 1, NULL),
(2, 'Công ty TechCorp đã gửi cho bạn một lời mời phỏng vấn.', 'Tuyendung', 0, '/interview/12', '2026-05-27 09:18:45', 2, NULL),
(3, 'Trần Minh Quang đã nhắc đến bạn trong một bình luận.', 'BinhLuan', 0, '/post/5#comment-88', '2026-05-27 09:18:45', 3, NULL),
(4, 'Trần Thị Bích đã gửi lời mời kết nối.', 'KetNoi', 0, '/helios/public/network', '2026-05-28 15:46:09', 1, 2),
(5, 'Lê Hoàng Phong đã tương tác với bài viết của bạn.', 'TuongTac', 0, '/helios/public/home', '2026-05-28 15:46:09', 1, 2),
(6, 'Bạn có một bình luận mới trên bài viết.', 'BinhLuan', 0, '/helios/public/home', '2026-05-28 15:46:09', 1, 2),
(11, 'Hoàng An Nguyên đã gửi lời mời kết nối cho bạn.', 'KetNoi', 0, '/helios/public/network', '2026-05-28 18:20:59', 6, 8),
(12, 'Phương Vy Misu đã gửi lời mời kết nối cho bạn.', 'KetNoi', 0, '/helios/public/network', '2026-05-28 18:23:30', 2, 9),
(13, '[TEST] Trương Nhật Phương Vy đã tương tác với bài viết của bạn.', 'TuongTac', 0, '/helios/public/home#post-1', '2026-05-28 18:19:10', 9, 1),
(14, '[TEST] Trần Thị Bích đã bình luận bài viết của bạn.', 'BinhLuan', 0, '/helios/public/home#post-1', '2026-05-28 18:20:10', 9, 2),
(15, '[TEST] Lê Hoàng Phong đã gửi lời mời kết nối cho bạn.', 'KetNoi', 0, '/helios/public/network', '2026-05-28 18:21:10', 9, 3),
(16, '[TEST] Phạm Quỳnh Như đã xem hồ sơ của bạn.', 'HoSo', 0, '/helios/public/about-me?id=4', '2026-05-28 18:22:10', 9, 4),
(17, '[TEST] Võ Đức Đam vừa đăng một bài viết mới.', 'BaiViet', 0, '/helios/public/home#post-3', '2026-05-28 18:23:10', 9, 5),
(18, '[TEST] Hoàng Lan Phương đã trả lời bình luận của bạn trên một bài viết.', 'TraLoi', 0, '/helios/public/home#post-3', '2026-05-28 18:24:10', 9, 6),
(19, '[TEST] Nguyễn Văn Nam đã nhắc đến bạn trong một bình luận.', 'NhacDen', 0, '/helios/public/home#post-3', '2026-05-28 18:25:10', 9, 7),
(20, '[TEST] Admin đã chỉnh sửa bài viết của bạn.', 'HeThong', 0, '/helios/public/home#post-1', '2026-05-28 18:26:10', 9, 8),
(21, 'Hoàng An Nguyên đã gửi lời mời kết nối cho bạn.', 'KetNoi', 0, '/helios/public/network', '2026-05-28 18:52:04', 9, 8),
(22, 'Hoàng An Nguyên đã gửi lời mời kết nối cho bạn.', 'KetNoi', 0, '/helios/public/network', '2026-05-28 18:52:10', 2, 8),
(23, 'Hoàng An Nguyên đã gửi lời mời kết nối cho bạn.', 'KetNoi', 0, '/helios/public/network', '2026-05-29 15:14:32', 12, 8),
(24, 'Hoàng An Nguyên đã gửi lời mời kết nối cho bạn.', 'KetNoi', 0, '/helios/public/network', '2026-05-29 15:14:42', 12, 8),
(25, 'Hoàng An Nguyên đã gửi lời mời kết nối cho bạn.', 'KetNoi', 0, '/helios/public/network', '2026-05-29 15:14:49', 1, 8),
(31, 'Hoàng An Nguyên vừa đăng một bài viết mới.', 'HeThong', 0, '/helios/public/home#post-12', '2026-05-29 17:29:49', 1, 8),
(32, 'Hoàng An Nguyên vừa đăng một bài viết mới.', 'HeThong', 0, '/helios/public/home#post-12', '2026-05-29 17:29:49', 2, 8),
(33, 'Hoàng An Nguyên vừa đăng một bài viết mới.', 'HeThong', 0, '/helios/public/home#post-12', '2026-05-29 17:29:49', 3, 8),
(34, 'Hoàng An Nguyên vừa đăng một bài viết mới.', 'HeThong', 0, '/helios/public/home#post-12', '2026-05-29 17:29:49', 4, 8),
(35, 'Hoàng An Nguyên vừa đăng một bài viết mới.', 'HeThong', 0, '/helios/public/home#post-12', '2026-05-29 17:29:49', 5, 8),
(36, 'Hoàng An Nguyên vừa đăng một bài viết mới.', 'HeThong', 0, '/helios/public/home#post-12', '2026-05-29 17:29:49', 6, 8),
(37, 'Hoàng An Nguyên vừa đăng một bài viết mới.', 'HeThong', 0, '/helios/public/home#post-12', '2026-05-29 17:29:49', 7, 8),
(38, 'Hoàng An Nguyên vừa đăng một bài viết mới.', 'HeThong', 0, '/helios/public/home#post-12', '2026-05-29 17:29:49', 9, 8),
(41, 'Hoàng An Nguyên vừa đăng một bài viết mới.', 'HeThong', 0, '/helios/public/home#post-12', '2026-05-29 17:29:49', 12, 8),
(50, 'Nguyễn Thanh Thảo đã chấp nhận lời mời kết nối của bạn.', 'KetNoi', 0, '/helios/public/network', '2026-05-29 18:45:46', 13, 11),
(57, 'Nguyễn Thanh Thảo đã gửi lời mời kết nối cho bạn.', 'KetNoi', 0, '/helios/public/network', '2026-05-29 20:40:00', 13, 11),
(59, 'Nguyễn Thanh Thảo đã tương tác với bài viết của bạn.', 'TuongTac', 0, '/helios/public/home#post-13', '2026-05-29 20:47:23', 13, 11),
(60, 'Hoàng An Nguyên đã chỉnh sửa một bài viết của bạn.', 'HeThong', 0, '/helios/public/home#post-3', '2026-05-30 01:30:59', 2, 8),
(61, 'Nguyễn Thanh Thảo đã gửi lời mời kết nối cho bạn.', 'KetNoi', 0, '/helios/public/network', '2026-05-30 01:43:02', 9, 11),
(62, 'Nguyễn Thanh Thảo đã gửi lời mời kết nối cho bạn.', 'KetNoi', 0, '/helios/public/network', '2026-05-30 01:43:07', 12, 11),
(63, 'Nguyễn Thanh Thảo đã gửi lời mời kết nối cho bạn.', 'KetNoi', 0, '/helios/public/network', '2026-05-30 01:43:10', 10, 11),
(69, 'Nguyễn Thanh Thảo đã trả lời bình luận của bạn trên một bài viết.', 'BinhLuan', 0, '/helios/public/home#post-14', '2026-05-30 12:43:21', 1, 11),
(70, 'Hoàng An Nguyên đã gửi lời mời kết nối cho bạn.', 'KetNoi', 0, '/helios/public/network', '2026-05-30 17:03:49', 13, 8);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tinnhan`
--

CREATE TABLE `tinnhan` (
  `MaTinNhan` int(11) NOT NULL,
  `NguoiGui` int(11) NOT NULL,
  `NguoiNhan` int(11) NOT NULL,
  `NoiDung` text NOT NULL,
  `DuongDanFile` varchar(500) DEFAULT NULL,
  `TrangThaiDoc` tinyint(1) DEFAULT 0,
  `DaGhim` tinyint(1) DEFAULT 0,
  `ThoiGianGui` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `tinnhan`
--

INSERT INTO `tinnhan` (`MaTinNhan`, `NguoiGui`, `NguoiNhan`, `NoiDung`, `DuongDanFile`, `TrangThaiDoc`, `DaGhim`, `ThoiGianGui`) VALUES
(1, 3, 1, 'Chào Phương Vy, công ty anh đang tuyển vị trí Backend Developer, em có quan tâm không?', NULL, 1, 0, '2026-05-22 09:18:45'),
(2, 1, 3, 'Dạ em quan tâm ạ. Cho em hỏi yêu cầu và mức lương như thế nào?', NULL, 1, 0, '2026-05-23 09:18:45'),
(3, 3, 1, 'Yêu cầu 1 năm kinh nghiệm PHP/Java, lương 18-25tr. Em gửi CV qua email anh nhé', NULL, 1, 0, '2026-05-24 09:18:45'),
(4, 1, 3, 'Dạ vâng ạ. Em sẽ gửi CV trong hôm nay, cảm ơn anh!', NULL, 0, 0, '2026-05-25 09:18:45'),
(5, 1, 4, 'Như ơi, cậu làm xong bài tập môn Web chưa?', NULL, 1, 0, '2026-05-25 09:18:45'),
(6, 4, 1, 'Mình làm xong giao diện rồi, còn phần đăng nhập với database', NULL, 1, 0, '2026-05-25 09:18:45'),
(7, 1, 4, 'Phần đăng nhập mình làm rồi, tối nay share code cho cậu nhé', NULL, 1, 0, '2026-05-26 09:18:45'),
(8, 4, 1, 'Cảm ơn cậu nhiều! Khi nào rảnh mời cậu đi ăn', NULL, 0, 0, '2026-05-26 21:18:45'),
(9, 1, 4, 'Ok cậu, hẹn cuối tuần nhé!', NULL, 0, 0, '2026-05-27 03:18:45'),
(10, 1, 5, 'Anh Đam ơi, em đang làm project Data Analysis, anh có thể hướng dẫn em được không ạ?', NULL, 1, 0, '2026-05-24 09:18:45'),
(11, 5, 1, 'Có, em muốn hỏi gì về Data Analysis?', NULL, 1, 0, '2026-05-24 09:18:45'),
(12, 1, 5, 'Dạ em muốn học cách xử lý dữ liệu lớn với Python ạ', NULL, 1, 0, '2026-05-25 09:18:45'),
(13, 5, 1, 'Ok, cuối tuần này anh rảnh, anh sẽ hướng dẫn em qua team call', NULL, 1, 0, '2026-05-26 09:18:45'),
(14, 1, 5, 'Dạ cảm ơn anh nhiều ạ!', NULL, 0, 0, '2026-05-26 21:18:45'),
(15, 2, 1, 'Phương Vy, bạn có biết ai đang tìm việc mảng Marketing không? Bên mình tuyển gấp', NULL, 1, 0, '2026-05-23 09:18:45'),
(16, 1, 2, 'Để mình hỏi bạn bè xem. Bên bạn yêu cầu gì thế?', NULL, 1, 0, '2026-05-23 09:18:45'),
(17, 2, 1, 'Cần 2 năm kinh nghiệm, ưu tiên biết chạy Facebook Ads', NULL, 1, 0, '2026-05-24 09:18:45'),
(18, 1, 2, 'Mình có bạn học chuyên ngành Marketing, để mình giới thiệu', NULL, 0, 0, '2026-05-25 09:18:45'),
(19, 6, 1, 'Phương Vy ơi, chị thấy em đăng bài về Figma, em có thể dạy chị được không?', NULL, 1, 0, '2026-05-26 09:18:45'),
(20, 1, 6, 'Dạ được chị ơi. Chị muốn học phần nào trước ạ?', NULL, 1, 0, '2026-05-26 10:18:45'),
(21, 6, 1, 'Chị muốn học cách tạo prototype cho web', NULL, 1, 0, '2026-05-26 11:18:45'),
(22, 1, 6, 'Dạ cuối tuần này em rảnh, em sẽ hướng dẫn chị qua Zoom nhé', NULL, 1, 0, '2026-05-26 13:18:45'),
(23, 6, 1, 'Cảm ơn em nhiều, chị sẽ cố gắng học', NULL, 0, 0, '2026-05-26 15:18:45'),
(24, 4, 3, 'Anh Phong ơi, công ty mình còn nhận thực tập sinh không ạ?', NULL, 1, 0, '2026-05-26 09:18:45'),
(25, 3, 4, 'Còn em, bên IT đang cần 2 bạn thực tập Frontend', NULL, 1, 0, '2026-05-26 10:18:45'),
(26, 4, 3, 'Dạ cho em xin thông tin với ạ', NULL, 1, 0, '2026-05-26 11:18:45'),
(27, 3, 4, 'Em gửi CV qua email anh nhé, anh sẽ chuyển cho team kỹ thuật', NULL, 1, 0, '2026-05-26 13:18:45'),
(28, 4, 3, 'Dạ vâng ạ, cảm ơn anh nhiều!', NULL, 0, 0, '2026-05-26 15:18:45'),
(29, 7, 1, 'Chào Phương Vy, mình có job web nhỏ, bạn có nhận không?', NULL, 1, 0, '2026-05-25 09:18:45'),
(30, 1, 7, 'Cảm ơn bạn, job gì vậy ạ?', NULL, 1, 0, '2026-05-25 09:18:45'),
(31, 7, 1, 'Làm landing page cho quán cà phê, thù lao 5tr', NULL, 1, 0, '2026-05-26 09:18:45'),
(32, 1, 7, 'Mình nhận được, khi nào cần bàn giao ạ?', NULL, 1, 0, '2026-05-26 09:18:45'),
(33, 7, 1, '1 tuần nữa bạn nhé, ok không?', NULL, 1, 0, '2026-05-26 21:18:45'),
(34, 1, 7, 'Ok bạn, mình sẽ cố gắng', NULL, 0, 0, '2026-05-27 03:18:45'),
(35, 9, 7, 'Hello bạn', NULL, 0, 0, '2026-05-27 17:29:11'),
(36, 8, 9, 'chào bạn', NULL, 1, 0, '2026-05-28 07:17:55'),
(39, 8, 7, 'bạn học web chưa', NULL, 0, 0, '2026-05-28 07:55:45'),
(42, 9, 8, '', '/uploads/messages/1779958576_6a18033082001.pdf', 1, 0, '2026-05-28 15:56:16'),
(46, 11, 13, 'Bạn ơi làm LAB 10 môn WEB chưa', NULL, 0, 0, '2026-05-30 02:24:27'),
(47, 11, 9, 'Mình gửi 7 câu Spark SQL nha', NULL, 0, 0, '2026-05-30 02:24:50'),
(48, 11, 12, 'Bạn nhớ up file lên github nha', NULL, 0, 0, '2026-05-30 02:25:36'),
(49, 1, 11, 'Hôm nay tui đang học về MVC mà vẫn hơi mơ hồ. MVC là gì vậy?', NULL, 1, 1, '2026-05-30 02:29:44'),
(50, 11, 1, 'MVC là viết tắt của Model, View với Controller đó.', NULL, 1, 0, '2026-05-30 02:30:46'),
(51, 1, 11, 'Nghe nhiều mà vẫn chưa hiểu lắm 😅', NULL, 1, 0, '2026-05-30 02:31:03'),
(52, 11, 1, 'Hiểu đơn giản nha. View là giao diện người dùng nhìn thấy, Model là dữ liệu, còn Controller là thằng đứng giữa xử lý mọi thứ.', NULL, 1, 0, '2026-05-30 02:31:18'),
(53, 1, 11, 'Ví dụ đi.', NULL, 1, 0, '2026-05-30 02:32:07'),
(55, 11, 1, 'Ví dụ bấm nút đăng nhập. Controller nhận thông tin tài khoản mật khẩu từ giao diện, sau đó gọi Model để kiểm tra dữ liệu.', NULL, 1, 0, '2026-05-30 02:44:45'),
(56, 11, 1, 'Model trả kết quả về cho Controller, rồi Controller chuyển sang View để hiện thông báo \"Đăng nhập thành công', NULL, 1, 0, '2026-05-30 02:44:55'),
(57, 11, 1, '', '/uploads/messages/1780085228_6a19f1ecf2d91.pdf', 0, 0, '2026-05-30 03:07:08');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tuongtac`
--

CREATE TABLE `tuongtac` (
  `MaNguoiDung` int(11) NOT NULL,
  `MaBaiViet` int(11) NOT NULL,
  `LoaiTuongTac` varchar(20) NOT NULL,
  `ThoiGian` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `tuongtac`
--

INSERT INTO `tuongtac` (`MaNguoiDung`, `MaBaiViet`, `LoaiTuongTac`, `ThoiGian`) VALUES
(1, 2, 'Hữu ích', '2026-05-27 09:18:45'),
(1, 14, 'Quan tâm', '2026-05-30 02:48:42'),
(4, 1, 'Thích', '2026-05-27 09:18:45'),
(5, 1, 'Quan tâm', '2026-05-27 09:18:45'),
(8, 6, 'Quan tâm', '2026-05-28 18:50:59'),
(9, 3, 'Thích', '2026-05-27 17:43:21');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `baiviet`
--
ALTER TABLE `baiviet`
  ADD PRIMARY KEY (`MaBaiViet`),
  ADD KEY `fk_baiviet_nguoidung` (`MaNguoiDung`);

--
-- Chỉ mục cho bảng `binhluan`
--
ALTER TABLE `binhluan`
  ADD PRIMARY KEY (`MaBinhLuan`),
  ADD KEY `fk_binhluan_baiviet` (`MaBaiViet`),
  ADD KEY `fk_binhluan_nguoidung` (`MaNguoiDung`);

--
-- Chỉ mục cho bảng `congty`
--
ALTER TABLE `congty`
  ADD PRIMARY KEY (`MaCongTy`);

--
-- Chỉ mục cho bảng `congviec`
--
ALTER TABLE `congviec`
  ADD PRIMARY KEY (`MaCongViec`),
  ADD KEY `fk_congviec_congty` (`MaCongTy`);

--
-- Chỉ mục cho bảng `congviec_kynang`
--
ALTER TABLE `congviec_kynang`
  ADD PRIMARY KEY (`MaCongViec`,`MaKyNang`),
  ADD KEY `fk_cvkn_kynang` (`MaKyNang`);

--
-- Chỉ mục cho bảng `hinhanh`
--
ALTER TABLE `hinhanh`
  ADD PRIMARY KEY (`MaHinhAnh`),
  ADD KEY `fk_hinhanh_baiviet` (`MaBaiViet`);

--
-- Chỉ mục cho bảng `hocvan`
--
ALTER TABLE `hocvan`
  ADD PRIMARY KEY (`MaHocVan`),
  ADD KEY `fk_hocvan_nguoidung` (`MaNguoiDung`);

--
-- Chỉ mục cho bảng `ketnoi`
--
ALTER TABLE `ketnoi`
  ADD PRIMARY KEY (`MaKetNoi`),
  ADD UNIQUE KEY `uq_ketnoi_cap` (`MaNguoiGui`,`MaNguoiNhan`),
  ADD KEY `fk_ketnoi_nguoinhan` (`MaNguoiNhan`);

--
-- Chỉ mục cho bảng `kinhnghiem`
--
ALTER TABLE `kinhnghiem`
  ADD PRIMARY KEY (`MaKinhNghiem`),
  ADD KEY `fk_kinhnghiem_nguoidung` (`MaNguoiDung`);

--
-- Chỉ mục cho bảng `kynang`
--
ALTER TABLE `kynang`
  ADD PRIMARY KEY (`MaKyNang`),
  ADD UNIQUE KEY `TenKyNang` (`TenKyNang`);

--
-- Chỉ mục cho bảng `nguoidung`
--
ALTER TABLE `nguoidung`
  ADD PRIMARY KEY (`MaNguoiDung`);

--
-- Chỉ mục cho bảng `nguoidung_kynang`
--
ALTER TABLE `nguoidung_kynang`
  ADD PRIMARY KEY (`MaNguoiDung`,`MaKyNang`),
  ADD KEY `fk_ndkn_kynang` (`MaKyNang`);

--
-- Chỉ mục cho bảng `taikhoan`
--
ALTER TABLE `taikhoan`
  ADD PRIMARY KEY (`MaTaiKhoan`),
  ADD UNIQUE KEY `MaNguoiDung` (`MaNguoiDung`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Chỉ mục cho bảng `thongbao`
--
ALTER TABLE `thongbao`
  ADD PRIMARY KEY (`MaThongBao`),
  ADD KEY `fk_thongbao_nguoidung` (`MaNguoiDung`),
  ADD KEY `NguoiKhoiTao` (`NguoiKhoiTao`);

--
-- Chỉ mục cho bảng `tinnhan`
--
ALTER TABLE `tinnhan`
  ADD PRIMARY KEY (`MaTinNhan`),
  ADD KEY `fk_tinnhan_nguoigui` (`NguoiGui`),
  ADD KEY `fk_tinnhan_nguoinhan` (`NguoiNhan`);

--
-- Chỉ mục cho bảng `tuongtac`
--
ALTER TABLE `tuongtac`
  ADD PRIMARY KEY (`MaNguoiDung`,`MaBaiViet`),
  ADD KEY `fk_tuongtac_baiviet` (`MaBaiViet`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `baiviet`
--
ALTER TABLE `baiviet`
  MODIFY `MaBaiViet` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT cho bảng `binhluan`
--
ALTER TABLE `binhluan`
  MODIFY `MaBinhLuan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `congty`
--
ALTER TABLE `congty`
  MODIFY `MaCongTy` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT cho bảng `congviec`
--
ALTER TABLE `congviec`
  MODIFY `MaCongViec` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT cho bảng `hinhanh`
--
ALTER TABLE `hinhanh`
  MODIFY `MaHinhAnh` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `hocvan`
--
ALTER TABLE `hocvan`
  MODIFY `MaHocVan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `ketnoi`
--
ALTER TABLE `ketnoi`
  MODIFY `MaKetNoi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT cho bảng `kinhnghiem`
--
ALTER TABLE `kinhnghiem`
  MODIFY `MaKinhNghiem` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `kynang`
--
ALTER TABLE `kynang`
  MODIFY `MaKyNang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `nguoidung`
--
ALTER TABLE `nguoidung`
  MODIFY `MaNguoiDung` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT cho bảng `taikhoan`
--
ALTER TABLE `taikhoan`
  MODIFY `MaTaiKhoan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT cho bảng `thongbao`
--
ALTER TABLE `thongbao`
  MODIFY `MaThongBao` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT cho bảng `tinnhan`
--
ALTER TABLE `tinnhan`
  MODIFY `MaTinNhan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `baiviet`
--
ALTER TABLE `baiviet`
  ADD CONSTRAINT `fk_baiviet_nguoidung` FOREIGN KEY (`MaNguoiDung`) REFERENCES `nguoidung` (`MaNguoiDung`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `binhluan`
--
ALTER TABLE `binhluan`
  ADD CONSTRAINT `fk_binhluan_baiviet` FOREIGN KEY (`MaBaiViet`) REFERENCES `baiviet` (`MaBaiViet`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_binhluan_nguoidung` FOREIGN KEY (`MaNguoiDung`) REFERENCES `nguoidung` (`MaNguoiDung`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `congviec`
--
ALTER TABLE `congviec`
  ADD CONSTRAINT `fk_congviec_congty` FOREIGN KEY (`MaCongTy`) REFERENCES `congty` (`MaCongTy`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `congviec_kynang`
--
ALTER TABLE `congviec_kynang`
  ADD CONSTRAINT `fk_cvkn_congviec` FOREIGN KEY (`MaCongViec`) REFERENCES `congviec` (`MaCongViec`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cvkn_kynang` FOREIGN KEY (`MaKyNang`) REFERENCES `kynang` (`MaKyNang`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `hinhanh`
--
ALTER TABLE `hinhanh`
  ADD CONSTRAINT `fk_hinhanh_baiviet` FOREIGN KEY (`MaBaiViet`) REFERENCES `baiviet` (`MaBaiViet`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `hocvan`
--
ALTER TABLE `hocvan`
  ADD CONSTRAINT `fk_hocvan_nguoidung` FOREIGN KEY (`MaNguoiDung`) REFERENCES `nguoidung` (`MaNguoiDung`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `ketnoi`
--
ALTER TABLE `ketnoi`
  ADD CONSTRAINT `fk_ketnoi_nguoigui` FOREIGN KEY (`MaNguoiGui`) REFERENCES `nguoidung` (`MaNguoiDung`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ketnoi_nguoinhan` FOREIGN KEY (`MaNguoiNhan`) REFERENCES `nguoidung` (`MaNguoiDung`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `kinhnghiem`
--
ALTER TABLE `kinhnghiem`
  ADD CONSTRAINT `fk_kinhnghiem_nguoidung` FOREIGN KEY (`MaNguoiDung`) REFERENCES `nguoidung` (`MaNguoiDung`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `nguoidung_kynang`
--
ALTER TABLE `nguoidung_kynang`
  ADD CONSTRAINT `fk_ndkn_kynang` FOREIGN KEY (`MaKyNang`) REFERENCES `kynang` (`MaKyNang`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ndkn_nguoidung` FOREIGN KEY (`MaNguoiDung`) REFERENCES `nguoidung` (`MaNguoiDung`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `taikhoan`
--
ALTER TABLE `taikhoan`
  ADD CONSTRAINT `fk_taikhoan_nguoidung` FOREIGN KEY (`MaNguoiDung`) REFERENCES `nguoidung` (`MaNguoiDung`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `thongbao`
--
ALTER TABLE `thongbao`
  ADD CONSTRAINT `fk_thongbao_nguoidung` FOREIGN KEY (`MaNguoiDung`) REFERENCES `nguoidung` (`MaNguoiDung`) ON DELETE CASCADE,
  ADD CONSTRAINT `thongbao_ibfk_1` FOREIGN KEY (`NguoiKhoiTao`) REFERENCES `nguoidung` (`MaNguoiDung`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `tinnhan`
--
ALTER TABLE `tinnhan`
  ADD CONSTRAINT `fk_tinnhan_nguoigui` FOREIGN KEY (`NguoiGui`) REFERENCES `nguoidung` (`MaNguoiDung`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_tinnhan_nguoinhan` FOREIGN KEY (`NguoiNhan`) REFERENCES `nguoidung` (`MaNguoiDung`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `tuongtac`
--
ALTER TABLE `tuongtac`
  ADD CONSTRAINT `fk_tuongtac_baiviet` FOREIGN KEY (`MaBaiViet`) REFERENCES `baiviet` (`MaBaiViet`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_tuongtac_nguoidung` FOREIGN KEY (`MaNguoiDung`) REFERENCES `nguoidung` (`MaNguoiDung`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
