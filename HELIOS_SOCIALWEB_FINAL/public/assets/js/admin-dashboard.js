// File: public/assets/js/admin-dashboard.js (Phiên bản mới)
document.addEventListener('DOMContentLoaded', function () {
    const dataContainer = document.getElementById('dashboard-data-container');
    if (!dataContainer) {
        console.error('Dashboard data container not found!');
        return;
    }

    const dashboardData = JSON.parse(dataContainer.getAttribute('data-initial'));

    /**
     * ---- BIỂU ĐỒ 1: TĂNG TRƯỞNG NGƯỜI DÙNG ----
     */
    const userGrowthCtx = document.getElementById('userGrowthChart');
    if (userGrowthCtx && dashboardData.charts && dashboardData.charts.userGrowth) {
        const chartData = dashboardData.charts.userGrowth;
        new Chart(userGrowthCtx, {
            type: 'line',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: 'Người dùng mới',
                    data: chartData.data,
                    borderColor: 'rgb(54, 162, 235)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,          // Tự động co giãn theo kích thước màn hình
                maintainAspectRatio: false, // Không giữ tỉ lệ cố định, cho phép set chiều cao tự do
                interaction: {
                    mode: 'index',         // Hiện tooltip theo cột dọc (tất cả điểm cùng ngày)
                    intersect: false       // Không cần hover đúng vào điểm, chỉ cần gần là hiện
                },
                plugins: { 
                    legend: { display: false } // Ẩn chú thích (legend) phía trên biểu đồ
                },
                scales: { 
                    y: { 
                        beginAtZero: true,      // Trục Y luôn bắt đầu từ 0
                        ticks: { precision: 0 } // Chỉ hiện số nguyên, không có số thập phân
                    } 
                }
            }
        });
    }

    /**
     * ---- BIỂU ĐỒ 2: HOẠT ĐỘNG NỘI DUNG ----
     */
    const contentActivityCtx = document.getElementById('contentActivityChart');
    if (contentActivityCtx && dashboardData.charts && dashboardData.charts.contentActivity) {
        const chartData = dashboardData.charts.contentActivity;
        new Chart(contentActivityCtx, {
            type: 'bar',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: 'Bài viết mới',
                    data: chartData.posts,
                    backgroundColor: 'rgba(75, 192, 192, 0.7)',
                }, {
                    label: 'Tin tuyển dụng mới',
                    data: chartData.jobs,
                    backgroundColor: 'rgba(255, 159, 64, 0.7)',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, stacked: true, ticks: { precision: 0 } },
                    x: { stacked: true } // Gom 2 cột lại với nhau theo ngày
                },
                plugins: {
                    tooltip: { mode: 'index', intersect: false }
                }
            }
        });
    }
    
    /**
     * ---- BỘ LỌC THÁNG/NĂM (Giữ nguyên) ----
     */
    const monthSelect = document.getElementById('dashboardMonth');
    const yearSelect = document.getElementById('dashboardYear');

    function handleFilterChange() {
        if (!monthSelect || !yearSelect) return;
        const month = monthSelect.value;
        const year = yearSelect.value;
        window.location.href = `/helios/public/admin/dashboard?month=${month}&year=${year}`;
    }

    if (monthSelect) monthSelect.addEventListener('change', handleFilterChange);
    if (yearSelect) yearSelect.addEventListener('change', handleFilterChange);
});