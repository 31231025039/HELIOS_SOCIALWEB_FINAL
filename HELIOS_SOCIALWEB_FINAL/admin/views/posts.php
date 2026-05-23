<?php $postTypes = $postTypes ?? []; ?>

<link rel="stylesheet" href="/helios/public/assets/css/admin.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<!-- KPI -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Quản lý bài viết</h1>
    <button id="btnAddPost" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Thêm bài viết
    </button>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-white" style="background: linear-gradient(135deg,#0d4f9e,#062b6b)">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-file-earmark-text-fill fs-2"></i>
                <div>
                    <div class="small">Tổng Bài Viết</div>
                    <strong class="fs-4" id="totalPosts">0</strong>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white" style="background: linear-gradient(135deg,#0f8a5f,#086242)">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-calendar-event-fill fs-2"></i>
                <div>
                    <div class="small">Sự kiện</div>
                    <strong class="fs-4" id="eventCount">0</strong>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white" style="background: linear-gradient(135deg,#f5b400,#d68600)">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-pencil-square fs-2"></i>
                <div>
                    <div class="small">Bài viết thường</div>
                    <strong class="fs-4" id="postCount">0</strong>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bộ lọc -->
<div class="card mb-4">
    <div class="card-body">
        <div class="input-group gap-2 flex-wrap">
            <input type="text" class="form-control" id="searchKeyword" placeholder="Tìm kiếm bài viết...">
            <select class="form-select" style="max-width:180px" id="filterPostType">
                <option value="">Tất cả loại</option>
                <?php foreach ($postTypes as $type): ?>
                    <option value="<?= htmlspecialchars($type) ?>"><?= $type == 'event' ? 'Sự kiện' : 'Bài viết' ?></option>
                <?php endforeach; ?>
            </select>
            <select class="form-select" style="max-width:180px" id="filterVisibility">
                <option value="all">Hiển thị tất cả</option>
                <option value="Public">Công khai</option>
                <option value="Private">Riêng tư</option>
            </select>
            <button id="resetFilters" class="btn btn-outline-secondary">Đặt lại</button>
        </div>
    </div>
</div>

<!-- Bảng -->
<div class="card">
    <div class="card-body">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tác giả</th>
                    <th data-sort="post_type">Loại</th>
                    <th>Nội dung</th>
                    <th data-sort="visibility">Hiển thị</th>
                    <th>Ngày đăng</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody id="postsTableBody">
                <tr><td colspan="7" class="text-center py-4 text-muted">Đang tải...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal xem chi tiết -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chi tiết bài viết</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalBody">Đang tải...</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="/helios/public/assets/js/admin-posts.js"></script>