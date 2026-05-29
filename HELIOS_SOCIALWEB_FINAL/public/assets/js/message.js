/* message.js - Xử lý trang tin nhắn Helios */

(function() {
    'use strict';

    // 1. CẤU HÌNH 
    const CFG = window.MSG_CONFIG || {};
    const BASE = CFG.baseUrl || '/helios/public/';
    let currentWith = CFG.with || null;      // ID người đang chat
    let lastMsgId = 0;                       // ID tin nhắn cuối (để polling)
    let pollTimer = null;                    // Timer polling

    // DOM elements
    const msgList = document.getElementById('msgList');
    const input = document.getElementById('msgInput');
    const sendBtn = document.getElementById('sendBtn');
    const convList = document.getElementById('convList');
    const layout = document.getElementById('msgLayout');
    const rightbar = document.getElementById('msgRightbar');
    const pinPopup = document.getElementById('pinPopup');

    // 2. HÀM TIỆN ÍCH 
    function escapeHtml(str) {
        if (!str) return '';
        return String(str).replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        });
    }

    // Tự động cuộn xuống tin nhắn cuối cùng
    function scrollBottom() {
        if (msgList) msgList.scrollTop = msgList.scrollHeight;
    }

    // Bắt đầu polling (gọi API mỗi 3 giây)
    function startPoll() {
        if (pollTimer) clearInterval(pollTimer);
        if (currentWith) pollTimer = setInterval(poll, CFG.pollInterval || 3000);
    }

    // Cập nhật số tin chưa đọc trên navbar
    function updateNavbarBadge(count) {
        const badge = document.getElementById('msgNavBadge');
        if (badge) {
            if (count > 0) {
                badge.textContent = count > 99 ? '99+' : count;
                badge.style.display = '';
            } else {
                badge.style.display = 'none';
            }
        }
    }

    // Cuộn đến tin nhắn có ID cụ thể (dùng sau khi tìm kiếm hoặc click ghim)
    function scrollToMessage(msgId) {
        const msgElement = document.querySelector(`.msg-item[data-id="${msgId}"]`);
        if (msgElement) {
            msgElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
            msgElement.style.backgroundColor = '#fef3c7';
            setTimeout(() => { msgElement.style.backgroundColor = ''; }, 2000);
        }
    }

    // Thêm tin nhắn mới vào khung chat
    function appendMessage(msg) {
        if (!msgList) return;
        
        const isMine = msg.is_mine;
        const time = new Date(msg.time).toLocaleTimeString('vi-VN', { hour: '2-digit', minute:'2-digit' });
        let content = '';
        
        // Xử lý nếu có file đính kèm (ảnh hoặc file khác)
        if (msg.file_path) {
            const isImage = msg.file_path.match(/\.(jpg|jpeg|png|gif|webp)$/i);
            const fileUrl = BASE + msg.file_path.replace(/^\/+/, '');
            if (isImage) {
                content = `<a href="${fileUrl}" target="_blank"><img src="${fileUrl}" class="msg-image"></a>`;
            } else {
                const fileName = msg.file_path.split('/').pop();
                content = `<a href="${fileUrl}" target="_blank"><i class="bi bi-file-earmark"></i> ${escapeHtml(fileName)}</a>`;
            }
        } 
        // Xử lý tin nhắn văn bản thường
        else if (msg.content) {
            content = escapeHtml(msg.content).replace(/\n/g, '<br>');
        }
        
        // Tạo HTML cho tin nhắn
        const div = document.createElement('div');
        div.className = `msg-item ${isMine ? 'msg-item--out' : 'msg-item--in'}`;
        div.dataset.id = msg.id;
        
        div.innerHTML = `
            <div class="msg-bubble ${isMine ? 'msg-bubble--out' : 'msg-bubble--in'}">
                ${content || '...'}
                <div class="msg-meta">
                    <span class="msg-time">${time}</span>
                    ${!isMine ? `<button class="msg-pin" data-id="${msg.id}"><i class="bi bi-pin"></i></button>` : ''}
                    ${isMine ? `<i class="bi bi-check2"></i><button class="msg-delete" data-id="${msg.id}"><i class="bi bi-trash3"></i></button>` : ''}
                </div>
            </div>
        `;
        
        msgList.appendChild(div);
        scrollBottom();
    }

    // 3. GỬI TIN NHẮN 
    async function sendMessage() {
        if (!currentWith || !input) return;
        const content = input.value.trim();
        if (!content) return;
        
        // Optimistic UI: hiển thị tin nhắn tạm ngay lập tức
        const tempId = 'temp_' + Date.now();
        appendMessage({ id: tempId, content, time: new Date(), is_mine: 1 });
        input.value = '';
        if (sendBtn) sendBtn.disabled = true;
        
        try {
            const fd = new FormData();
            fd.append('to', currentWith);
            fd.append('content', content);
            const res = await fetch(BASE + 'message/send', { method: 'POST', body: fd });
            const data = await res.json();
            
            if (data.success) {
                // Xóa tin tạm, thêm tin thật từ server
                document.querySelector(`.msg-item[data-id="${tempId}"]`)?.remove();
                appendMessage(data.message);
                lastMsgId = Math.max(lastMsgId, data.message.id);

                if (document.querySelector('.msg-conv-empty')) {
                    window.location.reload();
                }
            }
        } catch(e) { console.error(e); }
    }

    // 4. XÓA TIN NHẮN
    async function deleteMessage(msgId, el) {
        if (!confirm('Xóa tin nhắn này?')) return;
        try {
            const fd = new FormData();
            fd.append('msg_id', msgId);
            const res = await fetch(BASE + 'message/delete', { method: 'POST', body: fd });
            const data = await res.json();
            if (data.success) el.remove();
        } catch(e) { console.error(e); }
    }

    // 5. XÓA TOÀN BỘ HỘI THOẠI 
    async function deleteConversation() {
        if (!currentWith) return;
        if (!confirm('Xóa toàn bộ cuộc trò chuyện này?')) return;
        
        const fd = new FormData();
        fd.append('with', currentWith);
        const res = await fetch(BASE + 'message/delete-conversation', { method: 'POST', body: fd });
        const data = await res.json();
        if (data.success) window.location.href = BASE + 'message';
    }

    // 6. GHIM / BỎ GHIM TIN NHẮN 
    async function togglePin(msgId) {
        const fd = new FormData();
        fd.append('msg_id', msgId);
        const res = await fetch(BASE + 'message/pin', { method: 'POST', body: fd });
        const data = await res.json();
        if (data.success) window.location.reload(); // Reload để cập nhật danh sách ghim
    }

    // 7. POPUP DANH SÁCH TIN ĐÃ GHIM
    async function loadPinnedPopup() {
        if (!currentWith) return;
        const body = document.getElementById('pinPopupBody');
        if (!body) return;
        
        try {
            const res = await fetch(BASE + 'message/pinned-list?with=' + currentWith);
            const data = await res.json();
            if (data.success && data.pins.length) {
                body.innerHTML = data.pins.map(pin => `
                    <div class="msg-pin-popup-item" data-msg="${pin.id}">
                        <strong>${escapeHtml(pin.sender_name)}</strong>
                        <p>${escapeHtml(pin.content.substring(0, 100))}...</p>
                        <small>${new Date(pin.time).toLocaleString()}</small>
                    </div>
                `).join('');
                body.querySelectorAll('.msg-pin-popup-item').forEach(item => {
                    item.addEventListener('click', () => {
                        pinPopup.style.display = 'none';
                        scrollToMessage(item.dataset.msg);
                    });
                });
            } else {
                body.innerHTML = '<p class="text-muted text-center">Chưa có tin nhắn nào được ghim</p>';
            }
            pinPopup.style.display = 'flex';
        } catch(e) { console.error(e); }
    }

    // 8. POLLING - LẤY TIN NHẮN MỚI 
    async function poll() {
        if (!currentWith) return;
        try {
            // Gửi kèm ID tin nhắn cuối cùng để server chỉ trả về tin mới hơn
            const url = `${BASE}message/poll?with=${currentWith}&last_id=${lastMsgId}`;
            const data = await fetch(url).then(r => r.json());
            
            // Nếu có tin nhắn mới thì hiển thị
            if (data.success && data.messages?.length) {
                data.messages.forEach(msg => {
                    if (!document.querySelector(`.msg-item[data-id="${msg.id}"]`)) {
                        appendMessage(msg);
                        lastMsgId = Math.max(lastMsgId, msg.id);
                    }
                });
            }
            
            // Cập nhật sidebar, badge
            if (data.conversations && data.conversations.length > 0) updateSidebar(data.conversations);
            if (data.unread !== undefined) updateNavbarBadge(data.unread);
            
        } catch(e) {}
    }

    // Hàm cập nhật sidebar cuộc hội thoại
    function updateSidebar(conversations) {
        if (!convList) return;

        // Nếu không có cuộc hội thoại nào
        if (!conversations || conversations.length === 0) {
            convList.innerHTML = `
                <li class="msg-conv-empty">
                    <i class="bi bi-inbox"></i>
                    <p>Hộp thư trống</p>
                    <small>Chưa có hội thoại nào</small>
                </li>`;
            return;
        }

        let html = '';
        conversations.forEach(conv => {
            // Kiểm tra xem ai đang được chọn để bôi đậm
            const isActive = (currentWith == conv.user_id) ? 'msg-conv-item--active' : '';
            const unreadCount = parseInt(conv.unread) || 0;
            const hasUnread = unreadCount > 0 ? 'msg-conv-item--unread' : '';
            const badgeHtml = unreadCount > 0 ? `<span class="msg-unread-badge">${unreadCount > 99 ? '99+' : unreadCount}</span>` : '';
            
            // Xử lý nội dung tin nhắn hiển thị trước
            const isMine = (conv.last_sender == CFG.uid) ? 'Bạn: ' : '';
            let preview = conv.last_msg || 'Bắt đầu trò chuyện';
            if (preview.length > 25) preview = preview.substring(0, 25) + '...';

            // Xử lý thời gian
            let timeStr = '';
            if (conv.last_time) {
                const dateObj = new Date(conv.last_time);
                timeStr = dateObj.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
            }

            // Xử lý hiển thị Avatar hoặc Chữ cái đầu
            let avatarHtml = '';
            if (conv.avatar) {
                avatarHtml = `<img src="${BASE}${conv.avatar.replace(/^\/+/, '')}" alt="avatar" class="msg-conv-avatar-img">`;
            } else {
                const nameParts = (conv.name || 'U').trim().split(' ');
                let ini = nameParts[0].charAt(0);
                if (nameParts.length > 1) {
                    ini += nameParts[nameParts.length - 1].charAt(0);
                }
                avatarHtml = `<span>${ini.toUpperCase()}</span>`;
            }

            // Tạo cục HTML cho từng người
            html += `
            <li class="msg-conv-item ${isActive} ${hasUnread}" data-user="${conv.user_id}">
                <div class="msg-conv-avatar">${avatarHtml}</div>
                <div class="msg-conv-meta">
                    <div class="msg-conv-name">${escapeHtml(conv.name)}</div>
                    <div class="msg-conv-preview">${isMine}${escapeHtml(preview)}</div>
                </div>
                <div class="msg-conv-right">
                    <span class="msg-conv-time">${timeStr}</span>
                    ${badgeHtml}
                </div>
            </li>`;
        });

        // Cập nhật lại toàn bộ HTML của cột bên trái
        convList.innerHTML = html;
    }

    // 9. UPLOAD FILE
    async function uploadFile(file) {
        if (!file || !currentWith) return;
        
        const tempId = 'temp_' + Date.now();
        appendMessage({ id: tempId, content: `📎 Đang tải ${file.name}...`, time: new Date(), is_mine: 1 });
        
        const formData = new FormData();
        formData.append('to', currentWith);
        formData.append('file', file);
        formData.append('content', '');
        
        try {
            const res = await fetch(BASE + 'message/upload', { method: 'POST', body: formData });
            const data = await res.json();
            document.querySelector(`.msg-item[data-id="${tempId}"]`)?.remove();
            if (data.success) {
                appendMessage(data.message);
                lastMsgId = Math.max(lastMsgId, data.message.id);
            } else {
                appendMessage({ id: 'err_' + Date.now(), content: '❌ Gửi file thất bại', time: new Date(), is_mine: 1 });
            }
        } catch(err) {
            document.querySelector(`.msg-item[data-id="${tempId}"]`)?.remove();
            appendMessage({ id: 'err_' + Date.now(), content: '❌ Lỗi kết nối', time: new Date(), is_mine: 1 });
        }
    }

    // 10. TÌM KIẾM HỘI THOẠI (SIDEBAR) 
    function initSidebarSearch() {
        const searchInput = document.getElementById('searchConv');
        const searchDrop = document.getElementById('searchDrop');
        if (!searchInput || !searchDrop) return;
        
        searchInput.addEventListener('input', async function(e) {
            const keyword = e.target.value.trim();
            if (!keyword) { searchDrop.style.display = 'none'; return; }
            
            try {
                const res = await fetch(BASE + 'message/search?q=' + encodeURIComponent(keyword));
                const data = await res.json();
                if (!data.success || !data.results.length) {
                    searchDrop.style.display = 'block';
                    searchDrop.innerHTML = '<div class="msg-search-result-item">Không tìm thấy</div>';
                    return;
                }
                searchDrop.style.display = 'block';
                searchDrop.innerHTML = data.results.map(user => `
                    <div class="msg-search-result-item" data-user-id="${user.id}" style="cursor:pointer;">
                        <div style="display:flex; align-items:center; gap:8px;">
                            <div class="msg-conv-avatar" style="width:32px;height:32px;">
                                ${user.avatar ? `<img src="${BASE}${user.avatar.replace(/^\/+/, '')}">` : `<span>${(user.name || 'U').substring(0,2).toUpperCase()}</span>`}
                            </div>
                            <div>${escapeHtml(user.name)}<div style="font-size:11px;">${escapeHtml(user.headline || '')}</div></div>
                        </div>
                    </div>
                `).join('');
                searchDrop.querySelectorAll('.msg-search-result-item').forEach(item => {
                    item.addEventListener('click', () => {
                        window.location.href = BASE + 'message?with=' + item.dataset.userId;
                    });
                });
            } catch(err) { console.error(err); }
        });
        
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !searchDrop.contains(e.target)) {
                searchDrop.style.display = 'none';
            }
        });
    }

    // 11. TÌM KIẾM TIN NHẮN TRONG CHAT 
    function initChatSearch() {
        const searchBtn = document.getElementById('searchMsgBtn');
        const searchBar = document.getElementById('msgChatSearch');
        const searchInput = document.getElementById('chatSearchInput');
        const closeBtn = document.getElementById('closeChatSearch');
        const resultsDiv = document.getElementById('chatSearchResults');
        
        if (!searchBtn) return;
        
        searchBtn.addEventListener('click', () => {
            if (searchBar) searchBar.style.display = 'block';
            searchInput?.focus();
        });
        
        closeBtn?.addEventListener('click', () => {
            if (searchBar) searchBar.style.display = 'none';
            if (searchInput) searchInput.value = '';
            if (resultsDiv) resultsDiv.innerHTML = '';
        });
        
        searchInput?.addEventListener('input', async function(e) {
            const keyword = e.target.value.trim();
            if (!keyword || !currentWith) { if (resultsDiv) resultsDiv.innerHTML = ''; return; }
            
            try {
                const res = await fetch(BASE + 'message/search-messages?with=' + currentWith + '&q=' + encodeURIComponent(keyword));
                const data = await res.json();
                if (!data.success || !data.results.length) {
                    resultsDiv.innerHTML = '<div class="msg-chat-search-item">Không tìm thấy tin nhắn</div>';
                    return;
                }
                resultsDiv.innerHTML = data.results.map(msg => `
                    <div class="msg-chat-search-item" data-msg-id="${msg.id}">
                        <strong>${escapeHtml(msg.sender_name)}</strong> ${new Date(msg.time).toLocaleTimeString('vi-VN')}
                        <div style="font-size:12px;">${escapeHtml(msg.content.substring(0, 80))}...</div>
                    </div>
                `).join('');
                resultsDiv.querySelectorAll('.msg-chat-search-item').forEach(item => {
                    item.addEventListener('click', () => {
                        scrollToMessage(item.dataset.msgId);
                        if (searchBar) searchBar.style.display = 'none';
                        if (searchInput) searchInput.value = '';
                        if (resultsDiv) resultsDiv.innerHTML = '';
                    });
                });
            } catch(err) { console.error(err); }
        });
    }

    // 12. TÌM KIẾM NGƯỜI DÙNG (POPUP TIN NHẮN MỚI)
    function initFindUserSearch() {
        const searchInput = document.getElementById('findUserInput');
        const resultsDiv = document.getElementById('findUserResults');
        if (!searchInput || !resultsDiv) return;
        
        searchInput.addEventListener('input', async function(e) {
            const keyword = e.target.value.trim();
            if (!keyword) {
                resultsDiv.innerHTML = '<li class="msg-popup-hint">Nhập tên để tìm người dùng</li>';
                return;
            }
            try {
                const res = await fetch(BASE + 'message/search?q=' + encodeURIComponent(keyword));
                const data = await res.json();
                if (!data.success || !data.results.length) {
                    resultsDiv.innerHTML = '<li class="msg-popup-hint">Không tìm thấy người dùng</li>';
                    return;
                }
                resultsDiv.innerHTML = data.results.map(user => `
                    <li class="msg-popup-user" data-id="${user.id}" style="cursor:pointer;">
                        <div class="msg-popup-avatar">
                            ${user.avatar ? `<img src="${BASE}${user.avatar.replace(/^\/+/, '')}">` : `<span>${escapeHtml(user.name.charAt(0)).toUpperCase()}</span>`}
                        </div>
                        <div>
                            <div class="msg-popup-name">${escapeHtml(user.name)}</div>
                            <div class="msg-popup-info">${escapeHtml(user.headline || '')}</div>
                        </div>
                    </li>
                `).join('');
                resultsDiv.querySelectorAll('.msg-popup-user').forEach(li => {
                    li.addEventListener('click', () => {
                        window.location.href = BASE + 'message?with=' + li.dataset.id;
                    });
                });
            } catch(err) { console.error(err); }
        });
    }

    // 13. MỞ/ĐÓNG POPUP TÌM NGƯỜI 
    function openFindUserPopup() {
        const popup = document.getElementById('findUserPopup');
        if (popup) {
            popup.hidden = false;
            setTimeout(() => document.getElementById('findUserInput')?.focus(), 50);
        }
    }
    
    function closeFindUserPopup() {
        const popup = document.getElementById('findUserPopup');
        if (popup) popup.hidden = true;
        const results = document.getElementById('findUserResults');
        if (results) results.innerHTML = '<li class="msg-popup-hint">Nhập tên để tìm người dùng</li>';
        const search = document.getElementById('findUserInput');
        if (search) search.value = '';
    }

    // 14. KHỞI TẠO 
    function init() {
        // Nếu đang có chat, bắt đầu polling
        if (msgList && currentWith) {
            const msgs = msgList.querySelectorAll('.msg-item');
            if (msgs.length) lastMsgId = parseInt(msgs[msgs.length-1].dataset.id) || 0;
            scrollBottom();
            startPoll();
        }
        
        // Event listeners cơ bản
        sendBtn?.addEventListener('click', sendMessage);
        input?.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); }
        });
        input?.addEventListener('input', () => { if (sendBtn) sendBtn.disabled = !input.value.trim(); });
        
        // Chọn hội thoại từ sidebar
        convList?.addEventListener('click', (e) => {
            const item = e.target.closest('.msg-conv-item');
            if (!item) return;
            const userId = item.dataset.user;
            // Mobile: thêm class chat-open để trượt sang chat 
            window.location.href = BASE + 'message?with=' + userId;
        });
        
        // Các nút chức năng
        document.getElementById('newMsgBtn')?.addEventListener('click', openFindUserPopup);
        document.getElementById('emptyNewBtn')?.addEventListener('click', openFindUserPopup);
        document.getElementById('closeFindUserPopup')?.addEventListener('click', closeFindUserPopup);
        document.getElementById('backBtn')?.addEventListener('click', () => {
            // Mobile: quay về sidebar bằng cách redirect về trang message không có ?with
            if (window.innerWidth <= 768) {
                window.location.href = BASE + 'message';
            } else {
                layout?.classList.remove('chat-open');
            }
        });

        // Tự động add class chat-open khi đang xem chat (có currentWith) trên mobile
        if (currentWith && layout && window.innerWidth <= 768) {
            layout.classList.add('chat-open');
        }
        document.getElementById('deleteConvBtn')?.addEventListener('click', deleteConversation);
        document.getElementById('showPinnedBtn')?.addEventListener('click', loadPinnedPopup);
        document.getElementById('closePinPopup')?.addEventListener('click', () => { if (pinPopup) pinPopup.style.display = 'none'; });
        
        // Rightbar (mở/đóng thanh bên phải)
        document.getElementById('showRightbarBtn')?.addEventListener('click', () => { if (rightbar) rightbar.style.display = 'block'; });
        document.getElementById('closeRightbar')?.addEventListener('click', () => { if (rightbar) rightbar.style.display = 'none'; });
        
        // Click vào ảnh hoặc tin ghim trong rightbar để cuộn đến
        document.querySelectorAll('.msg-rightbar-image, .msg-rightbar-pin').forEach(el => {
            el.addEventListener('click', () => {
                scrollToMessage(el.dataset.msg);
                if (rightbar) rightbar.style.display = 'none';
            });
        });
        
        // Xóa và ghim tin nhắn
        msgList?.addEventListener('click', (e) => {
            const deleteBtn = e.target.closest('.msg-delete');
            if (deleteBtn) deleteMessage(deleteBtn.dataset.id, deleteBtn.closest('.msg-item'));
            const pinBtn = e.target.closest('.msg-pin');
            if (pinBtn) togglePin(pinBtn.dataset.id);
        });
        
        // Upload file
        document.getElementById('uploadBtn')?.addEventListener('click', () => document.getElementById('fileInput')?.click());
        document.getElementById('fileInput')?.addEventListener('change', async (e) => {
            if (e.target.files[0]) await uploadFile(e.target.files[0]);
            e.target.value = '';
        });
        
        // Khởi tạo các chức năng tìm kiếm
        initSidebarSearch();
        initChatSearch();
        initFindUserSearch();
    }

    // Khởi chạy khi trang đã tải xong
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();