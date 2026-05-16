<?php
// app/Views/home.php
?>
<div class="container-xl py-3">
  <div class="row g-3 align-items-start">

    <?php include VIEW_PATH . '/layouts/home_sidebar_left.php'; ?>

    <main class="col-12 col-md-8 col-lg-6 align-self-start">
      
      <!-- Khung soạn bài (có avatar) -->
      <div class="h-card mb-3 composer-card">
        <div class="composer-top px-3 pt-3 pb-2">
          <div class="d-flex gap-2 align-items-center">
            <!-- Avatar ở đây -->
            <div class="composer-avatar flex-shrink-0" style="background: linear-gradient(135deg, #15cbb7, #0d9488);">VY</div>
            <div class="composer-input-fake" data-bs-toggle="modal" data-bs-target="#composeModal" style="cursor:pointer">
              Bạn muốn chia sẻ gì hôm nay?
            </div>
          </div>
        </div>
        <div class="composer-actions px-3 pb-3 pt-2">
          <div class="composer-actions-inner d-flex gap-2">
            <button type="button" class="btn btn-light btn-sm text-muted fw-bold composer-action-btn" data-bs-toggle="modal" data-bs-target="#composeModal">
              <i class="bi bi-image text-primary"></i><span>Ảnh</span>
            </button>
            <button type="button" class="btn btn-light btn-sm text-muted fw-bold composer-action-btn" data-bs-toggle="modal" data-bs-target="#composeModal">
              <i class="bi bi-calendar-event text-warning"></i><span>Sự kiện</span>
            </button>
            <button type="button" class="btn btn-light btn-sm text-muted fw-bold composer-action-btn" data-bs-toggle="modal" data-bs-target="#composeModal">
              <i class="bi bi-pencil-square text-danger"></i><span>Bài viết</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Feed bài viết -->
      <div id="feed-posts">
        <?php if (!empty($posts)): ?>
          <?php foreach ($posts as $post): 
            $isEvent = ($post['LoaiBaiViet'] == 'event');
            $isOwner = ($post['MaNguoiDung'] == 1);
          ?>
          <article class="h-card mb-3" data-post-id="<?= $post['MaBaiViet'] ?>">
            <div class="post-content-block px-3 pt-3">
              <div class="d-flex gap-2 mb-2 justify-content-between">
                <div class="d-flex gap-2 align-items-center">
                  <div class="post-avatar" style="width:40px; height:40px; background:#6c5ce7; display:flex; align-items:center; justify-content:center; border-radius:50%; color:white; font-weight:bold;">
                    <?= mb_substr(htmlspecialchars($post['HoTen']), 0, 1) ?>
                  </div>
                  <div>
                    <div class="fw-bold"><?= htmlspecialchars($post['HoTen']) ?></div>
                    <div class="text-muted extra-small">
                      <?= htmlspecialchars($post['TieuDe'] ?? '') ?>
                      · <?= date('d/m/Y H:i', strtotime($post['ThoiGianDang'])) ?>
                      <?= $post['TrangThai'] === 'Private' ? '🔒' : '🌐' ?>
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
                <!-- HIỂN THỊ ẢNH -->
                <?php 
                $images = isset($post['images']) && is_array($post['images']) ? $post['images'] : [];
                if (!empty($images)): ?>
                  <div class="mt-2 row g-2">
                    <?php foreach ($images as $index => $img): ?>
                      <div class="col-<?= count($images) == 1 ? '12' : (count($images) == 2 ? '6' : (count($images) == 3 && $index == 0 ? '12' : '6')) ?>">
                        <img src="<?= htmlspecialchars($img['DuongDanURL']) ?>" 
                             class="img-fluid rounded" 
                             style="max-height: 300px; width: 100%; object-fit: cover; cursor: pointer;"
                             onclick="openImageModal(this.src)">
                      </div>
                    <?php endforeach; ?>
                  </div>
                <?php endif; ?>
                
                <!-- Nội dung bài viết (bỏ icon thừa) -->
                <?php if (!empty($post['NoiDung'])): ?>
                  <p class="post-body mb-0 mt-2"><?= nl2br(htmlspecialchars($post['NoiDung'])) ?></p>
                <?php endif; ?>
              <?php endif; ?>

              <!-- Reaction counts -->
              <div class="reactions-row d-flex align-items-center gap-1 mt-2" 
                    id="reactions-<?= $post['MaBaiViet'] ?>">
                <?php 
                $reactionIcons = [
                  'Thích' => '<i class="bi bi-hand-thumbs-up-fill text-primary"></i>',
                  'Quan tâm' => '<i class="bi bi-heart-fill text-danger"></i>',
                  'Hữu ích' => '<i class="bi bi-lightbulb-fill text-warning"></i>',
                  'Chúc mừng' => '<i class="bi bi-trophy-fill text-success"></i>',
                ];
                foreach ($post['reactions_detail'] as $react): ?>
                  <span class="reaction-pill <?= 'r-' . strtolower(str_replace(' ', '', $react['LoaiTuongTac'])) ?>">
                    <?= $reactionIcons[$react['LoaiTuongTac']] ?> <?= $react['SoLuong'] ?>
                  </span>
                <?php endforeach; ?>
                <?php if (empty($post['reactions_detail'])): ?>
                  <span class="text-muted">0 phản ứng</span>
                <?php endif; ?>
              </div>

              <!-- Action buttons -->
              <div class="d-flex p-1">
                <div class="post-action-like-wrap flex-fill position-relative">
                  <div class="reaction-picker">
                    <button type="button" class="reaction-opt" data-reaction="Thích">
                      <i class="bi bi-hand-thumbs-up"></i><span>Thích</span>
                    </button>
                    <button type="button" class="reaction-opt" data-reaction="Quan tâm">
                      <i class="bi bi-heart"></i><span>Quan tâm</span>
                    </button>
                    <button type="button" class="reaction-opt" data-reaction="Hữu ích">
                      <i class="bi bi-lightbulb"></i><span>Hữu ích</span>
                    </button>
                    <button type="button" class="reaction-opt" data-reaction="Chúc mừng">
                      <i class="bi bi-trophy"></i><span>Chúc mừng</span>
                    </button>
                  </div>
                  <button class="post-action-btn w-100 btn-like" data-post-id="<?= $post['MaBaiViet'] ?>">
                    <i class="bi <?= $post['user_reaction'] ? 'bi-hand-thumbs-up-fill' : 'bi-hand-thumbs-up' ?>"></i> 
                    <?= $post['user_reaction'] ?: 'Thích' ?>
                  </button>
                </div>
                <button class="post-action-btn btn-toggle-comments" data-post-id="<?= $post['MaBaiViet'] ?>">
                  <i class="bi bi-chat-dots"></i> Bình luận 
                  <span class="comment-count"><?= $post['comment_count'] ?? 0 ?></span>
                </button>
                <button class="post-action-btn btn-share" data-post-id="<?= $post['MaBaiViet'] ?>">
                  <i class="bi bi-share"></i> Chia sẻ
                </button>
              </div>

              <!-- Comment section -->
              <div class="comment-section d-none p-3 border-top" id="comment-section-<?= $post['MaBaiViet'] ?>">
                <div class="comments-list mb-3" id="comments-<?= $post['MaBaiViet'] ?>"><div class="text-muted small">Đang tải...</div></div>
                <div class="d-flex gap-2">
                  <div class="composer-avatar" style="width:32px;height:32px;font-size:12px;">VY</div>
                  <div class="flex-grow-1">
                    <textarea class="form-control form-control-sm comment-input" rows="1" placeholder="Viết bình luận..."></textarea>
                    <button class="btn btn-sm btn-primary mt-1 btn-submit-comment" data-post-id="<?= $post['MaBaiViet'] ?>">Đăng</button>
                  </div>
                </div>
              </div>
            </div>
          </article>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="h-card p-4 text-center text-muted">Chưa có bài viết nào.</div>
        <?php endif; ?>
      </div>
    </main>

    <?php include VIEW_PATH . '/layouts/home_sidebar_right.php'; ?>
  </div>
</div>

<!-- MODAL ĐĂNG BÀI -->
<div class="modal fade" id="composeModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold">Tạo bài viết</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <ul class="nav nav-tabs border-bottom px-3">
        <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#postTab"><i class="bi bi-pencil-square text-danger"></i> Bài viết</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#eventTab"><i class="bi bi-calendar-event text-warning"></i> Sự kiện</button></li>
      </ul>
      <form action="<?= $baseUrl ?>home/create-post" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="post_type" id="postTypeInput" value="post">
        <div class="modal-body pt-3">
          <div class="d-flex gap-2 align-items-center mb-3">
            <div class="composer-avatar">VY</div>
            <div>
              <div class="fw-bold small">Trương Nhật Phương Vy</div>
              <select name="status" class="form-select form-select-sm w-auto mt-1">
                <option value="Public">🌐 Công khai</option>
                <option value="Private">🔒 Chỉ mình tôi</option>
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
        <div class="modal-footer border-0 pt-0"><button type="submit" class="btn btn-primary fw-bold w-100">Đăng</button></div>
      </form>
    </div>
  </div>
</div>

<!-- MODAL SỬA BÀI VIẾT (có quản lý ảnh) -->
<div class="modal fade" id="editPostModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square"></i> Chỉnh sửa bài viết</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <textarea id="editPostContent" class="form-control" rows="5" placeholder="Nội dung bài viết..."></textarea>
        <div class="mt-2">
          <label class="text-muted small">Trạng thái:</label>
          <select id="editPostStatus" class="form-select form-select-sm w-auto">
            <option value="Public">🌐 Công khai</option>
            <option value="Private">🔒 Chỉ mình tôi</option>
          </select>
        </div>
        
        <!-- Quản lý ảnh khi sửa -->
        <div class="mt-3">
          <label class="text-muted small fw-bold">Ảnh hiện tại:</label>
          <div id="editImagesContainer" class="d-flex flex-wrap gap-2 mt-2"></div>
          <div class="mt-2">
            <label class="btn btn-outline-secondary btn-sm">
              <i class="bi bi-plus-circle"></i> Thêm ảnh mới
              <input type="file" id="editNewImages" style="display:none;" accept="image/*" multiple>
            </label>
            <span class="small text-muted ms-2" id="editImageCountText"></span>
          </div>
          <div id="editNewPreviewContainer" class="d-flex flex-wrap gap-2 mt-2"></div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
        <button type="button" class="btn btn-primary" id="saveEditPostBtn">Lưu thay đổi</button>
      </div>
    </div>
  </div>
</div>

<!-- MODAL CHIA SẺ (Popup có link + nút sao chép) -->
<div class="modal fade" id="shareModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-share-fill me-2"></i>Chia sẻ bài viết</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p class="small text-muted mb-2">Link bài viết</p>
        <div class="input-group">
          <input type="text" class="form-control" id="shareLinkInput" readonly>
          <button class="btn btn-primary" id="copyShareLinkBtn">
            <i class="bi bi-clipboard"></i> Sao chép
          </button>
        </div>
        <div id="copySuccessMsg" class="text-success small mt-2 d-none">
          ✓ Đã sao chép liên kết
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function openImageModal(src) {
  const modalHtml = `<div class="modal fade" id="imgModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered modal-xl"><div class="modal-content bg-transparent border-0"><div class="modal-body text-center"><img src="${src}" class="img-fluid rounded" style="max-height:90vh;"><button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal"></button></div></div></div></div>`;
  document.body.insertAdjacentHTML('beforeend', modalHtml);
  new bootstrap.Modal(document.getElementById('imgModal')).show();
  document.getElementById('imgModal').addEventListener('hidden.bs.modal', function() { this.remove(); });
}

// Preview nhiều ảnh khi đăng bài
const imagesInput = document.getElementById('postImagesInput');
const previewContainer = document.getElementById('imagePreviewContainer');
const imageCountText = document.getElementById('imageCountText');
let selectedFiles = [];

if (imagesInput) {
  imagesInput.addEventListener('change', function(e) {
    const files = Array.from(e.target.files);
    if (files.length > 10) {
      alert('Chỉ được chọn tối đa 10 ảnh!');
      imagesInput.value = '';
      return;
    }
    selectedFiles = files;
    imageCountText.textContent = `${files.length}/10 ảnh`;
    previewContainer.innerHTML = '';
    files.forEach((file, index) => {
      const reader = new FileReader();
      reader.onload = function(ev) {
        const previewDiv = document.createElement('div');
        previewDiv.className = 'position-relative';
        previewDiv.style.width = '100px';
        previewDiv.style.height = '100px';
        previewDiv.innerHTML = `
          <img src="${ev.target.result}" class="img-fluid rounded" style="width:100%; height:100%; object-fit:cover;">
          <button type="button" class="btn-close position-absolute top-0 end-0 bg-white rounded-circle m-1" style="width:20px; height:20px;" onclick="removeImage(${index})"></button>
        `;
        previewContainer.appendChild(previewDiv);
      };
      reader.readAsDataURL(file);
    });
  });
}

function removeImage(index) {
  selectedFiles.splice(index, 1);
  const dataTransfer = new DataTransfer();
  selectedFiles.forEach(file => dataTransfer.items.add(file));
  imagesInput.files = dataTransfer.files;
  imageCountText.textContent = `${selectedFiles.length}/10 ảnh`;
  imagesInput.dispatchEvent(new Event('change'));
}
</script>