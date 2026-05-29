<?php
/**
 * noti.php - Trang thông báo
 *
 * @var int $unreadCount
 * @var bool $notifEnabled
 * @var array $notifications
 * @var string $baseUrl
 * @var string $activeNav
 */
?>
<div class="container-xl py-3">
  <div class="row g-3 align-items-start">
    <?php include VIEW_PATH_APP . '/layouts/sidebar_left.php'; ?>

    <main class="col-12 col-md-8 col-lg-6 align-self-start">
      <div class="card noti-card shadow-sm border-0">
        <div class="card-header bg-white border-0 px-4 pt-4 pb-2">
          <div class="d-flex align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-2">
              <h5 class="fw-bold mb-0 text-helios-navy">Thông báo mới</h5>
              <span class="badge bg-danger rounded-pill px-2 py-1 <?php echo $unreadCount > 0 ? '' : 'd-none'; ?>" id="totalUnreadBadge">
                <?php echo $unreadCount > 99 ? '99+' : $unreadCount; ?>
              </span>
            </div>

            <div class="d-flex align-items-center gap-2">
              <button class="btn btn-helios-navy btn-sm rounded-circle noti-header-icon-btn"
                      id="toggleNotifBtn"
                      data-state="<?php echo $notifEnabled ? 1 : 0; ?>"
                      title="<?php echo $notifEnabled ? 'Tắt thông báo' : 'Bật thông báo'; ?>"
                      aria-label="<?php echo $notifEnabled ? 'Tắt thông báo' : 'Bật thông báo'; ?>">
                <i class="bi <?php echo $notifEnabled ? 'bi-bell' : 'bi-bell-slash'; ?>"></i>
              </button>
              <button class="btn btn-helios-navy btn-sm rounded-circle noti-header-icon-btn"
                      id="btnMarkAllRead"
                      title="Đánh dấu tất cả đã đọc"
                      aria-label="Đánh dấu tất cả đã đọc">
                <i class="bi bi-envelope-open"></i>
              </button>
            </div>
          </div>
        </div>

        <ul class="nav nav-tabs noti-tabs px-4" id="notiTab" role="tablist">
          <li class="nav-item">
            <button class="nav-link active fw-semibold" type="button" data-filter="all">Tất cả</button>
          </li>
          <li class="nav-item">
            <button class="nav-link fw-semibold" type="button" data-filter="connect">Kết nối</button>
          </li>
          <li class="nav-item">
            <button class="nav-link fw-semibold" type="button" data-filter="system">Hệ thống</button>
          </li>
        </ul>

        <div class="card-body p-0" id="notiListContainer">
          <?php include VIEW_PATH_APP . '/noti-list.php'; ?>
        </div>
      </div>
    </main>

    <?php include VIEW_PATH_APP . '/layouts/sidebar_right.php'; ?>
  </div>
</div>

<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
  <div id="notiToast" class="toast align-items-center text-bg-success border-0" role="alert" data-bs-delay="2000">
    <div class="d-flex">
      <div class="toast-body"><i class="bi bi-check-circle-fill me-2"></i>Đã thay đổi cài đặt thông báo.</div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Đóng"></button>
    </div>
  </div>
</div>