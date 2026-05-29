<?php
if (!isset($notifications)) $notifications = [];
?>
<div class="list-group list-group-flush">
  <?php if (!empty($notifications)): ?>
    <?php foreach ($notifications as $noti):
        $iconClass = 'bi-bell-fill';
        switch ($noti['LoaiThongBao']) {
            case 'KetNoi':   $iconClass = 'bi-person-plus-fill'; break;
            case 'HoSo':     $iconClass = 'bi-person-badge-fill'; break;
            case 'TuongTac': $iconClass = 'bi-heart-fill'; break;
            case 'BinhLuan': $iconClass = 'bi-chat-fill'; break;
            case 'TraLoi':   $iconClass = 'bi-reply-fill'; break;
            case 'NhacDen':  $iconClass = 'bi-at'; break;
            case 'BaiViet':  $iconClass = 'bi-file-earmark-post-fill'; break;
            case 'HeThong':  $iconClass = 'bi-megaphone-fill'; break;
        }
        $isUnread = ((int)$noti['TrangThaiDoc'] === 0);
        $link = trim($noti['LienKet'] ?? '');
    ?>
      <div class="list-group-item list-group-item-action noti-item <?php echo $isUnread ? 'bg-light' : ''; ?>"
           data-noti-id="<?php echo (int)$noti['MaThongBao']; ?>"
           data-link="<?php echo htmlspecialchars($link); ?>">
        <div class="d-flex align-items-start gap-3">
          <div class="noti-type-icon rounded-circle bg-helios-navy text-white d-flex align-items-center justify-content-center flex-shrink-0">
            <i class="bi <?php echo $iconClass; ?>"></i>
          </div>

          <div class="flex-grow-1 min-w-0">
            <p class="mb-1 noti-text"><?php echo htmlspecialchars($noti['NoiDung']); ?></p>
            <small class="text-muted">
              <i class="bi bi-clock me-1"></i><?php echo date('H:i d/m/Y', strtotime($noti['ThoiGianTao'])); ?>
            </small>
          </div>

          <div class="d-flex align-items-center gap-2 flex-shrink-0">
            <?php if ($isUnread): ?>
              <button class="btn btn-sm noti-action-btn btn-mark-read"
                      title="Đánh dấu đã đọc"
                      aria-label="Đánh dấu đã đọc">
                <i class="bi bi-check-circle" style="color:#6b7280;"></i>
              </button>
            <?php endif; ?>
            <button class="btn btn-sm noti-action-btn btn-delete-noti"
                    title="Xóa thông báo"
                    aria-label="Xóa thông báo">
              <i class="bi bi-trash3" style="color:#6b7280;"></i>
            </button>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <div class="noti-empty text-center text-muted py-4">
      <i class="bi bi-inbox fs-1 d-block mb-2"></i>
      <p class="mb-0">Không có thông báo nào.</p>
    </div>
  <?php endif; ?>
</div>