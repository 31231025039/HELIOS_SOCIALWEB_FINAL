<?php
class UserModel extends Database {
    // 1. Hàm lấy thông tin user
    public function getUser($id) {
        $sql = "SELECT nd.*, tk.Email, tk.VaiTro 
                FROM NguoiDung nd
                JOIN TaiKhoan tk ON nd.MaNguoiDung = tk.MaNguoiDung
                WHERE nd.MaNguoiDung = :id";
        $stmt = $this->db->prepare($sql);
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

    public function updateUserImage($userId, $imageType, $filePath) {
        $column = ($imageType === 'avatar') ? 'AnhDaiDien' : 'AnhBia';
        
        // Không thể bind tên cột, nên phải kiểm tra whitelist như thế này để an toàn
        if (!in_array($column, ['AnhDaiDien', 'AnhBia'])) {
            return false;
        }

        $sql = "UPDATE NguoiDung SET $column = :filepath WHERE MaNguoiDung = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':filepath', $filePath);
        $stmt->bindParam(':id', $userId);
        return $stmt->execute();
    }

    // Xử lý upload file
    public function uploadFile($file) {
        $targetDir = ROOT_PATH . "/public/uploads/profiles/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true); // Tạo thư mục nếu chưa có
        }

        // Kiểm tra file
        $maxSize = 5 * 1024 * 1024; // 5MB
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        $fileName = basename($file["name"]);
        $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if ($file["size"] > $maxSize) {
            return ['success' => false, 'message' => 'File quá lớn. Vui lòng chọn file nhỏ hơn 5MB.'];
        }
        if (!in_array($fileType, $allowedTypes)) {
            return ['success' => false, 'message' => 'Chỉ chấp nhận file ảnh JPG, JPEG, PNG, GIF.'];
        }

        // Tạo tên file mới, duy nhất để tránh bị ghi đè
        $newFileName = uniqid() . '_' . time() . '.' . $fileType;
        $targetFile = $targetDir . $newFileName;

        if (move_uploaded_file($file["tmp_name"], $targetFile)) {
            // Trả về đường dẫn tương đối để lưu vào CSDL
            $relativePath = '/uploads/profiles/' . $newFileName;
            return ['success' => true, 'filePath' => $relativePath];
        } else {
            return ['success' => false, 'message' => 'Đã có lỗi xảy ra khi tải file lên.'];
        }
    }

    public function findUserByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM TaiKhoan WHERE Email = :email");
        $stmt->execute([':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findUserByToken($token) {
        $stmt = $this->db->prepare("SELECT * FROM TaiKhoan WHERE VerificationToken = :token");
        $stmt->execute([':token' => $token]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createUser($name, $email, $hashedPassword, $token, $tokenExpires, $role = 'User') {
        $this->db->beginTransaction();
        try {
            // 1. Tạo bản ghi trong NguoiDung (Chỉ chứa Họ Tên)
            $stmt1 = $this->db->prepare(
                "INSERT INTO NguoiDung (HoTen) VALUES (:name)"
            );
            $stmt1->execute([':name' => $name]);
            
            // Lấy ID người dùng vừa tạo
            $userId = $this->db->lastInsertId();

            // 2. Tạo bản ghi trong TaiKhoan (Chứa Email, Mật Khẩu, Vai trò, Token...)
            $stmt2 = $this->db->prepare(
                "INSERT INTO TaiKhoan (MaNguoiDung, Email, MatKhau, VaiTro, VerificationToken, TokenExpiresAt)
                 VALUES (:userId, :email, :password, :role, :token, :expires)"
            );
            $stmt2->execute([
                ':userId' => $userId,
                ':email' => $email,
                ':password' => $hashedPassword,
                ':role' => $role,
                ':token' => $token,
                ':expires' => $tokenExpires
            ]);

            $this->db->commit();
            return $userId;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log('UserModel::createUser error: ' . $e->getMessage());
            return false;
        }
    }

    public function activateUser($accountId) {
        $stmt = $this->db->prepare(
            "UPDATE TaiKhoan
             SET TrangThai = 'active', VerificationToken = NULL, TokenExpiresAt = NULL
             WHERE MaTaiKhoan = :accountId"
        );
        return $stmt->execute([':accountId' => $accountId]);
    }

    public function updatePasswordResetToken($email, $token, $expiresAt) {
        $stmt = $this->db->prepare(
            "UPDATE TaiKhoan SET PasswordResetToken = :token, ResetTokenExpiresAt = :expires WHERE Email = :email"
        );
        return $stmt->execute([
            ':token' => $token,
            ':expires' => $expiresAt,
            ':email' => $email
        ]);
    }

    public function findUserByResetToken($token) {
        $stmt = $this->db->prepare("SELECT * FROM TaiKhoan WHERE PasswordResetToken = :token");
        $stmt->execute([':token' => $token]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updatePassword($accountId, $hashedPassword) {
        $stmt = $this->db->prepare(
            "UPDATE TaiKhoan 
             SET MatKhau = :password, PasswordResetToken = NULL, ResetTokenExpiresAt = NULL 
             WHERE MaTaiKhoan = :accountId"
        );
        return $stmt->execute([
            ':password' => $hashedPassword,
            ':accountId' => $accountId
        ]);
    }
    /**
     * Kiểm tra trạng thái kết nối giữa 2 người dùng
     */
    public function checkConnectionStatus($userId1, $userId2) {
        $sql = "SELECT TrangThai FROM KetNoi WHERE (MaNguoiGui = :u1 AND MaNguoiNhan = :u2) OR (MaNguoiGui = :u3 AND MaNguoiNhan = :u4)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':u1' => $userId1, ':u2' => $userId2, ':u3' => $userId2, ':u4' => $userId1]);
        return $stmt->fetchColumn();
    }
} 
?>