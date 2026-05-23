<?php
    // Các biến này được truyền từ AdminDashboardController
    $selectedMonth = $selectedMonth ?? (int) date('n');
    $selectedYear = $selectedYear ?? (int) date('Y');
    $dashboardData = $dashboardData ?? ['kpis' => [], 'charts' => []];

    $kpiCards = [
        ['key' => 'users', 'label' => 'Tổng Thành Viên', 'icon' => 'bi-people-fill', 'class' => 'users'],
        ['key' => 'posts', 'label' => 'Tổng Bài Viết', 'icon' => 'bi-file-earmark-text-fill', 'class' => 'posts'],
        ['key' => 'jobs', 'label' => 'Tổng Tin Tuyển Dụng', 'icon' => 'bi-briefcase-fill', 'class' => 'jobs'],
        ['key' => 'interactions', 'label' => 'Tương Tác Trong Tháng', 'icon' => 'bi-chat-heart-fill', 'class' => 'interactions'],
    ];
?>

<div class="dashboard-heading">
    <div>
        <h1>Dashboard</h1>
        <p>Tổng quan vận hành của HELIOS theo dữ liệu thật trong hệ thống.</p>
    </div>

    <form class="dashboard-filter" aria-label="Bộ lọc dashboard">
        <label>
            <span>Tháng</span>
            <select id="dashboardMonth" class="form-select form-select-sm">
                <option value="0" <?php echo $selectedMonth === 0 ? 'selected' : ''; ?>>
                    Tất cả 
                </option>
                
                <?php for ($month = 1; $month <= 12; $month++): ?>
                    <option value="<?php echo $month; ?>" <?php echo $month === $selectedMonth ? 'selected' : ''; ?>>
                        Tháng <?php echo $month; ?>
                    </option>
                <?php endfor; ?>
            </select>
        </label>
        <label>
            <span>Năm</span>
            <select id="dashboardYear" class="form-select form-select-sm">
                <?php 
                    // Hiển thị 5 năm gần nhất
                    $currentYear = date('Y');
                    for ($year = $currentYear; $year >= $currentYear - 4; $year--): 
                ?>
                    <option value="<?php echo $year; ?>" <?php echo $year === $selectedYear ? 'selected' : ''; ?>>
                        <?php echo $year; ?>
                    </option>
                <?php endfor; ?>
            </select>
        </label>
    </form>
</div>

<div class="admin-kpi-grid">
    <?php foreach ($kpiCards as $card): ?>
        <article class="admin-kpi-card admin-kpi-card--<?php echo $card['class']; ?>">
            <div class="admin-kpi-icon"><i class="bi <?php echo $card['icon']; ?>"></i></div>
            <div class="admin-kpi-content">
                <span class="admin-kpi-label"><?php echo htmlspecialchars($card['label']); ?></span>
                <strong class="admin-kpi-value" data-kpi="<?php echo $card['key']; ?>">
                    <?php echo number_format((int) ($dashboardData['kpis'][$card['key']] ?? 0)); ?>
                </strong>
            </div>
        </article>
    <?php endforeach; ?>
</div>

<div class="admin-chart-grid">
    <!-- BIỂU ĐỒ 1: TĂNG TRƯỞNG NGƯỜI DÙNG -->
    <section class="admin-chart-panel">
        <div class="admin-chart-head">
            <h2>Tăng trưởng người dùng</h2>
            <span>Số lượng tài khoản mới đăng ký theo ngày</span>
        </div>
        <!-- THAY ĐỔI ID -->
        <canvas id="userGrowthChart" height="300"></canvas>
        <div class="chart-footnote">
            💡 Di chuột vào biểu đồ để xem số liệu
        </div>
    </section>

    <!-- BIỂU ĐỒ 2: HOẠT ĐỘNG NỘI DUNG -->
    <section class="admin-chart-panel">
        <div class="admin-chart-head">
            <h2>Hoạt động nội dung</h2>
            <span>Số bài viết và tin tuyển dụng mới theo ngày</span>
        </div>
        <!-- THAY ĐỔI ID -->
        <canvas id="contentActivityChart" height="300"></canvas>
        <div class="chart-footnote">
            💡 Di chuột vào các cột để xem chi tiết
        </div>
    </section>
</div>

<!-- Div này chứa dữ liệu khởi tạo cho dashboard, JavaScript sẽ đọc từ đây -->
<div id="dashboard-data-container" data-initial='<?php echo json_encode($dashboardData, JSON_UNESCAPED_UNICODE); ?>' hidden></div>