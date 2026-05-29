<div class="container-xl py-5" style="max-width: 500px;">
    <h1 class="text-center mb-4">Đăng nhập vào Helios</h1>
    <div class="h-card p-4">
        <form action="<?= $GLOBALS['baseUrl'] ?>login" method="POST" id="loginForm">
            <div class="mb-3">
                <label for="email" class="form-label">Địa chỉ Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Mật khẩu</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Đăng nhập</button>
        </form>
        <div class="text-center mt-3">
            <a href="<?= $GLOBALS['baseUrl'] ?>forgot-password" class="d-block mb-2">Quên mật khẩu?</a>
            <span>Chưa có tài khoản? <a href="<?= $GLOBALS['baseUrl'] ?>register">Tạo tài khoản mới</a></span>
        </div>
    </div>
</div>
