<?php
class NotiController {
    
    private $userId = 1; // Giả lập user đang đăng nhập là Phương Vy (ID = 1)

    // 1. Hàm hiển thị trang web Thông báo
    public function index() {
        $pageTitle = "Thông báo | Helios";
        $activeNav = 'noti';
        $cssFiles = ['noti.css'];
        $jsFiles = ['noti.js'];

        $notiModel = new NotiModel();
        $notifications = $notiModel->getNotifications($this->userId);
        $unreadCount = $notiModel->countUnread($this->userId);

        $contentView = VIEW_PATH . '/noti.php';
        include VIEW_PATH . '/layouts/main.php';
    }

    // 2. Hàm AJAX: Đánh dấu 1 thông báo đã đọc
    public function markRead() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $notiId = $_POST['noti_id'] ?? 0;
            $notiModel = new NotiModel();
            $result = $notiModel->markAsRead($notiId, $this->userId);
            
            echo json_encode(['success' => $result]); exit();
        }
    }

    // 3. Hàm AJAX: Đánh dấu tất cả đã đọc
    public function markAllRead() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $notiModel = new NotiModel();
            $result = $notiModel->markAllAsRead($this->userId);
            
            echo json_encode(['success' => $result]); exit();
        }
    }

    // 4. Hàm AJAX: Xóa 1 thông báo
    public function deleteNoti() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $notiId = $_POST['noti_id'] ?? 0;
            $notiModel = new NotiModel();
            $result = $notiModel->deleteNotification($notiId, $this->userId);
            
            echo json_encode(['success' => $result]); exit();
        }
    }
}
?>