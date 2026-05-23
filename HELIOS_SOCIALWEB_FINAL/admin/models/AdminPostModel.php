<?php
// app/models/AdminPostModel.php

class AdminPostModel extends Database {
    /* Lấy tất cả bài viết */
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
        
        $sql .= " ORDER BY bv.ThoiGianDang DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* Lấy chi tiết bài viết theo ID */
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

    /* Cập nhật bài viết */
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
                    $id
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

    /* Xóa bài viết */
    public function deletePost($id) {
        $stmt = $this->db->prepare("DELETE FROM BaiViet WHERE MaBaiViet = ?");
        return $stmt->execute([$id]);
    }

    /* Lấy danh sách loại bài viết */
    public function getPostTypes() {
        $stmt = $this->db->query("SELECT DISTINCT LoaiBaiViet FROM BaiViet WHERE LoaiBaiViet IS NOT NULL");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /* Lấy thống kê */
    public function getStatistics() {
        $stmt = $this->db->query("SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN LoaiBaiViet = 'event' THEN 1 ELSE 0 END) as events,
            SUM(CASE WHEN LoaiBaiViet = 'post' THEN 1 ELSE 0 END) as posts
        FROM BaiViet");
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /* Lấy số lượt tương tác của bài viết */
    public function getPostStats($postId) {
        // Đếm lượt thích (tất cả loại tương tác)
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM TuongTac WHERE MaBaiViet = ?");
        $stmt->execute([$postId]);
        $likes = (int)$stmt->fetchColumn();

        // Đếm bình luận
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM BinhLuan WHERE MaBaiViet = ?");
        $stmt->execute([$postId]);
        $comments = (int)$stmt->fetchColumn();

        // Bảng ChiaSe chưa có trong CSDL, tạm thời để 0
        $shares = 0;

        return compact('likes', 'comments', 'shares');
    }

    /* Tạo bài viết mới */
    public function createPost($data) {
        // Dùng MaNguoiDung từ session (khớp với cột trong bảng BaiViet và NguoiDung)
        // Fallback về 1 nếu chưa có auth
        $adminId = $_SESSION['MaNguoiDung'] ?? 3;

        try {
            if ($data['post_type'] === 'event') {
                // TrangThai là tên cột đúng (thấy trong getAllPosts alias visibility)
                $sql = "INSERT INTO BaiViet (MaNguoiDung, NoiDung, LoaiBaiViet, TrangThai, TenSuKien, DiaDiemSuKien, ThoiGianSuKien)
                        VALUES (?, ?, 'event', ?, ?, ?, ?)";
                $stmt = $this->db->prepare($sql);
                return $stmt->execute([
                    $adminId,
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
                return $stmt->execute([
                    $adminId,
                    $data['content'],
                    $data['visibility'],
                ]);
            }
        } catch (PDOException $e) {
            // Ghi lỗi ra log thay vì crash — giúp debug dễ hơn
            error_log('AdminPostModel::createPost error: ' . $e->getMessage());
            return false;
        }
    }
}
?>