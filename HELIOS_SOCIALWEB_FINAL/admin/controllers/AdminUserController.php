<?php

class AdminUserController {
    private $userModel;

    public function __construct() {
        $this->userModel = new AdminUserModel();
    }

    public function index() {
        $pageTitle   = "Quản lý người dùng";
        $activeMenu  = "users";
        $jsFiles     = ['admin-users.js'];
        $contentView = VIEW_PATH_ADMIN . '/users.php';
        include VIEW_PATH_ADMIN . '/layouts/main.php';
    }

    public function getUsers() {
        header('Content-Type: application/json');
        
        $filters = [
            'keyword' => $_GET['keyword'] ?? ''
        ];

        $users = $this->userModel->getAllUsers($filters);
        $totalUsers = count($users); 

        echo json_encode([
            'success' => true,
            'data'    => $users,
            'total'   => $totalUsers
        ]);
        exit;
    }

    public function toggleStatus() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Method không hợp lệ']); 
            exit;
        }

        $id = $_POST['id'] ?? 0;
        if (!$id) { 
            echo json_encode(['success' => false, 'message' => 'Thiếu ID tài khoản']); 
            exit; 
        }

        $currentStatus = $this->userModel->getUserStatus($id);
        
        $newStatus = ($currentStatus === 'active') ? 'locked' : 'active';

        $ok = $this->userModel->updateStatus($id, $newStatus);
        
        echo json_encode([
            'success' => $ok, 
            'message' => $ok ? 'Cập nhật trạng thái tài khoản thành công' : 'Cập nhật trạng thái thất bại',
            'new_status' => $newStatus 
        ]);
        exit;
    }

    public function create() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Method không hợp lệ']); 
            exit;
        }

        $email    = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $fullName = trim($_POST['fullname'] ?? '');
        $role     = $_POST['role'] ?? 'User';

        if (empty($email) || empty($password) || empty($fullName)) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng điền đầy đủ thông tin bắt buộc']);
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Email không hợp lệ']);
            exit;
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $data = [
            'password' => $hashedPassword,
            'email'    => $email,
            'fullname' => $fullName,
            'role'     => $role,
            'status'   => 'active' 
        ];

        if ($this->userModel->checkExistEmail($email)) {
            echo json_encode(['success' => false, 'message' => 'Email này đã tồn tại trong hệ thống']);
            exit;
        }

        $ok = $this->userModel->createUser($data);
        echo json_encode([
            'success' => $ok, 
            'message' => $ok ? 'Thêm tài khoản thành công' : 'Thêm tài khoản thất bại'
        ]);
        exit;
    }

    public function changePassword() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Method không hợp lệ']); 
            exit;
        }

        $id          = $_POST['id'] ?? 0;
        $newPassword = trim($_POST['new_password'] ?? '');

        if (!$id || empty($newPassword)) {
            echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
            exit;
        }

        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

        $ok = $this->userModel->updatePassword($id, $hashedPassword);
        echo json_encode([
            'success' => $ok, 
            'message' => $ok ? 'Đổi mật khẩu thành công' : 'Đổi mật khẩu thất bại'
        ]);
        exit;
    }
}