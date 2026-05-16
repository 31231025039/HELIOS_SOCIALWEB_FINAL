document.addEventListener("DOMContentLoaded", function() {

    // 1. ĐÁNH DẤU 1 THÔNG BÁO LÀ ĐÃ ĐỌC
    document.querySelectorAll('.btn-mark-read').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            
            let notiItem = this.closest('.noti-item');
            let notiId = notiItem.getAttribute('data-noti-id');

            let formData = new FormData();
            formData.append('noti_id', notiId);

            fetch('/helios/public/noti/mark-read', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Update UI: Xóa nền xanh, xóa chấm xanh, xóa nút check
                    notiItem.classList.remove('unread');
                    let dot = notiItem.querySelector('.noti-unread-dot');
                    if (dot) dot.remove();
                    this.remove(); 
                    
                    updateBadgeCount(); // Giảm số lượng đỏ trên Header
                }
            });
        });
    });

    // 2. XÓA 1 THÔNG BÁO
    document.querySelectorAll('.btn-delete-noti').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            if (!confirm("Xóa thông báo này?")) return;

            let notiItem = this.closest('.noti-item');
            let notiId = notiItem.getAttribute('data-noti-id');

            let formData = new FormData();
            formData.append('noti_id', notiId);

            fetch('/helios/public/noti/delete', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Hiệu ứng mờ dần rồi xóa khỏi màn hình
                    notiItem.style.transition = 'opacity 0.3s';
                    notiItem.style.opacity = '0';
                    setTimeout(() => {
                        if (notiItem.classList.contains('unread')) updateBadgeCount();
                        notiItem.remove();
                    }, 300);
                }
            });
        });
    });

    // 3. ĐÁNH DẤU TẤT CẢ LÀ ĐÃ ĐỌC
    const btnMarkAll = document.getElementById('btnMarkAllRead');
    if (btnMarkAll) {
        btnMarkAll.addEventListener('click', function(e) {
            e.preventDefault();

            fetch('/helios/public/noti/mark-all-read', { method: 'POST' })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Xóa class unread ở mọi item
                    document.querySelectorAll('.noti-item.unread').forEach(item => {
                        item.classList.remove('unread');
                        let dot = item.querySelector('.noti-unread-dot');
                        if (dot) dot.remove();
                        let btnRead = item.querySelector('.btn-mark-read');
                        if (btnRead) btnRead.remove();
                    });
                    // Tắt luôn badge đỏ
                    let badge = document.querySelector('.noti-badge-new');
                    if (badge) badge.remove();
                }
            });
        });
    }

    // Hàm phụ: Giảm số đếm trên cái Badge đỏ
    function updateBadgeCount() {
        let badge = document.querySelector('.noti-badge-new');
        if (badge) {
            let currentText = badge.textContent; // Ví dụ "2 mới"
            let count = parseInt(currentText);
            if (count > 1) {
                badge.textContent = (count - 1) + ' mới';
            } else {
                badge.remove();
            }
        }
    }

});