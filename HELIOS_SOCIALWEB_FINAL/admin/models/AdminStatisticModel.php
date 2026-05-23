<?php
// File: admin/models/AdminStatisticModel.php (Đã nâng cấp)

class AdminStatisticModel extends Database {

    // Hàm getDashboardData được cập nhật để gọi các hàm biểu đồ mới
    public function getDashboardData($month, $year) {
        $range = $this->getMonthRange($month, $year);
        
        return [
            'period' => [
                'month' => (int) $month,
                'year' => (int) $year,
            ],
            'kpis' => [
                'users' => $this->countUsers(),
                'posts' => $this->countPosts(),
                'jobs' => $this->countJobs(),
                'interactions' => $this->countInteractionsInRange($range['start'], $range['end']),
            ],
            'charts' => [
                'userGrowth' => $this->getUserGrowthChartData($range), // Đổi tên hàm gọi
                'contentActivity' => $this->getContentActivityChartData($range), // Đổi tên hàm gọi
            ],
        ];
    }

    // --- HÀM CHO BIỂU ĐỒ MỚI ---

    /**
     * Lấy số lượng người dùng mới đăng ký mỗi ngày trong một khoảng thời gian.
     */
    // Đổi tên và thay thế hàm getDailyUserGrowth() cũ
    private function getUserGrowthChartData($range) {
        if ($range['isYearView']) { // Xử lý cho chế độ xem Cả Năm
            $data = array_fill(1, 12, 0);
            $sql = "SELECT MONTH(NgayTao) as month_num, COUNT(MaTaiKhoan) as total
                    FROM taikhoan
                    WHERE NgayTao >= :start AND NgayTao < :end
                    GROUP BY MONTH(NgayTao)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':start' => $range['start'], ':end' => $range['end']]);
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $data[(int)$row['month_num']] = (int)$row['total'];
            }
        } else { // Xử lý cho chế độ xem theo Tháng (như cũ)
            $data = array_fill(1, count($range['labels']), 0);
            $sql = "SELECT DAY(NgayTao) as day_num, COUNT(MaTaiKhoan) as total
                    FROM taikhoan
                    WHERE NgayTao >= :start AND NgayTao < :end
                    GROUP BY DAY(NgayTao)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':start' => $range['start'], ':end' => $range['end']]);
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $data[(int)$row['day_num']] = (int)$row['total'];
            }
        }
        return ['labels' => $range['labels'], 'data' => array_values($data)];
    }
    
    /**
     * Lấy số lượng bài viết và tin tuyển dụng mới mỗi ngày.
     */
    // Đổi tên và thay thế hàm getDailyContentActivity() cũ
    private function getContentActivityChartData($range) {
        $groupBy = $range['isYearView'] ? 'MONTH' : 'DAY';
        $column = $range['isYearView'] ? 'month_num' : 'day_num';
        $num_items = count($range['labels']);
        $initial_array = array_fill(1, $num_items, 0);

        $posts = $initial_array;
        $jobs = $initial_array;

        // Lấy dữ liệu bài viết
        $sqlPosts = "SELECT {$groupBy}(ThoiGianDang) as {$column}, COUNT(MaBaiViet) as total FROM baiviet WHERE ThoiGianDang >= :start AND ThoiGianDang < :end GROUP BY {$groupBy}(ThoiGianDang)";
        $stmtPosts = $this->db->prepare($sqlPosts);
        $stmtPosts->execute([':start' => $range['start'], ':end' => $range['end']]);
        foreach ($stmtPosts->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $posts[(int)$row[$column]] = (int)$row['total'];
        }

        // Lấy dữ liệu công việc
        $sqlJobs = "SELECT {$groupBy}(NgayDang) as {$column}, COUNT(MaCongViec) as total FROM congviec WHERE NgayDang >= :start AND NgayDang < :end GROUP BY {$groupBy}(NgayDang)";
        $stmtJobs = $this->db->prepare($sqlJobs);
        $stmtJobs->execute([':start' => $range['start'], ':end' => $range['end']]);
        foreach ($stmtJobs->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $jobs[(int)$row[$column]] = (int)$row['total'];
        }

        return [
            'labels' => $range['labels'],
            'posts' => array_values($posts),
            'jobs' => array_values($jobs),
        ];
    }
    
    // --- CÁC HÀM CŨ (giữ nguyên) ---
    public function countUsers() {
        $stmt = $this->db->query("SELECT COUNT(*) FROM nguoidung");
        return (int) $stmt->fetchColumn();
    }
    public function countPosts() {
        $stmt = $this->db->query("SELECT COUNT(*) FROM baiviet");
        return (int) $stmt->fetchColumn();
    }
    public function countJobs() {
        $stmt = $this->db->query("SELECT COUNT(*) FROM congviec");
        return (int) $stmt->fetchColumn();
    }
    // Thay thế hàm getMonthRange() cũ bằng hàm này
    private function getMonthRange($month, $year) {
        if ($month == 0) { // Nếu chọn "Cả năm"
            $start = date('Y-01-01', mktime(0, 0, 0, 1, 1, $year));
            $end = date('Y-01-01', mktime(0, 0, 0, 1, 1, $year + 1));
            // Trục X của biểu đồ sẽ là các tháng
            $labels = ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12'];
            return ['start' => $start, 'end' => $end, 'labels' => $labels, 'isYearView' => true];
        } else { // Nếu chọn tháng cụ thể
            $start = date('Y-m-01', mktime(0, 0, 0, $month, 1, $year));
            $days = (int) date('t', mktime(0, 0, 0, $month, 1, $year));
            $end = date('Y-m-d', mktime(0, 0, 0, $month + 1, 1, $year));
            // Trục X của biểu đồ sẽ là các ngày
            return ['start' => $start, 'end' => $end, 'labels' => range(1, $days), 'isYearView' => false];
        }
    }
    private function countInteractionsInRange($start, $end) {
        // ... (hàm này giữ nguyên không đổi)
    }
}