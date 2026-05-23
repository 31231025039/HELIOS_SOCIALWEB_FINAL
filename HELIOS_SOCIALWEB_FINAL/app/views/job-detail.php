<?php
/**
 * Biến được truyền từ JobController::detail() qua layouts/main.php.
 *
 * @var array<string, mixed> $job
 * @var string $baseUrl
 */
?>
<main class="main">
  <div class="container-xl">
    <div class="layout">
      <div class="job-main">

        <!-- THÔNG TIN HEADER -->
        <div class="card job-header-card border-0 shadow-sm mb-3">
          <div class="job-header-top">
            <div class="job-logo">
                <?php if (!empty($job['Logo'])): ?>
                    <img src="/helios/public<?php echo htmlspecialchars($job['Logo']); ?>" alt="<?php echo htmlspecialchars($job['TenCongTy']); ?>">
                <?php else: ?>
                    <i class="bi bi-buildings-fill text-secondary fs-3"></i>
                <?php endif; ?>
            </div>
            <div class="job-title-block">
              <h1 class="job-title"><?php echo htmlspecialchars($job['TieuDe']); ?></h1>
              <div class="job-meta">
                <span class="company-name">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                  <?php echo htmlspecialchars($job['TenCongTy']); ?>
                </span>
                <?php if (!empty($job['NoiLamViec'])): ?>
                <span class="meta-item">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                  <?php echo htmlspecialchars($job['NoiLamViec']); ?>
                </span>
                <?php endif; ?>
                <span class="meta-item meta-expire <?php echo ($job['DaysLeft'] < 0) ? 'text-danger' : ''; ?>">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                  Deadline: <?php echo date('j/n/Y', strtotime($job['HanNop'])); ?>
                </span>
              </div>
            </div>
          </div>
        </div>

        <div class="tabs">
          <button class="tab active">Chi tiết công việc</button>
        </div>

        <!-- THÔNG TIN NHANH -->
        <div class="card job-info-card border-0 shadow-sm mb-3">
          <div class="info-grid">
            <div class="info-item">
              <div class="info-icon income-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
              </div>
              <div><span class="info-label">Thu nhập:</span><span class="info-value fw-bold"><?php echo htmlspecialchars($job['MucLuong']); ?></span></div>
            </div>
            <div class="info-item">
              <div class="info-icon type-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
              </div>
              <div><span class="info-label">Loại hình:</span><span class="info-value fw-bold"><?php echo htmlspecialchars($job['LoaiHinh'] ?? 'Full-time'); ?></span></div>
            </div>
          </div>
        </div>

        <!-- MÔ TẢ CÔNG VIỆC -->
        <div class="card job-desc-card border-0 shadow-sm mb-3">
          <h2 class="section-title">Mô tả công việc</h2>
          <div class="company-desc job-desc-content">
            <?php echo $job['MoTa']; ?>
          </div>
        </div>

        <?php if (!empty($job['YeuCau'])): ?>
        <div class="card job-desc-card border-0 shadow-sm mb-3">
          <h2 class="section-title">Yêu cầu ứng viên</h2>
          <div class="company-desc job-desc-content">
            <?php echo $job['YeuCau']; ?>
          </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($job['QuyenLoi'])): ?>
        <div class="card job-desc-card border-0 shadow-sm mb-3">
          <h2 class="section-title">Quyền lợi</h2>
          <div class="company-desc job-desc-content">
            <?php echo $job['QuyenLoi']; ?>
          </div>
        </div>
        <?php endif; ?>

        <!-- THÔNG TIN CÔNG TY -->
        <div class="card border-0 shadow-sm">
          <h2 class="section-title">Giới thiệu công ty</h2>
          <p class="company-desc"><?php echo nl2br(htmlspecialchars($job['MoTaCongTy'])); ?></p>

          <div class="disclaimer">
            <p>Các thông tin được cung cấp chỉ nhằm mục đích tham khảo. Helios không đại diện cho doanh nghiệp <b><?php echo htmlspecialchars($job['TenCongTy']); ?></b> trong các hoạt động tuyển dụng.</p>
          </div>
        </div>

      </div>
    </div>
  </div>
</main>