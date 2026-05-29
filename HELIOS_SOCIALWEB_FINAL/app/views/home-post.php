<?php
/** home-post.php
 * Các biến được truyền vào từ file cha qua include():
 * @var array  $post          Bài viết hiện tại
 * @var int    $currentUserId ID người dùng đang đăng nhập
 * @var string $baseUrl       Đường dẫn gốc của ứng dụng
 */

// Khai báo mặc định để Intelephense không báo lỗi P1008 "biến chưa được định nghĩa"
$post          = $post          ?? [];
$currentUserId = $currentUserId ?? 0;
$baseUrl       = $baseUrl       ?? '';

// Kiểm tra loại bài viết: 'event' → hiển thị thẻ sự kiện, ngược lại hiển thị bài thường
$isEvent = ($post['LoaiBaiViet'] == 'event');

// Kiểm tra người đăng bài có phải chính mình không → hiện nút Sửa / Xóa
$isOwner = (int)$post['MaNguoiDung'] === (int)$currentUserId;

// Lấy chữ cái đầu của tên để làm avatar fallback khi không có ảnh đại diện
$authorInitial = mb_substr($post['HoTen'] ?? '?', 0, 1);

// Icon filled (màu) dùng khi hiển thị tổng số phản ứng dưới bài
$reactionIcons = [
    'Thích'    => '<i class="bi bi-hand-thumbs-up-fill text-primary"></i>',
    'Quan tâm' => '<i class="bi bi-heart-fill text-danger"></i>',
    'Hữu ích'  => '<i class="bi bi-lightbulb-fill text-warning"></i>',
    'Chúc mừng'=> '<i class="bi bi-trophy-fill text-success"></i>',
];

// Icon outline dùng trong reaction-picker (hover chọn loại phản ứng)
// Khi người dùng đã chọn, ta append '-fill' để chuyển sang icon filled
$reactionEmptyIcons = [
    'Thích'    => 'bi-hand-thumbs-up',
    'Quan tâm' => 'bi-heart',
    'Hữu ích'  => 'bi-lightbulb',
    'Chúc mừng'=> 'bi-trophy',
];

// Phản ứng hiện tại của người dùng với bài này (null/'' nếu chưa react)
$currentReaction = $post['user_reaction'] ?? '';

// Nếu đã react → dùng icon filled của loại đó; chưa react → mặc định thumbs-up outline
$currentReactionIcon = $currentReaction && isset($reactionEmptyIcons[$currentReaction])
    ? $reactionEmptyIcons[$currentReaction] . '-fill'
    : 'bi-hand-thumbs-up';
?>
<article class="h-card mb-3" data-post-id="<?= $post['MaBaiViet'] ?>">
  <div class="post-content-block px-3 pt-3">
    <div class="d-flex gap-2 mb-2 justify-content-between">
      <div class="d-flex gap-2 align-items-center">

        <div class="post-avatar" style="width:40px; height:40px; background:#6c5ce7; display:flex; align-items:center; justify-content:center; border-radius:50%; color:white; font-weight:bold; overflow:hidden;">
          <?php if (!empty($post['AnhDaiDien'])): ?>
            <img src="<?= $baseUrl . htmlspecialchars(ltrim($post['AnhDaiDien'], '/')) ?>" style="width:100%; height:100%; object-fit:cover;">
          <?php else: ?>
            <?= htmlspecialchars($authorInitial) ?>
          <?php endif; ?>
        </div>
       
        <div>
          <div class="fw-bold"><?= htmlspecialchars($post['HoTen']) ?></div>
          <div class="text-muted extra-small">
            <?= htmlspecialchars($post['TieuDe'] ?? '') ?>
            · <?= date('d/m/Y H:i', strtotime($post['ThoiGianDang'])) ?>
            <?= $post['TrangThai'] === 'Private' ? '<i class="bi bi-lock-fill"></i>' : '<i class="bi bi-globe2"></i>' ?>
            <?php if ($isEvent): ?>
              <span class="badge bg-warning text-dark ms-1"><i class="bi bi-calendar-event"></i> Sự kiện</span>
            <?php endif; ?>
          </div>
        </div>
      </div>
      
      <?php if ($isOwner): ?>
        <div class="dropdown">
          <button class="btn btn-sm btn-link text-muted" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></button>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><button class="dropdown-item btn-edit-post" data-post-id="<?= $post['MaBaiViet'] ?>"><i class="bi bi-pencil me-2"></i>Chỉnh sửa</button></li>
            <li><hr class="dropdown-divider"></li>
            <li><button class="dropdown-item text-danger btn-delete-post" data-post-id="<?= $post['MaBaiViet'] ?>"><i class="bi bi-trash me-2"></i>Xóa</button></li>
          </ul>
        </div>
      <?php endif; ?>
    </div>

    <?php if ($isEvent): ?>
      <div class="event-card">
        <h6><i class="bi bi-megaphone-fill"></i> <?= htmlspecialchars($post['TenSuKien'] ?? 'Sự kiện') ?></h6>
        <div class="event-detail"><?= nl2br(htmlspecialchars($post['NoiDung'])) ?></div>
        <?php if ($post['DiaDiemSuKien']): ?>
          <div class="event-detail"><i class="bi bi-geo-alt-fill"></i> <?= htmlspecialchars($post['DiaDiemSuKien']) ?></div>
        <?php endif; ?>
        <?php if ($post['ThoiGianSuKien']): ?>
          <div class="event-detail"><i class="bi bi-clock-fill"></i> <?= date('H:i d/m/Y', strtotime($post['ThoiGianSuKien'])) ?></div>
        <?php endif; ?>
      </div>
    <?php else: ?>
      <?php $images = isset($post['images']) && is_array($post['images']) ? $post['images'] : []; ?>
      <?php if (!empty($images)): ?>
        <div class="mt-2 row g-2">
          <?php foreach ($images as $index => $img): ?>
            <div class="col-<?= count($images) == 1 ? '12' : (count($images) == 2 ? '6' : (count($images) == 3 && $index == 0 ? '12' : '6')) ?>">
              <img src="<?= htmlspecialchars($img['DuongDanURL']) ?>" class="img-fluid rounded post-image" style="max-height: 300px; width: 100%; object-fit: cover; cursor: pointer;" alt="Ảnh bài viết">
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
      <?php if (!empty($post['NoiDung'])): ?>
        <p class="post-body mb-0 mt-2"><?= nl2br(htmlspecialchars($post['NoiDung'])) ?></p>
      <?php endif; ?>
    <?php endif; ?>

    <div class="reactions-row d-flex align-items-center gap-1 mt-2" id="reactions-<?= $post['MaBaiViet'] ?>">
      <?php foreach ($post['reactions_detail'] as $react): ?>
        <span class="reaction-pill"><?= $reactionIcons[$react['LoaiTuongTac']] ?? '' ?> <?= $react['SoLuong'] ?></span>
      <?php endforeach; ?>
      <?php if (empty($post['reactions_detail'])): ?>
        <span class="text-muted">0 phản ứng</span>
      <?php endif; ?>
    </div>

    <div class="d-flex p-1">
      <div class="post-action-like-wrap flex-fill position-relative">
        <div class="reaction-picker">
          <?php foreach ($reactionEmptyIcons as $label => $icon): ?>
            <button type="button" class="reaction-opt reaction-<?= strtolower(str_replace(' ', '-', $label)) ?>" data-reaction="<?= htmlspecialchars($label) ?>">
              <i class="bi <?= $icon ?>"></i><span><?= htmlspecialchars($label) ?></span>
            </button>
          <?php endforeach; ?>
        </div>
        <button class="post-action-btn w-100 btn-like <?= $currentReaction ? 'is-reacted' : '' ?>" data-post-id="<?= $post['MaBaiViet'] ?>">
          <i class="bi <?= $currentReactionIcon ?>"></i> <span><?= htmlspecialchars($currentReaction ?: 'Thích') ?></span>
        </button>
      </div>
      <button class="post-action-btn btn-toggle-comments" data-post-id="<?= $post['MaBaiViet'] ?>">
        <i class="bi bi-chat-dots"></i> Bình luận <span class="comment-count"><?= $post['comment_count'] ?? 0 ?></span>
      </button>
      <button class="post-action-btn btn-share" data-post-id="<?= $post['MaBaiViet'] ?>">
        <i class="bi bi-share"></i> Chia sẻ
      </button>
    </div>

    <?php include VIEW_PATH_APP . '/home-comment.php'; ?>
  </div>
</article>