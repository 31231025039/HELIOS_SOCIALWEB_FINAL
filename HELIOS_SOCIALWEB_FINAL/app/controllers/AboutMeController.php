<?php
// app/controllers/AboutMeController.php

class AboutMeController {
    
    // 1. Hàm hiển thị trang web (Đã sửa để dùng VIEW_PATH_APP)
    public function index() {
        $pageTitle = "Tôi | Helios";
        $activeNav = 'profile';
        $cssFiles = ['about-me.css']; 
        $jsFiles = ['about-me.js'];
        
        $userModel = new UserModel();
        $userData = $userModel->getUser(1); 
        $kinhNghiemList = $userModel->getKinhNghiemList(1); 
        $hocVanList = $userModel->getHocVanList(1);
        $userSkills = $userModel->getUserSkills(1);
        $availableSkills = $userModel->getAvailableSkills(1);

        $contentView = VIEW_PATH_APP . '/about-me.php';
        include VIEW_PATH_APP . '/layouts/main.php';
    }

    /**
     * Hàm helper để trả về JSON response và dừng script.
     */
    private function jsonResponse($success, $message = '', $data = []) {
        header('Content-Type: application/json');
        echo json_encode(['success' => $success, 'message' => $message] + $data);
        exit();
    }

    // 2. Cập nhật thông tin cá nhân (đã chuyển sang JSON)
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $userModel = new UserModel();
            $success = $userModel->updateUser(1, $_POST['hoten'], $_POST['tieude'], $_POST['diadiem']);
            $this->jsonResponse($success, $success ? '' : 'Lỗi cập nhật CSDL.');
        }
    }

    // 3. Cập nhật Bio (Giới thiệu)
    public function updateBio() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $bio = $_POST['bio'] ?? '';
            $userModel = new UserModel();
            $success = $userModel->updateBio(1, $bio);
            $this->jsonResponse($success, '', ['newBioHtml' => nl2br(htmlspecialchars($bio))]);
        }
    }

    // 4. Thêm kinh nghiệm (đã sửa lỗi thiếu tham số)
    public function addExperience() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $denNgay = !empty($_POST['denngay']) ? $_POST['denngay'] : NULL;
            $userModel = new UserModel();
            $success = $userModel->addKinhNghiem(1, $_POST['congty'], $_POST['vitri'], $_POST['mota'], $_POST['tungay'], $denNgay);
            $this->jsonResponse($success);
        }
    }

    // 5. Sửa kinh nghiệm
    public function editExperience() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $denNgay = !empty($_POST['denngay']) ? $_POST['denngay'] : NULL;
            $userModel = new UserModel();
            $success = $userModel->updateKinhNghiem($_POST['makinhnghiem'], 1, $_POST['congty'], $_POST['vitri'], $_POST['mota'], $_POST['tungay'], $denNgay);
            $this->jsonResponse($success);
        }
    }

    // 6. Thêm học vấn (đã chuyển sang JSON)
    public function addEducation() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $denNgay = !empty($_POST['denngay']) ? $_POST['denngay'] : NULL;
            $userModel = new UserModel();
            $success = $userModel->addHocVan(1, $_POST['truonghoc'], $_POST['chuyennganh'], $_POST['tungay'], $denNgay);
            $this->jsonResponse($success);
        }
    }

    // 7. Sửa học vấn (đã chuyển sang JSON)
    public function editEducation() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $denNgay = !empty($_POST['denngay']) ? $_POST['denngay'] : NULL;
            $userModel = new UserModel();
            $success = $userModel->updateHocVan($_POST['mahocvan'], 1, $_POST['truonghoc'], $_POST['chuyennganh'], $_POST['tungay'], $denNgay);
            $this->jsonResponse($success);
        }
    }

    // 8. Thêm kỹ năng (đã chuyển sang JSON)
    public function addSkill() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['makynang'])) {
            $userModel = new UserModel();
            $success = $userModel->addUserSkill(1, $_POST['makynang']);
            $this->jsonResponse($success);
        }
        $this->jsonResponse(false, 'Chưa chọn kỹ năng.');
    }

    // 9. Xóa kỹ năng (đã chuyển sang JSON)
    public function deleteSkill() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['makynang'])) {
            $userModel = new UserModel();
            $success = $userModel->removeUserSkill(1, $_POST['makynang']);
            $this->jsonResponse($success);
        }
        $this->jsonResponse(false, 'Không tìm thấy kỹ năng để xóa.');
    }

    // 10. Cập nhật ảnh đại diện/ảnh bìa (giữ nguyên)
    public function updateImage() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // ... (code upload ảnh của bạn đã tốt, giữ nguyên) ...
            // Chỉ cần đảm bảo nó cũng dùng $this->jsonResponse() ở cuối
            $imageType = $_POST['image_type'] ?? '';
            if (!in_array($imageType, ['avatar', 'cover'])) {
                $this->jsonResponse(false, 'Loại ảnh không hợp lệ.');
            }
            if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] == 0) {
                $userModel = new UserModel();
                $result = $userModel->uploadFile($_FILES['image_file']); 
                if ($result['success']) {
                    $filePath = $result['filePath'];
                    $updateSuccess = $userModel->updateUserImage(1, $imageType, $filePath);
                    if ($updateSuccess) {
                        $this->jsonResponse(true, '', ['filePath' => $filePath]);
                    } else {
                        @unlink(ROOT_PATH . '/public' . $filePath);
                        $this->jsonResponse(false, 'Lỗi cập nhật cơ sở dữ liệu.');
                    }
                } else {
                    $this->jsonResponse(false, $result['message']);
                }
            } else {
                $this->jsonResponse(false, 'Không có file nào được tải lên hoặc có lỗi xảy ra.');
            }
        }
    }
}
?>