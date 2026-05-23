<?php
// File: admin/models/AdminJobModel.php (Đã cập nhật để khớp với bảng `congviec`)

class AdminJobModel extends Database {

    public function getAllJobs() {
        $sql = "SELECT cv.*, c.TenCongTy 
                FROM congviec cv
                LEFT JOIN congty c ON cv.MaCongTy = c.MaCongTy
                ORDER BY cv.MaCongViec DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getJobById($id) {
        $stmt = $this->db->prepare("SELECT * FROM congviec WHERE MaCongViec = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

        // Sửa hàm addJob()
    public function addJob($data) {
        $sql = "INSERT INTO CongViec (TieuDe, MoTa, YeuCau, QuyenLoi, NoiLamViec, MucLuong, HanNop, MaCongTy) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['TieuDe'], $data['MoTa'], $data['YeuCau'], $data['QuyenLoi'], 
            $data['NoiLamViec'], $data['MucLuong'], $data['HanNop'], $data['MaCongTy']
        ]);
    }

    // Sửa hàm updateJob()
    public function updateJob($id, $data) {
        $sql = "UPDATE CongViec SET TieuDe = ?, MoTa = ?, YeuCau = ?, QuyenLoi = ?, NoiLamViec = ?, 
                MucLuong = ?, HanNop = ?, MaCongTy = ? WHERE MaCongViec = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['TieuDe'], $data['MoTa'], $data['YeuCau'], $data['QuyenLoi'],
            $data['NoiLamViec'], $data['MucLuong'], $data['HanNop'], $data['MaCongTy'], 
            $id
        ]);
    }

    public function deleteJob($id) {
        $stmt = $this->db->prepare("DELETE FROM congviec WHERE MaCongViec = ?");
        return $stmt->execute([$id]);
    }

    public function searchJobs($keyword) {
        $searchTerm = '%' . $keyword . '%';
        $sql = "SELECT cv.*, c.TenCongTy
                FROM congviec cv
                LEFT JOIN congty c ON cv.MaCongTy = c.MaCongTy
                WHERE cv.TieuDe LIKE ? OR c.TenCongTy LIKE ?
                ORDER BY cv.MaCongViec DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$searchTerm, $searchTerm]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllCompanies() {
        // Chỉ lấy MaCongTy và TenCongTy, sắp xếp theo tên để dễ tìm
        $stmt = $this->db->query("SELECT MaCongTy, TenCongTy FROM congty ORDER BY TenCongTy ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllSkills() {
        $stmt = $this->db->query("SELECT MaKyNang, TenKyNang FROM kynang ORDER BY TenKyNang ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // HÀM MỚI: Lấy các ID kỹ năng của một công việc cụ thể
    public function getJobSkillIds($jobId) {
        $stmt = $this->db->prepare("SELECT MaKyNang FROM CongViec_KyNang WHERE MaCongViec = ?");
        $stmt->execute([$jobId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    }

    public function updateJobSkills($jobId, $skillIds = []) {
        $this->db->beginTransaction();
        try {
            $stmt_delete = $this->db->prepare("DELETE FROM CongViec_KyNang WHERE MaCongViec = ?");
            $stmt_delete->execute([$jobId]);

            if (!empty($skillIds)) {
                $stmt_insert = $this->db->prepare("INSERT INTO CongViec_KyNang (MaCongViec, MaKyNang) VALUES (?, ?)");
                foreach ($skillIds as $skillId) {
                    $stmt_insert->execute([$jobId, $skillId]);
                }
            }
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}