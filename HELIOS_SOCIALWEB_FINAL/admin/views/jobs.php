<!-- File: admin/views/jobs.php (Đã cập nhật để khớp với bảng `congviec`) -->

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Quản lý tuyển dụng</h1>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addJobModal">
        <i class="bi bi-plus-circle"></i> Thêm tin tuyển dụng
    </button>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="/helios/public/admin/jobs">
            <div class="input-group">
                <input type="text" class="form-control" name="search" placeholder="Tìm kiếm theo tiêu đề hoặc công ty..." value="<?php echo htmlspecialchars($searchTerm ?? ''); ?>">
                <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i> Tìm kiếm</button>
                <?php if (!empty($searchTerm)): ?>
                    <a href="/helios/public/admin/companies" class="btn btn-outline-secondary">Xóa</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Tiêu đề</th>
                    <th>Mô tả ngắn</th>
                    <th>Công ty</th>
                    <th>Hạn nộp</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($jobs)): ?>
                    <tr>
                        <td colspan="5" class="text-center">Chưa có tin tuyển dụng nào.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($jobs as $job): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($job['TieuDe']); ?></td>
                            <td><?php echo htmlspecialchars(mb_substr($job['MoTa'], 0, 50)) . '...'; ?></td>
                            <td><?php echo htmlspecialchars($job['TenCongTy']); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($job['HanNop'])); ?></td>
                            <td>
                                <button class="btn btn-sm btn-warning btn-edit" data-job='<?php echo json_encode($job, JSON_HEX_QUOT | JSON_HEX_TAG); ?>'>
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button class="btn btn-sm btn-danger btn-delete-job" data-job-id="<?php echo $job['MaCongViec']; ?>">
                                    <i class="bi bi-trash-fill"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
// === Định nghĩa Form Fields dùng chung (ĐÃ CẬP NHẬT) ===
function renderJobFormFields($companies = [], $skills = []) {
    $html = '
        <div class="row">
            <div class="col-md-8 mb-3">
                <label class="form-label fw-bold">Tiêu đề công việc</label>
                <input type="text" class="form-control" name="TieuDe" required>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label fw-bold">Công ty</label>
                <select class="form-select" name="MaCongTy" required>
                    <option value="" disabled selected>-- Chọn công ty --</option>';
    if (!empty($companies)) {
        foreach ($companies as $company) {
            $html .= '<option value="' . htmlspecialchars($company['MaCongTy']) . '">' . htmlspecialchars($company['TenCongTy']) . '</option>';
        }
    }
    $html .= '
                </select>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label fw-bold">Nơi làm việc</label>
            <input type="text" class="form-control" name="NoiLamViec" required>
        </div>
        <div class="mb-3">
            <label class="form-label fw-bold">Mô tả công việc</label>
            <textarea class="form-control" name="MoTa" rows="4" required></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label fw-bold">Yêu cầu ứng viên</label>
            <textarea class="form-control" name="YeuCau" rows="4" required></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label fw-bold">Quyền lợi</label>
            <textarea class="form-control" name="QuyenLoi" rows="3" required></textarea>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Mức lương</label>
                <input type="text" class="form-control" name="MucLuong" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Hạn nộp hồ sơ</label>
                <input type="date" class="form-control" name="HanNop" required>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label fw-bold">Kỹ năng yêu cầu</label>
            <select multiple class="form-select" name="skills[]" placeholder="Gõ để tìm kiếm và chọn kỹ năng...">';
    if (!empty($skills)) {
        foreach ($skills as $skill) {
            $html .= '<option value="' . htmlspecialchars($skill['MaKyNang']) . '">' . htmlspecialchars($skill['TenKyNang']) . '</option>';
        }
    }
    $html .= '
            </select>
        </div>';
    return $html;
}
?>

<!-- =================================== -->
<!-- MODAL THÊM TIN TUYỂN DỤNG -->
<!-- =================================== -->
<div class="modal fade" id="addJobModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Thêm tin tuyển dụng mới</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="addJobModal" action="/helios/public/admin/jobs/create" method="POST">
        <div class="modal-body">
            <?php echo renderJobFormFields($companies, $skills); ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
          <button type="submit" class="btn btn-primary">Thêm mới</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- =================================== -->
<!-- MODAL CHỈNH SỬA TIN TUYỂN DỤNG -->
<!-- =================================== -->
<div class="modal fade" id="editJobModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Chỉnh sửa tin tuyển dụng</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="editJobForm" action="/helios/public/admin/jobs/update" method="POST">
        <input type="hidden" name="MaCongViec" id="editMaCongViec">
        <div class="modal-body">
            <?php echo renderJobFormFields($companies, $skills); ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
          <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
        </div>
      </form>
    </div>
  </div>
</div>