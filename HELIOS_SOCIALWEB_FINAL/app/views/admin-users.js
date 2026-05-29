document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("searchUserKeyword");
    const btnSearch = document.getElementById("btnSearchUser");
    const btnReset = document.getElementById("resetUserFilters");
    const tableBody = document.getElementById("usersTableBody");
    const textTotalUsers = document.getElementById("textTotalUsers");

    fetchUsers();

    btnSearch.addEventListener("click", function () {
        fetchUsers(searchInput.value.trim());
    });

    searchInput.addEventListener("keypress", function (e) {
        if (e.key === "Enter") {
            fetchUsers(searchInput.value.trim());
        }
    });

    btnReset.addEventListener("click", function () {
        searchInput.value = "";
        fetchUsers();
    });

    function fetchUsers(keyword = "") {
        tableBody.innerHTML = `<tr><td colspan="8" class="text-center py-4 text-muted">Đang tải dữ liệu...</td></tr>`;
        
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

    function bindStatusEvents() {
        const buttons = document.querySelectorAll(".btn-toggle-status");
        buttons.forEach(button => {
            button.addEventListener("click", function (e) {
                e.preventDefault();  
                
                const userId = this.getAttribute("data-id");
                
                const originalText = this.innerHTML;
                this.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>`;
                this.disabled = true; 
                const formData = new FormData();
                formData.append("id", userId);

                fetch("/helios/public/admin/users/toggle-status", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.json())
                .then(res => {
                    if (res.success) {

                        const tr = this.closest("tr");
                        const statusTd = tr.cells[4]; 
                        
                        if (res.new_status === "active") {
                            
                            statusTd.innerHTML = `<span class="badge bg-success-subtle text-success p-2">Hoạt động</span>`;
                            
                            this.className = "btn btn-sm btn-outline-warning btn-toggle-status";
                            this.innerHTML = "Khóa";
                        } else {
                            
                            statusTd.innerHTML = `<span class="badge bg-danger-subtle text-danger p-2">Đã khóa</span>`;
                            
                            this.className = "btn btn-sm btn-success btn-toggle-status";
                            this.innerHTML = "Mở khóa";
                        }
                        
                        
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

    
    function renderUsersTable(users) {
        if (!users || users.length === 0) {
            tableBody.innerHTML = `<tr><td colspan="8" class="text-center py-4 text-muted">Không tìm thấy tài khoản nào phù hợp.</td></tr>`;
            return;
        }

        let html = "";
        users.forEach(user => { 
            const statusBadge = user.status === "active" 
                ? `<span class="badge bg-success-subtle text-success p-2">Hoạt động</span>`
                : `<span class="badge bg-danger-subtle text-danger p-2">Đã khóa</span>`;

            const roleBadge = user.role === "Admin"
                ? `<span class="badge bg-primary text-white">Admin</span>`
                : `<span class="badge bg-secondary text-white">User</span>`;

            const actionButton = user.status === "active"
                ? `<button class="btn btn-sm btn-outline-warning btn-toggle-status" data-id="${user.id}">Khóa</button>`
                : `<button class="btn btn-sm btn-success btn-toggle-status" data-id="${user.id}">Mở khóa</button>`;

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

        bindStatusEvents();
    }
});
