<?php
class AdminPostController {
    private $postModel;

    public function __construct() {
        $this->postModel = new AdminPostModel();
    }

    // GET /admin/posts — trang danh sách
    public function index() {
        $postTypes  = $this->postModel->getPostTypes();
        $statistics = $this->postModel->getStatistics();
        $pageTitle   = "Quản lý bài viết";
        $activeMenu  = "posts";
        $contentView = VIEW_PATH_ADMIN . '/posts.php';
        include VIEW_PATH_ADMIN . '/layouts/main.php';
    }

    // GET /admin/posts/detail?id=
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

    // GET /admin/posts/get-posts?keyword=&type=&visibility=
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

    // GET /admin/posts/get-detail?id=
    public function getDetail() {
        header('Content-Type: application/json');
        $id = $_GET['id'] ?? 0;
        if (!$id) { echo json_encode(['success' => false, 'message' => 'Thiếu ID']); exit; }
        $post = $this->postModel->getPostById($id);
        echo json_encode($post
            ? ['success' => true,  'data' => $post]
            : ['success' => false, 'message' => 'Không tìm thấy bài viết']
        );
        exit;
    }

    // POST /admin/posts/create
    public function create() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Method không hợp lệ']); exit;
        }
        $data = $this->extractPostData($_POST);
        if ($data['content'] === '') {
            echo json_encode(['success' => false, 'message' => 'Nội dung không được để trống']); exit;
        }
        $ok = $this->postModel->createPost($data);
        echo json_encode(['success' => $ok, 'message' => $ok ? 'Thêm bài viết thành công' : 'Thêm bài viết thất bại']);
        exit;
    }

    // POST /admin/posts/update
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
        $ok = $this->postModel->updatePost($id, $data);
        echo json_encode(['success' => $ok, 'message' => $ok ? 'Cập nhật thành công' : 'Cập nhật thất bại']);
        exit;
    }

    // POST /admin/posts/delete
    public function delete() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Method không hợp lệ']); exit;
        }
        $id = $_POST['id'] ?? 0;
        if (!$id) { echo json_encode(['success' => false, 'message' => 'Thiếu ID']); exit; }
        $ok = $this->postModel->deletePost($id);
        echo json_encode(['success' => $ok, 'message' => $ok ? 'Xóa thành công' : 'Xóa thất bại']);
        exit;
    }

    // Hàm dùng chung để lấy & validate dữ liệu bài viết từ $_POST
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