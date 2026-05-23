<!-- File: admin/views/companies.php -->

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Quản lý công ty</h1>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCompanyModal">
        <i class="bi bi-plus-circle"></i> Thêm công ty
    </button>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="/helios/public/admin/companies">
            <div class="input-group">
                <input type="text" class="form-control" name="search" placeholder="Tìm kiếm theo tên công ty..." value="<?php echo htmlspecialchars($searchTerm ?? ''); ?>">
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
                    <th>Logo</th>
                    <th>Tên công ty</th>
                    <th>Mô tả</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($companies)): ?>
                    <tr>
                        <td colspan="4" class="text-center">Chưa có công ty nào.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($companies as $company): ?>
                        <tr>
                            <td>
                                <img src="/helios/public<?php echo htmlspecialchars($company['Logo'] ?? '/assets/images/default-logo.png'); ?>" 
                                     alt="<?php echo htmlspecialchars($company['TenCongTy']); ?>" 
                                     style="width: 50px; height: 50px; object-fit: contain;">
                            </td>
                            <td class="align-middle"><?php echo htmlspecialchars($company['TenCongTy']); ?></td>
                            <td class="align-middle"><?php echo htmlspecialchars(mb_substr($company['MoTa'], 0, 80)) . '...'; ?></td>
                            <td class="align-middle">
                                <button class="btn btn-sm btn-warning btn-edit-company" data-company='<?php echo json_encode($company, JSON_HEX_QUOT | JSON_HEX_TAG); ?>'>
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button class="btn btn-sm btn-danger btn-delete-company" data-company-id="<?php echo $company['MaCongTy']; ?>">
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

<!-- MODAL THÊM CÔNG TY -->
<div class="modal fade" id="addCompanyModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Thêm công ty mới</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <form id="addCompanyForm" action="/helios/public/admin/companies/create" method="POST" enctype="multipart/form-data">
        <div class="modal-body">
            <div class="mb-3">
                <label class="form-label">Tên công ty</label>
                <input type="text" class="form-control" name="TenCongTy" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Mô tả ngắn</label>
                <textarea class="form-control" name="MoTa" rows="3"></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Logo</label>
                <input type="file" class="form-control" name="Logo" accept="image/*">
            </div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button><button type="submit" class="btn btn-primary">Thêm mới</button></div>
      </form>
    </div>
  </div>
</div>

<!-- MODAL SỬA CÔNG TY -->
<div class="modal fade" id="editCompanyModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Chỉnh sửa công ty</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <form id="editCompanyForm" action="/helios/public/admin/companies/update" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="MaCongTy" id="editMaCongTy">
        <div class="modal-body">
            <div class="mb-3">
                <label class="form-label">Tên công ty</label>
                <input type="text" class="form-control" name="TenCongTy" id="editTenCongTy" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Mô tả ngắn</label>
                <textarea class="form-control" name="MoTa" id="editMoTa" rows="3"></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Tải lên logo mới (để trống nếu không muốn thay đổi)</label>
                <input type="file" class="form-control" name="Logo" accept="image/*">
            </div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button><button type="submit" class="btn btn-primary">Lưu thay đổi</button></div>
      </form>
    </div>
  </div>
</div>