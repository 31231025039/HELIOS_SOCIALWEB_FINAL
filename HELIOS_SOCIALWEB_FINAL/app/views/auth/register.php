<div class="container-xl py-5" style="max-width: 500px;">
    <h1 class="text-center mb-4">Tạo tài khoản Helios</h1>
    <div class="h-card p-4">
        <form action="<?= $GLOBALS['baseUrl'] ?>register" method="POST" id="registerForm">
            <div class="mb-3">
                <label for="name" class="form-label">Họ và tên</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">Loại tài khoản</label>
                <select class="form-select" id="role" name="role">
                    <option value="User" selected>Người dùng (User)</option>
                    <option value="Admin">Quản trị viên (Admin)</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Địa chỉ Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Mật khẩu</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Xác nhận mật khẩu</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Đăng ký</button>
        </form>
        <p class="text-center small mt-3">
            Đã có tài khoản? <a href="<?= $GLOBALS['baseUrl'] ?>login">Đăng nhập ngay</a>
        </p>
    </div>
</div>