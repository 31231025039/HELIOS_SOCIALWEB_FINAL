<?php

class AdminUserModel extends Database {
    
    public function getAllUsers($filters = []) {
        $sql = "SELECT 
                    tk.MaTaiKhoan as id,
                    tk.MaNguoiDung as user_id,
                    nd.HoTen as fullname,
                    tk.Email as email,
                    tk.TrangThai as status,
                    tk.NgayTao as created_at,
                    tk.VaiTro as role
                FROM TaiKhoan tk
                INNER JOIN NguoiDung nd ON tk.MaNguoiDung = nd.MaNguoiDung
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($filters['keyword'])) {
            $keyword = '%' . $filters['keyword'] . '%';
            $sql .= " AND (nd.HoTen LIKE ? OR tk.Email LIKE ?)";
            $params[] = $keyword;
            $params[] = $keyword;
        }
        
        $sql .= " ORDER BY tk.MaTaiKhoan DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserStatus($id) {
        $sql = "SELECT TrangThai FROM TaiKhoan WHERE MaTaiKhoan = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetchColumn() ?: 'locked';
    }

    public function updateStatus($id, $newStatus) {
        try {
            $sql = "UPDATE TaiKhoan SET TrangThai = ? WHERE MaTaiKhoan = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$newStatus, $id]);
        } catch (PDOException $e) {
            error_log('AdminUserModel::updateStatus error: ' . $e->getMessage());
            return false;
        }
    }

    public function updatePassword($id, $hashedPassword) {
        try {
            $sql = "UPDATE TaiKhoan SET MatKhau = ? WHERE MaTaiKhoan = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$hashedPassword, $id]);
        } catch (PDOException $e) {
            error_log('AdminUserModel::updatePassword error: ' . $e->getMessage());
            return false;
        }
    }

    public function checkExistEmail($email) {
        $sql = "SELECT COUNT(*) FROM TaiKhoan WHERE Email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        return (int)$stmt->fetchColumn() > 0;
    }


    public function createUser($data) {
        try {
            $this->db->beginTransaction();

            $sqlProfile = "INSERT INTO NguoiDung (HoTen) VALUES (?)";
            $stmtProfile = $this->db->prepare($sqlProfile);
            $stmtProfile->execute([$data['fullname']]);
            
            $newUserId = $this->db->lastInsertId();

            $sqlAccount = "INSERT INTO TaiKhoan (MaNguoiDung, Email, MatKhau, VaiTro, TrangThai, NgayTao) 
                           VALUES (?, ?, ?, ?, ?, NOW())";
            $stmtAccount = $this->db->prepare($sqlAccount);
            $stmtAccount->execute([
                $newUserId,
                $data['email'],
                $data['password'], 
                $data['role'],
                $data['status']
            ]);

            $this->db->commit();
            return true;

        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log('AdminUserModel::createUser error: ' . $e->getMessage());
            return false;
        }
    }
}