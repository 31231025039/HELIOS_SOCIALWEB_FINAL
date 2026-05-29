<div class="container-xl" style="max-width: 500px;">
    <h1 class="text-center mb-2">Quên mật khẩu</h1>
    <p class="text-center text-muted mb-4">Nhập địa chỉ email của bạn và chúng tôi sẽ gửi cho bạn một liên kết để đặt lại mật khẩu.</p>
    <div class="h-card p-4">
        <form action="<?= $GLOBALS['baseUrl'] ?>forgot-password" method="POST" id="forgotPasswordForm">
            <div class="mb-3">
                <label for="email" class="form-label">Địa chỉ Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Gửi liên kết đặt lại</button>
        </form>
        <p class="text-center small mt-3">
            <a href="<?= $GLOBALS['baseUrl'] ?>login">Quay lại trang Đăng nhập</a>
        </p>
    </div>
</div>