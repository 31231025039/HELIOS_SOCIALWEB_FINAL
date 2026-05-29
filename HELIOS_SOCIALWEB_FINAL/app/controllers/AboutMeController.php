<?php
// app/controllers/AboutMeController.php

class AboutMeController {
    /**
     * @var int|null ID của người dùng đã đăng nhập.
     */
    private $loggedInUserId;

    public function __construct() {
        // Lấy ID người dùng MỘT LẦN DUY NHẤT và lưu lại
        $this->loggedInUserId = $_SESSION['user_id'] ?? null;
    }

    
    // 1. Hàm hiển thị trang web (Đã sửa để dùng VIEW_PATH_APP)
    public function index() {
        $userId = $this->loggedInUserId; 
        if (!$userId) { 
            header('Location: ' . $GLOBALS['baseUrl'] . 'login'); 
            exit; 
        }

        $targetUserId = isset($_GET['id']) ? (int)$_GET['id'] : $userId;
        $isOwnProfile = ($targetUserId === $userId);

        $userModel = new UserModel();
        $userData = $userModel->getUser($targetUserId);

        if (!$userData) {
            header('Location: ' . $GLOBALS['baseUrl'] . '404');
            exit;
        }

        $relStatus = null;
        if (!$isOwnProfile) {
            $relStatus = $userModel->checkConnectionStatus($userId, $targetUserId);
            (new NotiModel())->notifyProfileViewed($targetUserId, $userId);
        }

        $pageTitle = $userData['HoTen'] . " | Helios";
        $activeNav = 'profile';
        $cssFiles = ['about-me.css'];
        $jsFiles = ['about-me.js', 'network.js']; 

        $kinhNghiemList = $userModel->getKinhNghiemList($targetUserId);
        $hocVanList = $userModel->getHocVanList($targetUserId);
        $userSkills = $userModel->getUserSkills($targetUserId);
        $availableSkills = $isOwnProfile ? $userModel->getAvailableSkills($userId) : [];

        $contentView = VIEW_PATH_APP . '/about-me.php';
        include VIEW_PATH_APP . '/layouts/main.php';
    }

    private function jsonResponse($success, $message = '', $data = []) {
        header('Content-Type: application/json');
        echo json_encode(['success' => $success, 'message' => $message] + $data);
        exit();
    }

    // 2. Cập nhật thông tin cá nhân 
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $userModel = new UserModel();
            $success = $userModel->updateUser($this->loggedInUserId, $_POST['hoten'], $_POST['tieude'], $_POST['diadiem']);
            $this->jsonResponse($success, $success ? '' : 'Lỗi cập nhật CSDL.');
        }
    }

    // 3. Cập nhật Bio (Giới thiệu)
    public function updateBio() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $bio = $_POST['bio'] ?? '';
            $userModel = new UserModel();
            $success = $userModel->updateBio($this->loggedInUserId, $bio);
            $this->jsonResponse($success, '', ['newBioHtml' => nl2br(htmlspecialchars($bio))]);
        }
    }

    // 4. Thêm kinh nghiệm 
    public function addExperience() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $denNgay = !empty($_POST['denngay']) ? $_POST['denngay'] : NULL;
            $userModel = new UserModel();
            $success = $userModel->addKinhNghiem($this->loggedInUserId, $_POST['congty'], $_POST['vitri'], $_POST['mota'], $_POST['tungay'], $denNgay);
            $this->jsonResponse($success);
        }
    }

    // 5. Sửa kinh nghiệm
    public function editExperience() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $denNgay = !empty($_POST['denngay']) ? $_POST['denngay'] : NULL;
            $userModel = new UserModel();
            $success = $userModel->updateKinhNghiem($_POST['makinhnghiem'], $this->loggedInUserId, $_POST['congty'], $_POST['vitri'], $_POST['mota'], $_POST['tungay'], $denNgay);
            $this->jsonResponse($success);
        }
    }

    // 6. Thêm học vấn 
    public function addEducation() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $denNgay = !empty($_POST['denngay']) ? $_POST['denngay'] : NULL;
            $userModel = new UserModel();
            $success = $userModel->addHocVan($this->loggedInUserId, $_POST['truonghoc'], $_POST['chuyennganh'], $_POST['tungay'], $denNgay);
            $this->jsonResponse($success);
        }
    }

    // 7. Sửa học vấn 
    public function editEducation() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $denNgay = !empty($_POST['denngay']) ? $_POST['denngay'] : NULL;
            $userModel = new UserModel();
            $success = $userModel->updateHocVan($_POST['mahocvan'], $this->loggedInUserId, $_POST['truonghoc'], $_POST['chuyennganh'], $_POST['tungay'], $denNgay);
            $this->jsonResponse($success);
        }
    }

    // 8. Thêm kỹ năng 
    public function addSkill() {
        header('Content-Type: application/json');
        
        $userId = $this->loggedInUserId; 
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['makynang'])) {
            $userModel = new UserModel();
            $skillsArray = $_POST['makynang']; 

            if (!is_array($skillsArray)) {
                $skillsArray = [$skillsArray]; 
            }

            $success = true;
            foreach ($skillsArray as $skillId) {
                $result = $userModel->addUserSkill($userId, $skillId);
                if (!$result) {
                    $success = false; 
                }
            }

            if ($success) {
                echo json_encode(['success' => true, 'message' => 'Thêm kỹ năng thành công!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Một số kỹ năng bị lỗi không thể thêm.']);
            }
            exit();
        }

        echo json_encode(['success' => false, 'message' => 'Chưa chọn kỹ năng.']);
        exit();
    }

    // 9. Xóa kỹ năng 
    public function deleteSkill() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST['makynang'])) {
            $userModel = new UserModel();
            $success = $userModel->removeUserSkill($this->loggedInUserId, $_POST['makynang']);
            $this->jsonResponse($success);
        }
        $this->jsonResponse(false, 'Không tìm thấy kỹ năng để xóa.');
    }

    // 10. Cập nhật ảnh đại diện/ảnh bìa 
    // Cập nhật ảnh đại diện/ảnh bìa
    public function updateImage() {
        header('Content-Type: application/json');
        
        $userId = $this->loggedInUserId;
        if (!$userId) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập lại.']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $imageType = $_POST['image_type'] ?? '';
            
            if (!in_array($imageType, ['avatar', 'cover'])) {
                echo json_encode(['success' => false, 'message' => 'Loại ảnh không hợp lệ.']);
                exit;
            }

            if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] == 0) {
                $userModel = new UserModel();
                
                $result = $userModel->uploadFile($_FILES['image_file']);
                
                if ($result['success']) {
                    $filePath = $result['filePath'];
                    
                    $updateSuccess = $userModel->updateUserImage($userId, $imageType, $filePath);
                    
                    if ($updateSuccess) {
                        echo json_encode([
                            'success' => true, 
                            'message' => 'Cập nhật ảnh thành công!',
                            'filePath' => $filePath
                        ]);
                    } else {
                        @unlink(ROOT_PATH . '/public' . $filePath); 
                        echo json_encode(['success' => false, 'message' => 'Lỗi cập nhật Cơ sở dữ liệu.']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => $result['message'] ?? 'Lỗi khi tải file lên.']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Không có file nào được chọn hoặc file bị lỗi.']);
            }
            exit;
        }
    }
}
?>
