<?php
// app/models/JobModel.php

class JobModel extends Database {

    /* Câu SQL cơ bản để lấy thông tin công việc kèm tên và logo công ty */
    private function baseSelectSql(): string
    {
        return "SELECT cv.*, ct.TenCongTy, ct.Logo 
                FROM CongViec cv
                JOIN CongTy ct ON cv.MaCongTy = ct.MaCongTy";
    }

    /**
     * Danh sách các thành phố và các từ khóa liên quan để tìm kiếm (Mapping)
     */
    private array $knownCities = [
        'Hồ Chí Minh' => ['Hồ Chí Minh', 'HCM', 'TP.HCM', 'Tp.HCM', 'Thủ Đức'],
        'Hà Nội'      => ['Hà Nội'],
        'Đà Nẵng'     => ['Đà Nẵng'],
        'Cần Thơ'     => ['Cần Thơ'],
        'Hải Phòng'   => ['Hải Phòng'],
        'Bình Dương'  => ['Bình Dương'],
        'Đồng Nai'    => ['Đồng Nai'],
    ];

    /* Lấy danh sách các địa điểm có công việc đang tuyển dụng */
    public function getDistinctLocations(): array
    {
        $result = [];
        foreach ($this->knownCities as $label => $keywords) {
            $wheres = [];
            $params = [];
            foreach ($keywords as $i => $kw) {
                $key = ':city' . $i;
                $wheres[] = "NoiLamViec LIKE {$key}";
                $params[$key] = '%' . $kw . '%';
            }
            $sql = "SELECT COUNT(*) FROM CongViec WHERE " . implode(' OR ', $wheres);
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            if ((int) $stmt->fetchColumn() > 0) {
                $result[] = $label;
            }
        }
        return $result;
    }

    /* Hàm dùng chung để build câu lệnh WHERE và Params dựa trên các bộ lọc */
    private function buildConditions(?string $keyword, ?string $location, ?string $deadline): array
    {
        $conditions = [];
        $params = [];

        // 1. Lọc theo từ khóa (Tìm trong Tiêu đề, Tên công ty, Mô tả, Yêu cầu, Quyền lợi, Nơi làm việc)
        if ($keyword !== null && $keyword !== '') {
            $conditions[] = "(cv.TieuDe LIKE :kw
                               OR ct.TenCongTy LIKE :kw
                               OR cv.MoTa LIKE :kw
                               OR cv.YeuCau LIKE :kw
                               OR cv.QuyenLoi LIKE :kw
                               OR cv.NoiLamViec LIKE :kw)";
            $params[':kw'] = '%' . $keyword . '%';
        }

        // 2. Lọc theo địa điểm
        if ($location !== null && $location !== '') {
            $keywords = $this->knownCities[$location] ?? [$location];
            $wheres = [];
            foreach ($keywords as $i => $kw) {
                $key = ':loc' . $i;
                $wheres[] = "cv.NoiLamViec LIKE {$key}";
                $params[$key] = '%' . $kw . '%';
            }
            $conditions[] = '(' . implode(' OR ', $wheres) . ')';
        }

        // 3. Lọc theo hạn nộp
        if ($deadline !== null && $deadline !== '') {
            if ($deadline === 'active') {
                $conditions[] = "cv.HanNop >= CURDATE()"; // Còn hạn
            } elseif ($deadline === 'soon') {
                $conditions[] = "cv.HanNop BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)"; // Sắp hết hạn (trong 7 ngày)
            } elseif ($deadline === 'expired') {
                $conditions[] = "cv.HanNop < CURDATE()"; // Đã hết hạn
            }
        }

        return [$conditions, $params];
    }

    /* Đếm tổng số công việc dựa trên các bộ lọc (Phục vụ phân trang) */
    public function countJobs(?string $keyword = null, ?string $location = null, ?string $deadline = null): int
    {
        [$conditions, $params] = $this->buildConditions($keyword, $location, $deadline);

        $sql = "SELECT COUNT(*) FROM CongViec cv
                JOIN CongTy ct ON cv.MaCongTy = ct.MaCongTy";

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    /* Lấy danh sách công việc theo trang và các bộ lọc */
    public function getJobs(?string $keyword, int $offset, int $limit, ?string $location = null, ?string $deadline = null): array
    {
        [$conditions, $params] = $this->buildConditions($keyword, $location, $deadline);

        $sql = $this->baseSelectSql();

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $offset = max(0, $offset);
        $limit = max(1, $limit);
        $sql .= " ORDER BY cv.MaCongViec DESC LIMIT {$offset}, {$limit}";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* Lấy chi tiết 1 công việc theo ID */      
    public function getJobById($jobId)
    {
        $sql = "SELECT cv.*, ct.TenCongTy, ct.Logo, ct.MoTa AS MoTaCongTy 
                FROM CongViec cv
                JOIN CongTy ct ON cv.MaCongTy = ct.MaCongTy
                WHERE cv.MaCongViec = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $jobId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /* Lấy danh sách kỹ năng của một công việc theo ID */
    public function getSkillsByJobId(int $jobId): array
    {
        $sql = "SELECT k.TenKyNang
                FROM KyNang k
                INNER JOIN CongViec_KyNang cvk ON k.MaKyNang = cvk.MaKyNang
                WHERE cvk.MaCongViec = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $jobId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
?>