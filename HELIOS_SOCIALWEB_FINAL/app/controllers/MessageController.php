<?php
// app/controllers/MessageController.php
class MessageController {
    /**
     * @var int|null ID của người dùng đã đăng nhập.
     */
    private $loggedInUserId;

    /**
     * @var MessageModel|null Model xử lý tin nhắn.
     */
    private $m;

    /* Lấy ID người dùng từ session và lưu vào thuộc tính của lớp. */
    public function __construct() {
        // Lấy ID người dùng MỘT LẦN DUY NHẤT và lưu lại
        $this->loggedInUserId = $_SESSION['user_id'] ?? null;
        $this->m = new MessageModel();
    }

    /* Trang chính message */
    public function index() {
        if (!$this->loggedInUserId) {
                    header('Location: ' . $GLOBALS['baseUrl'] . 'login');
                    exit;
                }
        $conversations = $this->m->getConversations($this->loggedInUserId);
        $with = isset($_GET['with']) ? (int)$_GET['with'] : null;
        $activeUser = null;
        $messages = [];
        $pinnedMessages = [];
        $attachments = [];

        if ($with) {
            $activeUser = $this->m->getUserInfo($with);
            if ($activeUser) {
                $messages = $this->m->getMessages($this->loggedInUserId, $with);
                $pinnedMessages = $this->m->getPinnedMessages($this->loggedInUserId, $with);
                $attachments = $this->m->getAttachmentsInConversation($this->loggedInUserId, $with);
                $this->m->markAsRead($this->loggedInUserId, $with);
            }
        }

        $unreadMessages = $this->m->countUnreadMessages($this->loggedInUserId);
        $unreadNotis = 0;
        $pageTitle = 'Tin nhắn';
        $activeNav = 'message';
        $cssFiles = ['message.css'];
        $jsFiles = ['message.js'];
        $contentView = VIEW_PATH_APP . '/message.php';
        include VIEW_PATH_APP . '/layouts/main.php';
    }

    /* Gửi tin nhắn văn bản */
    public function send() {
        $this->requireMethod('POST');
        $to = (int)($_POST['to'] ?? 0);
        $text = trim($_POST['content'] ?? '');
        
        if (!$to) {
            return $this->json(['success' => false, 'message' => 'Thiếu người nhận']);
        }
        
        $msgId = $this->m->sendMessage($this->loggedInUserId, $to, $text);
        if ($msgId) {
            $msg = $this->m->getMessageById($msgId);
            $msg['is_mine'] = 1;
            return $this->json(['success' => true, 'message' => $msg]);
        }
        return $this->json(['success' => false, 'message' => 'Gửi thất bại']);
    }

    /* Gửi tin nhắn kèm file */
    public function upload() {
        $this->requireMethod('POST');
        $to = (int)($_POST['to'] ?? 0);
        
        if (!$to) {
            return $this->json(['success' => false, 'message' => 'Thiếu người nhận']);
        }
        
        $uploadDir = ROOT_PATH . '/public/uploads/messages/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        
        $filePath = null;
        if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
            $fileName = time() . '_' . uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['file']['tmp_name'], $uploadDir . $fileName);
            $filePath = '/uploads/messages/' . $fileName;
        }
        
        $content = trim($_POST['content'] ?? '');
        $msgId = $this->m->sendMessageWithFile($this->loggedInUserId, $to, $content, $filePath);
        
        if ($msgId) {
            $msg = $this->m->getMessageById($msgId);
            $msg['is_mine'] = 1;
            return $this->json(['success' => true, 'message' => $msg]);
        }
        return $this->json(['success' => false, 'message' => 'Gửi thất bại']);
    }

    /* Polling - lấy tin nhắn mới */
    public function poll() {
        $this->requireMethod('GET');
        $with = (int)($_GET['with'] ?? 0);
        $lastId = (int)($_GET['last_id'] ?? 0);
        
        if (!$with) {
            return $this->json(['success' => false, 'messages' => []], 400);
        }
        
        $this->m->markAsRead($this->loggedInUserId, $with);
        $this->json([
            'success' => true,
            'messages' => $this->m->getNewMessages($this->loggedInUserId, $with, $lastId),
            'conversations' => $this->m->getConversations($this->loggedInUserId),
            'unread' => $this->m->countUnreadMessages($this->loggedInUserId),
        ]);
    }

    /* Xóa tin nhắn */
    public function delete() {
        $this->requireMethod('POST');
        $msgId = (int)($_POST['msg_id'] ?? 0);
        if (!$msgId) {
            return $this->json(['success' => false, 'message' => 'Thiếu message_id']);
        }
        $this->json(['success' => $this->m->deleteMessage($msgId, $this->loggedInUserId)]);
    }

    /* Xóa toàn bộ hội thoại */
    public function deleteConversation() {
        $this->requireMethod('POST');
        $with = (int)($_POST['with'] ?? 0);
        
        if (!$with) {
            return $this->json(['success' => false, 'message' => 'Thiếu người nhận']);
        }
        
        $success = $this->m->deleteConversation($this->loggedInUserId, $with);
        $this->json(['success' => $success]);
    }

    /* Ghim / bỏ ghim tin nhắn */
    public function pin() {
        $this->requireMethod('POST');
        $msgId = (int)($_POST['msg_id'] ?? 0);
        if (!$msgId) {
            return $this->json(['success' => false]);
        }
        $success = $this->m->togglePinMessage($msgId, $this->loggedInUserId);
        $this->json(['success' => $success]);
    }

    /* Tìm kiếm người dùng */
    public function search() {
        $this->requireMethod('GET');
        $q = trim($_GET['q'] ?? '');
        $this->json([
            'success' => true,
            'results' => $q ? $this->m->searchUsers($q, $this->loggedInUserId) : [],
        ]);
    }

    /* Tìm kiếm tin nhắn trong cuộc trò chuyện */
    public function searchMessages() {
        $this->requireMethod('GET');
        $with = (int)($_GET['with'] ?? 0);
        $q = trim($_GET['q'] ?? '');
        
        if (!$with || !$q) {
            return $this->json(['success' => false, 'results' => []]);
        }
        
        $this->json([
            'success' => true,
            'results' => $this->m->searchMessages($this->loggedInUserId, $with, $q)
        ]);
    }

    /* Lấy danh sách tin nhắn đã ghim (cho popup) */
    public function getPinnedList() {
        $this->requireMethod('GET');
        $with = (int)($_GET['with'] ?? 0);
        if (!$with) {
            return $this->json(['success' => false, 'pins' => []]);
        }
        $pins = $this->m->getPinnedMessages($this->loggedInUserId, $with);
        $this->json(['success' => true, 'pins' => $pins]);
    }

    // Helper methods
    private function json(array $data, int $code = 200): void {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    private function requireMethod(string $method): void {
        if ($_SERVER['REQUEST_METHOD'] !== strtoupper($method)) {
            $this->json(['success' => false, 'message' => 'Method not allowed'], 405);
        }
    }
}