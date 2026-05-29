document.addEventListener('DOMContentLoaded', function() {

    // ==========================================================
    // HÀM CHUNG ĐỂ XỬ LÝ SUBMIT FORM BẰNG AJAX
    // ==========================================================
    async function handleFormSubmit(form, successCallback, isJsonRequest = true) {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const url = this.getAttribute('action');
            const submitButton = this.querySelector('button[type="submit"]');
            if (!submitButton) return; // Dừng nếu form không có nút submit

            const originalButtonText = submitButton.innerHTML;
            
            submitButton.disabled = true;
            submitButton.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang lưu...`;

            try {
                const response = await fetch(url, { method: 'POST', body: formData });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

                if (data.success) {
                    // Gọi callback nếu submit thành công
                    if (successCallback) {
                        successCallback(data, form);
                    }
                } else {
                    alert('Lỗi: ' + (data.message || 'Không thể hoàn thành thao tác.'));
                }
            } catch (error) {
                console.error('Error submitting form:', error);
                alert('Đã có lỗi kết nối xảy ra. Vui lòng thử lại.');
            } finally {
                // Luôn khôi phục lại nút bấm
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonText;
            }
        });
    }

    // ==========================================================
    // ÁP DỤNG AJAX CHO CÁC FORM
    // ==========================================================
    const reloadPageCallback = () => {
        alert('Cập nhật thành công!');
        location.reload();
    };

    // Các form này chỉ cần reload lại trang sau khi thành công
    const formsToReload = [
        '#editProfileForm', '#addExpForm', '#addEduForm', '#addSkillForm',
        '.edit-exp-form', '.edit-edu-form', '.delete-skill-form'
    ];

    formsToReload.forEach(selector => {
        document.querySelectorAll(selector).forEach(form => {
            handleFormSubmit(form, reloadPageCallback);
        });
    });

    // Form chỉnh sửa Bio có xử lý đặc biệt (không reload)
    const editBioForm = document.getElementById('editBioForm');
    if (editBioForm) {
        handleFormSubmit(editBioForm, (data) => {
            document.querySelector('#bioContent').innerHTML = data.newBioHtml;
            bootstrap.Modal.getInstance(document.getElementById('editBioModal')).hide();
            alert('Cập nhật Giới thiệu thành công!');
        });
    }

    // ==========================================================
    // XỬ LÝ UPLOAD ẢNH (Code từ trước)
    // ==========================================================
    const setupImageUpload = (formId, previewId, imageDisplayId, modalId) => {
        const form = document.getElementById(formId);
        if (!form) return;

        // Lấy instance của Modal (hộp thoại)
        let modalInstance = bootstrap.Modal.getInstance(document.getElementById(modalId));
        if (!modalInstance) {
            modalInstance = new bootstrap.Modal(document.getElementById(modalId));
        }

        const fileInput = form.querySelector('input[type="file"]');
        const preview = document.getElementById(previewId);

        // Hiển thị ảnh preview ngay khi người dùng vừa chọn file từ máy tính
        fileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                }
                reader.readAsDataURL(this.files[0]);
            }
        });

        // Gửi form bằng AJAX khi bấm "Lưu thay đổi"
        handleFormSubmit(form, (data) => {
            // Tắt popup
            if(modalInstance) modalInstance.hide();
            
            // Thay vì chỉ đổi src của 1 ảnh, ta reload lại trang 
            // để Avatar trên thanh Navbar cũng được cập nhật đồng bộ!
            location.reload(); 
        });
    };
    
    // Khởi tạo cho modal Avatar
    const avatarModalEl = document.getElementById('editAvatarModal');
    if (avatarModalEl) {
        const avatarModal = new bootstrap.Modal(avatarModalEl);
        setupImageUpload('editAvatarForm', 'avatarPreview', 'avatarImage', 'editAvatarModal');
    }

    // Khởi tạo cho modal Ảnh bìa
    const coverModalEl = document.getElementById('editCoverModal');
    if (coverModalEl) {
        const coverModal = new bootstrap.Modal(coverModalEl);
        setupImageUpload('editCoverForm', 'coverPreview', 'coverImage', 'editCoverModal');
    }
});