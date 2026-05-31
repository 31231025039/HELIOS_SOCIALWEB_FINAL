<?php
class NotiController {
    private $loggedInUserId;
    
    public function __construct() {
        $this->loggedInUserId = $_SESSION['user_id'] ?? null;
    }
    
    public function index() {
        if (!$this->loggedInUserId) {
            header('Location: ' . $GLOBALS['baseUrl'] . 'login');
            exit;
        }
        $filter = $_GET['filter'] ?? 'all';
        
        $pageTitle = "Thông báo | Helios";
        $activeNav = 'noti';
        $cssFiles = ['home.css', 'noti.css'];
        $jsFiles = ['noti.js'];

        $networkModel = new NetworkModel();
        $networkStats = $networkModel->getSidebarStats($this->loggedInUserId);
        $upcomingEvents = (new PostModel())->getUpcomingEvents();

        $notiModel = new NotiModel();
        $notifications = $notiModel->getNotificationsFiltered($this->loggedInUserId, $filter);
        $unreadCount = $notiModel->getUnreadCount($this->loggedInUserId);
        $notifEnabled = $notiModel->getNotificationSetting($this->loggedInUserId);
        
        $userModel = new UserModel();
        $loggedInUserData = $userModel->getUser($this->loggedInUserId);

        $contentView = VIEW_PATH_APP . '/noti.php';
        include VIEW_PATH_APP . '/layouts/main.php';
    }

    public function filter() {
        if (!$this->loggedInUserId) exit;
        $filter = $_GET['filter'] ?? 'all';
        $notiModel = new NotiModel();
        $notifications = $notiModel->getNotificationsFiltered($this->loggedInUserId, $filter);
        ob_start();
        include VIEW_PATH_APP . '/noti-list.php';
        $html = ob_get_clean();
        echo json_encode(['html' => $html]);
        exit;
    }

    public function getUnreadCount() {
        if (!$this->loggedInUserId) {
            echo json_encode(['count' => 0]);
            exit;
        }
        $notiModel = new NotiModel();
        $count = $notiModel->getUnreadCount($this->loggedInUserId);
        echo json_encode(['count' => $count]);
        exit;
    }

    public function markRead() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $notiId = $_POST['noti_id'] ?? 0;
            $notiModel = new NotiModel();
            $result = $notiModel->markAsRead($notiId, $this->loggedInUserId);
            echo json_encode(['success' => $result]); exit();
        }
    }

    public function markAllRead() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $notiModel = new NotiModel();
            $result = $notiModel->markAllAsRead($this->loggedInUserId);
            echo json_encode(['success' => $result]); exit();
        }
    }

    public function deleteNoti() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $notiId = $_POST['noti_id'] ?? 0;
            $notiModel = new NotiModel();
            $result = $notiModel->deleteNotification($notiId, $this->loggedInUserId);
            echo json_encode(['success' => $result]); exit();
        }
    }

    public function toggleNotifications() {
        if (!$this->loggedInUserId) {
            echo json_encode(['success' => false]);
            exit;
        }
        $newState = $_POST['state'] ?? 1;
        $notiModel = new NotiModel();
        $result = $notiModel->updateNotificationSetting($this->loggedInUserId, $newState);
        echo json_encode(['success' => $result, 'new_state' => $newState]);
        exit;
    }
}
?>
