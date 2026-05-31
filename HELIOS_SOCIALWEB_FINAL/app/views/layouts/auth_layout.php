<?php
// File: app/views/layouts/auth_layout.php
// Layout cho các trang xác thực: Đăng nhập, Đăng ký, Quên mật khẩu

$pageTitle = $pageTitle ?? 'Helios';
$baseUrl   = '/helios/public/';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo htmlspecialchars($pageTitle); ?> | Helios</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Roboto — hỗ trợ tiếng Việt sẵn, không lỗi dấu -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>assets/css/style.css?v=<?php echo time(); ?>">

    <style>
        :root {
            --navy:   #1B2B5E;
            --navy-h: #3A5199;
            --mint:   #5BBFAB;
            --mint-d: #3A9E8A;
        }

        /* Nền gradient, layout dọc */
        body {
            font-family: 'Roboto', 'Segoe UI', sans-serif;
            -webkit-font-smoothing: antialiased;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background: linear-gradient(145deg, #a8d8e8 0%, #b8e0d4 45%, #c5eae0 100%);
        }

        /* Main căn giữa — bên trong chia 2 cột */
        .auth-container { flex-grow: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px; }
        .auth-inner { display: flex;
            align-items: center;
            gap: 64px;
            width: 100%;
            max-width: 980px; }

        /* Cột trái: logo, tagline, điểm nổi bật */
        .auth-left { flex: 1; min-width: 0; }

        /* Logo + HELIOS NETWORK cùng hàng, cùng cỡ, in đậm */
        .auth-logo-row { display: flex; align-items: center; gap: 14px; margin-bottom: 32px; }
        .auth-logo-row img { height: 52px; object-fit: contain; }
        .auth-brand { display: flex; align-items: baseline; gap: 9px; }
        .auth-brand-name, .auth-brand-sub { font-size: 30px; font-weight: 1000; letter-spacing: .08em; }
        .auth-brand-name { color: var(--navy); }
        .auth-brand-sub  { color: var(--mint-d); }

        .auth-left h1 { font-size: 34px; font-weight: 750; color: var(--navy); line-height: 1.25; margin-bottom: 14px; }
        .auth-desc    { font-size: 15px; font-weight: 300; color: #3a5060; line-height: 1.75; max-width: 340px; margin-bottom: 24px; }

        /* 3 điểm nổi bật */
        .auth-features     { display: flex; flex-direction: column; gap: 10px; }
        .auth-feature-item { display: flex; align-items: center; gap: 10px; font-size: 14px; font-weight: 500; color: var(--navy); }
        .auth-feature-item i { font-size: 18px; color: var(--mint-d); }

        /* Cột phải: card chứa form */
        .auth-right { flex-shrink: 0; width: 480px; }

        /* Card trắng với dải màu trên đầu */
        .h-card { background: #fff;
            border-radius: 18px;
            border: none !important;
            box-shadow: 0 8px 48px rgba(27,43,94,.13) !important;
            padding: 48px 52px !important;
            position: relative;
            overflow: hidden; }
        .h-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--mint), var(--navy) 60%, var(--mint-d));
        }

        /* Tiêu đề form, label, input, nút */
        h1.text-center   { font-size: 32px; font-weight: 750; color: var(--navy); margin-bottom: 28px; }
        label.form-label { font-size: 16px; font-weight: 500; margin-bottom: 7px; }

        .form-control, .form-select {
            border-color: #B2D6CF;
            background: #f8fdfb;
            border-radius: 10px;
            padding: 14px 18px;
            font-size: 16px;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--mint);
            box-shadow: 0 0 0 3px rgba(91,191,171,.18);
        }

        .btn-primary { background: var(--navy);
            border-color: var(--navy);
            border-radius: 10px;
            padding: 13px 20px;
            font-size: 16px;
            font-weight: 700;
            transition: background .2s, transform .15s; }
        .btn-primary:hover  { background: var(--navy-h); border-color: var(--navy-hh); transform: translateY(-1px); }
        .btn-primary:active { transform: none; }

        a { color: var(--navy); font-size: 15px; }
        a:hover { color: var(--mint-d); }

        /* Mobile: ẩn cột trái, form full width */
        @media (max-width: 767px) {
            .auth-left  { display: none; }
            .auth-right { width: 100%; max-width: 480px; }
            .auth-inner { justify-content: center; }
            .h-card     { padding: 28px 20px !important; }
        }
    </style>
</head>
<body>

<main class="auth-container">
    <div class="auth-inner">

        <!-- Cột trái: thương hiệu + giới thiệu -->
        <div class="auth-left">
            <div class="auth-logo-row">
                <a href="<?php echo $baseUrl; ?>">
                    <img src="<?php echo $baseUrl; ?>assets/images/headerLogo(vertical).png" alt="Helios Logo">
                </a>
                <div class="auth-brand">
                    <span class="auth-brand-name">HELIOS</span>
                    <span class="auth-brand-sub">NETWORK</span>
                </div>
            </div>
            <h1>Kết nối &amp; khám phá<br>cùng Helios</h1>
            <p class="auth-desc">Nơi mọi người gặp gỡ, chia sẻ và phát triển cùng nhau. Cùng xây dựng một cộng đồng ấm áp, sáng tạo và đầy cảm hứng.</p>
            <div class="auth-features">
                <div class="auth-feature-item"><i class="bi bi-people-fill"></i> Kết nối với bạn bè và cộng đồng</div>
                <div class="auth-feature-item"><i class="bi bi-image-fill"></i> Chia sẻ khoảnh khắc và câu chuyện</div>
                <div class="auth-feature-item"><i class="bi bi-graph-up-arrow"></i> Phát triển bản thân mỗi ngày</div>
            </div>
        </div>

        <!-- Cột phải: form đăng nhập / đăng ký / quên MK -->
        <div class="auth-right">
            <?php if (!empty($contentView) && file_exists($contentView)): ?>
                <?php include $contentView; ?>
            <?php else: ?>
                <div class="alert alert-danger">Nội dung trang không tìm thấy!</div>
            <?php endif; ?>
        </div>

    </div>
</main>

<?php include VIEW_PATH_APP . '/layouts/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?php $jsFiles = $jsFiles ?? []; foreach ($jsFiles as $js): ?>
    <script src="<?php echo $GLOBALS['baseUrl'] ?? '/helios/public/'; ?>assets/js/<?php echo htmlspecialchars($js); ?>?v=<?php echo time(); ?>"></script>
<?php endforeach; ?>

</body>
</html>