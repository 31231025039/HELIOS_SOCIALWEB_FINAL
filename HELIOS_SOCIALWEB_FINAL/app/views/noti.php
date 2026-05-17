<div class="container-xl py-3">
  <div class="row g-3 align-items-start">

    <?php include VIEW_PATH . '/layouts/home_sidebar_left.php'; ?>

    <main class="col-12 col-md-8 col-lg-6 align-self-start">
      <section class="noti-center">

        <!-- Header -->
        <div class="noti-header-card">
          <div class="noti-header-left">
            <h2 class="noti-title">Thông báo</h2>
            <?php if ($unreadCount > 0): ?>
                <span class="noti-badge-new"><?php echo $unreadCount; ?> mới</span>
            <?php endif; ?>
          </div>
          <!-- Nút đánh dấu tất cả -->
          <button class="noti-mark-all" id="btnMarkAllRead">Đánh dấu tất cả đã đọc</button>
        </div>

        <!-- Danh sách -->
        <div class="noti-list-card">
          <?php if (!empty($notifications)): ?>
              <?php foreach ($notifications as $noti): 
                  // Xử lý Icon và Màu sắc
                  $iconClass = "bi-bell-fill"; $colorClass = "blue";
                  switch ($noti['LoaiThongBao']) {
                      case 'KetNoi':    $iconClass = "bi-person-fill-add"; $colorClass = "green"; break;
                      case 'TuongTac':  $iconClass = "bi-heart-fill";      $colorClass = "pink";  break;
                      case 'BinhLuan':  $iconClass = "bi-chat-dots-fill";  $colorClass = "orange"; break;
                      case 'HeThong':   $iconClass = "bi-briefcase-fill";  $colorClass = "blue";  break;
                      case 'LuotXem':   $iconClass = "bi-eye-fill";        $colorClass = "purple"; break;
                  }
                  
                  $isUnread = ($noti['TrangThaiDoc'] == 0) ? 'unread' : '';
              ?>
              
              <!-- Khung 1 thông báo (Có gắn data-noti-id) -->
              <div class="noti-item <?php echo $isUnread; ?>" data-noti-id="<?php echo $noti['MaThongBao']; ?>">
                <div class="noti-icon-circle <?php echo $colorClass; ?>"><i class="bi <?php echo $iconClass; ?>"></i></div>
                <div class="noti-body">
                  <p><?php echo $noti['NoiDung']; ?></p>
                  <span class="noti-time"><?php echo date('H:i d/m/Y', strtotime($noti['ThoiGianTao'])); ?></span>
                </div>
                <div class="noti-actions">
                  <?php if ($isUnread): ?>
                      <!-- Nút Đánh dấu đã đọc -->
                      <button class="noti-action-icon btn-mark-read" title="Đánh dấu đã đọc"><i class="bi bi-check2-circle"></i></button>
                      <span class="noti-unread-dot"></span>
                  <?php endif; ?>
                  <!-- Nút Xóa -->
                  <button class="noti-action-icon btn-delete-noti" title="Xóa thông báo"><i class="bi bi-trash3"></i></button>
                </div>
              </div>

              <?php endforeach; ?>
          <?php else: ?>
              <div class="p-4 text-center text-muted">Bạn chưa có thông báo nào.</div>
          <?php endif; ?>
        </div>

      </section>
    </main>

    <?php include VIEW_PATH . '/layouts/home_sidebar_right.php'; ?>
  </div>
</div>