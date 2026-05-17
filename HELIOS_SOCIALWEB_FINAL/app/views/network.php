<?php
// app/views/network.php
?>
<div class="container-xl py-4">
    <div class="row g-3">
        <?php include VIEW_PATH . '/layouts/network_sidebar_left.php'; ?>
        <main class="col-12 col-lg-9">
            <!-- Banner -->
            <div class="h-card p-4 mb-3">
                <div class="row align-items-center">
                    <div class="col-md-7">
                        <h5 class="fw-bold text-navy">
                            Mời 5 đồng nghiệp của bạn kết nối ngay hôm nay
                        </h5>
                        <p class="text-muted small mb-0">
                            Nhà tuyển dụng thường chú ý đến những ứng viên có mạng lưới kết nối mạnh mẽ.
                            Hãy bắt đầu từ những người bạn quen biết.
                        </p>
                    </div>
                    <div class="col-md-5 text-center mt-3 mt-md-0">
                        <div class="d-flex justify-content-center gap-2 mb-3">
                            <div class="step-dot active">1</div>
                            <div class="step-dot">2</div>
                            <div class="step-dot">3</div>
                            <div class="step-dot">4</div>
                            <div class="step-dot">5</div>
                        </div>
                        <button class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                            <i class="bi bi-search me-2"></i>
                            Tìm kiếm bạn bè
                        </button>
                    </div>
                </div>
            </div>
            <!-- Pending invitations -->
            <div class="h-card mb-3">
                <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0">
                        Lời mời kết nối
                    </h6>
                    <a href="#" class="small fw-bold text-decoration-none text-primary">
                        Quản lý
                    </a>
                </div>
                <div class="p-3">
                    <?php if (!empty($pendingInvitations)) : ?>
                        <div class="row g-3">
                            <?php foreach ($pendingInvitations as $invite) : ?>
                                <div class="col-12 col-md-6">
                                    <div class="border rounded-3 p-3 d-flex align-items-center gap-3">
                                        <div class="network-avatar">
                                            <?php if (!empty($invite['AnhDaiDien'])) : ?>
                                                <img
                                                    src="<?= $baseUrl . $invite['AnhDaiDien'] ?>"
                                                    class="w-100 h-100 object-fit-cover rounded-circle"
                                                    alt="<?= htmlspecialchars($invite['HoTen']) ?>"
                                                >
                                            <?php else : ?>
                                                <?= strtoupper(substr($invite['HoTen'], 0, 1)) ?>
                                            <?php endif; ?>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="fw-bold mb-1">
                                                <?= htmlspecialchars($invite['HoTen']) ?>
                                            </h6>
                                            <div class="small text-muted mb-2">
                                                <?= htmlspecialchars($invite['TieuDe'] ?? '') ?>
                                            </div>
                                            <div class="d-flex gap-2">
                                                <button
                                                    class="btn btn-primary btn-sm btn-accept"
                                                    data-connection-id="<?= $invite['MaKetNoi'] ?>">
                                                    Chấp nhận
                                                </button>
                                                <button
                                                    class="btn btn-outline-secondary btn-sm btn-ignore"
                                                    data-connection-id="<?= $invite['MaKetNoi'] ?>">
                                                    Bỏ qua
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else : ?>
                        <div class="text-muted small">
                            Không có lời mời nào đang chờ
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <!-- May know -->
            <div class="h-card mb-3">
                <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0">
                        Những người bạn có thể biết
                    </h6>
                    <a href="#" class="small fw-bold text-decoration-none text-muted">
                        Xem tất cả
                    </a>
                </div>
                <div class="p-3">
                    <div class="row g-3" id="mayKnowGrid"></div>
                </div>
            </div>
            <!-- Popular -->
            <div class="h-card mb-3">
                <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0">
                        Phổ biến trên Helios
                    </h6>
                    <a href="#" class="small fw-bold text-decoration-none text-muted">
                        Hiển thị tất cả
                    </a>
                </div>
                <div class="p-3">
                    <div class="row g-3" id="popularGrid"></div>
                </div>
            </div>
            <!-- Suggested -->
            <div class="h-card">
                <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0">
                        Các đề xuất khác cho bạn
                    </h6>
                    <a href="#" class="small fw-bold text-decoration-none text-muted">
                        Hiển thị tất cả
                    </a>
                </div>
                <div class="p-3">
                    <div class="row g-3" id="suggestedGrid"></div>
                </div>
            </div>
        </main>
    </div>
</div>