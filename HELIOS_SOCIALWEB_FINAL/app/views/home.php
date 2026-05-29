<?php
// app/views/home.php
$currentUserId = $currentUserId ?? ($_SESSION['user_id'] ?? null);
?>
<div class="container-xl py-3">
  <div class="row g-3 align-items-start">
    <?php include VIEW_PATH_APP . '/layouts/sidebar_left.php'; ?>

    <main class="col-12 col-md-8 col-lg-6 align-self-start">
      <?php include VIEW_PATH_APP . '/home-create.php'; ?>

      <div id="feed-posts">
        <?php if (!empty($posts)): ?>
          <?php foreach ($posts as $post): ?>
            <?php include VIEW_PATH_APP . '/home-post.php'; ?>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="h-card p-4 text-center text-muted">Chưa có bài viết nào.</div>
        <?php endif; ?>
      </div>
    </main>

    <?php include VIEW_PATH_APP . '/layouts/sidebar_right.php'; ?>
  </div>
</div>

<?php include VIEW_PATH_APP . '/home-edit.php'; ?>

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
        <div id="copySuccessMsg" class="text-success small mt-2 d-none">Đã sao chép liên kết</div>
      </div>
    </div>
  </div>
</div>
