<?php
// app/controllers/JobController.php

class JobController {
    /**
     * @var int|null ID của người dùng đã đăng nhập.
     */
    private $loggedInUserId;

    /**
     * Hàm dựng: Tự động chạy khi JobController được tạo.
     * Lấy ID người dùng từ session và lưu vào thuộc tính của lớp.
     */
    public function __construct() {
        // Lấy ID người dùng MỘT LẦN DUY NHẤT và lưu lại
        $this->loggedInUserId = $_SESSION['user_id'] ?? null;
    }

    // GET /job
    public function index() {
        // Chốt chặn an toàn: Yêu cầu đăng nhập
        if (!$this->loggedInUserId) {
            header('Location: ' . $GLOBALS['baseUrl'] . 'login');
            exit;
        }

        $pageTitle = "Việc làm | Helios";
        $activeNav = 'job';
        $cssFiles  = ['job.css'];

        // Nhận các tham số tìm kiếm và lọc
        $keyword  = trim($_GET['q']        ?? '');
        $location = trim($_GET['location'] ?? '');
        $deadline = trim($_GET['deadline'] ?? '');
        $perPage  = JOBS_PER_PAGE;

        $currentPage = max(1, (int) ($_GET['page'] ?? 1));

        $jobModel  = new JobModel();
        
        // Lấy danh sách địa điểm phục vụ cho thẻ <select> ở giao diện
        $locations = $jobModel->getDistinctLocations();

        // Đếm tổng công việc với điều kiện tìm kiếm và lọc
        $totalJobs = $jobModel->countJobs(
            $keyword  !== '' ? $keyword  : null,
            $location !== '' ? $location : null,
            $deadline !== '' ? $deadline : null
        );

        // Tính toán tổng số trang
        $totalPages = $totalJobs > 0 ? (int) ceil($totalJobs / $perPage) : 0;
        if ($totalPages > 0 && $currentPage > $totalPages) {
            $currentPage = $totalPages;
        }

        // Tính toán offset cho truy vấn
        $offset = ($currentPage - 1) * $perPage;
        
        // Lấy danh sách việc làm theo các bộ lọc
        $jobs = $jobModel->getJobs(
            $keyword  !== '' ? $keyword  : null,
            $offset,
            $perPage,
            $location !== '' ? $location : null,
            $deadline !== '' ? $deadline : null
        );
        
        $contentView = VIEW_PATH_APP . '/job.php';
        include VIEW_PATH_APP . '/layouts/main.php';
    }

    // GET /job/detail?id=
    public function detail() {
        // Chốt chặn an toàn cho trang chi tiết
        if (!$this->loggedInUserId) {
            header('Location: ' . $GLOBALS['baseUrl'] . 'login');
            exit;
        }

        $jobId = $_GET['id'] ?? 0;

        $jobModel = new JobModel();
        $job      = $jobModel->getJobById($jobId);

        if (!$job) {
            header("Location: /helios/public/job");
            exit();
        }

        // Tính số ngày còn lại
        $date1    = new DateTime();
        $date2    = new DateTime($job['HanNop']);
        $interval = $date1->diff($date2);
        $job['DaysLeft'] = (int) $interval->format('%r%a');

        // Lấy danh sách kỹ năng của công việc
        $job['KyNang'] = $jobModel->getSkillsByJobId((int) $jobId);

        // Format in đậm câu ứng tuyển
        foreach (['MoTa', 'YeuCau', 'QuyenLoi'] as $field) {
            if (!empty($job[$field])) {
                $job[$field] = $this->boldCvApplySentence($job[$field]);
            }
        }

        $pageTitle   = $job['TieuDe'] . " | Helios";
        $activeNav   = 'job';
        $cssFiles    = ['job-detail.css'];
        $contentView = VIEW_PATH_APP . '/job-detail.php';
        
        include VIEW_PATH_APP . '/layouts/main.php';
    }

    /**
     * Hàm helper: In đậm dòng text gửi CV qua email
     */
    private function boldCvApplySentence(string $text): string
    {
        $escaped   = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
        $pattern   = '/(?:-\s*)*Nếu bạn quan tâm đến vị trí này, vui lòng gửi CV trực tiếp qua email:\s*[^\r\n]*/u';
        $formatted = preg_replace($pattern, '<strong>$0</strong>', $escaped);
        return nl2br($formatted ?? $escaped);
    }
}
?>