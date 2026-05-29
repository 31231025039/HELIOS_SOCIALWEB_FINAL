<?php 
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Quản lý người dùng</h1>
    <div class="text-muted fw-bold">Tổng: <span id="textTotalUsers" class="text-primary">0</span></div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <div class="input-group gap-2">
            <input type="text" class="form-control" id="searchUserKeyword" placeholder="Tìm theo tên hoặc email...">
            <button id="btnSearchUser" class="btn btn-primary d-flex align-items-center gap-1">
                <i class="bi bi-search"></i> Tìm kiếm
            </button>
            <button id="resetUserFilters" class="btn btn-outline-secondary">Đặt lại</button>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="users-col-id">ID</th>
                        <th class="users-col-name">Họ và tên</th>
                        <th class="users-col-email">Email</th>
                        <th class="users-col-role">Vai trò</th>
                        <th class="users-col-status">Trạng thái</th>
                        <th class="users-col-date">Ngày tạo</th>
                        <th class="users-col-action text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody id="usersTableBody">
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                            Đang tải dữ liệu...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<?php
    $adminUsersJs = dirname(__DIR__, 2) . '/public/assets/js/admin-users.js';
    $adminUsersVersion = file_exists($adminUsersJs) ? filemtime($adminUsersJs) : time();
?>
