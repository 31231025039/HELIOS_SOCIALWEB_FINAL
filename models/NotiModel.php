<?php
class NotiModel extends Database {
    
    // 1. Lấy danh sách tất cả thông báo của 1 user (Mới nhất lên đầu)
    public function getNotifications($userId) {
        $sql = "SELECT * FROM ThongBao WHERE MaNguoiDung = :uid ORDER BY ThoiGianTao DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':uid' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 2. Đếm số thông báo CHƯA ĐỌC (TrangThaiDoc = 0)
    public function countUnread($userId) {
        $sql = "SELECT COUNT(*) FROM ThongBao WHERE MaNguoiDung = :uid AND TrangThaiDoc = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':uid' => $userId]);
        return $stmt->fetchColumn();
    }

    // 3. Đánh dấu 1 thông báo là "Đã đọc" (TrangThaiDoc = 1)
    public function markAsRead($notiId, $userId) {
        $sql = "UPDATE ThongBao SET TrangThaiDoc = 1 WHERE MaThongBao = :nid AND MaNguoiDung = :uid";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':nid' => $notiId, ':uid' => $userId]);
    }

    // 4. Đánh dấu TẤT CẢ thông báo là "Đã đọc"
    public function markAllAsRead($userId) {
        $sql = "UPDATE ThongBao SET TrangThaiDoc = 1 WHERE MaNguoiDung = :uid";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':uid' => $userId]);
    }

    // 5. Xóa 1 thông báo
    public function deleteNotification($notiId, $userId) {
        $sql = "DELETE FROM ThongBao WHERE MaThongBao = :nid AND MaNguoiDung = :uid";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':nid' => $notiId, ':uid' => $userId]);
    }
}
?>