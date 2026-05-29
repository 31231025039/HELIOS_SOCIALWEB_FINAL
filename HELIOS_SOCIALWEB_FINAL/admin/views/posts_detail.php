<?php
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="/helios/public/assets/css/admin.css">

<div class="admin-content">
    <div class="dashboard-heading">
        <div>
            <h1>Chi tiết bài viết</h1>
            <p>Xem thông tin, tương tác và nội dung</p>
        </div>
        <div>
            <a href="/helios/public/admin/posts" class="btn-secondary">← Quay lại danh sách</a>
        </div>
    </div>

    <?php if (isset($post) && $post): ?>
    <div class="detail-container">
        <div class="detail-card">
            <div class="detail-header">
                <div class="detail-avatar"><i class="bi bi-person-circle"></i></div>
                <div class="detail-author">
                    <strong><?= htmlspecialchars($post['author_name'] ?? 'Không xác định') ?></strong>
                    <span>ID: #<?= $post['id'] ?></span>
                </div>
                <div class="detail-type">
                    <?= $post['post_type'] == 'event' ? '<span class="badge-event">Sự kiện</span>' : '<span class="badge-post">Bài viết thường</span>' ?>
                </div>
            </div>

            <div class="detail-body">
                <?php if ($post['post_type'] == 'event' && !empty($post['event_name'])): ?>
                <div class="detail-event">
                    <h3><i class="bi bi-calendar-event"></i> <?= htmlspecialchars($post['event_name']) ?></h3>
                    <p><i class="bi bi-geo-alt"></i> <?= htmlspecialchars($post['event_location'] ?? 'Không có địa điểm') ?></p>
                    <p><i class="bi bi-clock"></i> <?= $post['event_time'] ? date('d/m/Y H:i', strtotime($post['event_time'])) : 'Không có thời gian' ?></p>
                </div>
                <?php endif; ?>

                <div class="detail-content">
                    <h3>Nội dung</h3>
                    <div class="content-box">
                        <?= nl2br(htmlspecialchars($post['content'] ?? 'Không có nội dung')) ?>
                    </div>
                </div>

                <?php if (!empty($post['images'])): ?>
                <div class="detail-images">
                    <h3>Hình ảnh</h3>
                    <div class="image-list">
                        <?php foreach ($post['images'] as $img): ?>
                            <img src="<?= htmlspecialchars($img) ?>" alt="Hình ảnh bài viết" onerror="this.src='https://placehold.co/400x250?text=No+Image'">
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <div class="detail-stats">
                <div class="stat-item">
                    <i class="bi bi-heart-fill"></i>
                    <span class="stat-number"><?= number_format($post['likes'] ?? 0) ?></span>
                    <span class="stat-label">Lượt thích</span>
                </div>
                <div class="stat-item">
                    <i class="bi bi-chat-fill"></i>
                    <span class="stat-number"><?= number_format($post['comments'] ?? 0) ?></span>
                    <span class="stat-label">Bình luận</span>
                </div>
            </div>

            <div class="detail-footer">
                <div><i class="bi bi-eye"></i> Hiển thị: <?= $post['visibility'] == 'Public' ? 'Công khai' : 'Riêng tư' ?></div>
                <div><i class="bi bi-calendar3"></i> Ngày đăng: <?= date('d/m/Y H:i:s', strtotime($post['created_at'])) ?></div>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="alert-error">Không tìm thấy bài viết</div>
    <?php endif; ?>
</div>