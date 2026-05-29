<?php

class AdminPostModel extends Database {

    public function getAllPosts($filters = []) {
        $sql = "SELECT 
                    bv.MaBaiViet as id,
                    bv.NoiDung as content,
                    bv.LoaiBaiViet as post_type,
                    bv.TenSuKien as event_name,
                    bv.DiaDiemSuKien as event_location,
                    bv.ThoiGianSuKien as event_time,
                    bv.TrangThai as visibility,
                    bv.ThoiGianDang as created_at,
                    nd.HoTen as author_name,
                    nd.MaNguoiDung as author_id
                FROM BaiViet bv
                LEFT JOIN NguoiDung nd ON bv.MaNguoiDung = nd.MaNguoiDung
                WHERE 1=1";

        $params = [];

        if (!empty($filters['keyword'])) {
            $keyword = '%' . $filters['keyword'] . '%';
            $sql .= " AND (bv.NoiDung LIKE ? OR nd.HoTen LIKE ?)";
            $params[] = $keyword;
            $params[] = $keyword;
        }

        if (!empty($filters['post_type'])) {
            $sql .= " AND bv.LoaiBaiViet = ?";
            $params[] = $filters['post_type'];
        }

        if (!empty($filters['visibility']) && $filters['visibility'] != 'all') {
            $sql .= " AND bv.TrangThai = ?";
            $params[] = $filters['visibility'];
        }

        $sql .= " ORDER BY bv.MaBaiViet DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPostById($id) {
        $sql = "SELECT 
                    bv.MaBaiViet as id,
                    bv.NoiDung as content,
                    bv.LoaiBaiViet as post_type,
                    bv.TenSuKien as event_name,
                    bv.DiaDiemSuKien as event_location,
                    bv.ThoiGianSuKien as event_time,
                    bv.TrangThai as visibility,
                    bv.ThoiGianDang as created_at,
                    nd.HoTen as author_name,
                    nd.MaNguoiDung as author_id,
                    nd.TieuDe as author_title
                FROM BaiViet bv
                LEFT JOIN NguoiDung nd ON bv.MaNguoiDung = nd.MaNguoiDung
                WHERE bv.MaBaiViet = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($post) {
            $imgStmt = $this->db->prepare("SELECT DuongDanURL FROM HinhAnh WHERE MaBaiViet = ?");
            $imgStmt->execute([$id]);
            $post['images'] = $imgStmt->fetchAll(PDO::FETCH_COLUMN);
        }

        return $post;
    }


    public function createPost($data) {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) return false;

        try {
            if ($data['post_type'] === 'event') {
                $sql = "INSERT INTO BaiViet (MaNguoiDung, NoiDung, LoaiBaiViet, TrangThai, TenSuKien, DiaDiemSuKien, ThoiGianSuKien)
                        VALUES (?, ?, 'event', ?, ?, ?, ?)";
                $stmt = $this->db->prepare($sql);
                $ok   = $stmt->execute([
                    $userId,
                    $data['content'],
                    $data['visibility'],
                    $data['event_name'],
                    $data['event_location'],
                    $data['event_time'],
                ]);
            } else {
                $sql = "INSERT INTO BaiViet (MaNguoiDung, NoiDung, LoaiBaiViet, TrangThai)
                        VALUES (?, ?, 'post', ?)";
                $stmt = $this->db->prepare($sql);
                $ok   = $stmt->execute([
                    $userId,
                    $data['content'],
                    $data['visibility'],
                ]);
            }
            return $ok ? (int)$this->db->lastInsertId() : false;

        } catch (PDOException $e) {
            error_log('AdminPostModel::createPost error: ' . $e->getMessage());
            return false;
        }
    }

    public function updatePost($id, $data) {
        try {
            if ($data['post_type'] === 'event') {
                $sql = "UPDATE BaiViet SET NoiDung=?, LoaiBaiViet='event', TrangThai=?,
                        TenSuKien=?, DiaDiemSuKien=?, ThoiGianSuKien=?
                        WHERE MaBaiViet=?";
                $stmt = $this->db->prepare($sql);
                return $stmt->execute([
                    $data['content'], $data['visibility'],
                    $data['event_name'], $data['event_location'], $data['event_time'],
                    $id,
                ]);
            } else {
                $sql = "UPDATE BaiViet SET NoiDung=?, LoaiBaiViet='post', TrangThai=?,
                        TenSuKien=NULL, DiaDiemSuKien=NULL, ThoiGianSuKien=NULL
                        WHERE MaBaiViet=?";
                $stmt = $this->db->prepare($sql);
                return $stmt->execute([$data['content'], $data['visibility'], $id]);
            }
        } catch (PDOException $e) {
            error_log('AdminPostModel::updatePost error: ' . $e->getMessage());
            return false;
        }
    }


    public function deletePost($id) {
        $stmt = $this->db->prepare("DELETE FROM BaiViet WHERE MaBaiViet = ?");
        return $stmt->execute([$id]);
    }

    public function getPostTypes() {
        $stmt = $this->db->query("SELECT DISTINCT LoaiBaiViet FROM BaiViet WHERE LoaiBaiViet IS NOT NULL");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getStatistics() {
        $stmt = $this->db->query("SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN LoaiBaiViet = 'event' THEN 1 ELSE 0 END) as events,
            SUM(CASE WHEN LoaiBaiViet = 'post'  THEN 1 ELSE 0 END) as posts
        FROM BaiViet");
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getPostStats($postId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM TuongTac WHERE MaBaiViet = ?");
        $stmt->execute([$postId]);
        $likes = (int)$stmt->fetchColumn();

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM BinhLuan WHERE MaBaiViet = ?");
        $stmt->execute([$postId]);
        $comments = (int)$stmt->fetchColumn();

        $shares = 0; 

        return compact('likes', 'comments', 'shares');
    }


    public function savePostImages($postId, array $urls) {
        $stmt = $this->db->prepare(
            "INSERT INTO HinhAnh (MaBaiViet, DuongDanURL) VALUES (?, ?)"
        );
        foreach ($urls as $url) {
            $stmt->execute([$postId, $url]);
        }
    }

    public function deletePostImage($postId, $url) {
        $stmt = $this->db->prepare(
            "DELETE FROM HinhAnh WHERE MaBaiViet = ? AND DuongDanURL = ?"
        );
        return $stmt->execute([$postId, $url]);
    }
}
?>
