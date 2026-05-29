<?php
// File: admin/models/AdminStatisticModel.php (Đã nâng cấp)

class AdminStatisticModel extends Database {

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
                'userGrowth' => $this->getUserGrowthChartData($range), 
                'contentActivity' => $this->getContentActivityChartData($range), 
            ],
        ];
    }

    // --- HÀM CHO BIỂU ĐỒ ---
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
        } else { 
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
    
    public function countUsers() {
        $stmt = $this->db->query("SELECT COUNT(*) FROM taikhoan");
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

    private function getMonthRange($month, $year) {
        if ($month == 0) { 
            $start = date('Y-01-01', mktime(0, 0, 0, 1, 1, $year));
            $end = date('Y-01-01', mktime(0, 0, 0, 1, 1, $year + 1));
            $labels = ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12'];
            return ['start' => $start, 'end' => $end, 'labels' => $labels, 'isYearView' => true];
        } else { 
            $start = date('Y-m-01', mktime(0, 0, 0, $month, 1, $year));
            $days = (int) date('t', mktime(0, 0, 0, $month, 1, $year));
            $end = date('Y-m-d', mktime(0, 0, 0, $month + 1, 1, $year));
            return ['start' => $start, 'end' => $end, 'labels' => range(1, $days), 'isYearView' => false];
        }
    }
    private function countInteractionsInRange($start, $end) {
        $sql = "SELECT 
                    (SELECT COUNT(*) FROM tuongtac WHERE ThoiGian >= :start1 AND ThoiGian < :end1) +
                    (SELECT COUNT(*) FROM binhluan WHERE ThoiGianDang >= :start2 AND ThoiGianDang < :end2)
                AS total";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':start1' => $start, ':end1' => $end,
            ':start2' => $start, ':end2' => $end,
        ]);
        return (int) $stmt->fetchColumn();
    }
}