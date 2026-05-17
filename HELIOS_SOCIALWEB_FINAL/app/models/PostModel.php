<?php
class PostModel {
    private $db;

    public function __construct() {
        try {
            $this->db = new PDO(
                "mysql:host=localhost;dbname=db_helios;charset=utf8mb4",
                'root', ''
            );
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Lỗi kết nối: " . $e->getMessage());
        }
    }

    public function getFeed($userId, $limit = 10) {
        $sql = "SELECT 
                    bv.*,
                    nd.HoTen,
                    nd.TieuDe,
                    nd.AnhDaiDien,
                    (SELECT COUNT(*) FROM TuongTac tt WHERE tt.MaBaiViet = bv.MaBaiViet) AS SoTuongTac,
                    (SELECT COUNT(*) FROM BinhLuan bl WHERE bl.MaBaiViet = bv.MaBaiViet) AS SoBinhLuan,
                    (SELECT DuongDanURL FROM HinhAnh ha WHERE ha.MaBaiViet = bv.MaBaiViet LIMIT 1) AS AnhDauTien
                FROM BaiViet bv
                JOIN NguoiDung nd ON bv.MaNguoiDung = nd.MaNguoiDung
                WHERE bv.TrangThai = 'Public' OR bv.MaNguoiDung = :uid
                ORDER BY bv.ThoiGianDang DESC
                LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':uid', $userId);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserReaction($userId, $postId) {
        $stmt = $this->db->prepare("SELECT LoaiTuongTac FROM TuongTac WHERE MaNguoiDung = :uid AND MaBaiViet = :pid LIMIT 1");
        $stmt->execute([':uid' => $userId, ':pid' => $postId]);
        return $stmt->fetchColumn();
    }

    public function getAllReactions($postId) {
        $stmt = $this->db->prepare("SELECT LoaiTuongTac, COUNT(*) as SoLuong FROM TuongTac WHERE MaBaiViet = :pid GROUP BY LoaiTuongTac");
        $stmt->execute([':pid' => $postId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countReactions($postId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM TuongTac WHERE MaBaiViet = :pid");
        $stmt->execute([':pid' => $postId]);
        return $stmt->fetchColumn();
    }

    public function countComments($postId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM BinhLuan WHERE MaBaiViet = :pid");
        $stmt->execute([':pid' => $postId]);
        return $stmt->fetchColumn();
    }

    public function setReaction($userId, $postId, $newType) {
        $stmt = $this->db->prepare("SELECT LoaiTuongTac FROM TuongTac WHERE MaNguoiDung = :uid AND MaBaiViet = :pid");
        $stmt->execute([':uid' => $userId, ':pid' => $postId]);
        $current = $stmt->fetchColumn();

        if ($current === $newType) {
            $del = $this->db->prepare("DELETE FROM TuongTac WHERE MaNguoiDung = :uid AND MaBaiViet = :pid");
            $del->execute([':uid' => $userId, ':pid' => $postId]);
            return 'removed';
        } else {
            $del = $this->db->prepare("DELETE FROM TuongTac WHERE MaNguoiDung = :uid AND MaBaiViet = :pid");
            $del->execute([':uid' => $userId, ':pid' => $postId]);
            $ins = $this->db->prepare("INSERT INTO TuongTac (MaNguoiDung, MaBaiViet, LoaiTuongTac) VALUES (:uid, :pid, :type)");
            $ins->execute([':uid' => $userId, ':pid' => $postId, ':type' => $newType]);
            return 'changed';
        }
    }

    public function getComments($postId) {
        $sql = "SELECT bl.*, nd.HoTen, nd.AnhDaiDien
                FROM BinhLuan bl
                JOIN NguoiDung nd ON bl.MaNguoiDung = nd.MaNguoiDung
                WHERE bl.MaBaiViet = :pid
                ORDER BY bl.ThoiGianDang ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':pid' => $postId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addComment($userId, $postId, $content) {
        $sql = "INSERT INTO BinhLuan (NoiDung, MaBaiViet, MaNguoiDung) VALUES (:content, :pid, :uid)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':content' => $content, ':pid' => $postId, ':uid' => $userId]);
        return $this->db->lastInsertId();
    }

    public function create($userId, $data) {
        $type = $data['type'] ?? 'post';
        
        if ($type == 'event') {
            $content = $data['event_description'] ?? '';
            $title = $data['event_title'] ?? null;
            if (empty($title)) $title = 'Sự kiện không tên';
            $location = $data['event_location'] ?? null;
            $eventTime = $data['event_time'] ?? null;
        } else {
            $content = $data['content'] ?? '';
            $title = null;
            $location = null;
            $eventTime = null;
        }
        
        $sql = "INSERT INTO BaiViet (NoiDung, TrangThai, MaNguoiDung, LoaiBaiViet, TenSuKien, DiaDiemSuKien, ThoiGianSuKien) 
            VALUES (:content, :status, :uid, :type, :title, :location, :event_time)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':status', $data['status']);
        $stmt->bindParam(':uid', $userId);
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':location', $location);
        $stmt->bindParam(':event_time', $eventTime);
        $stmt->execute();
        
        $postId = $this->db->lastInsertId();
        
        if ($type == 'post' && isset($_FILES['post_images']) && !empty($_FILES['post_images']['name'][0])) {
            $imageUrls = $this->uploadMultipleImages($_FILES['post_images']);
            if (!empty($imageUrls)) $this->saveImages($postId, $imageUrls);
        }
        return $postId;
    }

    public function getPostById($postId) {
        $sql = "SELECT bv.*, nd.HoTen, nd.TieuDe, nd.AnhDaiDien
                FROM BaiViet bv
                JOIN NguoiDung nd ON bv.MaNguoiDung = nd.MaNguoiDung
                WHERE bv.MaBaiViet = :pid";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':pid' => $postId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getImagesByPostId($postId) {
        $stmt = $this->db->prepare("SELECT * FROM HinhAnh WHERE MaBaiViet = :pid ORDER BY MaHinhAnh ASC");
        $stmt->execute([':pid' => $postId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deletePost($postId, $userId) {
        $check = $this->db->prepare("SELECT MaNguoiDung FROM BaiViet WHERE MaBaiViet = :pid");
        $check->execute([':pid' => $postId]);
        $owner = $check->fetchColumn();
        if ($owner != $userId) return false;
        
        $this->db->beginTransaction();
        $this->db->prepare("DELETE FROM HinhAnh WHERE MaBaiViet = :pid")->execute([':pid' => $postId]);
        $this->db->prepare("DELETE FROM TuongTac WHERE MaBaiViet = :pid")->execute([':pid' => $postId]);
        $this->db->prepare("DELETE FROM BinhLuan WHERE MaBaiViet = :pid")->execute([':pid' => $postId]);
        $this->db->prepare("DELETE FROM BaiViet WHERE MaBaiViet = :pid")->execute([':pid' => $postId]);
        $this->db->commit();
        return true;
    }

    public function updatePost($postId, $userId, $content, $status) {
        $check = $this->db->prepare("SELECT MaNguoiDung FROM BaiViet WHERE MaBaiViet = :pid");
        $check->execute([':pid' => $postId]);
        $owner = $check->fetchColumn();
        if ($owner != $userId) return false;
        $stmt = $this->db->prepare("UPDATE BaiViet SET NoiDung = :content, TrangThai = :status WHERE MaBaiViet = :pid");
        return $stmt->execute([':content' => $content, ':status' => $status, ':pid' => $postId]);
    }

    public function deleteImage($imageId, $userId) {
        $check = $this->db->prepare("
            SELECT ha.MaHinhAnh FROM HinhAnh ha
            JOIN BaiViet bv ON ha.MaBaiViet = bv.MaBaiViet
            WHERE ha.MaHinhAnh = :imgId AND bv.MaNguoiDung = :uid
        ");
        $check->execute([':imgId' => $imageId, ':uid' => $userId]);
        if ($check->rowCount() == 0) return false;
        $stmt = $this->db->prepare("DELETE FROM HinhAnh WHERE MaHinhAnh = :imgId");
        return $stmt->execute([':imgId' => $imageId]);
    }

    public function deleteComment($commentId, $userId) {
        $check = $this->db->prepare("SELECT bl.MaNguoiDung FROM BinhLuan bl WHERE bl.MaBinhLuan = :cid");
        $check->execute([':cid' => $commentId]);
        $owner = $check->fetchColumn();
        if ($owner != $userId) return false;
        $stmt = $this->db->prepare("DELETE FROM BinhLuan WHERE MaBinhLuan = :cid");
        return $stmt->execute([':cid' => $commentId]);
    }
    
    public function updateComment($commentId, $userId, $content) {
        $check = $this->db->prepare("SELECT MaNguoiDung FROM BinhLuan WHERE MaBinhLuan = :cid");
        $check->execute([':cid' => $commentId]);
        $owner = $check->fetchColumn();
        if ($owner != $userId) return false;
        $stmt = $this->db->prepare("UPDATE BinhLuan SET NoiDung = :content WHERE MaBinhLuan = :cid");
        return $stmt->execute([':content' => $content, ':cid' => $commentId]);
    }
    
    public function saveImages($postId, $imageUrls) {
        $sql = "INSERT INTO HinhAnh (DuongDanURL, MaBaiViet) VALUES (:url, :postid)";
        $stmt = $this->db->prepare($sql);
        foreach ($imageUrls as $url) {
            $stmt->execute([':url' => $url, ':postid' => $postId]);
        }
        return true;
    }

    public function uploadMultipleImages($files) {
        $targetDir = ROOT_PATH . "/public/uploads/";
        if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);
        $uploadedUrls = [];
        foreach ($files['tmp_name'] as $key => $tmpName) {
            if ($files['error'][$key] == 0) {
                $fileName = time() . '_' . uniqid() . '_' . $files['name'][$key];
                $targetFile = $targetDir . $fileName;
                if (move_uploaded_file($tmpName, $targetFile)) {
                    $uploadedUrls[] = '/helios/public/uploads/' . $fileName;
                }
            }
        }
        return $uploadedUrls;
    }
}
?>