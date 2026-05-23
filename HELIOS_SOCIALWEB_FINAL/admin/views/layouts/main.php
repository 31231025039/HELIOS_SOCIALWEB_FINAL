<!-- File: admin/views/layouts/main.php (đã cập nhật) -->
<?php
    $pageTitle = $pageTitle ?? 'Admin Dashboard';
    $contentView = $contentView ?? '';
    $activeMenu = $activeMenu ?? 'dashboard';
    $adminCssPath = '/helios/public/assets/css/admin.css';
    $adminCssFile = dirname(__DIR__, 3) . '/public/assets/css/admin.css';
    $adminCssVersion = file_exists($adminCssFile) ? filemtime($adminCssFile) : time();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> | Helios Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $adminCssPath; ?>?v=<?php echo $adminCssVersion; ?>">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body>
    <div class="admin-wrapper">
        <?php include __DIR__ . '/sidebar.php'; ?>

        <div class="d-flex flex-column flex-grow-1">
            <main class="admin-content">
                <?php
                    if (!empty($contentView) && file_exists($contentView)) {
                        include $contentView;
                    } else {
                        echo '<div class="alert alert-danger">Nội dung trang không tìm thấy!</div>';
                    }
                ?>
            </main>
            <?php include __DIR__ . '/footer.php'; ?>
        </div>
    </div>
    
    <!-- Các thư viện JS chung -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <!-- Vòng lặp để nạp các file JS riêng của từng trang -->
    <?php
    $jsFiles = $jsFiles ?? [];
    foreach ($jsFiles as $jsFile):
    ?>
        <script src="/helios/public/assets/js/<?php echo $jsFile; ?>?v=<?php echo time(); // Thêm version để tránh cache ?>"></script>
    <?php endforeach; ?>

</body>
</html>