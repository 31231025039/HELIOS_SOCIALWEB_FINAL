// File: public/assets/js/admin-companies.js (Phiên bản AJAX)

document.addEventListener('DOMContentLoaded', function() {
    
    /**
     * HÀM CHUNG ĐỂ XỬ LÝ SUBMIT FORM BẰNG AJAX
     */
    async function handleFormSubmit(form, successCallback) {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const url = this.getAttribute('action');
            const submitButton = this.querySelector('button[type="submit"]');
            const originalButtonText = submitButton.innerHTML;
            
            submitButton.disabled = true;
            submitButton.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Đang xử lý...`;

            try {
                const response = await fetch(url, { method: 'POST', body: formData });
                const data = await response.json();

                if (data.success) {
                    alert(data.message || 'Thao tác thành công!');
                    if (successCallback) {
                        successCallback(data, form);
                    }
                } else {
                    alert('Lỗi: ' + (data.message || 'Không thể hoàn thành thao tác.'));
                }
            } catch (error) {
                console.error('Error submitting form:', error);
                alert('Đã có lỗi kết nối xảy ra.');
            } finally {
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonText;
            }
        });
    }

    // --- XỬ LÝ FORM THÊM MỚI ---
    const addForm = document.getElementById('addCompanyForm');
    if (addForm) {
        handleFormSubmit(addForm, () => {
            location.reload(); // Tải lại trang sau khi thêm thành công
        });
    }

    // --- XỬ LÝ FORM SỬA ---
    const editForm = document.getElementById('editCompanyForm');
    const editCompanyModal = new bootstrap.Modal(document.getElementById('editCompanyModal'));
    
    // 1. Điền dữ liệu vào form khi bấm nút Sửa
    document.querySelectorAll('.btn-edit-company').forEach(button => {
        button.addEventListener('click', function() {
            const companyData = JSON.parse(this.getAttribute('data-company'));
            
            // Điền dữ liệu vào các trường input
            editForm.querySelector('#editMaCongTy').value = companyData.MaCongTy;
            editForm.querySelector('#editTenCongTy').value = companyData.TenCongTy;
            editForm.querySelector('#editMoTa').value = companyData.MoTa;

            editCompanyModal.show();
        });
    });

    // 2. Gửi form sửa bằng AJAX
    if (editForm) {
        handleFormSubmit(editForm, () => {
            location.reload(); // Tải lại trang sau khi sửa thành công
        });
    }

    // --- XỬ LÝ NÚT XÓA ---
    document.querySelectorAll('.btn-delete-company').forEach(button => {
        button.addEventListener('click', async function() {
            if (!confirm('Bạn có chắc chắn muốn xóa công ty này không?')) {
                return;
            }

            const companyId = this.getAttribute('data-company-id');
            const formData = new FormData();
            formData.append('MaCongTy', companyId);

            try {
                const response = await fetch('/helios/public/admin/companies/delete', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                if (data.success) {
                    alert(data.message || 'Xóa thành công!');
                    location.reload(); // Tải lại trang để cập nhật danh sách
                } else {
                    alert('Lỗi: ' + (data.message || 'Không thể xóa.'));
                }
            } catch (error) {
                console.error('Error deleting company:', error);
                alert('Đã có lỗi kết nối xảy ra khi xóa.');
            }
        });
    });

});