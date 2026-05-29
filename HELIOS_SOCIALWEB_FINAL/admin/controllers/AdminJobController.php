<?php
// File: admin/controllers/AdminJobController.php 

class AdminJobController {
    
    private $jobModel;

    public function __construct() {
        $this->jobModel = new AdminJobModel();
    }

    private function jsonResponse($success, $message = '') {
        header('Content-Type: application/json');
        echo json_encode(['success' => $success, 'message' => $message]);
        exit();
    }

    public function index() {
        $searchTerm = $_GET['search'] ?? '';
        $jobs = !empty($searchTerm) 
            ? $this->jobModel->searchJobs($searchTerm) 
            : $this->jobModel->getAllJobs();

        $companies = $this->jobModel->getAllCompanies();
        $skills = $this->jobModel->getAllSkills();
        
        $pageTitle = "Quản lý tuyển dụng";
        $activeMenu = "jobs";
        $jsFiles = ['admin-jobs.js'];
        $contentView = VIEW_PATH_ADMIN . '/jobs.php';
        
        include VIEW_PATH_ADMIN . '/layouts/main.php';
    }

    // Xử lý thêm công việc
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $skillIds = $_POST['skills'] ?? [];
            unset($_POST['skills']);
            
            $success = $this->jobModel->addJob($_POST);
            if ($success) {
                $lastJobId = $this->jobModel->getLastInsertId(); 
                if ($lastJobId) {
                    $this->jobModel->updateJobSkills($lastJobId, $skillIds);
                }
            }
            $this->jsonResponse($success, $success ? 'Thêm tin tuyển dụng thành công!' : 'Lỗi khi thêm tin.');
        }
    }

    // Xử lý cập nhật công việc
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['MaCongViec'];
            $skillIds = $_POST['skills'] ?? [];
            unset($_POST['skills']);

            $success = $this->jobModel->updateJob($id, $_POST);
            // Chỉ cập nhật skill nếu update thông tin job thành công
            if ($success) {
                $this->jobModel->updateJobSkills($id, $skillIds);
            }
            $this->jsonResponse($success, $success ? 'Cập nhật thành công!' : 'Lỗi khi cập nhật.');
        }
    }

    // Xử lý xóa công việc
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['MaCongViec'];
            $success = $this->jobModel->deleteJob($id);
            $this->jsonResponse($success, $success ? 'Xóa tin tuyển dụng thành công!' : 'Lỗi khi xóa.');
        }
    }

    // Hàm getSkills() để lấy skill cho form sửa, giữ nguyên
    public function getSkills() {
        header('Content-Type: application/json');
        if (isset($_GET['id'])) {
            $jobId = $_GET['id'];
            $skillIds = $this->jobModel->getJobSkillIds($jobId);
            echo json_encode($skillIds);
        } else {
            echo json_encode([]);
        }
        exit;
    }
}