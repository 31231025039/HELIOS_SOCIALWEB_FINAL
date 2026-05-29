<?php
// app/models/MessageModel.php
class MessageModel extends Database {

    /* Lấy danh sách hội thoại của user */
    public function getConversations(int $userId): array {
        $sql = "
            SELECT 
                nd.MaNguoiDung AS user_id,
                nd.HoTen AS name,
                nd.AnhDaiDien AS avatar,
                nd.TieuDe AS headline,
                last_msg.NoiDung AS last_msg,
                last_msg.NguoiGui AS last_sender,
                last_msg.ThoiGianGui AS last_time,
                (SELECT COUNT(*) FROM TinNhan 
                 WHERE NguoiNhan = ? AND NguoiGui = nd.MaNguoiDung 
                   AND (TrangThaiDoc = 0 OR TrangThaiDoc IS NULL)) AS unread
            FROM (
                SELECT DISTINCT 
                    CASE WHEN NguoiGui = ? THEN NguoiNhan ELSE NguoiGui END AS other_id
                FROM TinNhan
                WHERE NguoiGui = ? OR NguoiNhan = ?
            ) AS conv
            JOIN NguoiDung nd ON nd.MaNguoiDung = conv.other_id
            LEFT JOIN TinNhan last_msg ON last_msg.MaTinNhan = (
                SELECT MaTinNhan FROM TinNhan 
                WHERE (NguoiGui = ? AND NguoiNhan = conv.other_id)
                   OR (NguoiGui = conv.other_id AND NguoiNhan = ?)
                ORDER BY ThoiGianGui DESC LIMIT 1
            )
            ORDER BY last_msg.ThoiGianGui DESC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $userId, $userId, $userId, $userId, $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* Lấy thông tin người dùng theo ID */
    public function getUserInfo(int $userId): ?array {
        $stmt = $this->db->prepare("
            SELECT MaNguoiDung AS id, HoTen AS name, AnhDaiDien AS avatar, TieuDe AS headline
            FROM NguoiDung WHERE MaNguoiDung = ?
        ");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /* Lấy tất cả tin nhắn giữa 2 người */
    public function getMessages(int $userId, int $otherId): array {
        $sql = "
            SELECT 
                tn.MaTinNhan AS id,
                tn.NguoiGui AS sender,
                tn.NoiDung AS content,
                tn.DuongDanFile AS file_path,
                tn.ThoiGianGui AS time,
                tn.TrangThaiDoc AS is_read,
                tn.DaGhim AS is_pinned,
                CASE WHEN tn.NguoiGui = ? THEN 1 ELSE 0 END AS is_mine,
                nd.HoTen AS sender_name,
                nd.AnhDaiDien AS sender_avatar
            FROM TinNhan tn
            JOIN NguoiDung nd ON nd.MaNguoiDung = tn.NguoiGui
            WHERE (tn.NguoiGui = ? AND tn.NguoiNhan = ?)
               OR (tn.NguoiGui = ? AND tn.NguoiNhan = ?)
            ORDER BY tn.ThoiGianGui ASC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $userId, $otherId, $otherId, $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* Lấy tin nhắn mới (polling) */
    public function getNewMessages(int $userId, int $otherId, int $lastId): array {
        $sql = "
            SELECT 
                tn.MaTinNhan AS id,
                tn.NguoiGui AS sender,
                tn.NoiDung AS content,
                tn.DuongDanFile AS file_path,
                tn.ThoiGianGui AS time,
                tn.TrangThaiDoc AS is_read,
                tn.DaGhim AS is_pinned,
                CASE WHEN tn.NguoiGui = ? THEN 1 ELSE 0 END AS is_mine,
                nd.HoTen AS sender_name,
                nd.AnhDaiDien AS sender_avatar
            FROM TinNhan tn
            JOIN NguoiDung nd ON nd.MaNguoiDung = tn.NguoiGui
            WHERE ((tn.NguoiGui = ? AND tn.NguoiNhan = ?) OR (tn.NguoiGui = ? AND tn.NguoiNhan = ?))
              AND tn.MaTinNhan > ?
            ORDER BY tn.ThoiGianGui ASC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $userId, $otherId, $otherId, $userId, $lastId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* Gửi tin nhắn văn bản */
    public function sendMessage(int $userId, int $otherId, string $content): int|false {
        $sql = "INSERT INTO TinNhan (NguoiGui, NguoiNhan, NoiDung, ThoiGianGui, TrangThaiDoc) VALUES (?, ?, ?, NOW(), 0)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userId, $otherId, $content]) ? (int)$this->db->lastInsertId() : false;
    }

    /* Gửi tin nhắn kèm file */
    public function sendMessageWithFile(int $userId, int $otherId, string $content, ?string $filePath = null): int|false {
        $sql = "INSERT INTO TinNhan (NguoiGui, NguoiNhan, NoiDung, DuongDanFile, ThoiGianGui, TrangThaiDoc) VALUES (?, ?, ?, ?, NOW(), 0)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userId, $otherId, $content, $filePath]) ? (int)$this->db->lastInsertId() : false;
    }

    /* Đánh dấu tin nhắn đã đọc */
    public function markAsRead(int $userId, int $otherId): void {
        $sql = "UPDATE TinNhan SET TrangThaiDoc = 1 WHERE NguoiNhan = ? AND NguoiGui = ? AND (TrangThaiDoc = 0 OR TrangThaiDoc IS NULL)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId, $otherId]);
    }

    /* Xóa tin nhắn (chỉ xóa được tin của mình) */
    public function deleteMessage(int $msgId, int $userId): bool {
        $stmt = $this->db->prepare("DELETE FROM TinNhan WHERE MaTinNhan = ? AND NguoiGui = ?");
        $stmt->execute([$msgId, $userId]);
        return $stmt->rowCount() > 0;
    }

    /* Xóa toàn bộ hội thoại (xóa tất cả tin nhắn giữa 2 người) */
    public function deleteConversation(int $userId, int $otherId): bool {
        $sql = "DELETE FROM TinNhan WHERE (NguoiGui = ? AND NguoiNhan = ?) OR (NguoiGui = ? AND NguoiNhan = ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userId, $otherId, $otherId, $userId]);
    }

    /* Đếm tin nhắn chưa đọc của user */
    public function countUnreadMessages(int $userId): int {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM TinNhan WHERE NguoiNhan = ? AND (TrangThaiDoc = 0 OR TrangThaiDoc IS NULL)");
        $stmt->execute([$userId]);
        return (int)$stmt->fetchColumn();
    }

    /* Tìm kiếm người dùng theo tên */
    public function searchUsers(string $kw, int $excludeId): array {
        $stmt = $this->db->prepare("
            SELECT MaNguoiDung AS id, HoTen AS name, AnhDaiDien AS avatar, TieuDe AS headline
            FROM NguoiDung
            WHERE HoTen LIKE ? AND MaNguoiDung != ? LIMIT 10
        ");
        $stmt->execute(["%$kw%", $excludeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* Tìm kiếm tin nhắn trong cuộc trò chuyện */
    public function searchMessages(int $userId, int $otherId, string $keyword): array {
        $stmt = $this->db->prepare("
            SELECT tn.MaTinNhan AS id, tn.NguoiGui AS sender, tn.NoiDung AS content,
                   tn.ThoiGianGui AS time, tn.DuongDanFile AS file_path,
                   CASE WHEN tn.NguoiGui = ? THEN 1 ELSE 0 END AS is_mine,
                   nd.HoTen AS sender_name
            FROM TinNhan tn
            JOIN NguoiDung nd ON nd.MaNguoiDung = tn.NguoiGui
            WHERE ((tn.NguoiGui = ? AND tn.NguoiNhan = ?) OR (tn.NguoiGui = ? AND tn.NguoiNhan = ?))
              AND tn.NoiDung LIKE ?
            ORDER BY tn.ThoiGianGui DESC
            LIMIT 50
        ");
        $stmt->execute([$userId, $userId, $otherId, $otherId, $userId, "%$keyword%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* Lấy tin nhắn theo ID */
    public function getMessageById(int $msgId): ?array {
        $sql = "
            SELECT tn.MaTinNhan AS id, tn.NguoiGui AS sender, tn.NoiDung AS content,
                   tn.DuongDanFile AS file_path, tn.ThoiGianGui AS time, tn.TrangThaiDoc AS is_read,
                   tn.DaGhim AS is_pinned,
                   nd.HoTen AS sender_name, nd.AnhDaiDien AS sender_avatar
            FROM TinNhan tn
            JOIN NguoiDung nd ON nd.MaNguoiDung = tn.NguoiGui
            WHERE tn.MaTinNhan = ?
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$msgId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /* Lấy tin nhắn đã ghim trong cuộc trò chuyện */
    public function getPinnedMessages(int $userId, int $otherId): array {
        $stmt = $this->db->prepare("
            SELECT tn.MaTinNhan AS id, tn.NguoiGui AS sender, tn.NoiDung AS content,
                   tn.ThoiGianGui AS time, tn.DuongDanFile AS file_path,
                   CASE WHEN tn.NguoiGui = ? THEN 1 ELSE 0 END AS is_mine,
                   nd.HoTen AS sender_name
            FROM TinNhan tn
            JOIN NguoiDung nd ON nd.MaNguoiDung = tn.NguoiGui
            WHERE ((tn.NguoiGui = ? AND tn.NguoiNhan = ?) OR (tn.NguoiGui = ? AND tn.NguoiNhan = ?))
              AND tn.DaGhim = 1
            ORDER BY tn.ThoiGianGui DESC
        ");
        $stmt->execute([$userId, $userId, $otherId, $otherId, $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* Lấy hình ảnh đã gửi trong cuộc trò chuyện */
    public function getImagesInConversation(int $userId, int $otherId): array {
        $stmt = $this->db->prepare("
            SELECT tn.MaTinNhan AS id, tn.DuongDanFile AS file_path, tn.ThoiGianGui AS time
            FROM TinNhan tn
            WHERE ((tn.NguoiGui = ? AND tn.NguoiNhan = ?) OR (tn.NguoiGui = ? AND tn.NguoiNhan = ?))
              AND tn.DuongDanFile IS NOT NULL
              AND tn.DuongDanFile REGEXP '\\.(jpg|jpeg|png|gif|webp)$'
            ORDER BY tn.ThoiGianGui DESC
            LIMIT 20
        ");
        $stmt->execute([$userId, $otherId, $otherId, $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* Ghim / bỏ ghim tin nhắn */
    public function togglePinMessage(int $msgId, int $userId): bool {
        $stmt = $this->db->prepare("UPDATE TinNhan SET DaGhim = NOT DaGhim WHERE MaTinNhan = ? AND NguoiNhan = ?");
        return $stmt->execute([$msgId, $userId]);
    }

    /* Lấy tất cả file đính kèm trong cuộc trò chuyện */
    public function getAttachmentsInConversation(int $userId, int $otherId): array {
        $stmt = $this->db->prepare("
            SELECT tn.MaTinNhan AS id, tn.DuongDanFile AS file_path, tn.ThoiGianGui AS time
            FROM TinNhan tn
            WHERE ((tn.NguoiGui = ? AND tn.NguoiNhan = ?) OR (tn.NguoiGui = ? AND tn.NguoiNhan = ?))
              AND tn.DuongDanFile IS NOT NULL
              AND tn.DuongDanFile != ''
            ORDER BY tn.ThoiGianGui DESC
            LIMIT 30
        ");
        $stmt->execute([$userId, $otherId, $otherId, $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}