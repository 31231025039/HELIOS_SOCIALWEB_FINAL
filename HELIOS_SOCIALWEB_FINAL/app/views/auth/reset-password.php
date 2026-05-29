<div class="container-xl" style="max-width: 500px;">
    <h1 class="text-center mb-4">Đặt lại mật khẩu</h1>
    <div class="h-card p-4">
        <form action="<?= $GLOBALS['baseUrl'] ?>reset-password" method="POST" id="resetPasswordForm">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') ?>">

            <div class="mb-3">
                <label for="password" class="form-label">Mật khẩu mới</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Xác nhận mật khẩu mới</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Lưu mật khẩu mới</button>
        </form>
    </div>
</div>