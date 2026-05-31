<?php
// app/Views/layouts/home_sidebar_right.php
?>
<aside class="col-12 col-md-4 col-lg-3 align-self-start">

  <div class="h-card mb-3 skills-sponsored-card" id="adsCard">
    <div class="skills-sponsored-head d-flex align-items-center justify-content-between px-3 pt-3 pb-1">
      <p class="sponsored-section-title mb-0 text-muted" style="font-size: 14px; font-weight: 700;">Quảng cáo</p>
    </div>
    <div class="skills-sponsored-list px-2 pb-2" id="adsList">
      <article class="skill-promo ad-item" id="ad-1">
        <div class="skill-promo-inner p-2">
          <div class="mb-2" style="width:100%; aspect-ratio:16/9; border-radius:8px; overflow:hidden; background:#e4e6eb; position:relative;">
            <div style="position:absolute; top:6px; right:6px; z-index:2; display:flex; gap:4px;">
              <button type="button" class="skill-promo-icon-btn skill-promo-icon-btn--float ad-hide-btn" data-id="ad-1">
                <i class="bi bi-eye-slash"></i>
              </button>
              <button type="button" class="skill-promo-icon-btn skill-promo-icon-btn--float ad-remove-btn" data-id="ad-1">
                <i class="bi bi-x-lg"></i>
              </button>
            </div>
            <img src="<?= $baseUrl ?>assets/images/AICouresa.png" style="width:100%; height:100%; object-fit:cover;">
          </div>
          <a href="https://www.coursera.org/learn/ai-for-everyone" target="_blank" rel="noopener" class="d-block text-decoration-none">
            <span class="skill-promo-title" style="font-size:13px; font-weight:600; color:#1a1a1a;">AI For Everyone — Andrew Ng — Coursera</span>
          </a>
          <span class="d-block mt-1" style="font-size:11px; color:#888;">Artificial Intelligence· Miễn phí kiểm toán</span>
        </div>
        <div class="ad-hidden-notice d-none p-2 text-center" style="font-size:12px; color:#888;">
          Đã ẩn quảng cáo này. 
          <button type="button" class="btn btn-link btn-sm p-0 ad-undo-btn" data-id="ad-1" style="font-size:12px;">Hoàn tác</button>
        </div>
      </article>

      <article class="skill-promo ad-item" id="ad-2">
        <div class="skill-promo-inner p-2">
          <div class="mb-2" style="width:100%; aspect-ratio:16/9; border-radius:8px; overflow:hidden; background:#e4e6eb; position:relative;">
            <div style="position:absolute; top:6px; right:6px; z-index:2; display:flex; gap:4px;">
              <button type="button" class="skill-promo-icon-btn skill-promo-icon-btn--float ad-hide-btn" data-id="ad-2">
                <i class="bi bi-eye-slash"></i>
              </button>
              <button type="button" class="skill-promo-icon-btn skill-promo-icon-btn--float ad-remove-btn" data-id="ad-2">
                <i class="bi bi-x-lg"></i>
              </button>
            </div>
            <img src="<?= $baseUrl ?>assets/images/pythonIBM.png" style="width:100%; height:100%; object-fit:cover;">
          </div>
          <a href="https://www.coursera.org/learn/python-for-applied-data-science-ai" target="_blank" rel="noopener" class="d-block text-decoration-none">
            <span class="skill-promo-title" style="font-size:13px; font-weight:600; color:#1a1a1a;">Python for Data Science, AI & Development — IBM</span>
          </a>
          <span class="d-block mt-1" style="font-size:11px; color:#888;">Data Science by Python of IBM · Coursera · Có chứng chỉ</span>
        </div>
        <div class="ad-hidden-notice d-none p-2 text-center" style="font-size:12px; color:#888;">
          Đã ẩn quảng cáo này. 
          <button type="button" class="btn btn-link btn-sm p-0 ad-undo-btn" data-id="ad-2" style="font-size:12px;">Hoàn tác</button>
        </div>
      </article>

      <article class="skill-promo ad-item" id="ad-3">
        <div class="skill-promo-inner p-2">
          <div class="mb-2" style="width:100%; aspect-ratio:16/9; border-radius:8px; overflow:hidden; background:#e4e6eb; position:relative;">
            <div style="position:absolute; top:6px; right:6px; z-index:2; display:flex; gap:4px;">
              <button type="button" class="skill-promo-icon-btn skill-promo-icon-btn--float ad-hide-btn" data-id="ad-3">
                <i class="bi bi-eye-slash"></i>
              </button>
              <button type="button" class="skill-promo-icon-btn skill-promo-icon-btn--float ad-remove-btn" data-id="ad-3">
                <i class="bi bi-x-lg"></i>
              </button>
            </div>
            <img src="<?= $baseUrl ?>assets/images/PMGoogle.jpg" style="width:100%; height:100%; object-fit:cover;">
          </div>
          <a href="https://www.coursera.org/professional-certificates/google-project-management" target="_blank" rel="noopener" class="d-block text-decoration-none">
            <span class="skill-promo-title" style="font-size:13px; font-weight:600; color:#1a1a1a;">Google Project Management Certificate — Coursera</span>
          </a>
          <span class="d-block mt-1" style="font-size:11px; color:#888;">Project Management of Google · Coursera · Có chứng chỉ</span>
        </div>
        <div class="ad-hidden-notice d-none p-2 text-center" style="font-size:12px; color:#888;">
          Đã ẩn quảng cáo này. 
          <button type="button" class="btn btn-link btn-sm p-0 ad-undo-btn" data-id="ad-3" style="font-size:12px;">Hoàn tác</button>
        </div>
      </article>

      <article class="skill-promo ad-item" id="ad-4">
        <div class="skill-promo-inner p-2">
          <div class="mb-2" style="width:100%; aspect-ratio:16/9; border-radius:8px; overflow:hidden; background:#e4e6eb; position:relative;">
            <div style="position:absolute; top:6px; right:6px; z-index:2; display:flex; gap:4px;">
              <button type="button" class="skill-promo-icon-btn skill-promo-icon-btn--float ad-hide-btn" data-id="ad-4">
                <i class="bi bi-eye-slash"></i>
              </button>
              <button type="button" class="skill-promo-icon-btn skill-promo-icon-btn--float ad-remove-btn" data-id="ad-4">
                <i class="bi bi-x-lg"></i>
              </button>
            </div>
            <img src="<?= $baseUrl ?>assets/images/PowerBI.jpeg" style="width:100%; height:100%; object-fit:cover;">
          </div>
          <a href="https://www.coursera.org/professional-certificates/microsoft-power-bi-data-analyst" target="_blank" rel="noopener" class="d-block text-decoration-none">
            <span class="skill-promo-title" style="font-size:13px; font-weight:600; color:#1a1a1a;">Microsoft Power BI — Data Analysis Fundamentals of Digital Marketing — Google Visualization — Coursera</span>
          </a>
          <span class="d-block mt-1" style="font-size:11px; color:#888;">Microsoft Power BI Data Analysis · Coursera · Có chứng chỉ</span>
        </div>
        <div class="ad-hidden-notice d-none p-2 text-center" style="font-size:12px; color:#888;">
          Đã ẩn quảng cáo này. 
          <button type="button" class="btn btn-link btn-sm p-0 ad-undo-btn" data-id="ad-4" style="font-size:12px;">Hoàn tác</button>
        </div>
      </article>

    </div>
  </div>

</aside>

<script>
document.querySelectorAll('.ad-hide-btn').forEach(btn => {
  btn.addEventListener('click', function() {
    const article = document.getElementById(this.dataset.id);
    article.querySelector('.skill-promo-inner').classList.add('d-none');
    article.querySelector('.ad-hidden-notice').classList.remove('d-none');
  });
});

document.querySelectorAll('.ad-undo-btn').forEach(btn => {
  btn.addEventListener('click', function() {
    const article = document.getElementById(this.dataset.id);
    article.querySelector('.skill-promo-inner').classList.remove('d-none');
    article.querySelector('.ad-hidden-notice').classList.add('d-none');
  });
});

document.querySelectorAll('.ad-remove-btn').forEach(btn => {
  btn.addEventListener('click', function() {
    document.getElementById(this.dataset.id).remove();
  });
});
</script>