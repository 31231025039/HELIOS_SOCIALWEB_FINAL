<?php
// File: admin/models/AdminCompanyModel.php

class AdminCompanyModel extends Database {

    // Lấy tất cả công ty, sắp xếp theo tên
    public function getAllCompanies() {
        $stmt = $this->db->query("SELECT * FROM congty ORDER BY MaCongTy DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Thêm một công ty mới
    public function addCompany($tenCongTy, $moTa, $logoPath = null) {
        $sql = "INSERT INTO congty (TenCongTy, MoTa, Logo) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$tenCongTy, $moTa, $logoPath]);
    }

    // Cập nhật thông tin công ty
    public function updateCompany($maCongTy, $tenCongTy, $moTa, $logoPath = null) {
        // Chỉ cập nhật logo nếu có file mới được tải lên
        if ($logoPath) {
            $sql = "UPDATE congty SET TenCongTy = ?, MoTa = ?, Logo = ? WHERE MaCongTy = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$tenCongTy, $moTa, $logoPath, $maCongTy]);
        } else {
            // Không cập nhật logo
            $sql = "UPDATE congty SET TenCongTy = ?, MoTa = ? WHERE MaCongTy = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$tenCongTy, $moTa, $maCongTy]);
        }
    }

    // Xóa một công ty
    public function deleteCompany($maCongTy) {
        $stmt = $this->db->prepare("DELETE FROM congty WHERE MaCongTy = ?");
        return $stmt->execute([$maCongTy]);
    }
    
    // Hàm xử lý upload file logo (tương tự như hàm upload ảnh profile)
    public function uploadLogo($file) {
        if (!isset($file) || $file['error'] != 0) {
            return ['success' => false]; // Không có file hoặc có lỗi
        }
        
        $targetDir = ROOT_PATH . "/public/uploads/logos/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $fileType = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
        $newFileName = uniqid('logo_') . '.' . $fileType;
        $targetFile = $targetDir . $newFileName;

        if (move_uploaded_file($file["tmp_name"], $targetFile)) {
            $relativePath = '/uploads/logos/' . $newFileName;
            return ['success' => true, 'filePath' => $relativePath];
        } else {
            return ['success' => false, 'message' => 'Lỗi khi tải file lên.'];
        }
    }

    /**
     * Tìm kiếm công ty theo từ khóa.
     * @param string $keyword Từ khóa để tìm kiếm.
     * @return array Danh sách các công ty khớp với từ khóa.
     */
    public function searchCompanies($keyword) {
        // Chuẩn bị từ khóa với ký tự đại diện '%'
        $searchTerm = '%' . $keyword . '%';
        
        // Tìm kiếm trong cột TenCongTy
        $sql = "SELECT * FROM congty WHERE TenCongTy LIKE ? ORDER BY MaCongTy DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$searchTerm]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
