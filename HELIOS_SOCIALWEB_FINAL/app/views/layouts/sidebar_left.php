<?php
// ================================================================
// CONTROLLER LAYER
// Biến nhận từ controller:
//   $loggedInUserData  array   Thông tin user (HoTen, AnhBia, AnhDaiDien, TieuDe)
//   $networkStats      array   ['connected' => int]
//   $upcomingEvents    array   Sự kiện sắp tới: TenSuKien, ThoiGianSuKien, DiaDiemSuKien, MoTa
//   $baseUrl           string  URL gốc
//   $maxEvents         int     Số sự kiện tối đa hiển thị (mặc định 5)
// ================================================================

$name      = $loggedInUserData['HoTen'] ?? 'Người dùng';
$nameParts = explode(' ', trim($name));
$initials  = mb_strtoupper(
    mb_substr($nameParts[0], 0, 1, 'UTF-8') .
    mb_substr(end($nameParts), 0, 1, 'UTF-8'),
    'UTF-8'
);

$events    = $upcomingEvents ?? [];
$maxEvents = $maxEvents ?? 5;
$dowLabels = ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'];

// ================================================================
// VIEW LAYER
// ================================================================
?>

<aside class="col-lg-3 d-none d-lg-block align-self-start">

    <!-- Profile card -->
    <div class="card border-0 shadow-sm rounded-3 mb-2 overflow-hidden">

        <!-- Banner ảnh bìa, fallback màu mint nếu chưa có ảnh -->
        <div class="sb-banner">
            <?php if (!empty($loggedInUserData['AnhBia'])): ?>
                <img src="<?= $baseUrl . htmlspecialchars($loggedInUserData['AnhBia']) ?>"
                     class="w-100 h-100 object-fit-cover">
            <?php endif ?>
        </div>

        <div class="px-3 pb-3 text-center">

            <!-- Avatar nhô lên đè banner nhờ margin âm trong .sb-avatar -->
            <a href="<?= $baseUrl ?>about-me"
               class="sb-avatar d-flex align-items-center justify-content-center mx-auto text-white text-decoration-none fw-bold">
                <?php if (!empty($loggedInUserData['AnhDaiDien'])): ?>
                    <img src="<?= $baseUrl . htmlspecialchars($loggedInUserData['AnhDaiDien']) ?>"
                         class="w-100 h-100 object-fit-cover">
                <?php else: ?>
                    <?= $initials ?>
                <?php endif ?>
            </a>

            <a href="<?= $baseUrl ?>about-me" class="sb-name d-block fw-bold text-decoration-none mt-2 mb-1">
                <?= htmlspecialchars($name) ?>
            </a>

            <p class="sb-title text-muted mb-0 text-truncate">
                <?= htmlspecialchars($loggedInUserData['TieuDe'] ?? 'Chưa có tiêu đề') ?>
            </p>
        </div>

        <div class="d-flex justify-content-between align-items-center px-3 py-2 border-top">
            <span class="sb-label fw-semibold text-muted">Kết nối</span>
            <span class="sb-stat fw-bold">
                <?= number_format($networkStats['connected'] ?? 0) ?>
            </span>
        </div>

        <div class="px-3 py-2 border-top">
            <a href="<?= $baseUrl ?>about-me"
               class="sb-edit-link d-flex align-items-center gap-1 text-decoration-none fw-semibold">
                <i class="bi bi-pencil"></i> Chỉnh sửa hồ sơ
            </a>
        </div>
    </div>

    <!-- Sự kiện sắp tới — chỉ render nếu có data -->
    <?php if (!empty($events)): ?>
    <div class="card border-0 shadow-sm rounded-3 mb-2 overflow-hidden">

        <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom">
            <span class="sb-section-title text-uppercase fw-bold text-muted">Sự kiện sắp tới</span>
            <?php if (count($events) > $maxEvents): ?>
                <a href="<?= $baseUrl ?>events" class="sb-see-all text-decoration-none fw-semibold">
                    Xem tất cả (<?= count($events) ?>)
                </a>
            <?php endif ?>
        </div>

        <?php foreach (array_slice($events, 0, $maxEvents) as $index => $ev):
            $ts      = strtotime($ev['ThoiGianSuKien'] ?? 'now');
            $dow     = $dowLabels[date('w', $ts)];
            $isToday = date('Y-m-d', $ts) === date('Y-m-d');

            // Xoay vòng màu badge để dễ phân biệt các sự kiện
            $colors = ['--helios-navy', '--helios-mint', '--helios-navy-bright', '--helios-navy-soft'];
            $color  = $colors[$index % count($colors)];
        ?>
        <div class="d-flex align-items-start gap-2 px-3 py-2 border-bottom sb-event-row">

            <div class="sb-event-date flex-shrink-0 <?= $isToday ? 'sb-event-date--today' : '' ?>"
                 style="background: var(<?= $color ?>);">
                <span class="sb-event-dow"><?= $isToday ? 'HN' : $dow ?></span>
                <span class="sb-event-day"><?= date('d', $ts) ?></span>
            </div>

            <div class="overflow-hidden flex-fill">
                <p class="sb-event-name fw-semibold mb-0 text-truncate">
                    <?= htmlspecialchars($ev['TenSuKien'] ?? '') ?>
                </p>
                <p class="sb-event-meta text-muted mb-0">
                    <?= date('H:i', $ts) ?>
                    <?= !empty($ev['DiaDiemSuKien']) ? '· ' . htmlspecialchars($ev['DiaDiemSuKien']) : '' ?>
                </p>
            </div>
        </div>
        <?php endforeach ?>

        <?php if (count($events) > $maxEvents): ?>
            <div class="px-3 py-2 text-center">
                <a href="<?= $baseUrl ?>events" class="sb-see-all text-decoration-none fw-semibold">
                    + <?= count($events) - $maxEvents ?> sự kiện khác
                </a>
            </div>
        <?php endif ?>

    </div>
    <?php endif ?>

</aside>