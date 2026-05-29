<?php
class AdminPostController {
    private $postModel;
        public function __construct() {
        $this->postModel = new AdminPostModel();
    }

    public function index() {
        $postTypes  = $this->postModel->getPostTypes();
        $statistics = $this->postModel->getStatistics();
        $pageTitle   = "Quản lý bài viết";
        $activeMenu  = "posts";
        $jsFiles     = ['admin-posts.js'];
        $contentView = VIEW_PATH_ADMIN . '/posts.php';
        include VIEW_PATH_ADMIN . '/layouts/main.php';
    }

    public function detail() {
        $id   = $_GET['id'] ?? 0;
        $post = null;
        if ($id) {
            $post = $this->postModel->getPostById($id);
            if ($post) {
                $stats            = $this->postModel->getPostStats($id);
                $post['likes']    = $stats['likes'];
                $post['comments'] = $stats['comments'];
                $post['shares']   = $stats['shares'];
            }
        }
        $pageTitle   = "Chi tiết bài viết";
        $activeMenu  = "posts";
        $contentView = VIEW_PATH_ADMIN . '/posts_detail.php';
        include VIEW_PATH_ADMIN . '/layouts/main.php';
    }

    public function getPosts() {
        header('Content-Type: application/json');
        $filters = [
            'keyword'    => $_GET['keyword']    ?? '',
            'post_type'  => $_GET['type']       ?? ($_GET['post_type'] ?? ''),
            'visibility' => $_GET['visibility'] ?? 'all',
        ];
        echo json_encode([
            'success' => true,
            'data'    => $this->postModel->getAllPosts($filters),
            'stats'   => $this->postModel->getStatistics(),
        ]);
        exit;
    }

    public function getDetail() {
        header('Content-Type: application/json');
        $id = $_GET['id'] ?? 0;
        if (!$id) { echo json_encode(['success' => false, 'message' => 'Thiếu ID']); exit; }
        $post = $this->postModel->getPostById($id);
        if ($post) {
            $stats            = $this->postModel->getPostStats($id);
            $post['likes']    = $stats['likes'];
            $post['comments'] = $stats['comments'];
            $post['shares']   = $stats['shares'];
        }
        echo json_encode($post
            ? ['success' => true,  'data' => $post]
            : ['success' => false, 'message' => 'Không tìm thấy bài viết']
        );
        exit;
    }

     public function create() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Method không hợp lệ']); exit;
        }
        // Kiểm tra đăng nhập
        if (empty($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập']); exit;
        }
        $data = $this->extractPostData($_POST);
        if ($data['content'] === '') {
            echo json_encode(['success' => false, 'message' => 'Nội dung không được để trống']); exit;
        }
        $newId = $this->postModel->createPost($data);
        if ($newId && $data['visibility'] === 'Public') {
            (new NotiModel())->notifyAudienceForNewPost($_SESSION['user_id'], $newId);
        }
        echo json_encode([
            'success' => (bool)$newId,
            'post_id' => $newId ?: null,
            'message' => $newId ? 'Thêm bài viết thành công' : 'Thêm bài viết thất bại',
        ]);
        exit;
    }

    public function update() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Method không hợp lệ']); exit;
        }
        $id = $_POST['id'] ?? 0;
        if (!$id) { echo json_encode(['success' => false, 'message' => 'Thiếu ID']); exit; }
        $data = $this->extractPostData($_POST);
        if ($data['content'] === '') {
            echo json_encode(['success' => false, 'message' => 'Nội dung không được để trống']); exit;
        }
        $oldPost = $this->postModel->getPostById($id);
        $ok = $this->postModel->updatePost($id, $data);
        if ($ok && $oldPost && (int)$oldPost['author_id'] !== (int)$_SESSION['user_id']) {
            (new NotiModel())->notifyAdminEditedPost($oldPost['author_id'], $_SESSION['user_id'], $id);
        }
        echo json_encode([
            'success' => $ok,
            'post_id' => $ok ? $id : null,
            'message' => $ok ? 'Cập nhật thành công' : 'Cập nhật thất bại',
        ]);
        exit;
    }

    public function delete() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Method không hợp lệ']); exit;
        }
        $id = $_POST['id'] ?? 0;
        if (!$id) { echo json_encode(['success' => false, 'message' => 'Thiếu ID']); exit; }
        $oldPost = $this->postModel->getPostById($id);
        $ok = $this->postModel->deletePost($id);
        if ($ok && $oldPost && (int)$oldPost['author_id'] !== (int)$_SESSION['user_id']) {
            (new NotiModel())->notifyAdminDeletedPost($oldPost['author_id'], $_SESSION['user_id']);
        }
        echo json_encode(['success' => $ok, 'message' => $ok ? 'Xóa thành công' : 'Xóa thất bại']);
        exit;
    }

    public function uploadImages() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Method không hợp lệ']); exit;
        }

        $postId = $_POST['post_id'] ?? 0;
        if (!$postId) {
            echo json_encode(['success' => false, 'message' => 'Thiếu post_id']); exit;
        }

        if (empty($_FILES['images']['name'][0])) {
            echo json_encode(['success' => false, 'message' => 'Không có ảnh nào được gửi lên']); exit;
        }

        $uploadDir = ROOT_PATH . '/public/uploads/posts/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize      = 5 * 1024 * 1024; // 5MB
        $savedUrls    = [];
        $errors       = [];

        foreach ($_FILES['images']['tmp_name'] as $i => $tmpName) {
            if ($_FILES['images']['error'][$i] !== UPLOAD_ERR_OK) {
                $errors[] = "File #{$i}: lỗi upload (code " . $_FILES['images']['error'][$i] . ")";
                continue;
            }

            $mime = mime_content_type($tmpName);
            if (!in_array($mime, $allowedTypes)) {
                $errors[] = htmlspecialchars($_FILES['images']['name'][$i]) . ": không phải định dạng ảnh hợp lệ";
                continue;
            }

            if ($_FILES['images']['size'][$i] > $maxSize) {
                $errors[] = htmlspecialchars($_FILES['images']['name'][$i]) . ": vượt quá 5MB";
                continue;
            }

            $ext      = strtolower(pathinfo($_FILES['images']['name'][$i], PATHINFO_EXTENSION));
            $filename = uniqid('post_' . $postId . '_') . '.' . $ext;
            $destPath = $uploadDir . $filename;

            if (move_uploaded_file($tmpName, $destPath)) {
                $savedUrls[] = '/helios/public/uploads/posts/' . $filename;
            } else {
                $errors[] = htmlspecialchars($_FILES['images']['name'][$i]) . ": không thể lưu file";
            }
        }

        if (!empty($savedUrls)) {
            $this->postModel->savePostImages($postId, $savedUrls);
        }

        echo json_encode([
            'success' => count($savedUrls) > 0,
            'urls'    => $savedUrls,
            'errors'  => $errors,
            'message' => count($savedUrls) > 0
                ? 'Tải lên thành công ' . count($savedUrls) . ' ảnh'
                : 'Không có ảnh nào được lưu',
        ]);
        exit;
    }

    private function extractPostData($post) {
        $postType = in_array($post['post_type'] ?? '', ['post', 'event']) ? $post['post_type'] : 'post';
        return [
            'content'        => trim($post['content']        ?? ''),
            'post_type'      => $postType,
            'visibility'     => in_array($post['visibility'] ?? '', ['Public', 'Private']) ? $post['visibility'] : 'Public',
            'event_name'     => trim($post['event_name']     ?? ''),
            'event_location' => trim($post['event_location'] ?? ''),
            'event_time'     => trim($post['event_time']     ?? '') ?: null,
        ];
    }
}
