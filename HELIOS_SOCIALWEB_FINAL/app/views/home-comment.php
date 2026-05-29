<div class="comment-section d-none p-3 border-top" id="comment-section-<?= $post['MaBaiViet'] ?>">
  <div class="comments-list mb-3" id="comments-<?= $post['MaBaiViet'] ?>">
    <div class="text-muted small">Đang tải...</div>
  </div>
  <div class="replying-to small text-primary mb-2 d-none" data-reply-label>
    Đang trả lời <strong></strong>
    <button type="button" class="btn btn-link btn-sm p-0 ms-1 btn-cancel-reply">Hủy</button>
  </div>
  <div class="d-flex gap-2">
    <div class="composer-avatar" style="width:32px;height:32px;font-size:12px;"><?= htmlspecialchars($viewerInitials ?? '??') ?></div>
    <div class="flex-grow-1">
      <textarea class="form-control form-control-sm comment-input" rows="1" placeholder="Viết bình luận..."></textarea>
      <input type="hidden" class="reply-parent-id" value="">
      <button class="btn btn-sm btn-primary mt-1 btn-submit-comment" data-post-id="<?= $post['MaBaiViet'] ?>">Đăng</button>
    </div>
  </div>
</div>
