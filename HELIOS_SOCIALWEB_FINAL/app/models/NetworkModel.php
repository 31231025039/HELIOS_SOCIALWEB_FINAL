<?php

class NetworkModel extends Database
{

        public function getSidebarStats($userId) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM KetNoi
            WHERE TrangThai = 'accepted'
            AND (MaNguoiGui = :uid OR MaNguoiNhan = :uid2)
        ");
        $stmt->execute([':uid' => $userId, ':uid2' => $userId]);
        $connectionCount = (int) $stmt->fetchColumn();

        return [
            'connected' => $connectionCount,
        ];
    }

    public function getSuggestedUsers($currentUserId, $keyword = '', $limit = 15)
    {
        $sql = "
        SELECT 
            nd.MaNguoiDung AS id,
            nd.HoTen AS name,
            nd.TieuDe AS bio,
            nd.AnhDaiDien AS img,
            nd.XacMinh AS verified,
            'Có thể bạn biết' AS sub,
            'bg-light' AS banner,
            kn.MaKetNoi AS connection_id,
            kn.TrangThai AS rel_status,
            kn.MaNguoiGui AS rel_sender_id,
            kn.MaNguoiNhan AS rel_receiver_id
        FROM NguoiDung nd
        JOIN TaiKhoan tk ON nd.MaNguoiDung = tk.MaNguoiDung -- THÊM DÒNG NÀY: Kết nối bảng TaiKhoan
        LEFT JOIN KetNoi kn ON (
            (kn.MaNguoiGui = :uid AND kn.MaNguoiNhan = nd.MaNguoiDung)
            OR 
            (kn.MaNguoiNhan = :uid2 AND kn.MaNguoiGui = nd.MaNguoiDung)
        )
        WHERE nd.MaNguoiDung != :uid3
        AND (
            kn.MaKetNoi IS NULL
            OR (kn.TrangThai = 'pending' AND kn.MaNguoiGui = :uid4)
        )
        AND tk.VaiTro = 'User'  -- SỬA DÒNG NÀY: Đổi nd thành tk
        ";

        $params = [
            ':uid'  => $currentUserId,
            ':uid2' => $currentUserId,
            ':uid3' => $currentUserId,
            ':uid4' => $currentUserId
        ];

        if (!empty($keyword)) {
            $sql .= " AND nd.HoTen LIKE :keyword";
            $params[':keyword'] = "%{$keyword}%";
        }

        $sql .= " ORDER BY RAND() LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPendingInvitations($userId)
    {
        $sql = "
        SELECT 
            kn.MaKetNoi AS connection_id,
            nd.MaNguoiDung AS id,
            nd.HoTen AS name,
            nd.TieuDe AS bio,
            nd.AnhDaiDien AS img,
            nd.XacMinh AS verified
        FROM KetNoi kn
        JOIN NguoiDung nd ON kn.MaNguoiGui = nd.MaNguoiDung
        WHERE kn.MaNguoiNhan = :uid AND kn.TrangThai = 'pending'
        ORDER BY kn.NgayTao DESC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':uid' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getConnections($userId)
    {
        $sql = "
        SELECT DISTINCT
            nd.MaNguoiDung AS id,
            nd.HoTen AS name,
            nd.TieuDe AS bio,
            nd.AnhDaiDien AS img,
            nd.XacMinh AS verified
        FROM KetNoi kn
        JOIN NguoiDung nd ON (
            (kn.MaNguoiGui = :uid AND nd.MaNguoiDung = kn.MaNguoiNhan)
            OR
            (kn.MaNguoiNhan = :uid2 AND nd.MaNguoiDung = kn.MaNguoiGui)
        )
        WHERE kn.TrangThai = 'accepted'
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':uid'  => $userId,
            ':uid2' => $userId
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function sendRequest($senderId, $receiverId)
    {
        $checkSql = "SELECT MaKetNoi FROM KetNoi WHERE (MaNguoiGui = :s AND MaNguoiNhan = :r) OR (MaNguoiGui = :r2 AND MaNguoiNhan = :s2)";
        $checkStmt = $this->db->prepare($checkSql);
        $checkStmt->execute([
            ':s'  => $senderId, 
            ':r'  => $receiverId, 
            ':r2' => $receiverId, 
            ':s2' => $senderId
        ]);
        
        if ($checkStmt->fetch()) {
            return false; 
        }

        $sql = "INSERT INTO KetNoi (MaNguoiGui, MaNguoiNhan, TrangThai, NgayTao) VALUES (:sender, :receiver, 'pending', NOW())";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':sender'   => $senderId, 
            ':receiver' => $receiverId
        ]);
    }

    public function getConnectionById($connectionId)
    {
        $stmt = $this->db->prepare("SELECT * FROM KetNoi WHERE MaKetNoi = :id");
        $stmt->execute([':id' => $connectionId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function removeRequest($currentUserId, $targetUserId)
    {
        $sql = "DELETE FROM KetNoi WHERE (MaNguoiGui = :uid AND MaNguoiNhan = :tuid) OR (MaNguoiGui = :tuid2 AND MaNguoiNhan = :uid2)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':uid'   => $currentUserId, 
            ':tuid'  => $targetUserId,
            ':tuid2' => $targetUserId, 
            ':uid2'  => $currentUserId
        ]);
    }

    public function acceptRequest($connectionId, $userId)
    {
        $sql = "UPDATE KetNoi SET TrangThai = 'accepted' WHERE MaKetNoi = :id AND MaNguoiNhan = :uid";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id'  => $connectionId, 
            ':uid' => $userId
        ]);
    }

    public function ignoreRequest($connectionId, $userId)
    {
        $sql = "DELETE FROM KetNoi WHERE MaKetNoi = :id AND MaNguoiNhan = :uid";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id'  => $connectionId, 
            ':uid' => $userId
        ]);
    }

    public function searchUsers($keyword)
    {
        $sql = "
        SELECT
            MaNguoiDung AS id,
            HoTen AS name,
            TieuDe AS bio,
            AnhDaiDien AS img,
            XacMinh AS verified
        FROM NguoiDung
        WHERE HoTen LIKE :keyword
        LIMIT 10
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':keyword' => "%{$keyword}%"
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
