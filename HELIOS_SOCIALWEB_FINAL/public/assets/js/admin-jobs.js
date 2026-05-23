// File: public/assets/js/admin-jobs.js (Phiên bản AJAX đầy đủ)

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
            if (!submitButton) return;
            
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

    // --- KHỞI TẠO TOM SELECT (giữ nguyên) ---
    const tomSelectOptions = { plugins: ['remove_button'], sortField: { field: "text", direction: "asc" } };
    const tomSelectAddCompany = new TomSelect('#addJobModal select[name="MaCongTy"]', tomSelectOptions);
    const tomSelectAddSkills = new TomSelect('#addJobModal select[name="skills[]"]', tomSelectOptions);
    const tomSelectEditCompany = new TomSelect('#editJobModal select[name="MaCongTy"]', tomSelectOptions);
    const tomSelectEditSkills = new TomSelect('#editJobModal select[name="skills[]"]', tomSelectOptions);

    // --- XỬ LÝ FORM THÊM MỚI ---
    const addForm = document.getElementById('addJobForm');
    if (addForm) {
        handleFormSubmit(addForm, () => location.reload());
    }

    // --- XỬ LÝ FORM SỬA ---
    const editForm = document.getElementById('editJobForm');
    const editJobModal = new bootstrap.Modal(document.getElementById('editJobModal'));
    
    // 1. Điền dữ liệu vào form khi bấm nút Sửa
    document.querySelectorAll('.btn-edit').forEach(button => {
        button.addEventListener('click', async function() {
            const jobData = JSON.parse(this.getAttribute('data-job'));
            
            // Xóa dữ liệu cũ trong TomSelect
            tomSelectEditCompany.clear(true);
            tomSelectEditSkills.clear(true);
            editForm.reset();

            // Điền dữ liệu thông thường
            for (const key in jobData) {
                const input = editForm.querySelector(`[name="${key}"]`);
                if (input) {
                    if (key === 'MaCongTy') {
                        tomSelectEditCompany.setValue(jobData[key], true);
                    } else {
                        input.value = jobData[key];
                    }
                }
            }
            
            // Lấy và điền các kỹ năng của công việc
            try {
                const response = await fetch(`/helios/public/admin/jobs/get-skills?id=${jobData.MaCongViec}`);
                const skillIds = await response.json();
                if (skillIds) {
                    tomSelectEditSkills.setValue(skillIds);
                }
            } catch (error) { console.error("Lỗi khi lấy kỹ năng:", error); }
            
            editJobModal.show();
        });
    });

    // 2. Gửi form sửa bằng AJAX
    if (editForm) {
        handleFormSubmit(editForm, () => location.reload());
    }

    // --- XỬ LÝ NÚT XÓA ---
    document.querySelectorAll('.btn-delete-job').forEach(button => {
        button.addEventListener('click', async function() {
            if (!confirm('Bạn có chắc chắn muốn xóa tin tuyển dụng này không?')) {
                return;
            }

            const jobId = this.getAttribute('data-job-id');
            const formData = new FormData();
            formData.append('MaCongViec', jobId);

            try {
                const response = await fetch('/helios/public/admin/jobs/delete', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                if (data.success) {
                    alert(data.message || 'Xóa thành công!');
                    location.reload();
                } else {
                    alert('Lỗi: ' + (data.message || 'Không thể xóa.'));
                }
            } catch (error) {
                console.error('Error deleting job:', error);
                alert('Đã có lỗi kết nối khi xóa.');
            }
        });
    });
});