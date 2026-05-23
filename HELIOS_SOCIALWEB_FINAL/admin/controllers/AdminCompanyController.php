<?php
// File: admin/controllers/AdminCompanyController.php (Đã cập nhật để dùng AJAX)

class AdminCompanyController {
    
    private $companyModel;

    public function __construct() {
        $this->companyModel = new AdminCompanyModel();
    }

    // Hàm helper để trả về JSON
    private function jsonResponse($success, $message = '') {
        header('Content-Type: application/json');
        echo json_encode(['success' => $success, 'message' => $message]);
        exit();
    }

    // Hàm index() giữ nguyên, chỉ cần đảm bảo có $jsFiles
    public function index() {
        $searchTerm = $_GET['search'] ?? '';
        $companies = !empty($searchTerm) 
            ? $this->companyModel->searchCompanies($searchTerm) 
            : $this->companyModel->getAllCompanies();

        $pageTitle = "Quản lý công ty";
        $activeMenu = "companies";
        $jsFiles = ['admin-companies.js']; // Đảm bảo file JS được nạp
        $contentView = VIEW_PATH_ADMIN . '/companies.php';
        
        include VIEW_PATH_ADMIN . '/layouts/main.php';
    }
    
    // Xử lý thêm công ty
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $logoResult = $this->companyModel->uploadLogo($_FILES['Logo']);
            $logoPath = $logoResult['success'] ? $logoResult['filePath'] : null;
            
            $success = $this->companyModel->addCompany($_POST['TenCongTy'], $_POST['MoTa'], $logoPath);
            $this->jsonResponse($success, $success ? 'Thêm công ty thành công!' : 'Lỗi khi thêm công ty.');
        }
    }

    // Xử lý cập nhật công ty
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $maCongTy = $_POST['MaCongTy'];
            $logoResult = $this->companyModel->uploadLogo($_FILES['Logo']);
            $logoPath = $logoResult['success'] ? $logoResult['filePath'] : null;

            $success = $this->companyModel->updateCompany($maCongTy, $_POST['TenCongTy'], $_POST['MoTa'], $logoPath);
            $this->jsonResponse($success, $success ? 'Cập nhật thành công!' : 'Lỗi khi cập nhật.');
        }
    }

    // Xử lý xóa công ty
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $maCongTy = $_POST['MaCongTy'];
            $success = $this->companyModel->deleteCompany($maCongTy);
            $this->jsonResponse($success, $success ? 'Xóa công ty thành công!' : 'Lỗi khi xóa.');
        }
    }
}