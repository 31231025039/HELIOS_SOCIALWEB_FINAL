<?php
// app/controllers/MessageController.php
// Xử lý request liên quan đến tin nhắn
class MessageController {
    private MessageModel $m;
    private int $uid;

    public function __construct() {
        $this->m = new MessageModel();
        $this->uid = (int)($_SESSION['user_id'] ?? 1);
    }

    /** Trang chính message
     * GET /message hoặc /message?with=2
     */
    public function index() {
        $conversations = $this->m->getConversations($this->uid);
        $with = isset($_GET['with']) ? (int)$_GET['with'] : null;
        $activeUser = null;
        $messages = [];
        $pinnedMessages = [];
        $attachments = [];

        if ($with) {
            $activeUser = $this->m->getUserInfo($with);
            if ($activeUser) {
                $messages = $this->m->getMessages($this->uid, $with);
                $pinnedMessages = $this->m->getPinnedMessages($this->uid, $with);
                $attachments = $this->m->getAttachmentsInConversation($this->uid, $with);
                $this->m->markAsRead($this->uid, $with);
            }
        }

        $unreadMessages = $this->m->countUnreadMessages($this->uid);
        $unreadNotis = 0;
        $pageTitle = 'Tin nhắn';
        $activeNav = 'message';
        $cssFiles = ['message.css'];
        $jsFiles = ['message.js'];
        $contentView = VIEW_PATH_APP . '/message.php';
        include VIEW_PATH_APP . '/layouts/main.php';
    }

    /**
     * Gửi tin nhắn văn bản
     * POST /message/send
     */
    public function send() {
        $this->requireMethod('POST');
        $to = (int)($_POST['to'] ?? 0);
        $text = trim($_POST['content'] ?? '');
        
        if (!$to) {
            return $this->json(['success' => false, 'message' => 'Thiếu người nhận']);
        }
        
        $msgId = $this->m->sendMessage($this->uid, $to, $text);
        if ($msgId) {
            $msg = $this->m->getMessageById($msgId);
            $msg['is_mine'] = 1;
            return $this->json(['success' => true, 'message' => $msg]);
        }
        return $this->json(['success' => false, 'message' => 'Gửi thất bại']);
    }

    /**
     * Gửi tin nhắn kèm file
     * POST /message/upload
     */
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
        $msgId = $this->m->sendMessageWithFile($this->uid, $to, $content, $filePath);
        
        if ($msgId) {
            $msg = $this->m->getMessageById($msgId);
            $msg['is_mine'] = 1;
            return $this->json(['success' => true, 'message' => $msg]);
        }
        return $this->json(['success' => false, 'message' => 'Gửi thất bại']);
    }

    /**
     * Polling - lấy tin nhắn mới
     * GET /message/poll?with=2&last_id=10
     */
    public function poll() {
        $this->requireMethod('GET');
        $with = (int)($_GET['with'] ?? 0);
        $lastId = (int)($_GET['last_id'] ?? 0);
        
        if (!$with) {
            return $this->json(['success' => false, 'messages' => []], 400);
        }
        
        $this->m->markAsRead($this->uid, $with);
        $this->json([
            'success' => true,
            'messages' => $this->m->getNewMessages($this->uid, $with, $lastId),
            'conversations' => $this->m->getConversations($this->uid),
            'unread' => $this->m->countUnreadMessages($this->uid),
        ]);
    }

    /**
     * Xóa tin nhắn
     * POST /message/delete
     */
    public function delete() {
        $this->requireMethod('POST');
        $msgId = (int)($_POST['msg_id'] ?? 0);
        if (!$msgId) {
            return $this->json(['success' => false, 'message' => 'Thiếu message_id']);
        }
        $this->json(['success' => $this->m->deleteMessage($msgId, $this->uid)]);
    }

    /**
     * Xóa toàn bộ hội thoại
     * POST /message/delete-conversation
     */
    public function deleteConversation() {
        $this->requireMethod('POST');
        $with = (int)($_POST['with'] ?? 0);
        
        if (!$with) {
            return $this->json(['success' => false, 'message' => 'Thiếu người nhận']);
        }
        
        $success = $this->m->deleteConversation($this->uid, $with);
        $this->json(['success' => $success]);
    }

    /**
     * Ghim / bỏ ghim tin nhắn
     * POST /message/pin
     */
    public function pin() {
        $this->requireMethod('POST');
        $msgId = (int)($_POST['msg_id'] ?? 0);
        if (!$msgId) {
            return $this->json(['success' => false]);
        }
        $success = $this->m->togglePinMessage($msgId, $this->uid);
        $this->json(['success' => $success]);
    }

    /**
     * Tìm kiếm người dùng
     * GET /message/search?q=ten
     */
    public function search() {
        $this->requireMethod('GET');
        $q = trim($_GET['q'] ?? '');
        $this->json([
            'success' => true,
            'results' => $q ? $this->m->searchUsers($q, $this->uid) : [],
        ]);
    }

    /**
     * Tìm kiếm tin nhắn trong cuộc trò chuyện
     * GET /message/search-messages?with=2&q=tukhoa
     */
    public function searchMessages() {
        $this->requireMethod('GET');
        $with = (int)($_GET['with'] ?? 0);
        $q = trim($_GET['q'] ?? '');
        
        if (!$with || !$q) {
            return $this->json(['success' => false, 'results' => []]);
        }
        
        $this->json([
            'success' => true,
            'results' => $this->m->searchMessages($this->uid, $with, $q)
        ]);
    }

    /**
     * Lấy danh sách tin nhắn đã ghim (cho popup)
     * GET /message/pinned-list?with=2
     */
    public function getPinnedList() {
        $this->requireMethod('GET');
        $with = (int)($_GET['with'] ?? 0);
        if (!$with) {
            return $this->json(['success' => false, 'pins' => []]);
        }
        $pins = $this->m->getPinnedMessages($this->uid, $with);
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