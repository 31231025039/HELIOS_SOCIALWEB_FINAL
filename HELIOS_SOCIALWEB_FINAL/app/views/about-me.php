<?php
// app/Views/about-me.php
// Đã có các biến: $userData, $kinhNghiemList, $hocVanList, $userSkills, $availableSkills, $isOwnProfile, $baseUrl
?>
<div class="container-xl py-3">
    <div class="row g-3">

        <!-- CỘT TRÁI: Nội dung chính -->
        <div class="col-12 col-lg-9">
            
            <!-- 1. INTRO SECTION (Ảnh bìa, Avatar, Tên, Headline) -->
            <section class="h-card position-relative mb-3">
                <div class="profile-cover">
                    <?php if (!empty($userData['AnhBia'])): ?>
                        <img src="<?= $baseUrl . htmlspecialchars($userData['AnhBia']) ?>" alt="Ảnh bìa" class="img-fluid w-100 d-block" id="coverImage">
                    <?php else: ?>
                        <img src="<?= $baseUrl ?>assets/images/Cover.png" alt="Ảnh bìa mặc định" class="img-fluid w-100 d-block" id="coverImage">
                    <?php endif; ?>
                    
                    <?php if ($isOwnProfile): ?>
                    <button class="btn btn-light btn-sm position-absolute" style="top: 15px; right: 15px;" data-bs-toggle="modal" data-bs-target="#editCoverModal">
                        <i class="bi bi-pencil-fill"></i>
                    </button>
                    <?php endif; ?>
                </div>

                <div class="profile-avatar-lg-page position-absolute">
                    <?php if (!empty($userData['AnhDaiDien'])): ?>
                        <img src="<?= $baseUrl . htmlspecialchars($userData['AnhDaiDien']) ?>" alt="Avatar" class="rounded-circle w-100 h-100" style="object-fit: cover;" id="avatarImage">
                    <?php else: ?>
                        <div class="rounded-circle w-100 h-100 d-flex align-items-center justify-content-center" style="background-color: #062b6b; color: white; font-size: 48px; font-weight: bold;" id="avatarImage">
                            <?php
                                $name = $userData['HoTen'] ?? '??';
                                $nameParts = explode(' ', trim($name));
                                $initials = '';
                                if (count($nameParts) >= 2) {
                                    $initials = mb_substr($nameParts[0], 0, 1) . mb_substr(end($nameParts), 0, 1);
                                } else if (count($nameParts) === 1 && mb_strlen($nameParts[0]) > 0) {
                                    $initials = mb_substr($nameParts[0], 0, 2);
                                } else { $initials = '??'; }
                                echo strtoupper($initials);
                            ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($isOwnProfile): ?>
                    <button class="btn btn-light btn-sm rounded-circle position-absolute" style="bottom: 5px; right: 5px; width: 32px; height: 32px; line-height: 1;" data-bs-toggle="modal" data-bs-target="#editAvatarModal">
                        <i class="bi bi-camera-fill"></i>
                    </button>
                    <?php endif; ?>
                </div>

                <div class="p-3 position-relative"> 
                    
                    <?php if ($isOwnProfile): ?>
                    <!-- Nút sửa thông tin: Thêm z-index để chắc chắn nó luôn nổi lên trên cùng -->
                    <button type="button" class="btn btn-outline-secondary btn-sm position-absolute" style="top: 15px; right: 15px; z-index: 10;" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                        <i class="bi bi-pencil-fill"></i>
                    </button>
                    <?php endif; ?>

                    <div class="profile-intro-details" style="padding-top: 65px !important;">
                        <h4 class="fw-bold mb-10"><?= htmlspecialchars($userData['HoTen']) ?></h4>
                        <p class="text-muted mb-1 text-nowrap-md"><?= htmlspecialchars($userData['TieuDe'] ?? '') ?></p>
                        <p class="small text-muted mb-1 text-nowrap-md"><?= htmlspecialchars($userData['DiaDiem'] ?? '') ?></p>
                        
                        <?php if (!$isOwnProfile): ?>
                        <div class="d-flex flex-wrap gap-2 mt-3">
                            <?php if (!$relStatus): ?>
                                <!-- Chưa kết nối -->
                                <button class="btn btn-primary fw-bold btn-connect" data-userid="<?= $userData['MaNguoiDung'] ?>"><i class="bi bi-person-plus-fill me-1"></i> Kết nối</button>
                                <a href="<?= $baseUrl ?>message?with=<?= $userData['MaNguoiDung'] ?>" class="btn btn-outline-primary fw-bold"><i class="bi bi-chat-dots-fill me-1"></i> Nhắn tin</a>
                            
                            <?php elseif ($relStatus === 'pending'): ?>
                                <!-- Đang chờ -->
                                <button class="btn btn-secondary fw-bold" disabled>Đang chờ...</button>
                                <a href="<?= $baseUrl ?>message?with=<?= $userData['MaNguoiDung'] ?>" class="btn btn-outline-primary fw-bold"><i class="bi bi-chat-dots-fill me-1"></i> Nhắn tin</a>
                            
                            <?php elseif ($relStatus === 'accepted'): ?>
                                <!-- Đã là bạn bè (Ẩn nút kết nối, nút Nhắn tin chuyển sang màu xanh đậm nổi bật) -->
                                <a href="<?= $baseUrl ?>message?with=<?= $userData['MaNguoiDung'] ?>" class="btn btn-primary fw-bold"><i class="bi bi-chat-dots-fill me-1"></i> Nhắn tin</a>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </section>

            <!-- 2. ABOUT SECTION (Giới thiệu) -->
            <section class="h-card p-3 mb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="fw-bold mb-0">Giới thiệu</h5>
                    <?php if ($isOwnProfile): ?>
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#editBioModal">
                        <i class="bi bi-pencil-fill"></i>
                    </button>
                    <?php endif; ?>
                </div>
                <p class="post-body text-muted" id="bioContent">
                    <?= !empty($userData['Bio']) ? nl2br(htmlspecialchars($userData['Bio'])) : 'Chưa có thông tin giới thiệu.' ?>
                </p>
            </section>

            <!-- 3. EXPERIENCE SECTION (Kinh nghiệm) -->
            <section class="h-card p-3 mb-3" id="experience-section">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Kinh nghiệm</h5>
                    <?php if ($isOwnProfile): ?>
                    <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#addExpModal">
                        <i class="bi bi-plus-lg"></i>
                    </button>
                    <?php endif; ?>
                </div>
                <div id="experience-list">
                    <?php if (!empty($kinhNghiemList)): ?>
                        <?php foreach ($kinhNghiemList as $kn): ?>
                            <div class="d-flex mb-4 position-relative">
                                <div class="company-logo-sm me-3 bg-light border d-flex align-items-center justify-content-center">
                                    <i class="bi bi-building text-secondary fs-4"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold mb-0"><?= htmlspecialchars($kn['ViTri']) ?></h6>
                                    <p class="small text-muted mb-0"><?= htmlspecialchars($kn['CongTy']) ?></p>
                                    <p class="small text-muted mb-1">
                                        <?= date('m/Y', strtotime($kn['ThoiGianTu'])) ?> – 
                                        <?= $kn['ThoiGianDen'] ? date('m/Y', strtotime($kn['ThoiGianDen'])) : 'Hiện tại' ?>
                                    </p>
                                    <div class="post-body text-muted">
                                        <?= nl2br(htmlspecialchars($kn['MoTa'])) ?>
                                    </div>
                                </div>
                                <?php if ($isOwnProfile): ?>
                                <button class="btn btn-light btn-sm position-absolute top-0 end-0" data-bs-toggle="modal" data-bs-target="#editExpModal_<?= $kn['MaKinhNghiem'] ?>">
                                    <i class="bi bi-pencil-fill text-muted"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted small py-2">Chưa có thông tin kinh nghiệm.</p>
                    <?php endif; ?>
                </div>
            </section>

            <!-- 4. EDUCATION SECTION (Học vấn) -->
            <section class="h-card p-3 mb-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Học vấn</h5>
                    <?php if ($isOwnProfile): ?>
                    <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#addEduModal">
                        <i class="bi bi-plus-lg"></i>
                    </button>
                    <?php endif; ?>
                </div>
                <div id="education-list">
                    <?php if (!empty($hocVanList)): ?>
                        <?php foreach ($hocVanList as $hv): ?>
                            <div class="d-flex mb-4 position-relative">
                                <div class="company-logo-sm me-3 bg-light border d-flex align-items-center justify-content-center">
                                    <i class="bi bi-bank text-secondary fs-4"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold mb-0"><?= htmlspecialchars($hv['TruongHoc']) ?></h6>
                                    <p class="small text-muted mb-0"><?= htmlspecialchars($hv['ChuyenNganh']) ?></p>
                                    <p class="small text-muted mb-0">
                                        <?= date('Y', strtotime($hv['ThoiGianTu'])) ?> – 
                                        <?= $hv['ThoiGianDen'] ? date('Y', strtotime($hv['ThoiGianDen'])) : 'Hiện tại' ?>
                                    </p>
                                </div>
                                <?php if ($isOwnProfile): ?>
                                <button class="btn btn-light btn-sm position-absolute top-0 end-0" data-bs-toggle="modal" data-bs-target="#editEduModal_<?= $hv['MaHocVan'] ?>">
                                    <i class="bi bi-pencil-fill text-muted"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted small py-2">Chưa có thông tin học vấn.</p>
                    <?php endif; ?>
                </div>
            </section>

            <!-- 5. SKILLS SECTION (Kỹ năng) -->
            <section class="h-card p-3 mb-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Kỹ năng</h5>
                    <?php if ($isOwnProfile): ?>
                    <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#manageSkillsModal">
                        <i class="bi bi-pencil-fill"></i>
                    </button>
                    <?php endif; ?>
                </div>
                <div id="skills-list" class="d-flex flex-wrap gap-2">
                    <?php if (!empty($userSkills)): ?>
                        <?php foreach ($userSkills as $skill): ?>
                            <span class="badge bg-light text-dark border p-2"><?= htmlspecialchars($skill['TenKyNang']) ?></span>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <span class="text-muted small">Chưa có kỹ năng nào.</span>
                    <?php endif; ?>
                </div>
            </section>

        </div>

        <!-- CỘT PHẢI: Sidebar -->
        <?php include __DIR__ . '/layouts/sidebar_right.php'; ?>

    </div>
</div>

<!-- ========================================================= -->
<!-- KHU VỰC CÁC MODALS CHỈNH SỬA (CHỈ RENDER NẾU LÀ TRANG CỦA MÌNH) -->
<!-- ========================================================= -->
<?php if ($isOwnProfile): ?>

    <!-- Modal Edit Profile (Tên, Tiêu đề...) -->
    <div class="modal fade" id="editProfileModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Chỉnh sửa thông tin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editProfileForm" action="<?= $baseUrl ?>about-me/update" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label text-muted small mb-1">Họ và tên *</label>
                            <input type="text" class="form-control" name="hoten" value="<?= htmlspecialchars($userData['HoTen']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small mb-1">Tiêu đề (Headline) *</label>
                            <input type="text" class="form-control" name="tieude" value="<?= htmlspecialchars($userData['TieuDe'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small mb-1">Tỉnh/Thành phố</label>
                            <input type="text" class="form-control" name="diadiem" value="<?= htmlspecialchars($userData['DiaDiem'] ?? '') ?>">
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

    <!-- Modal Edit Bio -->
    <div class="modal fade" id="editBioModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Chỉnh sửa Giới thiệu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editBioForm" action="<?= $baseUrl ?>about-me/update-bio" method="POST">
                    <div class="modal-body">
                        <textarea class="form-control" name="bio" rows="6" required><?= htmlspecialchars($userData['Bio'] ?? '') ?></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary fw-bold">Lưu thay đổi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Add Experience -->
    <div class="modal fade" id="addExpModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title fw-bold">Thêm kinh nghiệm mới</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <form id="addExpForm" action="<?= $baseUrl ?>about-me/add-experience" method="POST">
                    <div class="modal-body">
                        <div class="mb-3"><label class="form-label text-muted small">Vị trí *</label><input type="text" class="form-control" name="vitri" required></div>
                        <div class="mb-3"><label class="form-label text-muted small">Tên công ty *</label><input type="text" class="form-control" name="congty" required></div>
                        <div class="row">
                            <div class="col-6 mb-3"><label class="form-label text-muted small">Từ ngày *</label><input type="date" class="form-control" name="tungay" required></div>
                            <div class="col-6 mb-3"><label class="form-label text-muted small">Đến ngày</label><input type="date" class="form-control" name="denngay"></div>
                        </div>
                        <div class="mb-3"><label class="form-label text-muted small">Mô tả</label><textarea class="form-control" name="mota" rows="4"></textarea></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary fw-bold">Thêm kinh nghiệm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modals Edit Experience (Lặp qua từng kinh nghiệm) -->
    <?php if (!empty($kinhNghiemList)): ?>
        <?php foreach ($kinhNghiemList as $kn): ?>
        <div class="modal fade" id="editExpModal_<?= $kn['MaKinhNghiem'] ?>" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header"><h5 class="modal-title fw-bold">Sửa kinh nghiệm</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <form class="edit-exp-form" action="<?= $baseUrl ?>about-me/edit-experience" method="POST">
                        <div class="modal-body">
                            <input type="hidden" name="makinhnghiem" value="<?= $kn['MaKinhNghiem'] ?>">
                            <div class="mb-3"><label class="form-label text-muted small">Vị trí *</label><input type="text" class="form-control" name="vitri" value="<?= htmlspecialchars($kn['ViTri']) ?>" required></div>
                            <div class="mb-3"><label class="form-label text-muted small">Tên công ty *</label><input type="text" class="form-control" name="congty" value="<?= htmlspecialchars($kn['CongTy']) ?>" required></div>
                            <div class="row">
                                <div class="col-6 mb-3"><label class="form-label text-muted small">Từ ngày *</label><input type="date" class="form-control" name="tungay" value="<?= $kn['ThoiGianTu'] ?>" required></div>
                                <div class="col-6 mb-3"><label class="form-label text-muted small">Đến ngày</label><input type="date" class="form-control" name="denngay" value="<?= $kn['ThoiGianDen'] ?>"></div>
                            </div>
                            <div class="mb-3"><label class="form-label text-muted small">Mô tả</label><textarea class="form-control" name="mota" rows="4"><?= htmlspecialchars($kn['MoTa']) ?></textarea></div>
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
    <?php endif; ?>

    <!-- Modal Add Education -->
    <div class="modal fade" id="addEduModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title fw-bold">Thêm học vấn mới</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <form id="addEduForm" action="<?= $baseUrl ?>about-me/add-education" method="POST">
                    <div class="modal-body">
                        <div class="mb-3"><label class="form-label text-muted small">Trường học *</label><input type="text" class="form-control" name="truonghoc" required></div>
                        <div class="mb-3"><label class="form-label text-muted small">Ngành học/Bằng cấp *</label><input type="text" class="form-control" name="chuyennganh" required></div>
                        <div class="row">
                            <div class="col-6 mb-3"><label class="form-label text-muted small">Từ ngày *</label><input type="date" class="form-control" name="tungay" required></div>
                            <div class="col-6 mb-3"><label class="form-label text-muted small">Đến ngày</label><input type="date" class="form-control" name="denngay"></div>
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

    <!-- Modals Edit Education (Lặp qua từng học vấn) -->
    <?php if (!empty($hocVanList)): ?>
        <?php foreach ($hocVanList as $hv): ?>
        <div class="modal fade" id="editEduModal_<?= $hv['MaHocVan'] ?>" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header"><h5 class="modal-title fw-bold">Sửa học vấn</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <form class="edit-edu-form" action="<?= $baseUrl ?>about-me/edit-education" method="POST">
                        <div class="modal-body">
                            <input type="hidden" name="mahocvan" value="<?= $hv['MaHocVan'] ?>">
                            <div class="mb-3"><label class="form-label text-muted small">Trường học *</label><input type="text" class="form-control" name="truonghoc" value="<?= htmlspecialchars($hv['TruongHoc']) ?>" required></div>
                            <div class="mb-3"><label class="form-label text-muted small">Ngành học/Bằng cấp *</label><input type="text" class="form-control" name="chuyennganh" value="<?= htmlspecialchars($hv['ChuyenNganh']) ?>" required></div>
                            <div class="row">
                                <div class="col-6 mb-3"><label class="form-label text-muted small">Từ ngày *</label><input type="date" class="form-control" name="tungay" value="<?= $hv['ThoiGianTu'] ?>" required></div>
                                <div class="col-6 mb-3"><label class="form-label text-muted small">Đến ngày</label><input type="date" class="form-control" name="denngay" value="<?= $hv['ThoiGianDen'] ?>"></div>
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
    <?php endif; ?>

    <!-- Modal Manage Skills -->
    <div class="modal fade" id="manageSkillsModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title fw-bold">Quản lý Kỹ năng</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <p class="fw-bold small mb-2">Kỹ năng hiện tại:</p>
                    <div class="d-flex flex-wrap gap-2 mb-4">
                        <?php foreach ($userSkills as $skill): ?>
                            <form class="delete-skill-form" action="<?= $baseUrl ?>about-me/delete-skill" method="POST" class="d-inline">
                                <input type="hidden" name="makynang" value="<?= $skill['MaKyNang'] ?>">
                                <span class="badge bg-light text-dark border p-2 d-flex align-items-center gap-2">
                                    <?= htmlspecialchars($skill['TenKyNang']) ?>
                                    <button type="submit" class="btn-close" style="font-size: 8px;" aria-label="Xóa"></button>
                                </span>
                            </form>
                        <?php endforeach; ?>
                    </div>
                    <hr>
                    <p class="fw-bold small mb-2">Thêm kỹ năng mới:</p>
                    <form id="addSkillForm" action="<?= $baseUrl ?>about-me/add-skill" method="POST">
                        <!-- CHÚ Ý: name đã đổi thành mảng makynang[], có chữ multiple và thêm ID để JS gọi -->
                        <div class="mb-3">
                            <select id="multiSkillSelect" name="makynang[]" multiple placeholder="Gõ để tìm kiếm và chọn kỹ năng..." required>
                                <?php foreach ($availableSkills as $newSkill): ?>
                                    <option value="<?= $newSkill['MaKyNang'] ?>"><?= htmlspecialchars($newSkill['TenKyNang']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary btn-sm fw-bold px-4">Thêm các kỹ năng này</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Avatar -->
    <div class="modal fade" id="editAvatarModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cập nhật ảnh đại diện</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editAvatarForm" action="<?= $baseUrl ?>about-me/update-image" method="POST" enctype="multipart/form-data">
                    <div class="modal-body text-center">
                        <p class="text-muted small">Chọn ảnh mới từ thiết bị của bạn.</p>
                        <img id="avatarPreview" src="<?= $baseUrl . ($userData['AnhDaiDien'] ?? 'assets/images/default-avatar.png') ?>" class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover; border: 2px solid #eee;">
                        <input type="file" class="form-control" name="image_file" accept="image/*" required>
                        <input type="hidden" name="image_type" value="avatar">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Cover -->
    <div class="modal fade" id="editCoverModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cập nhật ảnh bìa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editCoverForm" action="<?= $baseUrl ?>about-me/update-image" method="POST" enctype="multipart/form-data">
                    <div class="modal-body text-center">
                        <p class="text-muted small">Để có kết quả tốt nhất, hãy tải lên ảnh có chiều rộng tối thiểu 1128 pixel.</p>
                        <img id="coverPreview" src="<?= $baseUrl . ($userData['AnhBia'] ?? 'assets/images/Cover.png') ?>" class="img-fluid rounded mb-3" style="max-height: 250px; object-fit: cover; border: 2px solid #eee;">
                        <input type="file" class="form-control" name="image_file" accept="image/*" required>
                        <input type="hidden" name="image_type" value="cover">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php endif; ?>