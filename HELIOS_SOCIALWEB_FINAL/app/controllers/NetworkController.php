<?php
require_once APP_PATH . '/models/NetworkModel.php';

class NetworkController
{
    /**
     * @var int|null ID của người dùng đã đăng nhập.
     */
    private $loggedInUserId;

    /**
     * @var NetworkModel
     */
    private $networkModel;


    public function __construct() {
        $this->loggedInUserId = $_SESSION['user_id'] ?? null;
        $this->networkModel = new NetworkModel();
    }

    public function index()
    {
        if (!$this->loggedInUserId) {
            header('Location: ' . $GLOBALS['baseUrl'] . 'login');
            exit;
        }

        $suggestedUsers = $this->networkModel->getSuggestedUsers($this->loggedInUserId);
        $pendingInvitations = $this->networkModel->getPendingInvitations($this->loggedInUserId);
        $connections = $this->networkModel->getConnections($this->loggedInUserId);

        $userModel = new UserModel();
        $loggedInUserData = $userModel->getUser($this->loggedInUserId);

        $postModel = new PostModel();
        $upcomingEvents = $postModel->getUpcomingEvents();
        

        $networkModelForSidebar = new NetworkModel();
        $networkStats = $networkModelForSidebar->getSidebarStats($this->loggedInUserId);

        $pageTitle = "Mạng lưới";
        $cssFiles = ['network.css'];
        $jsFiles = ['network.js'];
        $activeNav = 'network';
        $contentView = VIEW_PATH_APP . '/network.php';
        
        include VIEW_PATH_APP . '/layouts/main.php';
    }

    public function acceptRequest()
    {
        header('Content-Type: application/json');
        
        if (!$this->loggedInUserId) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        $data = json_decode(file_get_contents("php://input"), true);
        $connectionId = $data['connection_id'] ?? null;

        if (!$connectionId) {
            echo json_encode(['success' => false]);
            return;
        }

        $connection = $this->networkModel->getConnectionById($connectionId);
        $success = $this->networkModel->acceptRequest($connectionId, $this->loggedInUserId);
        if ($success && $connection) {
            (new NotiModel())->notifyConnectionAccepted($connection['MaNguoiGui'], $this->loggedInUserId);
        }
        
        echo json_encode(['success' => $success]);
    }

    public function ignoreRequest()
    {
        header('Content-Type: application/json');
        
        if (!$this->loggedInUserId) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        $data = json_decode(file_get_contents("php://input"), true);
        $connectionId = $data['connection_id'] ?? null;

        if (!$connectionId) {
            echo json_encode(['success' => false]);
            return;
        }


        $success = $this->networkModel->ignoreRequest($connectionId, $this->loggedInUserId);
        
        echo json_encode(['success' => $success]);
    }

    public function sendRequest()
    {
        header('Content-Type: application/json');

        if (!$this->loggedInUserId) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }
        $data = json_decode(file_get_contents("php://input"), true);
        $receiverId = $data['receiver_id'] ?? null;

        if (!$receiverId) {
            echo json_encode(['success' => false, 'message' => 'Thiếu ID người nhận']);
            return;
        }
        $success = $this->networkModel->sendRequest($this->loggedInUserId, $receiverId);

        if ($success) {
            (new NotiModel())->notifyConnectionRequest($receiverId, $this->loggedInUserId);
        }

        echo json_encode(['success' => $success]);
    }

    public function search()
    {
        header('Content-Type: application/json');
        
        if (!$this->loggedInUserId) {
            echo json_encode([]);
            return;
        }

        $keyword = trim($_GET['keyword'] ?? '');
        
        if (empty($keyword)) {
            echo json_encode([]);
            return;
        }

        $users = $this->networkModel->searchUsers($keyword);
        echo json_encode($users);
    }

    public function suggestions()
    {
        header('Content-Type: application/json');

        if (!$this->loggedInUserId) {
            echo json_encode([]);
            return;
        }

        $keyword = trim($_GET['keyword'] ?? '');


        $users = $this->networkModel->getSuggestedUsers($this->loggedInUserId, $keyword);
        
        echo json_encode($users);
    }

    public function getQuickProfile()
    {
        header('Content-Type: application/json');
        if (!$this->loggedInUserId) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        $targetId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if (!$targetId) {
            echo json_encode(['success' => false, 'message' => 'Thiếu ID']);
            return;
        }

        require_once APP_PATH . '/models/UserModel.php';
        /** @var UserModel $userModel */
        $userModel = new UserModel();
        
        $user = $userModel->getUser($targetId);
        if (!$user) {
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy người dùng']);
            return;
        }

        $exp = $userModel->getKinhNghiemList($targetId);
        $edu = $userModel->getHocVanList($targetId);
        $skills = $userModel->getUserSkills($targetId) ?: [];

        echo json_encode([
            'success' => true,
            'data' => [
                'info' => $user,
                'experience' => $exp,
                'education' => $edu,
                'skills' => $skills
            ]
        ]);
    }
    public function removeConnection()
    {
        header('Content-Type: application/json');
        
        if (!$this->loggedInUserId) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        $data = json_decode(file_get_contents("php://input"), true);
        $targetId = $data['target_id'] ?? null;

        if (!$targetId) {
            echo json_encode(['success' => false, 'message' => 'Thiếu ID người dùng']);
            return;
        }


        $success = $this->networkModel->removeRequest($this->loggedInUserId, $targetId);
        
        echo json_encode(['success' => $success]);
    }
}
?>
