<?php
// app/Views/layouts/main.php
// Các biến này sẽ được truyền từ Controller
$pageTitle = $pageTitle ?? 'Helios'; 
$cssFiles = $cssFiles ?? []; 
$jsFiles = $jsFiles ?? []; 
$contentView = $contentView ?? ''; 
$activeNav = $activeNav ?? 'home'; 

$baseUrl = '/helios/public/'; 
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?php echo htmlspecialchars($pageTitle); ?> | Helios</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  
  <?php $cssVersion = time(); ?>

  <!-- CSS Chung (Bao gồm Sidebar) -->
  <link rel="stylesheet" href="<?php echo $baseUrl; ?>assets/css/style.css?v=<?php echo $cssVersion; ?>">

  <!-- CSS Riêng từng trang -->
  <?php foreach ($cssFiles as $css) : ?>
      <link rel="stylesheet" href="<?php echo $baseUrl; ?>assets/css/<?php echo htmlspecialchars($css); ?>?v=<?php echo $cssVersion; ?>">
  <?php endforeach; ?>

</head>
<body>

<?php
// Bao gồm Navbar
include VIEW_PATH . '/layouts/navbar.php';
?>

<div class="app-content">
  <?php
  // Bao gồm nội dung chính của trang
  if (!empty($contentView) && file_exists($contentView)) {
      include $contentView;
  } else {
      echo '<div class="container-xl py-3"><div class="alert alert-danger" role="alert">Nội dung trang không tìm thấy!</div></div>';
  }
  ?>
</div>

<?php
// Bao gồm Footer
include VIEW_PATH . '/layouts/footer.php';
?>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- JS riêng trang -->
<?php foreach ($jsFiles as $js) : ?>
<script src="<?php echo $baseUrl; ?>assets/js/<?php echo htmlspecialchars($js); ?>?v=<?php echo $cssVersion; ?>"></script>
<?php endforeach; ?>

</body>
</html>