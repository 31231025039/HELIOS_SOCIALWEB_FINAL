<!-- File: admin/views/layouts/sidebar.php -->
<?php $activeMenu = $activeMenu ?? 'dashboard'; ?>
<aside class="admin-sidebar">
    <div class="sidebar-header">
        <a href="/helios/public/admin" class="sidebar-brand" aria-label="Helios Admin">
            <span class="sidebar-brand-logo-box" aria-hidden="true">
                <img
                    src="/helios/public/assets/images/headerLogo(vertical).png"
                    alt=""
                    width="46"
                    height="46"
                    class="sidebar-brand-logo"
                >
            </span>
            <span class="sidebar-brand-text">Helios Admin</span>
        </a>
    </div>
    <ul class="admin-menu">
        <li class="<?php echo ($activeMenu === 'dashboard') ? 'active' : ''; ?>">
            <a href="/helios/public/admin"><i class="bi bi-speedometer2"></i><span>Dashboard</span></a>
        </li>
        <li class="<?php echo ($activeMenu === 'users') ? 'active' : ''; ?>">
            <a href="#"><i class="bi bi-people-fill"></i><span>Quản lý tài khoản</span></a>
        </li>
        <li class="<?php echo ($activeMenu === 'posts') ? 'active' : ''; ?>">
            <a href="/helios/public/admin/posts"><i class="bi bi-file-earmark-text-fill"></i><span>Quản lý bài viết</span></a>
        </li>
        <li class="<?php echo ($activeMenu === 'jobs') ? 'active' : ''; ?>">
            <a href="/helios/public/admin/jobs"><i class="bi bi-briefcase-fill"></i><span>Quản lý tuyển dụng</span></a>
        </li>
        <li class="<?php echo ($activeMenu === 'companies') ? 'active' : ''; ?>">
            <a href="/helios/public/admin/companies"><i class="bi bi-building"></i><span>Quản lý công ty</span></a>
        </li>

        <li class="admin-menu-divider" aria-hidden="true"></li>

        <li>
           <a href="#" class="admin-logout"><i class="bi bi-box-arrow-left"></i><span>Đăng xuất</span></a>
        </li>
    </ul>
</aside>