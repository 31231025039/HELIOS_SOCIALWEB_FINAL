const NET_BASE_URL = window.BASE_URL || '/helios/public/';

function initials(name) {
    if (!name) return '??';
    return name.split(' ').map(w => w[0]).slice(-2).join('').toUpperCase();
}

function bannerStyle(banner) {
    if (!banner) return `class="member-banner bg-light"`;
    if (banner.startsWith('bg-')) return `class="member-banner ${banner}"`;
    return `class="member-banner" style="background:${banner};"`;
}

function renderConnectCard(user, colClass = 'col-6 col-md-4 col-xl-3') {
    const ini = initials(user.name);
    const verifiedBadge = user.verified ? `<i class="bi bi-patch-check-fill text-primary ms-1"></i>` : '';
    const avatar = user.img ? `${NET_BASE_URL}${user.img}` : '';

    let actionButton = '';
    if (!user.rel_status) {
        actionButton = `<button class="btn btn-outline-primary btn-sm rounded-pill w-100 fw-bold btn-connect" data-userid="${user.id}">Kết nối</button>`;
    } else if (user.rel_status === 'pending') {
        actionButton = `<button class="btn btn-secondary btn-sm rounded-pill w-100 fw-bold" disabled>Đang chờ...</button>`;
    } else if (user.rel_status === 'accepted') {
        actionButton = `<button class="btn btn-primary btn-sm rounded-pill w-100 fw-bold" onclick="window.location.href='${NET_BASE_URL}message?with=${user.id}'">Nhắn tin</button>`;
    }
    if (user.rel_status === 'pending') {
        actionButton = `<button class="btn btn-outline-secondary btn-sm rounded-pill w-100 fw-bold btn-cancel-request" data-userid="${user.id}">Đã gửi lời mời</button>`;
    }
    if (user.rel_status === 'pending') {
        actionButton = `<button class="btn btn-outline-secondary btn-sm rounded-pill w-100 fw-bold btn-cancel-request" data-userid="${user.id}" title="Bấm để hủy lời mời">Đang chờ</button>`;
    }

    return `
    <div class="${colClass}">
        <div class="member-card border rounded-3 overflow-hidden position-relative h-100 bg-white shadow-sm">
            <div ${bannerStyle(user.banner)}></div>
            <div class="text-center px-2 pb-3">
                
                <!-- BỌC THẺ <a> QUANH AVATAR -->
                <a href="#" class="quick-profile-btn" data-id="${user.id}" style="display: block; text-decoration: none;">
                    <div class="member-avatar">
                        <img src="${avatar}" alt="${user.name}" class="w-100 h-100 object-fit-cover" onerror="this.style.display='none'; this.parentElement.innerHTML='${ini}';">
                    </div>
                </a>


                <!-- BỌC THẺ <a> QUANH TÊN -->
                <h6 class="fw-bold mb-0 text-truncate" title="${user.name}">
                    <a href="#" class="quick-profile-btn text-dark text-decoration-none" data-id="${user.id}">
                        ${user.name || 'Người dùng'}
                    </a>
                    ${verifiedBadge}
                </h6>
                
                <p class="text-muted extra-small mb-2 member-bio" style="height:32px; overflow:hidden;">${user.bio || 'Chưa có thông tin'}</p>
                <div class="extra-small text-muted mb-3"><i class="bi bi-people-fill me-1"></i>${user.sub || ''}</div>
                ${actionButton}
            </div>
        </div>
    </div>`;
}

document.addEventListener("DOMContentLoaded", () => {
    const grid = document.getElementById('suggestedGrid');

    const profileModal = new bootstrap.Modal(document.getElementById('quickProfileModal'));
    const profileBody = document.getElementById('quickProfileBody');

    document.body.addEventListener('click', async (e) => {
        const profileBtn = e.target.closest('.quick-profile-btn');
        const legacyLink = e.target.closest('a[href^="about-me?id="]'); 

        let targetId = null;
        if (profileBtn) {
            e.preventDefault();
            targetId = profileBtn.dataset.id;
        } else if (legacyLink) {
            e.preventDefault();
            const urlParams = new URLSearchParams(legacyLink.getAttribute('href').split('?')[1]);
            targetId = urlParams.get('id');
        }

        if (targetId) {
            profileBody.innerHTML = `
                <div class="text-center py-5">
                    <span class="spinner-border text-primary" style="width: 3rem; height: 3rem;"></span>
                    <p class="mt-2 text-muted">Đang tải hồ sơ...</p>
                </div>`;
            profileModal.show();

            profileBody.innerHTML = `
                <div class="text-center py-5">
                    <span class="spinner-border text-primary" style="width: 3rem; height: 3rem;"></span>
                    <p class="mt-2 text-muted">Đang tải hồ sơ...</p>
                </div>`;
            profileModal.show();

            try {
                const res = await fetch(`${NET_BASE_URL}about-me?id=${targetId}`);
                if (!res.ok) throw new Error('Không thể tải trang');
                
                const htmlText = await res.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(htmlText, 'text/html');
                
                const mainProfileContent = doc.querySelector('.col-12.col-lg-9');
                
                if (mainProfileContent) {
                    const spacer = mainProfileContent.querySelector('.d-flex.justify-content-end.mb-2');
                    if (spacer) spacer.remove();

                    const fixCss = `
                    <style>
                        /* TẠO LỀ 2 BÊN VÀ TRÊN DƯỚI CHO POPUP */
                        #quickProfileBody { padding: 40px 24px 24px 24px !important; }
                        
                        #quickProfileBody .h-card { background: #fff; border-radius: 12px; border: 1px solid #e9ebee; margin-bottom: 16px; position: relative; overflow: visible; }
                        #quickProfileBody .profile-cover { height: 130px; overflow: hidden; border-radius: 12px 12px 0 0; position: relative; }
                        #quickProfileBody .profile-cover img { width: 100%; height: 100%; object-fit: cover; }
                        
                        /* Fix Avatar */
                        #quickProfileBody .profile-avatar-lg-page { width: 110px; height: 110px; position: absolute; top: 75px; left: 24px; border: 4px solid #fff; border-radius: 50%; background: #062b6b; z-index: 10; box-shadow: 0 2px 5px rgba(0,0,0,0.1); overflow: hidden; }
                        #quickProfileBody .profile-avatar-lg-page img { width: 100%; height: 100%; object-fit: cover; }
                        #quickProfileBody .profile-avatar-lg-page div { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 38px !important; font-weight: bold; }
                        
                        /* Đẩy chữ xuống để không đè Avatar */
                        #quickProfileBody .profile-intro-details { padding-top: 50px; }
                        
                        /* Style cho list kinh nghiệm/học vấn */
                        #quickProfileBody .company-logo-sm { width: 48px; height: 48px; background: #e0f2fe; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
                    </style>`;

                    profileBody.innerHTML = fixCss + mainProfileContent.innerHTML;
                    
                    profileBody.style.backgroundColor = 'var(--helios-bg, #f3f8ff)';

                    profileBody.querySelectorAll('button').forEach(btn => {
                        if (btn.innerText.includes('Kết nối')) {
                            btn.classList.add('btn-connect'); 
                            btn.dataset.userid = targetId;
                        }
                    });
                } else {
                     profileBody.innerHTML = `<div class="text-center py-5 text-danger">Không tìm thấy thông tin hồ sơ</div>`;
                }
                
            } catch (err) {
                console.error(err);
                profileBody.innerHTML = `<div class="text-center py-5 text-danger">Lỗi kết nối mạng</div>`;
            }
        }
    });

    async function loadSuggestedUsers(keyword = '') {
        if (!grid) return;
        
        grid.innerHTML = '<div class="col-12 text-center text-muted py-4"><span class="spinner-border spinner-border-sm text-primary me-2"></span> Đang tải dữ liệu...</div>';
        
        try {
            let url = `${NET_BASE_URL}network/suggestions`;
            if (keyword !== '') {
                url += `?keyword=${encodeURIComponent(keyword)}`;
            }

            const response = await fetch(url);
            if (!response.ok) throw new Error("Lỗi HTTP: " + response.status);
            
            const data = await response.json();
            const users = Array.isArray(data) ? data : (data.data || []);

            if (users.length === 0) {
                const emptyMsg = keyword !== '' ? `Không tìm thấy người dùng nào với từ khóa "<strong>${escapeHtml(keyword)}</strong>".` : 'Không có gợi ý kết nối nào.';
                grid.innerHTML = `<div class="col-12 text-center text-muted py-4">${emptyMsg}</div>`;
                return;
            }

            grid.innerHTML = users.map(user => renderConnectCard(user)).join('');
        } catch (error) {
            console.error('Lỗi khi tải dữ liệu API:', error);
            grid.innerHTML = `<div class="col-12 text-center text-danger py-4">Lỗi: Không thể tải dữ liệu.</div>`;
        }
    }

    async function handleInviteAction(button, actionEndpoint) {
        const connectionId = button.dataset.id;
        const inviteBox = document.getElementById(`invite-box-${connectionId}`);
        const originalText = button.innerHTML;

        button.disabled = true;
        button.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

        try {
            const response = await fetch(`${NET_BASE_URL}${actionEndpoint}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ connection_id: connectionId })
            });

            const result = await response.json();

            if (result.success) {
                if (inviteBox) {
                    inviteBox.style.transition = "opacity 0.3s ease, transform 0.3s ease";
                    inviteBox.style.opacity = "0";
                    inviteBox.style.transform = "scale(0.95)";
                    
                    setTimeout(() => {
                        inviteBox.remove();
                        window.location.reload();
                    }, 300);
                }
            } else {
                alert('Thao tác không thành công.');
                button.disabled = false;
                button.innerHTML = originalText;
            }
        } catch (error) {
            console.error(error);
            alert('Lỗi kết nối máy chủ');
            button.disabled = false;
            button.innerHTML = originalText;
        }
    }

    loadSuggestedUsers();
    
    const btnReload = document.getElementById('btnReloadSuggestions');
    if(btnReload) {
        btnReload.addEventListener('click', (e) => {
            e.preventDefault();
            if(grid) grid.innerHTML = '<div class="col-12 text-center text-muted py-4"><span class="spinner-border spinner-border-sm text-primary me-2"></span> Đang tải lại...</div>';
            loadSuggestedUsers();
        });
    }

    const searchInput = document.getElementById('networkSearchInput');
    const btnSearch = document.getElementById('btnNetworkSearch');
    const btnClearSearch = document.getElementById('btnClearSearch'); 

    function executeSearch() {
        if (searchInput) {
            const keyword = searchInput.value.trim();
            loadSuggestedUsers(keyword);
            grid.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }

    if (searchInput && btnClearSearch) {
        searchInput.addEventListener('input', function() {
            if (this.value.length > 0) {
                btnClearSearch.classList.remove('d-none'); 
            } else {
                btnClearSearch.classList.add('d-none');    
                executeSearch(); 
            }
        });

        btnClearSearch.addEventListener('click', function() {
            searchInput.value = '';             
            this.classList.add('d-none');     
            executeSearch();                    
            searchInput.focus();               
        });
    }

    if (btnSearch) {
        btnSearch.addEventListener('click', (e) => {
            e.preventDefault();
            executeSearch();
        });
    }

    if (searchInput) {
        searchInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                executeSearch();
            }
        });
    }

    document.body.addEventListener('click', async (e) => {
        
        const connectBtn = e.target.closest('.btn-connect');
        if (connectBtn) {
            const userId = connectBtn.dataset.userid;
            const originalText = connectBtn.innerHTML;
            
            connectBtn.disabled = true;
            connectBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

            try {
                const response = await fetch(`${NET_BASE_URL}network/send-request`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ receiver_id: userId })
                });
                const result = await response.json();

                if (result.success) {
                    connectBtn.classList.remove('btn-outline-primary', 'btn-connect');
                    connectBtn.classList.add('btn-outline-secondary', 'btn-cancel-request');
                    connectBtn.disabled = false;
                    connectBtn.title = 'Bấm để hủy lời mời';
                    connectBtn.innerHTML = 'Đang chờ';
                    setTimeout(() => {
                        connectBtn.innerHTML = 'Đang chờ';
                    }, 0);
                    connectBtn.innerHTML = 'Đã gửi lời mời';
                } else {
                    alert('Lỗi: ' + (result.message || 'Không thể gửi lời mời'));
                    connectBtn.disabled = false;
                    connectBtn.innerHTML = originalText;
                }
            } catch (error) {
                console.error(error);
                alert('Lỗi kết nối máy chủ');
                connectBtn.disabled = false;
                connectBtn.innerHTML = originalText;
            }
            return;
        }

        const cancelRequestBtn = e.target.closest('.btn-cancel-request');
        if (cancelRequestBtn) {
            const targetId = cancelRequestBtn.dataset.userid;
            const originalHtml = cancelRequestBtn.innerHTML;

            cancelRequestBtn.disabled = true;
            cancelRequestBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

            try {
                const response = await fetch(`${NET_BASE_URL}network/remove-connection`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ target_id: targetId })
                });
                const result = await response.json();

                if (result.success) {
                    cancelRequestBtn.classList.remove('btn-secondary', 'btn-outline-secondary', 'btn-cancel-request');
                    cancelRequestBtn.classList.add('btn-outline-primary', 'btn-connect');
                    cancelRequestBtn.disabled = false;
                    cancelRequestBtn.innerHTML = 'Kết nối';
                } else {
                    alert('Lỗi: ' + (result.message || 'Không thể hủy lời mời'));
                    cancelRequestBtn.disabled = false;
                    cancelRequestBtn.innerHTML = originalHtml;
                }
            } catch (error) {
                console.error(error);
                alert('Lỗi kết nối máy chủ');
                cancelRequestBtn.disabled = false;
                cancelRequestBtn.innerHTML = originalHtml;
            }
            return;
        }

        const acceptBtn = e.target.closest('.btn-accept-invite');
        if (acceptBtn) {
            handleInviteAction(acceptBtn, 'network/accept-request');
            return;
        }

        const ignoreBtn = e.target.closest('.btn-ignore-invite');
        if (ignoreBtn) {
            handleInviteAction(ignoreBtn, 'network/ignore-request');
            return;
        }

        const unfriendBtn = e.target.closest('.btn-unfriend');
        if (unfriendBtn) {
            const targetId = unfriendBtn.dataset.id;
            
            if (!confirm('Bạn có chắc chắn muốn hủy kết bạn với người này?')) return;

            const originalHtml = unfriendBtn.innerHTML;
            unfriendBtn.disabled = true;
            unfriendBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

            try {
                const response = await fetch(`${NET_BASE_URL}network/remove-connection`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ target_id: targetId })
                });
                const result = await response.json();

                if (result.success) {
                    const card = unfriendBtn.closest('.col-12');
                    if (card) {
                        card.style.transition = "opacity 0.3s ease, transform 0.3s ease";
                        card.style.opacity = "0";
                        card.style.transform = "scale(0.95)";
                        
                        setTimeout(() => {
                            card.remove();
                            window.location.reload();
                        }, 300);
                    }
                } else {
                    alert('Lỗi: ' + (result.message || 'Không thể hủy kết bạn'));
                    unfriendBtn.disabled = false;
                    unfriendBtn.innerHTML = originalHtml;
                }
            } catch (error) {
                console.error(error);
                alert('Lỗi kết nối máy chủ');
                unfriendBtn.disabled = false;
                unfriendBtn.innerHTML = originalHtml;
            }
            return;
        }

        const profileModal = new bootstrap.Modal(document.getElementById('quickProfileModal'));
        const profileBody = document.getElementById('quickProfileBody');
        const profileBtn = e.target.closest('.quick-profile-btn');
        const legacyLink = e.target.closest('a[href^="about-me?id="]'); 

        let targetId = null;
        if (profileBtn) {
            e.preventDefault();
            targetId = profileBtn.dataset.id;
        } else if (legacyLink) {
            e.preventDefault();
            const urlParams = new URLSearchParams(legacyLink.getAttribute('href').split('?')[1]);
            targetId = urlParams.get('id');
        }

        if (targetId) {
            profileBody.innerHTML = `
                <div class="text-center py-5">
                    <span class="spinner-border text-primary" style="width: 3rem; height: 3rem;"></span>
                    <p class="mt-2 text-muted">Đang tải hồ sơ...</p>
                </div>`;
            profileModal.show();

            try {
                const res = await fetch(`${NET_BASE_URL}about-me?id=${targetId}`);
                if (!res.ok) throw new Error('Không thể tải trang');
                
                const htmlText = await res.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(htmlText, 'text/html');
                const mainProfileContent = doc.querySelector('.col-12.col-lg-9');
                
                if (mainProfileContent) {
                    const spacer = mainProfileContent.querySelector('.d-flex.justify-content-end.mb-2');
                    if (spacer) spacer.remove();

                    const fixCss = `
                    <style>
                        #quickProfileBody { padding: 40px 24px 24px 24px !important; }
                        #quickProfileBody .h-card { background: #fff; border-radius: 12px; border: 1px solid #e9ebee; margin-bottom: 16px; position: relative; overflow: visible; box-shadow: none !important; }
                        #quickProfileBody .profile-cover { height: 130px !important; overflow: hidden; border-radius: 12px 12px 0 0; position: relative; }
                        #quickProfileBody .profile-cover img { width: 100%; height: 100%; object-fit: cover; }
                        #quickProfileBody .profile-avatar-lg-page { width: 110px !important; height: 110px !important; position: absolute; top: 75px !important; left: 24px !important; border: 4px solid #fff; border-radius: 50%; background: #062b6b; z-index: 10; box-shadow: 0 2px 5px rgba(0,0,0,0.1); overflow: hidden; }
                        #quickProfileBody .profile-avatar-lg-page img { width: 100%; height: 100%; object-fit: cover; }
                        #quickProfileBody .profile-avatar-lg-page div { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 38px !important; font-weight: bold; }
                        #quickProfileBody .profile-intro-details { padding-top: 50px !important; }
                        #quickProfileBody .company-logo-sm { width: 48px; height: 48px; background: #e0f2fe; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
                    </style>`;

                    profileBody.innerHTML = fixCss + mainProfileContent.innerHTML;
                    profileBody.style.backgroundColor = 'var(--helios-bg, #f3f8ff)';

                    profileBody.querySelectorAll('button').forEach(btn => {
                        if (btn.innerText.includes('Kết nối')) {
                            btn.classList.add('btn-connect'); 
                            btn.dataset.userid = targetId;
                        }
                    });
                } else {
                     profileBody.innerHTML = `<div class="text-center py-5 text-danger">Không tìm thấy thông tin hồ sơ</div>`;
                }
            } catch (err) {
                console.error(err);
                profileBody.innerHTML = `<div class="text-center py-5 text-danger">Lỗi kết nối mạng</div>`;
            }
        }
    });
});
