// File: public/assets/js/auth.js

document.addEventListener('DOMContentLoaded', function() {

    async function handleAuthFormSubmit(formElement) {
        formElement.addEventListener('submit', async function(e) {
            e.preventDefault(); 

            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            if (!submitButton) return;

            const originalButtonText = submitButton.innerHTML;
            let isSuccess = false; 

            submitButton.disabled = true;
            submitButton.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang xử lý...`;

            try {
                const response = await fetch(this.action, {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();

                if (result.success) {
                    isSuccess = true; 
                    
                    if (formElement.id === 'registerForm') {
                        document.querySelector('.h-card').innerHTML = `
                            <div class="text-center py-4">
                                <i class="bi bi-envelope-check text-success display-1"></i>
                                <h3 class="mt-3 fw-bold">Đăng ký thành công!</h3>
                                <p class="text-muted">Chúng tôi đã gửi một liên kết kích hoạt đến email của bạn.</p>
                                <p class="text-muted">Vui lòng kiểm tra hộp thư đến (hoặc thư mục Spam).</p>
                            </div>
                        `;
                    } else if (formElement.id === 'loginForm') {
                        window.location.href = result.redirectUrl;
                    } 
                    // XỬ LÝ CHO FORM QUÊN MẬT KHẨU
                    else if (formElement.id === 'forgotPasswordForm') {
                        document.querySelector('.h-card').innerHTML = `
                            <div class="text-center py-4">
                                <i class="bi bi-send-check text-primary display-1"></i>
                                <h4 class="mt-3 fw-bold">Kiểm tra email của bạn</h4>
                                <p class="text-muted">${result.message}</p>
                                <a href="/helios/public/login" class="btn btn-outline-primary mt-3">Quay lại Đăng nhập</a>
                            </div>
                        `;
                    } 
                    // XỬ LÝ CHO FORM ĐẶT LẠI MẬT KHẨU
                    else if (formElement.id === 'resetPasswordForm') {
                        document.querySelector('.h-card').innerHTML = `
                            <div class="text-center py-4">
                                <i class="bi bi-check-circle-fill text-success display-1"></i>
                                <h4 class="mt-3 fw-bold">Đổi mật khẩu thành công!</h4>
                                <p class="text-muted">${result.message}</p>
                                <a href="/helios/public/login" class="btn btn-primary mt-3">Đăng nhập ngay</a>
                            </div>
                        `;
                    }

                } else {
                    alert((result.message || 'Thao tác không thành công.'));
                }

            } catch (error) {
                console.error('Error:', error);
                alert('Đã có lỗi kết nối xảy ra. Vui lòng thử lại.');
            } finally {
                if (!isSuccess) {
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalButtonText;
                } else if (formElement.id !== 'registerForm' && formElement.id !== 'forgotPasswordForm' && formElement.id !== 'resetPasswordForm') {
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalButtonText;
                }
            }
        });
    }

    // Áp dụng hàm xử lý cho tất cả các form
    const forms = ['registerForm', 'loginForm', 'forgotPasswordForm', 'resetPasswordForm'];
    forms.forEach(id => {
        const form = document.getElementById(id);
        if (form) handleAuthFormSubmit(form);
    });
});