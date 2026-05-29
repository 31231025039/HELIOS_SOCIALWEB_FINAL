<?php $postTypes = $postTypes ?? []; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Quản lý bài viết</h1>
    <button id="btnAddPost" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Thêm bài viết
    </button>
</div>

<div class="row mb-4">
    <?php foreach ([
        ['totalPosts', 'file-earmark-text-fill', 'Tổng Bài Viết', '#0d4f9e,#062b6b'],
        ['eventCount', 'calendar-event-fill',    'Sự kiện',        '#0f8a5f,#086242'],
        ['postCount',  'pencil-square',           'Bài viết thường','#f5b400,#d68600'],
    ] as [$id, $icon, $label, $grad]): ?>
    <div class="col-md-4">
        <div class="card text-white" style="background:linear-gradient(135deg,<?= $grad ?>)">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-<?= $icon ?> fs-2"></i>
                <div><div class="small"><?= $label ?></div><strong class="fs-4" id="<?= $id ?>">0</strong></div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="card mb-4">
    <div class="card-body d-flex gap-2 flex-wrap">
        <input type="text" id="searchKeyword" class="form-control" placeholder="Tìm kiếm...">
        <select id="filterPostType" class="form-select" style="max-width:180px">
            <option value="">Tất cả loại</option>
            <?php foreach ($postTypes as $t): ?>
                <option value="<?= htmlspecialchars($t) ?>"><?= $t === 'event' ? 'Sự kiện' : 'Bài viết' ?></option>
            <?php endforeach; ?>
        </select>
        <select id="filterVisibility" class="form-select" style="max-width:180px">
            <option value="all">Tất cả</option>
            <option value="Public">Công khai</option>
            <option value="Private">Riêng tư</option>
        </select>
        <button id="resetFilters" class="btn btn-outline-secondary">Đặt lại</button>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
        <table class="table table-hover">
            <thead><tr>
                <th class="col-id">ID</th><th class="col-author">Tác giả</th><th class="col-type">Loại</th>
                <th class="col-content col-content-large">Nội dung</th><th class="col-visibility">Hiển thị</th><th class="col-date">Ngày đăng</th><th>Hành động</th>
            </tr></thead>
            <tbody id="postsTableBody">
                <tr><td colspan="7" class="text-center py-4 text-muted">Đang tải...</td></tr>
            </tbody>
        </table>
        </div>
    </div>
</div>

<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-eye-fill me-2"></i>Chi tiết bài viết</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailModalBody"></div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="formModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="formModalTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="formPostId">

                <div class="mb-3">
                    <label class="form-label fw-semibold">Loại bài viết</label>
                    <select id="formPostType" class="form-select">
                        <option value="post">Bài viết thường</option>
                        <option value="event">Sự kiện</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Nội dung <span class="text-danger">*</span></label>
                    <textarea id="formContent" rows="5" class="form-control" placeholder="Nhập nội dung..."></textarea>
                </div>

                <div id="eventFields" class="mb-3 p-3 border rounded bg-light" style="display:none">
                    <div class="fw-semibold mb-2 text-danger"><i class="bi bi-calendar-event me-1"></i>Thông tin sự kiện</div>
                    <div class="mb-2">
                        <label class="form-label">Tên sự kiện</label>
                        <input type="text" id="formEventName" class="form-control">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Địa điểm</label>
                        <input type="text" id="formEventLocation" class="form-control">
                    </div>
                    <div>
                        <label class="form-label">Thời gian</label>
                        <input type="datetime-local" id="formEventTime" class="form-control">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Hiển thị</label>
                    <select id="formVisibility" class="form-select">
                        <option value="Public">Công khai</option>
                        <option value="Private">Riêng tư</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Hình ảnh</label>
                    <div id="existingImagesWrap" style="display:none" class="mb-2">
                        <div class="text-muted small mb-1">Ảnh hiện tại:</div>
                        <div id="existingImages" class="d-flex flex-wrap gap-2"></div>
                    </div>
                    <input type="file" id="formImages" class="form-control" accept="image/jpeg,image/png,image/gif,image/webp" multiple>
                    <div class="form-text">Tối đa 5MB/ảnh. Định dạng: JPG, PNG, GIF, WEBP.</div>
                    <div id="imagePreview" class="d-flex flex-wrap gap-2 mt-2"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button id="btnSubmitPost" class="btn btn-primary">
                    <i class="bi bi-send me-1"></i><span id="btnSubmitLabel">Đăng bài</span>
                </button>
            </div>
        </div>
    </div>
</div>
