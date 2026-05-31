<?php
$baseUrl = $baseUrl ?? '/helios/public/'; 

$viewerName = $loggedInUserData['HoTen'] ?? ($_SESSION['user_name'] ?? 'Bạn');
$viewerAvatar = $loggedInUserData['AnhDaiDien'] ?? '';
$viewerInitials = '';
$nameParts = preg_split('/\s+/', trim($viewerName));

if (!empty($nameParts[0])) {
    $viewerInitials = mb_substr($nameParts[0], 0, 1) . mb_substr(end($nameParts), 0, 1);
}
$viewerInitials = strtoupper($viewerInitials ?: '??');
?>

<div class="h-card mb-3 composer-card">
  <div class="composer-top px-3 pt-3 pb-2">
    <div class="d-flex gap-2 align-items-center">
      <div class="composer-avatar flex-shrink-0" style="background-color: transparent; color: inherit;">
        <?php if (!empty($viewerAvatar)): ?>
          <img src="<?= $baseUrl . htmlspecialchars($viewerAvatar) ?>" alt="Avatar" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
        <?php else: ?>
          <div class="d-flex align-items-center justify-content-center w-100 h-100" style="background-color: #062b6b; color: white; border-radius: 50%;"><?= htmlspecialchars($viewerInitials) ?></div>
        <?php endif; ?>
      </div>
      <div class="composer-input-fake" data-bs-toggle="modal" data-bs-target="#composeModal" style="cursor:pointer">
        Bạn muốn chia sẻ gì hôm nay?
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="composeModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold">Tạo bài viết</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <ul class="nav nav-tabs border-bottom px-3">
        <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#postTab"><i class="bi bi-pencil-square text-danger"></i> Bài viết</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#eventTab"><i class="bi bi-calendar-event text-warning"></i> Sự kiện</button></li>
      </ul>

      <form action="<?= $baseUrl ?>home/create-post" method="POST" enctype="multipart/form-data" id="createPostForm">
        <input type="hidden" name="post_type" id="postTypeInput" value="post">
        <div class="modal-body pt-3">
          <div class="d-flex gap-2 align-items-center mb-3">
            <div class="composer-avatar"><?= htmlspecialchars($viewerInitials) ?></div>
            <div>
              <div class="fw-bold small"><?= htmlspecialchars($viewerName) ?></div>
              <select name="status" class="form-select form-select-sm w-auto mt-1">
                <option value="Public">Công khai</option>
                <option value="Private">Chỉ mình tôi</option>
              </select>
            </div>
          </div>

          <div class="tab-content">
            <div class="tab-pane fade show active" id="postTab">
              <textarea class="form-control border-0 fs-5" name="content" rows="5" placeholder="Bạn đang nghĩ gì?" style="resize:none;"></textarea>
              <div class="mt-2">
                <label class="btn btn-outline-secondary btn-sm">
                  <i class="bi bi-images"></i> Thêm ảnh (tối đa 10)
                  <input type="file" name="post_images[]" id="postImagesInput" style="display:none;" accept="image/*" multiple>
                </label>
                <span class="small text-muted ms-2" id="imageCountText">0/10 ảnh</span>
                <div id="imagePreviewContainer" class="mt-2 d-flex flex-wrap gap-2"></div>
              </div>
            </div>

            <div class="tab-pane fade" id="eventTab">
              <input type="text" name="event_title" class="form-control mb-2" placeholder="Tên sự kiện (bắt buộc)">
              <textarea name="event_description" class="form-control mb-2" rows="3" placeholder="Mô tả"></textarea>
              <input type="text" name="event_location" class="form-control mb-2" placeholder="Địa điểm">
              <input type="datetime-local" name="event_time" class="form-control">
            </div>

          </div>
        </div>
        
        <div class="modal-footer border-0 pt-0">
          <button type="submit" class="btn btn-primary fw-bold w-100">Đăng</button>
        </div>
      </form>
    </div>
  </div>
</div>
