<?php
// app/Views/layouts/navbar.php
// Xác định trang hiện tại để thêm class 'active'
// Biến $activeNav được truyền từ Controller
$activeNav = $activeNav ?? 'home'; // Mặc định là 'home' nếu không được truyền
?>
<header class="topbar sticky-top">
  <div class="container-xl h-100 px-2">
    <div class="row align-items-center h-100 g-0 flex-nowrap">

      <!-- Logo -->
      <div class="col-auto d-flex align-items-center flex-shrink-0">
        <a href="<?php echo $baseUrl; ?>home" class="me-1 flex-shrink-0">
          <img src="<?php echo $baseUrl; ?>assets/images/headerLogo(vertical).png" alt="Helios Logo" class="logo-img">
        </a>
        <div class="search-wrap d-none d-md-flex align-items-center px-2">
          <i class="bi bi-search text-secondary me-2"></i>
          <input type="text" class="form-control border-0 bg-transparent p-0 shadow-none" placeholder="Tìm kiếm">
        </div>
      </div>

      <!-- Nav cuộn ngang trên mobile -->
      <div class="col d-flex justify-content-end h-100 overflow-hidden">
        <nav class="nav-main d-flex align-items-center h-100">
          <a href="<?php echo $baseUrl; ?>home" class="nav-link-item nav-menu-item <?php echo ($activeNav == 'home') ? 'active' : ''; ?>" data-nav="home">
            <i class="bi bi-house-door-fill"></i>
            <span>Trang chủ</span>
          </a>
          <a href="<?php echo $baseUrl; ?>network" class="nav-link-item nav-menu-item <?php echo ($activeNav == 'network') ? 'active' : ''; ?>" data-nav="network">
            <i class="bi bi-people-fill"></i>
            <span>Mạng lưới</span>
          </a>
          <a href="<?php echo $baseUrl; ?>job" class="nav-link-item nav-menu-item <?php echo ($activeNav == 'job') ? 'active' : ''; ?>" data-nav="job">
            <i class="bi bi-briefcase-fill"></i>
            <span>Việc làm</span>
          </a>
          <a href="<?php echo $baseUrl; ?>noti" class="nav-link-item nav-menu-item position-relative <?php echo ($activeNav == 'noti') ? 'active' : ''; ?>" data-nav="noti">
            <i class="bi bi-bell-fill"></i>
            <span>Thông báo</span>
            <span class="nav-badge">2</span>
          </a>
          <a href="<?php echo $baseUrl; ?>about-me" class="nav-link-item user-profile-nav border-start <?php echo ($activeNav == 'profile') ? 'active' : ''; ?>" data-nav="profile">
            <div class="nav-avatar-sm">VY</div>
            <span>Tôi <i class="bi bi-caret-down-fill" style="font-size:8px"></i></span>
          </a>
        </nav>
      </div>

    </div>
  </div>
</header>