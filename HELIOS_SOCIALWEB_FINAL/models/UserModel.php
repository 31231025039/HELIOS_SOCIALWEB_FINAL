<?php
class UserModel extends Database {
    // 1. Hàm lấy thông tin user
    public function getUser($id) {
        $stmt = $this->db->prepare("SELECT * FROM NguoiDung WHERE MaNguoiDung = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC); 
    }

    // 2. Hàm cập nhật thông tin cá nhân (Tên, Tiêu đề, Địa điểm)
    public function updateUser($id, $hoTen, $tieuDe, $diaDiem) {
        $stmt = $this->db->prepare("UPDATE NguoiDung SET HoTen = :hoten, TieuDe = :tieude, DiaDiem = :diadiem WHERE MaNguoiDung = :id");
        $stmt->bindParam(':hoten', $hoTen);
        $stmt->bindParam(':tieude', $tieuDe);
        $stmt->bindParam(':diadiem', $diaDiem);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // 3. Hàm cập nhật phần Giới thiệu (Bio) - ĐÂY CHÍNH LÀ HÀM BỊ THIẾU LÚC NÃY
    public function updateBio($id, $bio) {
        $sql = "UPDATE NguoiDung SET Bio = :bio WHERE MaNguoiDung = :id";
        $stmt = $this->db->prepare($sql);
        
        $stmt->bindParam(':bio', $bio);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

    // --- CÁC HÀM CHO PHẦN KINH NGHIỆM ---

    // 1. Lấy danh sách kinh nghiệm của 1 User (Sắp xếp mới nhất lên đầu)
    public function getKinhNghiemList($userId) {
        $stmt = $this->db->prepare("SELECT * FROM KinhNghiem WHERE MaNguoiDung = :id ORDER BY ThoiGianTu DESC");
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Lấy ra nhiều dòng (mảng 2 chiều)
    }

    // 2. Thêm mới kinh nghiệm
    public function addKinhNghiem($userId, $congTy, $viTri, $moTa, $tuNgay, $denNgay) {
        $sql = "INSERT INTO KinhNghiem (CongTy, ViTri, MoTa, ThoiGianTu, ThoiGianDen, MaNguoiDung) 
                VALUES (:congty, :vitri, :mota, :tungay, :denngay, :userid)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':congty', $congTy);
        $stmt->bindParam(':vitri', $viTri);
        $stmt->bindParam(':mota', $moTa);
        $stmt->bindParam(':tungay', $tuNgay);
        $stmt->bindParam(':denngay', $denNgay); // Có thể NULL
        $stmt->bindParam(':userid', $userId);
        return $stmt->execute();
    }

    // 3. Sửa kinh nghiệm (Bắt buộc phải check MaKinhNghiem VÀ MaNguoiDung để bảo mật)
    public function updateKinhNghiem($maKinhNghiem, $userId, $congTy, $viTri, $moTa, $tuNgay, $denNgay) {
        $sql = "UPDATE KinhNghiem 
                SET CongTy = :congty, ViTri = :vitri, MoTa = :mota, ThoiGianTu = :tungay, ThoiGianDen = :denngay 
                WHERE MaKinhNghiem = :makn AND MaNguoiDung = :userid";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':congty', $congTy);
        $stmt->bindParam(':vitri', $viTri);
        $stmt->bindParam(':mota', $moTa);
        $stmt->bindParam(':tungay', $tuNgay);
        $stmt->bindParam(':denngay', $denNgay);
        $stmt->bindParam(':makn', $maKinhNghiem);
        $stmt->bindParam(':userid', $userId);
        return $stmt->execute();
    }
    // 1. Lấy danh sách học vấn
    public function getHocVanList($userId) {
        $stmt = $this->db->prepare("SELECT * FROM HocVan WHERE MaNguoiDung = :id ORDER BY ThoiGianTu DESC");
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 2. Thêm mới học vấn
    public function addHocVan($userId, $truongHoc, $chuyenNganh, $tuNgay, $denNgay) {
        $sql = "INSERT INTO HocVan (TruongHoc, ChuyenNganh, ThoiGianTu, ThoiGianDen, MaNguoiDung) 
                VALUES (:truonghoc, :chuyennganh, :tungay, :denngay, :userid)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':truonghoc', $truongHoc);
        $stmt->bindParam(':chuyennganh', $chuyenNganh);
        $stmt->bindParam(':tungay', $tuNgay);
        $stmt->bindParam(':denngay', $denNgay);
        $stmt->bindParam(':userid', $userId);
        return $stmt->execute();
    }

    // 3. Sửa học vấn
    public function updateHocVan($maHocVan, $userId, $truongHoc, $chuyenNganh, $tuNgay, $denNgay) {
        $sql = "UPDATE HocVan 
                SET TruongHoc = :truonghoc, ChuyenNganh = :chuyennganh, ThoiGianTu = :tungay, ThoiGianDen = :denngay 
                WHERE MaHocVan = :mahocvan AND MaNguoiDung = :userid";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':truonghoc', $truongHoc);
        $stmt->bindParam(':chuyennganh', $chuyenNganh);
        $stmt->bindParam(':tungay', $tuNgay);
        $stmt->bindParam(':denngay', $denNgay);
        $stmt->bindParam(':mahocvan', $maHocVan);
        $stmt->bindParam(':userid', $userId);
        return $stmt->execute();
    }
    // 1. Lấy các kỹ năng người dùng ĐANG CÓ
    public function getUserSkills($userId) {
        $sql = "SELECT k.MaKyNang, k.TenKyNang 
                FROM KyNang k 
                JOIN NguoiDung_KyNang nk ON k.MaKyNang = nk.MaKyNang 
                WHERE nk.MaNguoiDung = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 2. Lấy các kỹ năng trong hệ thống mà người dùng CHƯA CÓ (Để đưa vào List thêm mới)
    public function getAvailableSkills($userId) {
        $sql = "SELECT * FROM KyNang 
                WHERE MaKyNang NOT IN (SELECT MaKyNang FROM NguoiDung_KyNang WHERE MaNguoiDung = :id)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 3. Thêm kỹ năng cho User
    public function addUserSkill($userId, $maKyNang) {
        $sql = "INSERT INTO NguoiDung_KyNang (MaNguoiDung, MaKyNang) VALUES (:uid, :kid)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':uid', $userId);
        $stmt->bindParam(':kid', $maKyNang);
        return $stmt->execute();
    }

    // 4. Xóa kỹ năng của User
    public function removeUserSkill($userId, $maKyNang) {
        $sql = "DELETE FROM NguoiDung_KyNang WHERE MaNguoiDung = :uid AND MaKyNang = :kid";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':uid', $userId);
        $stmt->bindParam(':kid', $maKyNang);
        return $stmt->execute();
    }
} 
?>