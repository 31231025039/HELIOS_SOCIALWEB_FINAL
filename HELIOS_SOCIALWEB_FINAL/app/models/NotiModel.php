<?php
class NotiModel extends Database {
    public function getNotifications($userId) {
        $sql = "SELECT * FROM thongbao WHERE MaNguoiDung = :uid ORDER BY ThoiGianTao DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':uid' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countUnread($userId) {
        $sql = "SELECT COUNT(*) FROM thongbao WHERE MaNguoiDung = :uid AND TrangThaiDoc = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':uid' => $userId]);
        return (int)$stmt->fetchColumn();
    }

    public function markAsRead($notiId, $userId) {
        $sql = "UPDATE thongbao SET TrangThaiDoc = 1 WHERE MaThongBao = :nid AND MaNguoiDung = :uid";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':nid' => $notiId, ':uid' => $userId]);
    }

    public function markAllAsRead($userId) {
        $sql = "UPDATE thongbao SET TrangThaiDoc = 1 WHERE MaNguoiDung = :uid";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':uid' => $userId]);
    }

    public function deleteNotification($notiId, $userId) {
        $sql = "DELETE FROM thongbao WHERE MaThongBao = :nid AND MaNguoiDung = :uid";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':nid' => $notiId, ':uid' => $userId]);
    }

    public function getNotificationSetting($userId) {
        $sql = "SELECT NhanThongBao FROM nguoidung WHERE MaNguoiDung = :uid";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':uid' => $userId]);
        return (bool)$stmt->fetchColumn();
    }

    public function updateNotificationSetting($userId, $state) {
        $sql = "UPDATE nguoidung SET NhanThongBao = :state WHERE MaNguoiDung = :uid";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':state' => $state, ':uid' => $userId]);
    }

    public function create($userId, $actorId, $content, $type, $link = null) {
        if ((int)$userId === (int)$actorId || !$this->getNotificationSetting($userId)) {
            return false;
        }

        $sql = "INSERT INTO thongbao (MaNguoiDung, NguoiKhoiTao, NoiDung, LoaiThongBao, LienKet, ThoiGianTao, TrangThaiDoc)
                VALUES (:uid, :actor, :content, :type, :link, NOW(), 0)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':uid' => $userId,
            ':actor' => $actorId,
            ':content' => $content,
            ':type' => $type,
            ':link' => $link
        ]);
    }

    public function notifyPostInteracted($postOwnerId, $actorId, $postId) {
        $actorName = $this->fetchUserName($actorId);
        return $this->create(
            $postOwnerId,
            $actorId,
            "{$actorName} đã tương tác với bài viết của bạn.",
            'TuongTac',
            "/helios/public/home#post-{$postId}"
        );
    }

    public function notifyPostCommented($postOwnerId, $actorId, $postId) {
        $actorName = $this->fetchUserName($actorId);
        return $this->create(
            $postOwnerId,
            $actorId,
            "{$actorName} đã bình luận bài viết của bạn.",
            'BinhLuan',
            "/helios/public/home#post-{$postId}"
        );
    }

    public function notifyCommentReplied($parentCommentOwnerId, $actorId, $postId) {
        $actorName = $this->fetchUserName($actorId);
        return $this->create(
            $parentCommentOwnerId,
            $actorId,
            "{$actorName} đã trả lời bình luận của bạn trên một bài viết.",
            'BinhLuan',
            "/helios/public/home#post-{$postId}"
        );
    }

    public function notifyConnectionRequest($receiverId, $senderId) {
        $senderName = $this->fetchUserName($senderId);
        return $this->create(
            $receiverId,
            $senderId,
            "{$senderName} đã gửi lời mời kết nối cho bạn.",
            'KetNoi',
            '/helios/public/network'
        );
    }

    public function notifyConnectionAccepted($requesterId, $accepterId) {
        $accepterName = $this->fetchUserName($accepterId);
        return $this->create(
            $requesterId,
            $accepterId,
            "{$accepterName} đã chấp nhận lời mời kết nối của bạn.",
            'KetNoi',
            '/helios/public/network'
        );
    }

    /**
     * Tạo thông báo cho người dùng khi có người khác xem hồ sơ của họ.
     *
     * @param int $profileOwnerId ID của người sở hữu hồ sơ (người nhận thông báo)
     * @param int $viewerId ID của người đã xem hồ sơ
     * @return bool
     */
    public function notifyProfileViewed($profileOwnerId, $viewerId) {
        // Lấy tên của người đã xem hồ sơ
        $viewerName = $this->fetchUserName($viewerId);

        // Tạo nội dung thông báo
        $content = "{$viewerName} đã xem hồ sơ của bạn.";

        // Tạo link dẫn đến hồ sơ của người đã xem
        $link = "/helios/public/about-me?id={$viewerId}";

        // Gọi hàm create() chung để tạo thông báo
        return $this->create(
            $profileOwnerId, // Người nhận thông báo
            $viewerId,       // Người gây ra hành động
            $content,        // Nội dung
            'HoSo',          // Loại thông báo (Hồ sơ)
            $link            // Link
        );
    }

    public function notifyNewPostFromConnection($receiverId, $actorId, $postId) {
        $actorName = $this->fetchUserName($actorId);
        return $this->create(
            $receiverId,
            $actorId,
            "{$actorName} vừa đăng một bài viết mới.",
            'BaiViet',
            "/helios/public/home#post-{$postId}"
        );
    }

    public function notifyAdminNewPost($receiverId, $adminId, $postId) {
        $adminName = $this->fetchUserName($adminId) ?: 'Admin';
        return $this->create(
            $receiverId,
            $adminId,
            "{$adminName} vừa đăng một bài viết mới.",
            'HeThong',
            "/helios/public/home#post-{$postId}"
        );
    }

    /**
     * Tạo thông báo cho người dùng khi một Admin đã chỉnh sửa bài viết của họ.
     *
     * @param int $postOwnerId ID của người sở hữu bài viết (người sẽ nhận thông báo)
     * @param int $adminId ID của admin đã thực hiện hành động chỉnh sửa
     * @param int $postId ID của bài viết đã bị chỉnh sửa
     * @return bool
     */
    public function notifyAdminEditedPost($postOwnerId, $adminId, $postId) {
        $adminName = $this->fetchUserName($adminId) ?: 'Quản trị viên';
        
        return $this->create(
            $postOwnerId, 
            $adminId,     
            "{$adminName} đã chỉnh sửa một bài viết của bạn.", 
            'HeThong',    
            "/helios/public/home#post-{$postId}" 
        );
    }
    
    public function notifyAdminDeletedPost($postOwnerId, $adminId) {
        $adminName = $this->fetchUserName($adminId) ?: 'Quản trị viên';
        return $this->create(
            $postOwnerId,
            $adminId,
            "{$adminName} đã xóa một bài viết của bạn.",
            'HeThong',
            '/helios/public/home'
        );
    }

    public function notifyAudienceForNewPost($actorId, $postId) {
        $receivers = $this->isAdmin($actorId)
            ? $this->getAllUserIdsExcept($actorId)
            : $this->getConnectedUserIds($actorId);

        foreach ($receivers as $receiverId) {
            if ($this->isAdmin($actorId)) {
                $this->notifyAdminNewPost($receiverId, $actorId, $postId);
            } else {
                $this->notifyNewPostFromConnection($receiverId, $actorId, $postId);
            }
        }
    }

    public function getNotificationsFiltered($userId, $filter = 'all') {
        $sql = "SELECT * FROM thongbao WHERE MaNguoiDung = :uid";
        if ($filter === 'connect') {
            $sql .= " AND LoaiThongBao = 'KetNoi'";
        } elseif ($filter === 'system') {
            $sql .= " AND LoaiThongBao = 'HeThong'";
        }
        $sql .= " ORDER BY ThoiGianTao DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':uid' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUnreadCount($userId) {
        return $this->countUnread($userId);
    }

    private function fetchUserName($userId): string {
        $sql = "SELECT HoTen FROM nguoidung WHERE MaNguoiDung = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $userId]);
        return $stmt->fetchColumn() ?: '';
    }

    private function isAdmin($userId) {
        $sql = "SELECT COUNT(*) FROM taikhoan WHERE MaNguoiDung = :uid AND VaiTro = 'Admin'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':uid' => $userId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    private function getAllUserIdsExcept($userId) {
        $sql = "SELECT MaNguoiDung FROM nguoidung WHERE MaNguoiDung <> :uid";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':uid' => $userId]);
        return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
    }

    private function getConnectedUserIds($userId) {
        $sql = "SELECT DISTINCT CASE
                    WHEN MaNguoiGui = :uid THEN MaNguoiNhan
                    ELSE MaNguoiGui
                END AS receiver_id
                FROM ketnoi
                WHERE TrangThai = 'accepted'
                  AND (MaNguoiGui = :uid2 OR MaNguoiNhan = :uid3)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':uid' => $userId, ':uid2' => $userId, ':uid3' => $userId]);
        return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
    }
}
?>