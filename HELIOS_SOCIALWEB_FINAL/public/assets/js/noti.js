document.addEventListener("DOMContentLoaded", function () {
    const tabBtns = document.querySelectorAll('#notiTab button');
    const notiContainer = document.getElementById('notiListContainer');

    function loadNotifications(filter) {
        fetch('/helios/public/noti/filter?filter=' + filter)
            .then(res => res.json())
            .then(data => {
                if (data.html) {
                    notiContainer.innerHTML = data.html;
                    attachEventHandlers();
                }
            })
            .catch(err => console.error('Lỗi tải thông báo:', err));
    }

    tabBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            tabBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            loadNotifications(this.getAttribute('data-filter'));
        });
    });

    function attachEventHandlers() {
        document.querySelectorAll('.noti-item').forEach(item => {
            item.addEventListener('click', function () {
                const link = this.getAttribute('data-link');
                if (link) window.location.href = link;
            });
        });

        document.querySelectorAll('.btn-mark-read').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                const notiItem = this.closest('.noti-item');
                const notiId = notiItem.getAttribute('data-noti-id');
                const fd = new FormData();
                fd.append('noti_id', notiId);

                fetch('/helios/public/noti/mark-read', { method: 'POST', body: fd })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            notiItem.classList.remove('bg-light');
                            this.remove();
                            updateUnreadCount();
                        }
                    })
                    .catch(err => console.error('Lỗi đánh dấu đã đọc:', err));
            });
        });

        document.querySelectorAll('.btn-delete-noti').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                if (!confirm('Xóa thông báo này?')) return;

                const notiItem = this.closest('.noti-item');
                const notiId = notiItem.getAttribute('data-noti-id');
                const fd = new FormData();
                fd.append('noti_id', notiId);

                fetch('/helios/public/noti/delete', { method: 'POST', body: fd })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            notiItem.remove();
                            if (notiContainer.querySelectorAll('.noti-item').length === 0) {
                                notiContainer.innerHTML = '<div class="noti-empty text-center text-muted py-4"><i class="bi bi-inbox fs-1 d-block mb-2"></i><p class="mb-0">Không có thông báo nào.</p></div>';
                            }
                            updateUnreadCount();
                        }
                    })
                    .catch(err => console.error('Lỗi xóa thông báo:', err));
            });
        });
    }

    document.getElementById('btnMarkAllRead')?.addEventListener('click', function () {
        fetch('/helios/public/noti/mark-all-read', { method: 'POST' })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.querySelectorAll('.noti-item.bg-light').forEach(item => {
                        item.classList.remove('bg-light');
                        const btn = item.querySelector('.btn-mark-read');
                        if (btn) btn.remove();
                    });
                    updateUnreadCount();
                }
            })
            .catch(err => console.error('Lỗi đánh dấu tất cả đã đọc:', err));
    });

    const toggleBtn = document.getElementById('toggleNotifBtn');
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function () {
            const current = this.getAttribute('data-state') === '1' ? 1 : 0;
            const newState = current === 1 ? 0 : 1;

            fetch('/helios/public/noti/toggle-notifications', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'state=' + newState
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        this.setAttribute('data-state', newState);
                        this.innerHTML = newState === 1 ? '<i class="bi bi-bell"></i>' : '<i class="bi bi-bell-slash"></i>';
                        this.title = newState === 1 ? 'Tắt thông báo' : 'Bật thông báo';
                        this.setAttribute('aria-label', this.title);
                        bootstrap.Toast.getOrCreateInstance(document.getElementById('notiToast')).show();
                    }
                })
                .catch(err => console.error('Lỗi bật/tắt thông báo:', err));
        });
    }

    function updateUnreadCount() {
        fetch('/helios/public/noti/unread-count')
            .then(res => res.json())
            .then(data => {
                const count = parseInt(data.count, 10) || 0;
                const text = count > 99 ? '99+' : count;
                const badge = document.getElementById('totalUnreadBadge');
                if (badge) {
                    badge.textContent = text;
                    badge.classList.toggle('d-none', count === 0);
                }

                const bellBadge = document.getElementById('notiBadge');
                if (bellBadge) {
                    bellBadge.textContent = text;
                    bellBadge.style.display = count > 0 ? '' : 'none';
                }
            })
            .catch(err => console.error('Lỗi cập nhật số lượng chưa đọc:', err));
    }

    attachEventHandlers();
    updateUnreadCount();
    setInterval(updateUnreadCount, 30000);
});
