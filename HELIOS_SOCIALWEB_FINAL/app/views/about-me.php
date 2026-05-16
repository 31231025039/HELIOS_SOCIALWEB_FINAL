<?php
// app/Views/about-me.php
// $baseUrl được truyền từ main.php
?>
<div class="container-xl py-3">
  <div class="row g-3">

    <!-- Main Content Column -->
    <div class="col-12 col-lg-9">

      <!-- Intro Section -->
      <section class="h-card position-relative mb-3">
        <div class="profile-cover">
          <img src="<?php echo $baseUrl; ?>assets/images/Cover.png" alt="Ảnh bìa" class="img-fluid w-100 d-block">
        </div>

        <div class="profile-avatar-lg-page position-absolute">VY</div>

        <div class="p-3">
          <div class="d-flex justify-content-end mb-2">
              <!-- Nút mở Modal -->
              <button type="button" class="btn btn-outline-secondary btn-sm me-2" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                  <i class="bi bi-pencil-fill"></i>
              </button>
          </div>

          <div class="profile-intro-details">
            <h4 class="fw-bold mb-0"><?php echo htmlspecialchars($userData['HoTen']); ?></h4>
            <p class="text-muted mb-1 text-nowrap-md"><?php echo htmlspecialchars($userData['TieuDe']); ?></p>
            <p class="small text-muted mb-1 text-nowrap-md"><?php echo htmlspecialchars($userData['DiaDiem']); ?></p>
            <p class="small text-muted mb-2">487 kết nối</p>

            <div class="d-flex flex-wrap gap-2">
              <button class="btn btn-primary fw-bold">Kết nối</button>
              <button class="btn btn-outline-secondary fw-bold">Nhắn tin</button>
            </div>
          </div>
        </div>
      </section>

      <!-- About Section -->
      <section class="h-card p-3 mb-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <h5 class="fw-bold mb-0">Giới thiệu</h5>
          <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#editBioModal">
            <i class="bi bi-pencil-fill"></i>
          </button>
        </div>
        <p class="post-body">
          <?php echo nl2br(htmlspecialchars($userData['Bio'])); ?>
        </p>
      </section>

      <!-- Experience Section -->
      <section class="h-card p-3 mb-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="fw-bold mb-0">Kinh nghiệm</h5>
          <!-- Nút mở Form Thêm Mới -->
          <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#addExpModal">
            <i class="bi bi-plus-lg"></i>
          </button>
        </div>

        <!-- VÒNG LẶP PHP: Duyệt qua từng kinh nghiệm trong Database -->
        <?php if (!empty($kinhNghiemList)): ?>
            <?php foreach ($kinhNghiemList as $kn): ?>
                <div class="d-flex mb-4 position-relative">
                  <!-- Icon mặc định (nếu có Logo Cty trong DB thì gắn vào) -->
                  <div class="company-logo-sm me-3 bg-light border d-flex align-items-center justify-content-center">
                      <i class="bi bi-building text-secondary fs-4"></i>
                  </div>
                  
                  <div class="flex-grow-1">
                    <h6 class="fw-bold mb-0"><?php echo htmlspecialchars($kn['ViTri']); ?></h6>
                    <p class="small text-muted mb-0"><?php echo htmlspecialchars($kn['CongTy']); ?></p>
                    
                    <!-- Xử lý hiển thị Ngày tháng -->
                    <p class="small text-muted mb-1">
                      <?php echo date('m/Y', strtotime($kn['ThoiGianTu'])); ?> – 
                      <?php echo $kn['ThoiGianDen'] ? date('m/Y', strtotime($kn['ThoiGianDen'])) : 'Hiện tại'; ?>
                    </p>
                    
                    <div class="post-body text-muted">
                        <?php echo nl2br(htmlspecialchars($kn['MoTa'])); ?>
                    </div>
                  </div>

                  <!-- NÚT SỬA: Mở Modal tương ứng với ID của kinh nghiệm này -->
                  <button class="btn btn-light btn-sm position-absolute top-0 end-0" data-bs-toggle="modal" data-bs-target="#editExpModal_<?php echo $kn['MaKinhNghiem']; ?>">
                    <i class="bi bi-pencil-fill text-muted"></i>
                  </button>
                </div>

                <!-- ==========================================
                    MODAL SỬA (NẰM BÊN TRONG VÒNG LẶP)
                =========================================== -->
                <div class="modal fade" id="editExpModal_<?php echo $kn['MaKinhNghiem']; ?>" tabindex="-1" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                      <div class="modal-header"><h5 class="modal-title fw-bold">Sửa kinh nghiệm</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                      <form action="<?php echo $baseUrl; ?>about-me/edit-experience" method="POST">
                        <div class="modal-body">
                          <!-- TRƯỜNG ẨN: Gửi ID lên server để biết đang sửa dòng nào -->
                          <input type="hidden" name="makinhnghiem" value="<?php echo $kn['MaKinhNghiem']; ?>">
                          
                          <div class="mb-3">
                            <label class="form-label text-muted small">Vị trí *</label>
                            <input type="text" class="form-control" name="vitri" value="<?php echo htmlspecialchars($kn['ViTri']); ?>" required>
                          </div>
                          <div class="mb-3">
                            <label class="form-label text-muted small">Tên công ty *</label>
                            <input type="text" class="form-control" name="congty" value="<?php echo htmlspecialchars($kn['CongTy']); ?>" required>
                          </div>
                          <div class="row">
                              <div class="col-6 mb-3">
                                <label class="form-label text-muted small">Từ ngày *</label>
                                <input type="date" class="form-control" name="tungay" value="<?php echo $kn['ThoiGianTu']; ?>" required>
                              </div>
                              <div class="col-6 mb-3">
                                <label class="form-label text-muted small">Đến ngày (Để trống nếu đang làm)</label>
                                <input type="date" class="form-control" name="denngay" value="<?php echo $kn['ThoiGianDen']; ?>">
                              </div>
                          </div>
                          <div class="mb-3">
                            <label class="form-label text-muted small">Mô tả công việc</label>
                            <textarea class="form-control" name="mota" rows="4"><?php echo htmlspecialchars($kn['MoTa']); ?></textarea>
                          </div>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Hủy</button>
                          <button type="submit" class="btn btn-primary fw-bold">Lưu thay đổi</button>
                      </div>
                      </form>
                    </div>
                  </div>
                </div>
                <!-- KẾT THÚC MODAL SỬA -->

            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-muted small text-center py-3">Chưa có thông tin kinh nghiệm.</p>
        <?php endif; ?>
      </section>

      <!-- Education Section -->
      <section class="h-card p-3 mb-3">
        
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="fw-bold mb-0">Học vấn</h5>
          <!-- NÚT THÊM MỚI (Bỏ nút Sửa ở đây) -->
          <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#addEduModal">
            <i class="bi bi-plus-lg"></i>
          </button>
        </div>

        <!-- VÒNG LẶP LẤY DỮ LIỆU TỪ DATABASE -->
        <?php if (!empty($hocVanList)): ?>
            <?php foreach ($hocVanList as $hv): ?>
                <div class="d-flex mb-4 position-relative">
                  <div class="company-logo-sm me-3 bg-light border d-flex align-items-center justify-content-center">
                      <i class="bi bi-bank text-secondary fs-4"></i>
                  </div>
                  <div class="flex-grow-1">
                    <h6 class="fw-bold mb-0"><?php echo htmlspecialchars($hv['TruongHoc']); ?></h6>
                    <p class="small text-muted mb-0"><?php echo htmlspecialchars($hv['ChuyenNganh']); ?></p>
                    <p class="small text-muted mb-0">
                      <!-- Chỉ lấy Năm để in ra -->
                      <?php echo date('Y', strtotime($hv['ThoiGianTu'])); ?> – 
                      <?php echo $hv['ThoiGianDen'] ? date('Y', strtotime($hv['ThoiGianDen'])) : 'Hiện tại'; ?>
                    </p>
                  </div>

                  <!-- NÚT SỬA ĐẶT CẠNH TỪNG TRƯỜNG -->
                  <button class="btn btn-light btn-sm position-absolute top-0 end-0" data-bs-toggle="modal" data-bs-target="#editEduModal_<?php echo $hv['MaHocVan']; ?>">
                    <i class="bi bi-pencil-fill text-muted"></i>
                  </button>
                </div>

                <!-- MODAL SỬA (NẰM TRONG VÒNG LẶP) -->
                <div class="modal fade" id="editEduModal_<?php echo $hv['MaHocVan']; ?>" tabindex="-1" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                      <div class="modal-header"><h5 class="modal-title fw-bold">Sửa học vấn</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                      <form action="<?php echo $baseUrl; ?>about-me/edit-education" method="POST">
                        <div class="modal-body">
                          <input type="hidden" name="mahocvan" value="<?php echo $hv['MaHocVan']; ?>">
                          <div class="mb-3"><label class="form-label text-muted small">Trường học *</label><input type="text" class="form-control" name="truonghoc" value="<?php echo htmlspecialchars($hv['TruongHoc']); ?>" required></div>
                          <div class="mb-3"><label class="form-label text-muted small">Ngành học/Bằng cấp *</label><input type="text" class="form-control" name="chuyennganh" value="<?php echo htmlspecialchars($hv['ChuyenNganh']); ?>" required></div>
                          <div class="row">
                              <div class="col-6 mb-3"><label class="form-label text-muted small">Từ ngày *</label><input type="date" class="form-control" name="tungay" value="<?php echo $hv['ThoiGianTu']; ?>" required></div>
                              <div class="col-6 mb-3"><label class="form-label text-muted small">Đến ngày</label><input type="date" class="form-control" name="denngay" value="<?php echo $hv['ThoiGianDen']; ?>"></div>
                          </div>
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Hủy</button>
                          <button type="submit" class="btn btn-primary fw-bold">Lưu thay đổi</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>

            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-muted small text-center py-3">Chưa có thông tin học vấn.</p>
        <?php endif; ?>
      </section>

      <!-- Skills Section -->
      <section class="h-card p-3 mb-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="fw-bold mb-0">Kỹ năng</h5>
          <!-- CHỈ 1 NÚT BÚT CHÌ DUY NHẤT -->
          <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#manageSkillsModal">
            <i class="bi bi-pencil-fill"></i>
          </button>
        </div>
        
        <!-- In tất cả kỹ năng đang có ra ngoài -->
        <div class="d-flex flex-wrap gap-2">
          <?php if (!empty($userSkills)): ?>
              <?php foreach ($userSkills as $skill): ?>
                  <span class="badge bg-light text-dark border p-2"><?php echo htmlspecialchars($skill['TenKyNang']); ?></span>
              <?php endforeach; ?>
          <?php else: ?>
              <span class="text-muted small">Chưa có kỹ năng nào.</span>
          <?php endif; ?>
        </div>
      </section>
    </div>

    <!-- Right Sidebar Column -->
    <aside class="col-12 col-lg-3 d-none d-lg-block">
      <div class="h-card p-3 mb-3">
        <h6 class="fw-bold mb-3">Những người bạn có thể biết</h6>
        <div class="d-flex align-items-center mb-3">
          <div class="profile-suggestion-avatar bg-primary me-2">NM</div>
          <div>
            <div class="fw-bold small">Nguyễn Minh</div>
            <div class="text-muted extra-small">Chuyên viên Phân tích Dữ liệu</div>
            <button class="btn btn-outline-primary btn-sm mt-1">Kết nối</button>
          </div>
        </div>
        <div class="d-flex align-items-center mb-3">
          <div class="profile-suggestion-avatar bg-success me-2">HT</div>
          <div>
            <div class="fw-bold small">Hoàng Tuấn</div>
            <div class="text-muted extra-small">Product Manager</div>
            <button class="btn btn-outline-primary btn-sm mt-1">Kết nối</button>
          </div>
        </div>
        <button class="see-all-btn w-100">Xem thêm</button>
      </div>

      <div class="h-card p-3">
        <h6 class="fw-bold mb-3">Những người đã xem hồ sơ này cũng xem</h6>
        <div class="d-flex align-items-center mb-3">
          <div class="profile-suggestion-avatar bg-info me-2">LH</div>
          <div>
            <div class="fw-bold small">Lê Hằng</div>
            <div class="text-muted extra-small">Sinh viên CNTT</div>
            <button class="btn btn-outline-primary btn-sm mt-1">Kết nối</button>
          </div>
        </div>
        <div class="d-flex align-items-center mb-3">
          <div class="profile-suggestion-avatar bg-warning me-2">PT</div>
          <div>
            <div class="fw-bold small">Phạm Tú</div>
            <div class="text-muted extra-small">Business Analyst</div>
            <button class="btn btn-outline-primary btn-sm mt-1">Kết nối</button>
          </div>
        </div>
        <button class="see-all-btn w-100">Xem thêm</button>
      </div>
    </aside>
  </div>

  <!-- Modal Chỉnh sửa phần giới thiệu -->
  <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered"> <!-- Thêm modal-dialog-centered để popup nằm giữa màn hình -->
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title fw-bold" id="editProfileModalLabel">Chỉnh sửa thông tin giới thiệu</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
        </div>
        
        <form action="<?php echo $baseUrl; ?>about-me/update" method="POST">
          <div class="modal-body">
            
            <div class="mb-3">
              <label class="form-label text-muted small mb-1">Họ và tên *</label>
              <!-- Thuộc tính name="hoten" sẽ được Controller nhận qua $_POST['hoten'] -->
              <input type="text" class="form-control" name="hoten" value="<?php echo htmlspecialchars($userData['HoTen']); ?>" required>
            </div>

            <div class="mb-3">
              <label class="form-label text-muted small mb-1">Tiêu đề (Headline) *</label>
              <input type="text" class="form-control" name="tieude" value="<?php echo htmlspecialchars($userData['TieuDe']); ?>" required>
            </div>

            <div class="mb-3">
              <label class="form-label text-muted small mb-1">Tỉnh/Thành phố</label>
              <input type="text" class="form-control" name="diadiem" value="<?php echo htmlspecialchars($userData['DiaDiem']); ?>">
            </div>

          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Hủy</button>
            <button type="submit" class="btn btn-primary fw-bold">Lưu thay đổi</button>
          </div>
        </form>        
      </div>
    </div>
  </div>

  <!-- Modal Chỉnh sửa phần Giới thiệu (Bio) -->
  <div class="modal fade" id="editBioModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg"> <!-- modal-lg cho form rộng ra -->
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title fw-bold">Chỉnh sửa Giới thiệu</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
        </div>
        
        <form action="<?php echo $baseUrl; ?>about-me/update-bio" method="POST">
          <div class="modal-body">
            <p class="small text-muted">Bạn có thể viết về số năm kinh nghiệm, lĩnh vực chuyên môn hoặc kỹ năng của mình.</p>
            <!-- Dùng thẻ textarea để nhập đoạn văn dài -->
            <textarea class="form-control" name="bio" rows="6" required><?php echo htmlspecialchars($userData['Bio']); ?></textarea>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Hủy</button>
            <button type="submit" class="btn btn-primary fw-bold">Lưu thay đổi</button>
          </div>
        </form>
        
      </div>
    </div>
  </div>
  <!-- POPUP THÊM MỚI KINH NGHIỆM (Ẩn) -->
  <div class="modal fade" id="addExpModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header"><h5 class="modal-title fw-bold">Thêm kinh nghiệm mới</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        
        <form action="<?php echo $baseUrl; ?>about-me/add-experience" method="POST">
          <div class="modal-body">
            <!-- Các ô input trống bóc, không có thuộc tính value -->
            <div class="mb-3"><label class="form-label text-muted small">Vị trí *</label><input type="text" class="form-control" name="vitri" required></div>
            <div class="mb-3"><label class="form-label text-muted small">Tên công ty *</label><input type="text" class="form-control" name="congty" required></div>
            <div class="row">
                <div class="col-6 mb-3"><label class="form-label text-muted small">Từ ngày *</label><input type="date" class="form-control" name="tungay" required></div>
                <div class="col-6 mb-3"><label class="form-label text-muted small">Đến ngày (Để trống nếu đang làm)</label><input type="date" class="form-control" name="denngay"></div>
            </div>
            <div class="mb-3"><label class="form-label text-muted small">Mô tả công việc</label><textarea class="form-control" name="mota" rows="4"></textarea></div>
          </div>
          <div class="modal-footer"><button type="submit" class="btn btn-primary fw-bold">Thêm kinh nghiệm</button></div>
        </form>
      </div>
    </div>
  </div>
  <!-- MODAL THÊM MỚI HỌC VẤN -->
  <div class="modal fade" id="addEduModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header"><h5 class="modal-title fw-bold">Thêm học vấn mới</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <form action="<?php echo $baseUrl; ?>about-me/add-education" method="POST">
          <div class="modal-body">
            <div class="mb-3"><label class="form-label text-muted small">Trường học *</label><input type="text" class="form-control" name="truonghoc" required></div>
            <div class="mb-3"><label class="form-label text-muted small">Ngành học/Bằng cấp *</label><input type="text" class="form-control" name="chuyennganh" required></div>
            <div class="row">
                <div class="col-6 mb-3"><label class="form-label text-muted small">Từ ngày *</label><input type="date" class="form-control" name="tungay" required></div>
                <div class="col-6 mb-3"><label class="form-label text-muted small">Đến ngày (Để trống nếu đang học)</label><input type="date" class="form-control" name="denngay"></div>
            </div>
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Hủy</button>
              <button type="submit" class="btn btn-primary fw-bold">Thêm học vấn</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <!-- MODAL QUẢN LÝ KỸ NĂNG -->
  <div class="modal fade" id="manageSkillsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header"><h5 class="modal-title fw-bold">Quản lý Kỹ năng</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        
        <div class="modal-body">
          <!-- 1. Danh sách kỹ năng đang có (kèm nút xóa) -->
          <p class="fw-bold small mb-2">Kỹ năng hiện tại của bạn:</p>
          <div class="d-flex flex-wrap gap-2 mb-4">
            <?php foreach ($userSkills as $skill): ?>
                <form action="<?php echo $baseUrl; ?>about-me/delete-skill" method="POST" class="d-inline">
                    <input type="hidden" name="makynang" value="<?php echo $skill['MaKyNang']; ?>">
                    <span class="badge bg-light text-dark border p-2 d-flex align-items-center gap-2">
                        <?php echo htmlspecialchars($skill['TenKyNang']); ?>
                        <!-- Nút bấm chữ X để xóa -->
                        <button type="submit" class="btn-close" style="font-size: 8px;" aria-label="Xóa"></button>
                    </span>
                </form>
            <?php endforeach; ?>
          </div>

          <hr>

          <!-- 2. Form chọn thêm kỹ năng mới -->
          <p class="fw-bold small mb-2">Thêm kỹ năng mới:</p>
          <form action="<?php echo $baseUrl; ?>about-me/add-skill" method="POST" class="d-flex gap-2">
              <select class="form-select form-select-sm" name="makynang" required>
                  <option value="" selected disabled>-- Chọn kỹ năng --</option>
                  <?php foreach ($availableSkills as $newSkill): ?>
                      <option value="<?php echo $newSkill['MaKyNang']; ?>"><?php echo htmlspecialchars($newSkill['TenKyNang']); ?></option>
                  <?php endforeach; ?>
              </select>
              <button type="submit" class="btn btn-primary btn-sm fw-bold px-3">Thêm</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>