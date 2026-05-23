<?php
class JobModel extends Database {

    private function baseSelectSql(): string
    {
        return "SELECT cv.*, ct.TenCongTy, ct.Logo 
                FROM CongViec cv
                JOIN CongTy ct ON cv.MaCongTy = ct.MaCongTy";
    }

    private function searchWhereSql(): string
    {
        return " WHERE cv.TieuDe LIKE :kw
                   OR ct.TenCongTy LIKE :kw
                   OR cv.MoTa LIKE :kw
                   OR cv.YeuCau LIKE :kw
                   OR cv.QuyenLoi LIKE :kw
                   OR cv.NoiLamViec LIKE :kw";
    }

    // Bước 1: Đếm tổng số công việc (có hoặc không có từ khóa)
    public function countJobs(?string $keyword = null): int
    {
        if ($keyword !== null && $keyword !== '') {
            $sql = "SELECT COUNT(*) FROM CongViec cv
                    JOIN CongTy ct ON cv.MaCongTy = ct.MaCongTy"
                    . $this->searchWhereSql();
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':kw' => '%' . $keyword . '%']);
        } else {
            $sql = "SELECT COUNT(*) FROM CongViec cv
                    JOIN CongTy ct ON cv.MaCongTy = ct.MaCongTy";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
        }
        return (int) $stmt->fetchColumn();
    }

    // Bước 5: Lấy danh sách theo trang (LIMIT)
    public function getJobs(?string $keyword, int $offset, int $limit): array
    {
        $sql = $this->baseSelectSql();

        if ($keyword !== null && $keyword !== '') {
            $sql .= $this->searchWhereSql();
        }

        $offset = max(0, $offset);
        $limit = max(1, $limit);
        $sql .= " ORDER BY cv.MaCongViec DESC LIMIT {$offset}, {$limit}";

        $stmt = $this->db->prepare($sql);

        if ($keyword !== null && $keyword !== '') {
            $stmt->bindValue(':kw', '%' . $keyword . '%');
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy chi tiết 1 công việc theo ID
    public function getJobById($jobId) {
        $sql = "SELECT cv.*, ct.TenCongTy, ct.Logo, ct.MoTa AS MoTaCongTy 
                FROM CongViec cv
                JOIN CongTy ct ON cv.MaCongTy = ct.MaCongTy
                WHERE cv.MaCongViec = :id";
                
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $jobId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
