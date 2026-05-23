<?php
// File: admin/controllers/AdminDashboardController.php

class AdminDashboardController {
    
    private $statisticModel;

    public function __construct() {
        $this->statisticModel = new AdminStatisticModel();
    }

    public function index() {
        // Cho phép giá trị '0' (Cả năm) cho tháng
        $selectedMonth = (int) ($_GET['month'] ?? date('n'));
        $selectedYear = (int) ($_GET['year'] ?? date('Y'));

        // Gọi model để lấy dữ liệu tổng hợp
        $dashboardData = $this->statisticModel->getDashboardData($selectedMonth, $selectedYear);

        // Các biến dùng trong layout và view
        $pageTitle = "Dashboard";
        $activeMenu = "dashboard";
        
        // Định nghĩa các file JS cần thiết cho trang này
        $jsFiles = ['admin-dashboard.js']; 
        
        $contentView = VIEW_PATH_ADMIN . '/dashboard.php';
        
        // Nạp layout chính, layout sẽ tự động nạp các biến và file JS
        include VIEW_PATH_ADMIN . '/layouts/main.php';
    }
}