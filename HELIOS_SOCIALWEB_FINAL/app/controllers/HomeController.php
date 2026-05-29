<?php
class HomeController
{
    /** @var int|null */
    private $loggedInUserId;

    public function __construct()
    {
        $this->loggedInUserId = $_SESSION['user_id'] ?? null;
    }

    // TRANG CHỦ — FEED
    public function index()
    {
        if (!$this->loggedInUserId) {
            header('Location: ' . $GLOBALS['baseUrl'] . 'login');
            exit;
        }

        $pageTitle     = 'Trang chủ';
        $cssFiles      = ['home.css'];
        $jsFiles       = ['home.js'];
        $activeNav     = 'home';
        $currentUserId = $this->loggedInUserId;

        // Lấy thống kê sidebar (số kết nối)
        $networkModel = new NetworkModel();
        $networkStats = $networkModel->getSidebarStats($this->loggedInUserId);

        // Lấy thông tin chi tiết user đang đăng nhập (tên, avatar, tiêu đề, ảnh bìa)
        $userModel       = new UserModel();
        $loggedInUserData = $userModel->getUser($this->loggedInUserId);

        // Lấy feed bài viết, bổ sung reaction + ảnh + số bình luận cho từng bài
        $postModel = $this->postModel();
        $posts     = array_map(function ($post) use ($postModel) {
            $postId = $post['MaBaiViet'];
            $post['user_reaction']   = $postModel->getUserReaction($this->loggedInUserId, $postId);
            $post['reactions_detail'] = $postModel->getAllReactions($postId);
            $post['images']          = $postModel->getImagesByPostId($postId);
            $post['comment_count']   = $postModel->countComments($postId);
            return $post;
        }, $postModel->getFeed($this->loggedInUserId));

        $upcomingEvents = $postModel->getUpcomingEvents();

        $contentView = VIEW_PATH_APP . '/home.php';
        include VIEW_PATH_APP . '/layouts/main.php';
    }

    // TẠO BÀI VIẾT
    public function createPost()
    {
        if (!$this->isPost()) return;

        $this->postModel()->create($this->loggedInUserId, [
            'type'              => $_POST['post_type']          ?? 'post',
            'status'            => $_POST['status']             ?? 'Public',
            'content'           => $_POST['content']            ?? '',
            'event_title'       => $_POST['event_title']        ?? null,
            'event_description' => $_POST['event_description']  ?? null,
            'event_location'    => $_POST['event_location']     ?? null,
            'event_time'        => $_POST['event_time']         ?? null,
        ]);

        header('Location: /helios/public/home');
        exit();
    }

    // TƯƠNG TÁC (REACTION) — JSON API
    public function react()
    {
        if (!$this->isPost()) return;

        $postModel    = $this->postModel();
        $postId       = $_POST['post_id']       ?? 0;
        $reactionType = $_POST['reaction_type'] ?? '';
        $action = $postModel->setReaction($this->loggedInUserId, $postId, $reactionType);
        if ($action !== 'removed') {
            $postOwnerId = $postModel->getPostOwnerId($postId);
            (new NotiModel())->notifyPostInteracted($postOwnerId, $this->loggedInUserId, $postId);
        }

        $this->json([
            'success'         => true,
            'action'          => $action,
            'total_reactions' => $postModel->countReactions($postId),
            'new_reaction'    => $action !== 'removed' ? $reactionType : null,
        ]);
    }

    public function getReactionCounts()
    {
        if (!$this->isPost()) return;

        $counts = [];
        foreach ($this->postModel()->getAllReactions($_POST['post_id'] ?? 0) as $reaction) {
            $counts[$reaction['LoaiTuongTac']] = $reaction['SoLuong'];
        }

        $this->json(['success' => true, 'counts' => $counts]);
    }

    // BÌNH LUẬN — JSON API
    public function getComments()
    {
        if (!$this->isPost()) return;

        $comments = $this->postModel()->getComments($_POST['post_id'] ?? 0, $this->loggedInUserId);
        $comments = array_map(function ($comment) {
            // can_edit: chỉ chủ bình luận mới được sửa
            $comment['can_edit'] = (int)$comment['MaNguoiDung'] === (int)$this->loggedInUserId;

            // can_hide: chủ bài có thể ẩn bình luận của người khác (nếu đang hiện)
            $comment['can_hide'] = (int)$comment['MaChuBaiViet'] === (int)$this->loggedInUserId
                && (int)$comment['MaNguoiDung'] !== (int)$this->loggedInUserId
                && ($comment['TrangThaiBinhLuan'] ?? 'Hien') === 'Hien';

            return $comment;
        }, $comments);

        $this->json(['success' => true, 'comments' => $comments]);
    }

    public function addComment()
    {
        if (!$this->isPost()) return;

        $content = trim($_POST['content'] ?? '');
        if ($content === '') {
            $this->json(['success' => false]);
        }

        $postModel = $this->postModel();
        $postId    = $_POST['post_id'] ?? 0;
        $parentId  = $_POST['parent_comment_id'] ?? null;
        $commentId = $postModel->addComment($this->loggedInUserId, $postId, $content, $parentId);
        if ($commentId) {
            $noti = new NotiModel();
            $postOwnerId = $postModel->getPostOwnerId($postId);
            if ($parentId) {
                $parentOwnerId = $postModel->getCommentOwnerId($parentId);
                $noti->notifyCommentReplied($parentOwnerId, $this->loggedInUserId, $postId);
            } else {
                $noti->notifyPostCommented($postOwnerId, $this->loggedInUserId, $postId);
            }
        }
        $this->json(['success' => (bool)$commentId, 'comment_id' => $commentId]);
    }

    public function deleteComment()
    {
        if (!$this->isPost()) return;
        $this->json([
            'success' => $this->postModel()->deleteComment($_POST['comment_id'] ?? 0, $this->loggedInUserId),
        ]);
    }


    public function updateComment()
    {
        if (!$this->isPost()) return;
        $this->json([
            'success' => $this->postModel()->updateComment(
                $_POST['comment_id'] ?? 0,
                $this->loggedInUserId,
                trim($_POST['content'] ?? '')
            ),
        ]);
    }

    public function hideComment()
    {
        if (!$this->isPost()) return;
        $this->json([
            'success' => $this->postModel()->hideComment($_POST['comment_id'] ?? 0, $this->loggedInUserId),
        ]);
    }

    //  SỬA / XÓA BÀI VIẾT — JSON API
    public function deletePost()
    {
        if (!$this->isPost()) return;
        $this->json([
            'success' => $this->postModel()->deletePost($_POST['post_id'] ?? 0, $this->loggedInUserId),
        ]);
    }

    /* Cập nhật nội dung và trạng thái bài viết */
    public function updatePost()
    {
        if (!$this->isPost()) return;
        $this->json([
            'success' => $this->postModel()->updatePost(
                $_POST['post_id'] ?? 0,
                $this->loggedInUserId,
                trim($_POST['content'] ?? ''),
                $_POST['status'] ?? 'Public'
            ),
        ]);
    }

    /**
     * Lấy dữ liệu bài viết để điền sẵn vào modal chỉnh sửa (GET).
     * Chỉ trả về nếu bài thuộc về user đang đăng nhập.
     */
    public function getPostForEdit()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') return;

        $postModel = $this->postModel();
        $postId    = $_GET['post_id'] ?? 0;
        $post      = $postModel->getPostById($postId);

        if (!$post || (int)$post['MaNguoiDung'] !== (int)$this->loggedInUserId) {
            $this->json(['success' => false]);
        }

        $this->json([
            'success' => true,
            'post'    => $post,
            'images'  => $postModel->getImagesByPostId($postId),
        ]);
    }

    // QUẢN LÝ ẢNH — JSON API

    /* Xóa một ảnh khỏi bài viết. */
    public function deletePostImage()
    {
        if (!$this->isPost()) return;
        $this->json([
            'success' => $this->postModel()->deleteImage($_POST['image_id'] ?? 0, $this->loggedInUserId),
        ]);
    }

    /* Thêm ảnh mới vào bài viết đã tồn tại */
    public function addPostImages()
    {
        if (!$this->isPost()) return;

        if (!empty($_FILES['new_images']['name'][0])) {
            $postModel = $this->postModel();
            $imageUrls = $postModel->uploadMultipleImages($_FILES['new_images']);
            if (!empty($imageUrls)) {
                $postModel->saveImages($_POST['post_id'] ?? 0, $imageUrls);
            }
        }

        $this->json(['success' => true]);
    }

    // PRIVATE HELPERS

    /** Kiểm tra request hiện tại có phải POST không.
     * @return bool
     */
    private function isPost()
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /** Tạo instance PostModel mới.
     * Tách ra helper để tránh lặp code `new PostModel()`.
     * @return PostModel
     */
    private function postModel()
    {
        return new PostModel();
    }

    /** Xuất JSON và kết thúc request.
     * Dùng cho tất cả các endpoint trả về JSON (AJAX).
     * @param array $payload
     */
    private function json($payload)
    {
        header('Content-Type: application/json');
        echo json_encode($payload);
        exit();
    }
}
?>