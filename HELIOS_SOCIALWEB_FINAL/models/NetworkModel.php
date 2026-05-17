<?php
// app/models/NetworkModel.php
class NetworkModel
{
    private $db;
    public function __construct()
    {
        try {
            $this->db = new PDO(
                "mysql:host=localhost;dbname=db_helios;charset=utf8mb4",
                "root",
                ""
            );
            $this->db->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );
        } catch (PDOException $e) {
            die("Lỗi kết nối: " . $e->getMessage());
        }
    }
    /*
    |--------------------------------------------------------------------------
    | Gợi ý kết nối
    |--------------------------------------------------------------------------
    */
    public function getSuggestedUsers($currentUserId, $limit = 12)
    {
        $sql = "
        SELECT
            nd.MaNguoiDung AS id,
            nd.HoTen AS name,
            nd.TieuDe AS bio,
            nd.AnhDaiDien AS img,
            nd.XacMinh AS verified,
            'Có kết nối chung' AS sub,
            'bg-light' AS banner
        FROM NguoiDung nd
        WHERE nd.MaNguoiDung != :uid
        LIMIT :limit
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue(':uid', $currentUserId);

        $stmt->bindValue(
            ':limit',
            $limit,
            PDO::PARAM_INT
        );

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    /*
    |--------------------------------------------------------------------------
    | Danh sách lời mời
    |--------------------------------------------------------------------------
    */
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
        JOIN NguoiDung nd
        ON kn.MaNguoiGui = nd.MaNguoiDung
        WHERE kn.MaNguoiNhan = :uid
        AND kn.TrangThai = 'pending'
        ORDER BY kn.NgayTao DESC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':uid' => $userId
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    /*
    |--------------------------------------------------------------------------
    | Danh sách kết nối
    |--------------------------------------------------------------------------
    */
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
        JOIN NguoiDung nd
        ON (
            (
                kn.MaNguoiGui = :uid
                AND nd.MaNguoiDung = kn.MaNguoiNhan
            )
            OR
            (
                kn.MaNguoiNhan = :uid2
                AND nd.MaNguoiDung = kn.MaNguoiGui
            )
        )
        WHERE kn.TrangThai = 'accepted'
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':uid' => $userId,
            ':uid2' => $userId
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    /*
    |--------------------------------------------------------------------------
    | Gửi lời mời kết nối
    |--------------------------------------------------------------------------
    */
    public function sendRequest($senderId, $receiverId)
    {
        $checkSql = "
        SELECT MaKetNoi
        FROM KetNoi
        WHERE
        (
            MaNguoiGui = :sender
            AND MaNguoiNhan = :receiver
        )
        OR
        (
            MaNguoiGui = :receiver2
            AND MaNguoiNhan = :sender2
        )
        ";
        $checkStmt = $this->db->prepare($checkSql);
        $checkStmt->execute([
            ':sender' => $senderId,
            ':receiver' => $receiverId,
            ':receiver2' => $receiverId,
            ':sender2' => $senderId
        ]);
        if ($checkStmt->fetch()) {
            return false;
        }
        $sql = "
        INSERT INTO KetNoi (
            MaNguoiGui,
            MaNguoiNhan,
            TrangThai
        )
        VALUES (
            :sender,
            :receiver,
            'pending'
        )
        ";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':sender' => $senderId,
            ':receiver' => $receiverId
        ]);
    }
    /*
    |--------------------------------------------------------------------------
    | Chấp nhận lời mời
    |--------------------------------------------------------------------------
    */
    public function acceptRequest($connectionId, $userId)
    {
        $sql = "
        UPDATE KetNoi
        SET TrangThai = 'accepted'
        WHERE MaKetNoi = :id
        AND MaNguoiNhan = :uid
        ";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $connectionId,
            ':uid' => $userId
        ]);
    }
    /*
    |--------------------------------------------------------------------------
    | Bỏ qua lời mời
    |--------------------------------------------------------------------------
    */
    public function ignoreRequest($connectionId, $userId)
    {
        $sql = "
        DELETE FROM KetNoi
        WHERE MaKetNoi = :id
        AND MaNguoiNhan = :uid
        ";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $connectionId,
            ':uid' => $userId
        ]);
    }
    /*
    |--------------------------------------------------------------------------
    | Search user
    |--------------------------------------------------------------------------
    */
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