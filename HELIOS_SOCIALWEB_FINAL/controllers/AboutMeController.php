<?php
// app/controllers/AboutMeController.php

class AboutMeController {
    
    // 1. Hàm hiển thị trang web
    public function index() {
            $pageTitle = "Tôi | Helios";
            $activeNav = 'profile';
            $cssFiles = ['about-me.css']; 
            
            $userModel = new UserModel();
            // Lấy thông tin user
            $userData = $userModel->getUser(1); 
            // LẤY DANH SÁCH KINH NGHIỆM ĐỂ TRUYỀN RA VIEW
            $kinhNghiemList = $userModel->getKinhNghiemList(1); 
            $hocVanList = $userModel->getHocVanList(1);
            $userSkills = $userModel->getUserSkills(1);
            $availableSkills = $userModel->getAvailableSkills(1);

            $contentView = VIEW_PATH . '/about-me.php';
            include VIEW_PATH . '/layouts/main.php';
    }

    // 2. Hàm xử lý khi Form chỉnh sửa được submit
    public function update() {
        // Kiểm tra xem phương thức gửi lên có phải là POST không
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            
            // Nhận dữ liệu từ các thẻ <input name="..."> trong Form
            $hoTen = $_POST['hoten'];
            $tieuDe = $_POST['tieude'];
            $diaDiem = $_POST['diadiem'];
            
            // Gọi Model để lưu vào Database (Update cho user ID = 1)
            $userModel = new UserModel();
            $userModel->updateUser(1, $hoTen, $tieuDe, $diaDiem);

            // Lưu thành công -> Chuyển hướng người dùng quay lại trang about-me
            header("Location: /helios/public/about-me");
            exit();
        }
    }
    // Hàm xử lý khi Form Giới thiệu được submit
    public function updateBio() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            
            $bio = $_POST['bio']; // Lấy nội dung từ ô textarea có name="bio"
            
            // Gọi Model để lưu vào Database (User ID = 1)
            $userModel = new UserModel();
            $userModel->updateBio(1, $bio);

            // Chuyển hướng về lại trang
            header("Location: /helios/public/about-me");
            exit();
        }
    }

    public function addExperience() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $congTy = $_POST['congty'];
            $viTri = $_POST['vitri'];
            $tuNgay = $_POST['tungay'];
            // Nếu người dùng không nhập ngày kết thúc, gán là NULL
            $denNgay = !empty($_POST['denngay']) ? $_POST['denngay'] : NULL; 
            $moTa = $_POST['mota'];

            $userModel = new UserModel();
            $userModel->addKinhNghiem(1, $congTy, $viTri, $moTa, $tuNgay, $denNgay);

            header("Location: /helios/public/about-me"); exit();
        }
    }

    // HÀM XỬ LÝ SỬA KINH NGHIỆM
    public function editExperience() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $maKN = $_POST['makinhnghiem']; // Bắt buộc phải có ID của dòng cần sửa
            $congTy = $_POST['congty'];
            $viTri = $_POST['vitri'];
            $tuNgay = $_POST['tungay'];
            $denNgay = !empty($_POST['denngay']) ? $_POST['denngay'] : NULL;
            $moTa = $_POST['mota'];

            $userModel = new UserModel();
            $userModel->updateKinhNghiem($maKN, 1, $congTy, $viTri, $moTa, $tuNgay, $denNgay);

            header("Location: /helios/public/about-me"); exit();
        }
    }
     public function addEducation() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $truongHoc = $_POST['truonghoc'];
            $chuyenNganh = $_POST['chuyennganh'];
            $tuNgay = $_POST['tungay'];
            $denNgay = !empty($_POST['denngay']) ? $_POST['denngay'] : NULL;

            $userModel = new UserModel();
            $userModel->addHocVan(1, $truongHoc, $chuyenNganh, $tuNgay, $denNgay);

            header("Location: /helios/public/about-me"); exit();
        }
    }

    // HÀM XỬ LÝ SỬA HỌC VẤN
    public function editEducation() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $maHV = $_POST['mahocvan']; 
            $truongHoc = $_POST['truonghoc'];
            $chuyenNganh = $_POST['chuyennganh'];
            $tuNgay = $_POST['tungay'];
            $denNgay = !empty($_POST['denngay']) ? $_POST['denngay'] : NULL;

            $userModel = new UserModel();
            $userModel->updateHocVan($maHV, 1, $truongHoc, $chuyenNganh, $tuNgay, $denNgay);

            header("Location: /helios/public/about-me"); exit();
        }
    }
    public function addSkill() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['makynang'])) {
            $maKyNang = $_POST['makynang'];
            $userModel = new UserModel();
            $userModel->addUserSkill(1, $maKyNang);
        }
        header("Location: /helios/public/about-me"); exit();
    }

    // HÀM XỬ LÝ XÓA KỸ NĂNG
    public function deleteSkill() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['makynang'])) {
            $maKyNang = $_POST['makynang'];
            $userModel = new UserModel();
            $userModel->removeUserSkill(1, $maKyNang);
        }
        header("Location: /helios/public/about-me"); exit();
    }
}
?>