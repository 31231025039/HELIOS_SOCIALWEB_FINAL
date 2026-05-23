<?php
/**
 * View: danh sách việc làm (JobController::index → layouts/main.php).
 *
 * @var string $keyword
 * @var int $currentPage
 * @var int $totalPages
 * @var int $totalJobs
 * @var array<int, array<string, mixed>> $jobs
 * @var string $baseUrl
 */
?>
<!-- ===================== HERO SECTION ===================== -->
<section class="hero" style="--hero-banner: url('<?php echo $baseUrl; ?>assets/images/heroBanner.png');">
  <div class="container-xl hero-inner">
    <div class="row">
      <div class="col-lg-8 col-xl-7 hero-content-col">
        <h1>Tìm <span>việc làm tốt nhất</span><br>phù hợp với bạn</h1>
        <p>Hơn 10.000 việc làm mới nhất từ hàng nghìn công ty hàng đầu Việt Nam</p>
        <form class="search-bar" action="/helios/public/job" method="get" role="search">
          <i class="bi bi-search text-muted" style="font-size:18px; padding-left:6px;"></i>
          <input class="search-input" type="search" name="q" value="<?php echo htmlspecialchars($keyword ?? ''); ?>" placeholder="Từ khóa, chức danh hoặc công ty" autocomplete="off">
          <div class="search-divider"></div>
          <button class="btn-search" type="submit">TÌM VIỆC</button>
        </form>
        <div class="hero-stats">
          <div class="hero-stat"><strong>10,000+</strong>Việc làm đang tuyển</div>
          <div class="hero-stat"><strong>12,500+</strong>Công ty uy tín</div>
          <div class="hero-stat"><strong>1.2M+</strong>Ứng viên đăng ký</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ===================== MAIN CONTENT (JOB LIST) ===================== -->
<section class="py-4 main-content-section">
  <div class="container-xl">
    <div class="row g-3 justify-content-center">
      <div class="col-12 col-lg-10 col-xl-9">
        
        <div class="d-flex align-items-center justify-content-between mb-3">
          <?php if (!empty($keyword)): ?>
            <h2 class="section-title mb-0">Kết quả tìm kiếm: "<?php echo htmlspecialchars($keyword); ?>"</h2>
            <a href="/helios/public/job" class="text-decoration-none small">Xóa bộ lọc</a>
          <?php else: ?>
            <h2 class="section-title mb-0">Việc làm tốt nhất</h2>
          <?php endif; ?>
        </div>

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
                              <i class="bi bi-buildings-fill text-secondary" style="font-size: 40px;"></i>
                          <?php endif; ?>
                      </div>
                      <div class="flex-1 min-w-0">
                        <span class="badge-hot">HOT</span>
                        <div class="job-title mt-1"><?php echo htmlspecialchars($job['TieuDe']); ?></div>
                      </div>
                    </div>
                    <div class="job-company"><?php echo htmlspecialchars($job['TenCongTy']); ?></div>
                    <div class="job-salary"><i class="bi bi-cash-coin me-1"></i><?php echo htmlspecialchars($job['MucLuong']); ?></div>
                    <div class="job-location">
                      <i class="bi bi-geo-alt-fill me-1"></i>
                      <?php echo !empty($job['NoiLamViec']) ? htmlspecialchars($job['NoiLamViec']) : 'Xem chi tiết'; ?>
                    </div>
                  </div>
                </a>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="col-12 text-center text-muted py-5">
              <?php if (!empty($keyword)): ?>
                Không tìm thấy việc làm phù hợp với "<?php echo htmlspecialchars($keyword); ?>".
              <?php else: ?>
                Hiện chưa có việc làm nào.
              <?php endif; ?>
            </div>
          <?php endif; ?>
        </div>

        <?php if (!empty($totalPages) && !empty($jobs)): ?>
          <nav class="job-pagination mt-4" aria-label="Phân trang việc làm">
            <?php
              $buildPageUrl = function (int $pageNum) use ($keyword) {
                  $params = ['page' => $pageNum];
                  if (!empty($keyword)) {
                      $params['q'] = $keyword;
                  }
                  return '/helios/public/job?' . http_build_query($params);
              };
            ?>
            <?php if ($currentPage > 1): ?>
              <a class="page-link page-nav" href="<?php echo $buildPageUrl($currentPage - 1); ?>">&laquo; Trước</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
              <a class="page-link <?php echo $i === $currentPage ? 'active' : ''; ?>"
                 href="<?php echo $buildPageUrl($i); ?>"
                 <?php echo $i === $currentPage ? 'aria-current="page"' : ''; ?>>
                <?php echo $i; ?>
              </a>
            <?php endfor; ?>

            <?php if ($currentPage < $totalPages): ?>
              <a class="page-link page-nav" href="<?php echo $buildPageUrl($currentPage + 1); ?>">Sau &raquo;</a>
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