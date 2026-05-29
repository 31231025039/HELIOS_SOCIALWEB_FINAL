<?php
class PostModel extends Database
{
    public function getFeed($userId, $limit = null)
    {
        $sql = "SELECT
                    bv.*,
                    nd.HoTen, nd.TieuDe, nd.AnhDaiDien,
                    (SELECT COUNT(*) FROM TuongTac tt WHERE tt.MaBaiViet = bv.MaBaiViet) AS SoTuongTac,
                    (SELECT COUNT(*) FROM BinhLuan bl
                        WHERE bl.MaBaiViet = bv.MaBaiViet
                          AND COALESCE(bl.TrangThaiBinhLuan, 'Hien') = 'Hien') AS SoBinhLuan,
                    (SELECT DuongDanURL FROM HinhAnh ha WHERE ha.MaBaiViet = bv.MaBaiViet LIMIT 1) AS AnhDauTien
                FROM BaiViet bv
                JOIN NguoiDung nd ON bv.MaNguoiDung = nd.MaNguoiDung
                WHERE bv.TrangThai = 'Public' OR bv.MaNguoiDung = :uid
                ORDER BY bv.ThoiGianDang DESC, bv.MaBaiViet DESC";

        if ($limit !== null) $sql .= " LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':uid', $userId);
        if ($limit !== null) $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // FIX: thêm mới — HomeController.index() thiếu $upcomingEvents nên sidebar không hiện sự kiện
    public function getUpcomingEvents($limit = 5)
    {
        $stmt = $this->db->prepare(
            "SELECT TenSuKien, ThoiGianSuKien, DiaDiemSuKien, NoiDung AS MoTa
             FROM BaiViet
             WHERE LoaiBaiViet = 'event'
               AND TrangThai   = 'Public'
               AND ThoiGianSuKien >= NOW()
             ORDER BY ThoiGianSuKien ASC
             LIMIT :limit"
        );
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($userId, $data)
    {
        $type = $data['type'] ?? 'post';

        if ($type === 'event') {
            $content   = $data['event_description'] ?? '';
            $title     = !empty($data['event_title']) ? $data['event_title'] : 'Sự kiện không tên';
            $location  = $data['event_location'] ?? null;
            $eventTime = $this->normalizeEventTime($data['event_time'] ?? null);
        } else {
            $content = $data['content'] ?? '';
            $title = $location = $eventTime = null;
        }

        $stmt = $this->db->prepare(
            "INSERT INTO BaiViet (NoiDung, TrangThai, MaNguoiDung, LoaiBaiViet, TenSuKien, DiaDiemSuKien, ThoiGianSuKien)
             VALUES (:content, :status, :uid, :type, :title, :location, :event_time)"
        );
        $stmt->bindParam(':content',    $content);
        $stmt->bindParam(':status',     $data['status']);
        $stmt->bindParam(':uid',        $userId);
        $stmt->bindParam(':type',       $type);
        $stmt->bindParam(':title',      $title);
        $stmt->bindParam(':location',   $location);
        $stmt->bindParam(':event_time', $eventTime);
        $stmt->execute();

        $postId = $this->db->lastInsertId();

        if ($type === 'post' && !empty($_FILES['post_images']['name'][0])) {
            $urls = $this->uploadMultipleImages($_FILES['post_images']);
            if (!empty($urls)) $this->saveImages($postId, $urls);
        }

        return $postId;
    }

    public function getPostById($postId)
    {
        $stmt = $this->db->prepare(
            "SELECT bv.*, nd.HoTen, nd.TieuDe, nd.AnhDaiDien
             FROM BaiViet bv
             JOIN NguoiDung nd ON bv.MaNguoiDung = nd.MaNguoiDung
             WHERE bv.MaBaiViet = :pid"
        );
        $stmt->execute([':pid' => $postId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getPostOwnerId($postId)
    {
        $stmt = $this->db->prepare("SELECT MaNguoiDung FROM BaiViet WHERE MaBaiViet = :pid");
        $stmt->execute([':pid' => $postId]);
        return (int)$stmt->fetchColumn();
    }

    public function getCommentOwnerId($commentId)
    {
        $stmt = $this->db->prepare("SELECT MaNguoiDung FROM BinhLuan WHERE MaBinhLuan = :cid");
        $stmt->execute([':cid' => $commentId]);
        return (int)$stmt->fetchColumn();
    }

    public function updatePost($postId, $userId, $content, $status)
    {
        $check = $this->db->prepare("SELECT MaNguoiDung FROM BaiViet WHERE MaBaiViet = :pid");
        $check->execute([':pid' => $postId]);
        if ($check->fetchColumn() != $userId) return false;

        $stmt = $this->db->prepare("UPDATE BaiViet SET NoiDung = :content, TrangThai = :status WHERE MaBaiViet = :pid");
        return $stmt->execute([':content' => $content, ':status' => $status, ':pid' => $postId]);
    }

    public function deletePost($postId, $userId)
    {
        $check = $this->db->prepare("SELECT MaNguoiDung FROM BaiViet WHERE MaBaiViet = :pid");
        $check->execute([':pid' => $postId]);
        if ($check->fetchColumn() != $userId) return false;

        $this->db->beginTransaction();
        foreach (['HinhAnh', 'TuongTac', 'BinhLuan', 'BaiViet'] as $table) {
            $this->db->prepare("DELETE FROM $table WHERE MaBaiViet = :pid")->execute([':pid' => $postId]);
        }
        $this->db->commit();
        return true;
    }

    public function getUserReaction($userId, $postId)
    {
        $stmt = $this->db->prepare("SELECT LoaiTuongTac FROM TuongTac WHERE MaNguoiDung = :uid AND MaBaiViet = :pid LIMIT 1");
        $stmt->execute([':uid' => $userId, ':pid' => $postId]);
        return $stmt->fetchColumn();
    }

    public function getAllReactions($postId)
    {
        $stmt = $this->db->prepare("SELECT LoaiTuongTac, COUNT(*) AS SoLuong FROM TuongTac WHERE MaBaiViet = :pid GROUP BY LoaiTuongTac");
        $stmt->execute([':pid' => $postId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countReactions($postId)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM TuongTac WHERE MaBaiViet = :pid");
        $stmt->execute([':pid' => $postId]);
        return $stmt->fetchColumn();
    }

    public function setReaction($userId, $postId, $newType)
    {
        $stmt = $this->db->prepare("SELECT LoaiTuongTac FROM TuongTac WHERE MaNguoiDung = :uid AND MaBaiViet = :pid");
        $stmt->execute([':uid' => $userId, ':pid' => $postId]);
        $current = $stmt->fetchColumn();

        $this->db->prepare("DELETE FROM TuongTac WHERE MaNguoiDung = :uid AND MaBaiViet = :pid")
                 ->execute([':uid' => $userId, ':pid' => $postId]);

        if ($current === $newType) return 'removed';

        $this->db->prepare("INSERT INTO TuongTac (MaNguoiDung, MaBaiViet, LoaiTuongTac) VALUES (:uid, :pid, :type)")
                 ->execute([':uid' => $userId, ':pid' => $postId, ':type' => $newType]);
        return 'changed';
    }

    public function countComments($postId)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM BinhLuan WHERE MaBaiViet = :pid AND TrangThaiBinhLuan = 'Hien'");
        $stmt->execute([':pid' => $postId]);
        return $stmt->fetchColumn();
    }

    public function getComments($postId, $viewerId = null)
    {
        $stmt = $this->db->prepare(
            "SELECT bl.*, nd.HoTen, nd.AnhDaiDien, bv.MaNguoiDung AS MaChuBaiViet
             FROM BinhLuan bl
             JOIN NguoiDung nd ON bl.MaNguoiDung = nd.MaNguoiDung
             JOIN BaiViet   bv ON bl.MaBaiViet    = bv.MaBaiViet
             WHERE bl.MaBaiViet = :pid AND TrangThaiBinhLuan = 'Hien'
             ORDER BY bl.ThoiGianDang ASC"
        );
        $stmt->execute([':pid' => $postId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addComment($userId, $postId, $content, $parentCommentId = null)
    {
        $parentCommentId = $parentCommentId ? (int)$parentCommentId : null;
        if ($parentCommentId && !$this->isCommentVisibleInPost($parentCommentId, $postId)) return false;

        $stmt = $this->db->prepare("INSERT INTO BinhLuan (NoiDung, MaBaiViet, MaNguoiDung, MaBinhLuanCha) VALUES (:content, :pid, :uid, :parent_id)");
        $stmt->execute([':content' => $content, ':pid' => $postId, ':uid' => $userId, ':parent_id' => $parentCommentId]);
        return $this->db->lastInsertId();
    }

    public function deleteComment($commentId, $userId)
    {
        $check = $this->db->prepare("SELECT MaNguoiDung FROM BinhLuan WHERE MaBinhLuan = :cid");
        $check->execute([':cid' => $commentId]);
        if ($check->fetchColumn() != $userId) return false;

        $ids = $this->getCommentTreeIds($commentId);
        $ph  = implode(',', array_fill(0, count($ids), '?'));
        return $this->db->prepare("DELETE FROM BinhLuan WHERE MaBinhLuan IN ($ph)")->execute($ids);
    }

    public function updateComment($commentId, $userId, $content)
    {
        $check = $this->db->prepare("SELECT MaNguoiDung FROM BinhLuan WHERE MaBinhLuan = :cid");
        $check->execute([':cid' => $commentId]);
        if ($check->fetchColumn() != $userId) return false;

        $stmt = $this->db->prepare("UPDATE BinhLuan SET NoiDung = :content WHERE MaBinhLuan = :cid");
        return $stmt->execute([':content' => $content, ':cid' => $commentId]);
    }

    public function hideComment($commentId, $userId)
    {
        $check = $this->db->prepare(
            "SELECT bv.MaNguoiDung FROM BinhLuan bl JOIN BaiViet bv ON bl.MaBaiViet = bv.MaBaiViet WHERE bl.MaBinhLuan = :cid"
        );
        $check->execute([':cid' => $commentId]);
        if ((int)$check->fetchColumn() !== (int)$userId) return false;

        $ids = $this->getCommentTreeIds($commentId);
        $ph  = implode(',', array_fill(0, count($ids), '?'));
        return $this->db->prepare(
            "UPDATE BinhLuan SET TrangThaiBinhLuan = 'An', AnBoiNguoiDung = ?, ThoiGianBiAn = NOW() WHERE MaBinhLuan IN ($ph)"
        )->execute(array_merge([$userId], $ids));
    }

    public function canManageComment($commentId, $userId)
    {
        $stmt = $this->db->prepare(
            "SELECT bv.MaNguoiDung FROM BinhLuan bl JOIN BaiViet bv ON bl.MaBaiViet = bv.MaBaiViet WHERE bl.MaBinhLuan = :cid"
        );
        $stmt->execute([':cid' => $commentId]);
        return (int)$stmt->fetchColumn() === (int)$userId;
    }

    public function getImagesByPostId($postId)
    {
        $stmt = $this->db->prepare("SELECT * FROM HinhAnh WHERE MaBaiViet = :pid ORDER BY MaHinhAnh ASC");
        $stmt->execute([':pid' => $postId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function saveImages($postId, $imageUrls)
    {
        $stmt = $this->db->prepare("INSERT INTO HinhAnh (DuongDanURL, MaBaiViet) VALUES (:url, :postid)");
        foreach ($imageUrls as $url) $stmt->execute([':url' => $url, ':postid' => $postId]);
        return true;
    }

    public function deleteImage($imageId, $userId)
    {
        $check = $this->db->prepare(
            "SELECT ha.MaHinhAnh FROM HinhAnh ha JOIN BaiViet bv ON ha.MaBaiViet = bv.MaBaiViet
             WHERE ha.MaHinhAnh = :imgId AND bv.MaNguoiDung = :uid"
        );
        $check->execute([':imgId' => $imageId, ':uid' => $userId]);
        if ($check->rowCount() === 0) return false;

        return $this->db->prepare("DELETE FROM HinhAnh WHERE MaHinhAnh = :imgId")->execute([':imgId' => $imageId]);
    }

    public function uploadMultipleImages($files)
    {
        $targetDir = ROOT_PATH . '/public/uploads/';
        if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);

        $uploadedUrls = [];
        foreach ($files['tmp_name'] as $key => $tmpName) {
            if ($files['error'][$key] === 0) {
                $fileName = time() . '_' . uniqid() . '_' . $files['name'][$key];
                if (move_uploaded_file($tmpName, $targetDir . $fileName)) {
                    $uploadedUrls[] = '/helios/public/uploads/' . $fileName;
                }
            }
        }
        return $uploadedUrls;
    }

    private function normalizeEventTime($value, $fallback = null)
    {
        $value = trim((string)$value);
        if ($value === '') return null;

        $value = str_replace('T', ' ', $value);
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value))
            return $value . ' ' . ($fallback ? date('H:i:s', strtotime($fallback)) : date('H:i:s'));
        if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}$/', $value)) $value .= ':00';
        if ($fallback && date('H:i:s', strtotime($value)) === '00:00:00')
            return date('Y-m-d', strtotime($value)) . ' ' . date('H:i:s', strtotime($fallback));

        return date('Y-m-d H:i:s', strtotime($value));
    }

    private function isCommentVisibleInPost($commentId, $postId)
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM BinhLuan WHERE MaBinhLuan = :cid AND MaBaiViet = :pid AND TrangThaiBinhLuan = 'Hien'"
        );
        $stmt->execute([':cid' => $commentId, ':pid' => $postId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    private function getCommentTreeIds($commentId)
    {
        $ids = $cursor = [(int)$commentId];
        while (!empty($cursor)) {
            $ph   = implode(',', array_fill(0, count($cursor), '?'));
            $stmt = $this->db->prepare("SELECT MaBinhLuan FROM BinhLuan WHERE MaBinhLuanCha IN ($ph)");
            $stmt->execute($cursor);
            $children = array_values(array_diff(array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN)), $ids));
            if (empty($children)) break;
            $ids    = array_merge($ids, $children);
            $cursor = $children;
        }
        return $ids;
    }
}
?>
