<?php
$keyword     = $keyword     ?? '';
$location    = $location    ?? '';
$deadline    = $deadline    ?? '';
$locations   = $locations   ?? [];
$totalJobs   = $totalJobs   ?? 0;
$totalPages  = $totalPages  ?? 1;
$currentPage = $currentPage ?? 1;
$baseUrl     = $baseUrl     ?? '/helios/public/';

$deadlineLabels = [
    'active'  => 'Còn hạn',
    'soon'    => 'Sắp hết hạn',
    'expired' => 'Đã hết hạn',
];

$buildPageUrl = function (int $pageNum) use ($keyword, $location, $deadline) {
    $params = ['page' => $pageNum];
    if ($keyword  !== '') $params['q']        = $keyword;
    if ($location !== '') $params['location'] = $location;
    if ($deadline !== '') $params['deadline'] = $deadline;
    return '/helios/public/job?' . http_build_query($params);
};

$hasFilter = $keyword !== '' || $location !== '' || $deadline !== '';
?>

<!-- ===================== HERO SECTION ===================== -->
<section class="hero" style="--hero-banner: url('<?php echo $baseUrl; ?>assets/images/heroBanner.png');">
    <div class="container-xl hero-inner">
        <div class="row">
            <div class="col-lg-8 col-xl-7 hero-content-col">
                <h1>Tìm <span>việc làm tốt nhất</span><br>phù hợp với bạn</h1>
                <p>Kết nối ứng viên với những cơ hội việc làm chất lượng từ các công ty hàng đầu Việt Nam</p>
                <form class="search-bar" action="/helios/public/job" method="get" role="search">
                    <i class="bi bi-search text-muted" style="font-size:18px; padding-left:6px;"></i>
                    <input class="search-input" type="search" name="q" value="<?php echo htmlspecialchars($keyword); ?>" placeholder="Từ khóa, chức danh hoặc công ty" autocomplete="off">
                    <div class="search-divider"></div>
                    <button class="btn-search" type="submit">TÌM VIỆC</button>
                </form>
                <div class="hero-stats">
                    <div class="hero-stat"><strong>Việc làm</strong>Đa dạng ngành nghề</div>
                    <div class="hero-stat"><strong>Công ty</strong>Uy tín hàng đầu</div>
                    <div class="hero-stat"><strong>Tin tuyển dụng mới</strong>Cập nhật liên tục</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===================== MAIN CONTENT ===================== -->
<section class="py-4 main-content-section">
    <div class="container-xl">
        <div class="job-layout">

            <!-- SIDEBAR FILTER -->
            <aside class="job-filter-sidebar">
                <form action="/helios/public/job" method="get" id="filterForm">
                    <?php if ($keyword !== ''): ?>
                        <input type="hidden" name="q" value="<?php echo htmlspecialchars($keyword); ?>">
                    <?php endif; ?>

                    <div class="filter-card">
                        <div class="filter-card-title">
                            <i class="bi bi-sliders"></i> Lọc việc làm
                        </div>

                        <!-- Lọc Địa điểm -->
                        <div class="filter-group">
                            <label class="filter-label" for="filter-location"><i class="bi bi-geo-alt"></i> Địa điểm</label>
                            <select id="filter-location" name="location" class="filter-select" onchange="document.getElementById('filterForm').submit()">
                                <option value="">Tất cả địa điểm</option>
                                <?php foreach ($locations as $loc): ?>
                                    <option value="<?php echo htmlspecialchars($loc); ?>" <?php echo $location === $loc ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($loc); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Lọc Hạn nộp -->
                        <div class="filter-group">
                            <label class="filter-label" for="filter-deadline"><i class="bi bi-calendar2-check"></i> Hạn nộp</label>
                            <select id="filter-deadline" name="deadline" class="filter-select" onchange="document.getElementById('filterForm').submit()">
                                <option value="">Tất cả</option>
                                <?php foreach ($deadlineLabels as $val => $label): ?>
                                    <option value="<?php echo $val; ?>" <?php echo $deadline === $val ? 'selected' : ''; ?>>
                                        <?php echo $label; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <?php if ($hasFilter): ?>
                            <a href="/helios/public/job" class="btn-clear-filter">
                                <i class="bi bi-x-circle"></i> Xóa bộ lọc
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </aside>

            <!-- DANH SÁCH JOB -->
            <div class="job-main-content">

                <!-- Header -->
                <div class="job-list-header">
                    <h2 class="section-title mb-0">
                        <?php if ($keyword !== ''): ?>
                            Kết quả: "<?php echo htmlspecialchars($keyword); ?>"
                        <?php elseif ($hasFilter): ?>
                            Kết quả lọc
                        <?php else: ?>
                            Việc làm tốt nhất
                        <?php endif; ?>
                    </h2>
                    <?php if ($totalJobs > 0): ?>
                        <span class="result-count"><?php echo number_format($totalJobs); ?> việc làm</span>
                    <?php endif; ?>
                </div>

                <!-- Filter tags -->
                <?php if ($hasFilter): ?>
                    <div class="active-filter-tags">
                        <?php if ($location !== ''): ?>
                            <span class="filter-tag">
                                <i class="bi bi-geo-alt-fill"></i> <?php echo htmlspecialchars($location); ?>
                                <a href="<?php
                                    $p = [];
                                    if ($keyword  !== '') $p['q'] = $keyword;
                                    if ($deadline !== '') $p['deadline'] = $deadline;
                                    echo '/helios/public/job' . (!empty($p) ? '?' . http_build_query($p) : '');
                                ?>" class="filter-tag-remove">&times;</a>
                            </span>
                        <?php endif; ?>

                        <?php if ($deadline !== ''): ?>
                            <span class="filter-tag">
                                <i class="bi bi-calendar-fill"></i> <?php echo $deadlineLabels[$deadline] ?? ''; ?>
                                <a href="<?php
                                    $p = [];
                                    if ($keyword  !== '') $p['q'] = $keyword;
                                    if ($location !== '') $p['location'] = $location;
                                    echo '/helios/public/job' . (!empty($p) ? '?' . http_build_query($p) : '');
                                ?>" class="filter-tag-remove">&times;</a>
                            </span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- Job Grid -->
                <div class="row g-3">
                    <?php if (!empty($jobs)): ?>
                        <?php foreach ($jobs as $job): ?>
                            <div class="col-md-6">
                                <a href="/helios/public/job/detail?id=<?= $job['MaCongViec'] ?>" class="text-decoration-none text-dark">
                                    <div class="job-card">
                                        <div class="d-flex align-items-start gap-3">
                                            <div class="job-logo">
                                                <?php if (!empty($job['Logo'])): ?>
                                                    <img src="/helios/public<?php echo htmlspecialchars($job['Logo']); ?>" alt="<?php echo htmlspecialchars($job['TenCongTy']); ?>" loading="lazy">
                                                <?php else: ?>
                                                    <i class="bi bi-buildings-fill text-secondary" style="font-size:40px;"></i>
                                                <?php endif; ?>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <span class="badge-hot">HOT</span>
                                                <div class="job-title mt-1"><?php echo htmlspecialchars($job['TieuDe']); ?></div>
                                            </div>
                                        </div>
                                        <div class="job-company"><?php echo htmlspecialchars($job['TenCongTy']); ?></div>
                                        <div class="job-salary">
                                            <i class="bi bi-cash-coin me-1"></i><?php echo htmlspecialchars($job['MucLuong']); ?>
                                        </div>
                                        <div class="job-meta-row">
                                            <span class="job-location">
                                                <i class="bi bi-geo-alt-fill me-1"></i>
                                                <?php echo !empty($job['NoiLamViec']) ? htmlspecialchars($job['NoiLamViec']) : 'Xem chi tiết'; ?>
                                            </span>
                                            <span class="job-deadline <?php echo strtotime($job['HanNop']) < time() ? 'expired' : ''; ?>">
                                                <i class="bi bi-calendar2-check me-1"></i>
                                                <?php echo date('d/m/Y', strtotime($job['HanNop'])); ?>
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12 text-center text-muted py-5">
                            <?php if ($hasFilter): ?>
                                Không tìm thấy việc làm phù hợp với bộ lọc hiện tại.
                            <?php else: ?>
                                Hiện chưa có việc làm nào.
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- PAGINATION -->
                <?php if ($totalPages > 1 && !empty($jobs)): ?>
                    <nav class="job-pagination mt-4" aria-label="Phân trang việc làm">
                        <?php if ($currentPage > 1): ?>
                            <a class="page-link page-nav" href="<?php echo $buildPageUrl(1); ?>" title="Trang đầu">&laquo;&laquo; Trước</a>
                        <?php endif; ?>

                        <?php
                        $shown = [];
                        for ($i = 1; $i <= $totalPages; $i++) {
                            if ($i === 1 || $i === $totalPages || abs($i - $currentPage) <= 1) {
                                $shown[] = $i;
                            }
                        }
                        $shown = array_unique($shown);
                        sort($shown);
                        $prev = null;
                        foreach ($shown as $pg):
                            if ($prev !== null && $pg - $prev > 1): ?>
                                <span class="page-link page-dots">…</span>
                            <?php endif; ?>
                            <a class="page-link <?php echo $pg === $currentPage ? 'active' : ''; ?>" href="<?php echo $buildPageUrl($pg); ?>" <?php echo $pg === $currentPage ? 'aria-current="page"' : ''; ?>>
                                <?php echo $pg; ?>
                            </a>
                            <?php $prev = $pg;
                        endforeach; ?>

                        <?php if ($currentPage < $totalPages): ?>
                            <a class="page-link page-nav" href="<?php echo $buildPageUrl($totalPages); ?>" title="Trang cuối">Sau &raquo;&raquo;</a>
                        <?php endif; ?>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- ===================== SUPPORT SECTION ===================== -->
<section class="support-section">
    <div class="container-xl">
        <h4>HỖ TRỢ ỨNG VIÊN</h4>
        <p>Nếu gặp bất cứ vấn đề gì cần hỗ trợ, hãy gọi tới HOTLINE hoặc gửi email để được hỗ trợ nhanh nhất.</p>
        <div class="d-flex flex-wrap justify-content-center gap-3">
            <a href="#" class="contact-pill">
                <span class="icon-wrap"><i class="bi bi-telephone-fill"></i></span>
                1900.61.62.63
            </a>
            <a href="#" class="contact-pill">
                <span class="icon-wrap mail"><i class="bi bi-envelope-fill"></i></span>
                hotro@helios.com
            </a>
        </div>
    </div>
</section>