<?php
class HomeController
{
    private $userId = 1;
    
    public function index()
    {
        $pageTitle = "Trang chủ";
        $cssFiles = ['home.css'];
        $jsFiles = ['home.js'];
        $activeNav = 'home';

        $postModel = new PostModel();
        $posts = $postModel->getFeed($this->userId, 10);
        
        foreach ($posts as &$post) {
            $post['user_reaction'] = $postModel->getUserReaction($this->userId, $post['MaBaiViet']);
            $post['reactions_detail'] = $postModel->getAllReactions($post['MaBaiViet']);
            $post['images'] = $postModel->getImagesByPostId($post['MaBaiViet']);
            // Lấy số bình luận
            $post['comment_count'] = $postModel->countComments($post['MaBaiViet']);
        }

        $contentView = VIEW_PATH . '/home.php';
        include VIEW_PATH . '/layouts/main.php';
    }

    public function createPost()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'type' => $_POST['post_type'] ?? 'post',
                'status' => $_POST['status'] ?? 'Public',
                'content' => $_POST['content'] ?? '',
                'event_title' => $_POST['event_title'] ?? null,
                'event_description' => $_POST['event_description'] ?? null,
                'event_location' => $_POST['event_location'] ?? null,
                'event_time' => $_POST['event_time'] ?? null
            ];

            $postModel = new PostModel();
            $postModel->create($this->userId, $data);
            header("Location: /helios/public/home");
            exit();
        }
    }

    public function react()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $postId = $_POST['post_id'] ?? 0;
            $reactionType = $_POST['reaction_type'] ?? '';
            $postModel = new PostModel();
            $action = $postModel->setReaction($this->userId, $postId, $reactionType);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'action' => $action,
                'total_reactions' => $postModel->countReactions($postId),
                'new_reaction' => ($action != 'removed') ? $reactionType : null
            ]);
            exit();
        }
    }

    public function getReactionCounts()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $postId = $_POST['post_id'] ?? 0;
            $postModel = new PostModel();
            $counts = $postModel->getAllReactions($postId);
            $countsAssoc = [];
            foreach ($counts as $c) {
                $countsAssoc[$c['LoaiTuongTac']] = $c['SoLuong'];
            }
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'counts' => $countsAssoc]);
            exit();
        }
    }

    public function getComments()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $postId = $_POST['post_id'] ?? 0;
            $postModel = new PostModel();
            $comments = $postModel->getComments($postId);
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'comments' => $comments]);
            exit();
        }
    }

    public function addComment()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $postId = $_POST['post_id'] ?? 0;
            $content = trim($_POST['content'] ?? '');
            if (empty($content)) {
                echo json_encode(['success' => false]);
                exit();
            }
            $postModel = new PostModel();
            $postModel->addComment($this->userId, $postId, $content);
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
            exit();
        }
    }

    public function deletePost()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $postId = $_POST['post_id'] ?? 0;
            $postModel = new PostModel();
            $result = $postModel->deletePost($postId, $this->userId);
            header('Content-Type: application/json');
            echo json_encode(['success' => $result]);
            exit();
        }
    }

    public function updatePost()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $postId = $_POST['post_id'] ?? 0;
            $content = trim($_POST['content'] ?? '');
            $status = $_POST['status'] ?? 'Public';
            $postModel = new PostModel();
            $result = $postModel->updatePost($postId, $this->userId, $content, $status);
            header('Content-Type: application/json');
            echo json_encode(['success' => $result]);
            exit();
        }
    }

    // Xóa ảnh khi sửa bài
    public function deletePostImage()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $imageId = $_POST['image_id'] ?? 0;
            $postModel = new PostModel();
            $result = $postModel->deleteImage($imageId, $this->userId);
            header('Content-Type: application/json');
            echo json_encode(['success' => $result]);
            exit();
        }
    }

    // Thêm ảnh mới khi sửa bài
    public function addPostImages()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $postId = $_POST['post_id'] ?? 0;
            if (isset($_FILES['new_images']) && !empty($_FILES['new_images']['name'][0])) {
                $postModel = new PostModel();
                $imageUrls = $postModel->uploadMultipleImages($_FILES['new_images']);
                if (!empty($imageUrls)) {
                    $postModel->saveImages($postId, $imageUrls);
                }
            }
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
            exit();
        }
    }

    public function deleteComment() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $commentId = $_POST['comment_id'] ?? 0;
            $postModel = new PostModel();
            $result = $postModel->deleteComment($commentId, $this->userId);
            header('Content-Type: application/json');
            echo json_encode(['success' => $result]);
            exit();
        }
    }
    
    public function updateComment() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $commentId = $_POST['comment_id'] ?? 0;
            $content = trim($_POST['content'] ?? '');
            $postModel = new PostModel();
            $result = $postModel->updateComment($commentId, $this->userId, $content);
            header('Content-Type: application/json');
            echo json_encode(['success' => $result]);
            exit();
        }
    }
    
    public function getPostForEdit() {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $postId = $_GET['post_id'] ?? 0;
            $postModel = new PostModel();
            $post = $postModel->getPostById($postId);
            $images = $postModel->getImagesByPostId($postId);
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'post' => $post, 'images' => $images]);
            exit();
        }
    }
}
?>