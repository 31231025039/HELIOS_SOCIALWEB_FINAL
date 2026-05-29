<?php
$baseUrl = '/helios/public/';
?>
<div class="container-xl py-4">
    <div class="row g-3">
        <?php include VIEW_PATH_APP . '/layouts/sidebar_left.php'; ?>
        
        <main class="col-12 col-lg-9">
            <div class="h-card p-4 mb-3 bg-white border rounded-3 shadow-sm">
                <div class="row align-items-center">
                    <div class="col-md-7">
                        <h5 class="fw-bold text-navy mb-2">Tìm kiếm đồng nghiệp & Mở rộng mạng lưới</h5>
                        <p class="text-muted small mb-0">
                            Nhà tuyển dụng thường chú ý đến những ứng viên có mạng lưới kết nối mạnh mẽ trên Helios. Hãy gõ tìm kiếm những người bạn quen biết xung quanh.
                        </p>
                    </div>
                    <div class="col-md-5 mt-3 mt-md-0">
                        <div class="input-group position-relative">
                            <input type="text" class="form-control rounded-start-pill ps-3 pe-5" id="networkSearchInput" placeholder="Nhập tên bạn bè...">
                            
                            <i class="bi bi-x-circle-fill text-muted position-absolute top-50 translate-middle-y d-none" id="btnClearSearch" style="right: 70px; z-index: 10; cursor: pointer; font-size: 16px;"></i>
                            
                            <button class="btn btn-primary rounded-end-pill px-4" id="btnNetworkSearch" style="z-index: 11;">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="h-card mb-4 bg-white border rounded-3 shadow-sm">
                <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0"><i class="bi bi-person-plus-fill me-2 text-primary"></i>Lời mời kết nối đang chờ</h6>
                </div>
                <div class="p-3">
                    <?php if (!empty($pendingInvitations)) : ?>
                        <div class="row g-3">
                            <?php foreach ($pendingInvitations as $invite) : ?>
                                <div class="col-12 col-md-6" id="invite-box-<?= $invite['connection_id'] ?>">
                                    <div class="border rounded-3 p-3 d-flex align-items-center gap-3 bg-light">
                                        <a href="about-me?id=<?= $invite['id'] ?>" class="text-decoration-none">
                                            <div class="network-avatar bg-primary text-white d-flex align-items-center justify-content-center rounded-circle fw-bold" style="width:55px; height:55px; overflow:hidden;">
                                                <?php if (!empty($invite['img'])) : ?>
                                                    <img src="<?= $baseUrl . $invite['img'] ?>" class="w-100 h-100 object-fit-cover" alt="Avatar">
                                                <?php else : ?>
                                                    <?= strtoupper(substr($invite['name'], 0, 1)) ?>
                                                <?php endif; ?>
                                            </div>
                                        </a>
                                        <div class="flex-grow-1">
                                            <h6 class="fw-bold mb-0">
                                                <a href="about-me?id=<?= $invite['id'] ?>" class="text-decoration-none text-dark"><?= htmlspecialchars($invite['name']) ?></a>
                                            </h6>
                                            <div class="small text-muted mb-2 text-truncate" style="max-width: 220px;"><?= htmlspecialchars($invite['bio'] ?? 'Thành viên Helios') ?></div>
                                            <div class="d-flex gap-2">
                                                <button class="btn btn-primary btn-sm px-3 btn-accept-invite" data-id="<?= $invite['connection_id'] ?>">Chấp nhận</button>
                                                <button class="btn btn-outline-secondary btn-sm btn-ignore-invite" data-id="<?= $invite['connection_id'] ?>">Bỏ qua</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else : ?>
                        <div class="text-muted small text-center py-2">Không có lời mời nào đang chờ duyệt.</div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="h-card mb-4 bg-white border rounded-3 shadow-sm">
                <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0"><i class="bi bi-person-lines-fill me-2 text-info"></i>Danh bạ của bạn</h6>
                </div>
                <div class="p-3">
                    <?php if (!empty($connections)) : ?>
                        <div class="row g-3">
                            <?php foreach ($connections as $conn) : ?>
                                <div class="col-12 col-md-6 col-xl-4">
                                    <div class="border rounded-3 p-3 d-flex align-items-center gap-3 bg-white shadow-sm h-100">
                                        <a href="about-me?id=<?= $conn['id'] ?>" class="text-decoration-none flex-shrink-0">
                                            <div class="network-avatar bg-secondary text-white d-flex align-items-center justify-content-center rounded-circle fw-bold" style="width:50px; height:50px; overflow:hidden;">
                                                <?php if (!empty($conn['img'])) : ?>
                                                    <img src="<?= $baseUrl . $conn['img'] ?>" class="w-100 h-100 object-fit-cover" alt="Avatar">
                                                <?php else : ?>
                                                    <?= strtoupper(substr($conn['name'], 0, 1)) ?>
                                                <?php endif; ?>
                                            </div>
                                        </a>
                                        <div class="flex-grow-1 min-w-0">
                                            <h6 class="fw-bold mb-1 text-truncate">
                                                <a href="about-me?id=<?= $conn['id'] ?>" class="text-decoration-none text-dark"><?= htmlspecialchars($conn['name']) ?></a>
                                                <?php if($conn['verified']): ?><i class="bi bi-patch-check-fill text-primary"></i><?php endif; ?>
                                            </h6>
                                            <div class="small text-muted mb-2 text-truncate"><?= htmlspecialchars($conn['bio'] ?? 'Thành viên Helios') ?></div>
                                            <div class="d-flex gap-2">
                                                <a href="<?= $baseUrl ?>message?with=<?= $conn['id'] ?>" class="btn btn-primary btn-sm rounded-pill flex-grow-1 fw-bold">
                                                    <i class="bi bi-chat-dots-fill me-1"></i> Nhắn tin
                                                </a>
                                                <button class="btn btn-outline-danger btn-sm rounded-pill fw-bold px-3 btn-unfriend" data-id="<?= $conn['id'] ?>" title="Hủy kết bạn">
                                                    <i class="bi bi-person-dash-fill"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else : ?>
                        <div class="text-muted small text-center py-4">Bạn chưa có kết nối nào. Hãy tìm kiếm và kết nối thêm bạn bè!</div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="h-card bg-white border rounded-3 shadow-sm">
                <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0"><i class="bi bi-people-fill me-2 text-success"></i>Gợi ý kết nối cho bạn</h6>
                    <button class="btn btn-sm btn-link text-decoration-none text-muted p-0" id="btnReloadSuggestions"><i class="bi bi-arrow-clockwise"></i> Làm mới</button>
                </div>
                <div class="p-3">
                    
                    <div class="row g-3 row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-4" id="suggestedGrid">
                        <div class="col-12 text-center text-muted py-4">
                            <span class="spinner-border spinner-border-sm text-primary me-2"></span> Đang tải dữ liệu...
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<div class="modal fade" id="quickProfileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 pb-0 position-absolute w-100" style="z-index: 10;">
                <button type="button" class="btn-close btn-close-white bg-dark rounded-circle p-2" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" id="quickProfileBody">
                <div class="text-center py-5">
                    <span class="spinner-border text-primary" style="width: 3rem; height: 3rem;"></span>
                    <p class="mt-2 text-muted">Đang tải hồ sơ...</p>
                </div>
            </div>
        </div>
    </div>
</div>
