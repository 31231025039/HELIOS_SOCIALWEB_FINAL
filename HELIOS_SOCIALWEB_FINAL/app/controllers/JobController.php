<?php
class JobController {
    
    public function index() {
        $pageTitle = "Việc làm | Helios";
        $activeNav = 'job';
        $cssFiles = ['job.css']; 

        $keyword = trim($_GET['q'] ?? '');
        $perPage = JOBS_PER_PAGE;

        $currentPage = max(1, (int) ($_GET['page'] ?? 1));

        $jobModel = new JobModel();
        $totalJobs = $jobModel->countJobs($keyword !== '' ? $keyword : null);

        $totalPages = $totalJobs > 0 ? (int) ceil($totalJobs / $perPage) : 0;
        if ($totalPages > 0 && $currentPage > $totalPages) {
            $currentPage = $totalPages;
        }

        $offset = ($currentPage - 1) * $perPage;
        $jobs = $jobModel->getJobs(
            $keyword !== '' ? $keyword : null,
            $offset,
            $perPage
        );

        $contentView = VIEW_PATH_APP . '/job.php';
        include VIEW_PATH_APP . '/layouts/main.php';
    }

    public function detail() {
        $jobId = $_GET['id'] ?? 0;
        
        $jobModel = new JobModel();
        $job = $jobModel->getJobById($jobId);

        if (!$job) {
            header("Location: /helios/public/job");
            exit();
        }

        $date1 = new DateTime();
        $date2 = new DateTime($job['HanNop']);
        $interval = $date1->diff($date2);
        $daysLeft = $interval->format('%r%a');
        $job['DaysLeft'] = $daysLeft;

        foreach (['MoTa', 'YeuCau', 'QuyenLoi'] as $field) {
            if (!empty($job[$field])) {
                $job[$field] = $this->boldCvApplySentence($job[$field]);
            }
        }

        $pageTitle = $job['TieuDe'] . " | Helios";
        $activeNav = 'job';
        $cssFiles = ['job-detail.css'];

        $contentView = VIEW_PATH_APP . '/job-detail.php';
        include VIEW_PATH_APP . '/layouts/main.php';
    }

    private function boldCvApplySentence(string $text): string
    {
        $escaped = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
        $pattern = '/(?:-\s*)*Nếu bạn quan tâm đến vị trí này, vui lòng gửi CV trực tiếp qua email:\s*[^\r\n]*/u';
        $formatted = preg_replace($pattern, '<strong>$0</strong>', $escaped);
        return nl2br($formatted ?? $escaped);
    }
}
?>