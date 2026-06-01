// public/assets/js/admin-users.js

document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("searchUserKeyword");
    const btnSearch = document.getElementById("btnSearchUser");
    const btnReset = document.getElementById("resetUserFilters");
    const tableBody = document.getElementById("usersTableBody");
    const textTotalUsers = document.getElementById("textTotalUsers");

    // Tự động gọi tải danh sách khi vừa load xong trang
    fetchUsers();

    // Sự kiện khi nhấn nút Tìm kiếm
    btnSearch.addEventListener("click", function () {
        fetchUsers(searchInput.value.trim());
    });

    // Sự kiện khi nhấn Enter trong ô tìm kiếm
    searchInput.addEventListener("keypress", function (e) {
        if (e.key === "Enter") {
            fetchUsers(searchInput.value.trim());
        }
    });

    // Đặt lại ô tìm kiếm
    btnReset.addEventListener("click", function () {
        searchInput.value = "";
        fetchUsers();
    });

    // Hàm gọi AJAX fetch danh sách người dùng từ Controller
    function fetchUsers(keyword = "") {
        tableBody.innerHTML = `<tr><td colspan="8" class="text-center py-4 text-muted">Đang tải dữ liệu...</td></tr>`;
        
        // Sử dụng đường dẫn tuyệt đối từ gốc thư mục
        let url = "/helios/public/admin/users/get-users";
        if (keyword) {
            url += `?keyword=${encodeURIComponent(keyword)}`;
        }

        fetch(url)
            .then(response => response.json())
            .then(res => {
                if (res.success) {
                    textTotalUsers.textContent = res.total;
                    renderUsersTable(res.data);
                } else {
                    tableBody.innerHTML = `<tr><td colspan="8" class="text-center py-4 text-danger">Lỗi dữ liệu: ${res.message}</td></tr>`;
                }
            })
            .catch(err => {
                console.error("Lỗi fetch users:", err);
                tableBody.innerHTML = `<tr><td colspan="8" class="text-center py-4 text-danger">Không thể kết nối đến máy chủ.</td></tr>`;
            });
    }

    // Viết lại hàm này để cập nhật Giao diện cực mượt, không chớp bảng
    function bindStatusEvents() {
        const buttons = document.querySelectorAll(".btn-toggle-status");
        buttons.forEach(button => {
            button.addEventListener("click", function (e) {
                e.preventDefault(); // Ngăn mọi hành vi mặc định
                
                const userId = this.getAttribute("data-id");
                
                // Lưu lại chữ trên nút và đổi sang icon xoay vòng (Loading)
                const originalText = this.innerHTML;
                this.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>`;
                this.disabled = true; // Khóa nút tránh click 2 lần

                const formData = new FormData();
                formData.append("id", userId);

                // Sử dụng đường dẫn tuyệt đối
                fetch("/helios/public/admin/users/toggle-status", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.json())
                .then(res => {
                    if (res.success) {
                        // TÌM DÒNG (TR) HIỆN TẠI VÀ CẬP NHẬT TRỰC TIẾP
                        const tr = this.closest("tr");
                        const statusTd = tr.cells[4]; // Cột Trạng thái (Cột thứ 5)
                        
                        if (res.new_status === "active") {
                            // Nếu trạng thái mới là active (Mở khóa thành công)
                            statusTd.innerHTML = `<span class="badge bg-success-subtle text-success p-2">Hoạt động</span>`;
                            
                            this.className = "btn btn-sm btn-outline-warning btn-toggle-status";
                            this.innerHTML = "Khóa";
                        } else {
                            // Nếu trạng thái mới là locked (Khóa thành công)
                            statusTd.innerHTML = `<span class="badge bg-danger-subtle text-danger p-2">Đã khóa</span>`;
                            
                            this.className = "btn btn-sm btn-success btn-toggle-status";
                            this.innerHTML = "Mở khóa";
                        }
                        
                        // Mở lại nút
                        this.disabled = false;
                        
                    } else {
                        alert("Thao tác thất bại: " + res.message);
                        this.innerHTML = originalText;
                        this.disabled = false;
                    }
                })
                .catch(err => {
                    console.error("Lỗi cập nhật trạng thái:", err);
                    alert("Có lỗi xảy ra trong quá trình xử lý.");
                    this.innerHTML = originalText;
                    this.disabled = false;
                });
            });
        });
    }

    // Hàm đổ mảng dữ liệu vào cấu trúc HTML của bảng
    function renderUsersTable(users) {
        if (!users || users.length === 0) {
            tableBody.innerHTML = `<tr><td colspan="8" class="text-center py-4 text-muted">Không tìm thấy tài khoản nào phù hợp.</td></tr>`;
            return;
        }

        let html = "";
        users.forEach(user => {
            // Định dạng badge trạng thái
            const statusBadge = user.status === "active" 
                ? `<span class="badge bg-success-subtle text-success p-2">Hoạt động</span>`
                : `<span class="badge bg-danger-subtle text-danger p-2">Đã khóa</span>`;

            // Định dạng nhãn vai trò tương ứng màu sắc
            const roleBadge = user.role === "Admin"
                ? `<span class="badge bg-primary text-white">Admin</span>`
                : `<span class="badge bg-secondary text-white">User</span>`;

            // Màu sắc nút hành động thay đổi dựa theo trạng thái 'active'
            const actionButton = user.status === "active"
                ? `<button class="btn btn-sm btn-outline-warning btn-toggle-status" data-id="${user.id}">Khóa</button>`
                : `<button class="btn btn-sm btn-success btn-toggle-status" data-id="${user.id}">Mở khóa</button>`;

            // CHÚ Ý: CHỈ CÓ ĐÚNG 8 THẺ <td> Ở ĐÂY, HOÀN TOÀN KHÔNG CÓ CỘT N/A
            html += `
                <tr>
                    <td class="users-col-id"><strong>${user.id}</strong></td>
                    <td class="users-col-name">${user.fullname || '<em class="text-muted">Chưa cập nhật</em>'}</td>
                    <td class="users-col-email"><span>${user.email}</span></td>
                    <td class="users-col-role">${roleBadge}</td>
                    <td class="users-col-status">${statusBadge}</td>
                    <td class="users-col-date">${user.created_at || '—'}</td>
                    <td class="users-col-action text-center">${actionButton}</td>
                </tr>
            `;
        });

        tableBody.innerHTML = html;

        // Đính kèm sự kiện kích hoạt nút thay đổi trạng thái (Khóa / Mở khóa)
        bindStatusEvents();
    }
});
