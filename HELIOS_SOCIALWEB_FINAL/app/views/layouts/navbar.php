<?php
// app/Views/layouts/navbar.php
$activeNav      = $activeNav      ?? 'home';
$unreadMessages = $unreadMessages ?? 0;
$unreadNotis    = $unreadNotis    ?? 0;
$baseUrl        = $baseUrl        ?? '/';
?>

<header class="topbar sticky-top">
  <div class="container-xl h-100 px-2">
    <div class="d-flex align-items-center h-100 flex-nowrap">

      <!-- Logo -->
      <a href="<?= $baseUrl ?>home" class="nav-logo me-2 d-flex align-items-center text-decoration-none">
        <img src="<?= $baseUrl ?>assets/images/headerLogo(vertical).png" alt="Helios" class="logo-img">
        <span class="logo-wordmark ms-2 d-none d-md-block">HELIOS <em>NETWORK</em></span>
      </a>

      <!-- Tất cả nav items + Profile + Logout trong 1 flex container -->
      <div class="d-flex align-items-center h-100 flex-grow-1">

        <a href="<?= $baseUrl ?>home"
           class="nav-link-item <?= $activeNav === 'home' ? 'active' : '' ?>">
          <i class="bi bi-house-door-fill"></i>
          <span>Trang chủ</span>
        </a>

        <a href="<?= $baseUrl ?>network"
           class="nav-link-item <?= $activeNav === 'network' ? 'active' : '' ?>">
          <i class="bi bi-people-fill"></i>
          <span>Mạng lưới</span>
        </a>

        <a href="<?= $baseUrl ?>job"
           class="nav-link-item <?= $activeNav === 'job' ? 'active' : '' ?>">
          <i class="bi bi-briefcase-fill"></i>
          <span>Việc làm</span>
        </a>

        <a href="<?= $baseUrl ?>message"
          class="nav-link-item position-relative <?= $activeNav === 'message' ? 'active' : '' ?>">
          <i class="bi bi-chat-dots-fill"></i>
          <span>Tin nhắn</span>
          <?php if ($unreadMessages > 0): ?>
            <span class="nav-badge"><?= $unreadMessages ?></span>
          <?php endif; ?>
        </a>

        <a href="<?= $baseUrl ?>noti"
           class="nav-link-item position-relative <?= $activeNav === 'noti' ? 'active' : '' ?>">
          <i class="bi bi-bell-fill"></i>
          <span>Thông báo</span>
          <?php if ($unreadNotis > 0): ?>
            <span class="nav-badge noti-badge-new"><?= $unreadNotis ?></span>
          <?php endif; ?>
        </a>

        <!-- Nút Tôi -->
        <a href="<?= $baseUrl ?>about-me"
           class="nav-profile <?= $activeNav === 'profile' ? 'active' : '' ?>">
          <div class="nav-avatar-sm">
            <?php if (!empty($_SESSION['user_avatar'])): ?>
              <img src="<?= $baseUrl . htmlspecialchars(ltrim($_SESSION['user_avatar'], '/')) ?>" 
              style="width:100%; height:100%; object-fit:cover; border-radius:50%;">  
            <?php else: ?>
              <?php
                $name = $_SESSION['user_name'] ?? '??';
                $nameParts = explode(' ', trim($name));
                $initials = '';
                if (count($nameParts) >= 2) {
                  $initials = mb_substr($nameParts[0], 0, 1) . mb_substr(end($nameParts), 0, 1);
                } elseif (count($nameParts) === 1 && mb_strlen($nameParts[0]) > 0) {
                  $initials = mb_substr($nameParts[0], 0, 2);
                } else {
                  $initials = '??';
                }
                echo mb_strtoupper($initials, 'UTF-8');
              ?>
            <?php endif; ?>
          </div>
          <span>Tôi</span>
        </a>

        <!-- Nút Thoát -->
        <a href="<?= $baseUrl ?>logout"
           class="nav-link-item text-danger"
           title="Đăng xuất"
           aria-label="Đăng xuất"
           onclick="return confirm('Bạn có chắc chắn muốn đăng xuất khỏi hệ thống?');">
          <i class="bi bi-box-arrow-right"></i>
          <span>Thoát</span>
        </a>

      </div>
    </div>
  </div>
</header>