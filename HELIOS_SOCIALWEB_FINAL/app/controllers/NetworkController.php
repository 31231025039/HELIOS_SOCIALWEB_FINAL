<?php
// app/controllers/NetworkController.php
require_once APP_PATH . '/models/NetworkModel.php';
class NetworkController
{
    private $networkModel;
    public function __construct()
    {
        $this->networkModel = new NetworkModel();
    }
    /*
    |--------------------------------------------------------------------------
    | Trang Network
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        session_start();
        /*
        |--------------------------------------------------------------------------
        | TEMP USER
        |--------------------------------------------------------------------------
        */
        $userId = $_SESSION['user_id'] ?? 1;
        /*
        |--------------------------------------------------------------------------
        | Data
        |--------------------------------------------------------------------------
        */
        $suggestedUsers = $this->networkModel
            ->getSuggestedUsers($userId);
        $pendingInvitations = $this->networkModel
            ->getPendingInvitations($userId);
        $connections = $this->networkModel
            ->getConnections($userId);
        /*
        |--------------------------------------------------------------------------
        | Layout
        |--------------------------------------------------------------------------
        */
        $pageTitle = "Mạng lưới";
        $cssFiles = [
            'network.css'
        ];
        $jsFiles = [
            'network.js'
        ];
        $activeNav = 'network';
        /*
        |--------------------------------------------------------------------------
        | View
        |--------------------------------------------------------------------------
        */
        $contentView =
            VIEW_PATH . '/network.php';
        include VIEW_PATH .
            '/layouts/main.php';
    }
    /*
    |--------------------------------------------------------------------------
    | Gửi lời mời kết nối
    |--------------------------------------------------------------------------
    */
    public function sendRequest()
    {
        session_start();
        header('Content-Type: application/json');
        $data = json_decode(
            file_get_contents("php://input"),
            true
        );
        $senderId =
            $_SESSION['user_id'] ?? 1;
        $receiverId =
            $data['receiver_id'] ?? null;
        if (!$receiverId) {
            echo json_encode([
                'success' => false,
                'message' => 'Thiếu receiver_id'
            ]);
            return;
        }
        $success = $this->networkModel
            ->sendRequest(
                $senderId,
                $receiverId
            );
        echo json_encode([
            'success' => $success
        ]);
    }
    /*
    |--------------------------------------------------------------------------
    | Accept request
    |--------------------------------------------------------------------------
    */
    public function acceptRequest()
    {
        session_start();
        header('Content-Type: application/json');
        $data = json_decode(
            file_get_contents("php://input"),
            true
        );
        $connectionId =
            $data['connection_id'] ?? null;
        $userId =
            $_SESSION['user_id'] ?? 1;
        if (!$connectionId) {
            echo json_encode([
                'success' => false
            ]);
            return;
        }
        $success = $this->networkModel
            ->acceptRequest(
                $connectionId,
                $userId
            );
        echo json_encode([
            'success' => $success
        ]);
    }
    /*
    |--------------------------------------------------------------------------
    | Ignore request
    |--------------------------------------------------------------------------
    */
    public function ignoreRequest()
    {
        session_start();
        header('Content-Type: application/json');
        $data = json_decode(
            file_get_contents("php://input"),
            true
        );
        $connectionId =
            $data['connection_id'] ?? null;
        $userId =
            $_SESSION['user_id'] ?? 1;
        if (!$connectionId) {
            echo json_encode([
                'success' => false
            ]);
            return;
        }
        $success = $this->networkModel
            ->ignoreRequest(
                $connectionId,
                $userId
            );
        echo json_encode([
            'success' => $success
        ]);
    }
    /*
    |--------------------------------------------------------------------------
    | Search user
    |--------------------------------------------------------------------------
    */
    public function search()
    {
        header('Content-Type: application/json');
        $keyword =
            trim($_GET['keyword'] ?? '');
        if (empty($keyword)) {
            echo json_encode([]);
            return;
        }
        $users = $this->networkModel
            ->searchUsers($keyword);
        echo json_encode($users);
    }
    public function suggestions()
    {
        session_start();

        header('Content-Type: application/json');

        $userId = $_SESSION['user_id'] ?? 1;

        $users = $this->networkModel
            ->getSuggestedUsers($userId);

        echo json_encode($users);
    }
}
?>