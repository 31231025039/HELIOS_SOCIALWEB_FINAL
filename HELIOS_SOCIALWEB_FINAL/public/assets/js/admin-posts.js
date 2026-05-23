document.addEventListener('DOMContentLoaded', function () {
    const tbody = document.getElementById('postsTableBody');
    if (!tbody) return;

    const API = '/helios/public/admin/posts';
    let filters = { keyword: '', type: '', visibility: 'all' };

    const $  = id => document.getElementById(id);
    const esc = s => s ? s.replace(/[&<>"]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c])) : '';
    const apiFetch = async (url, opts) => (await fetch(url, opts)).json();
    const modalBody = () => $('modalBody');

    // ── Stats ────────────────────────────────────────────────────────────────
    function updateStats(s) {
        if (!s) return;
        $('totalPosts').innerText = s.total  || 0;
        $('eventCount').innerText = s.events || 0;
        $('postCount').innerText  = s.posts  || 0;
    }

    // ── Bảng ─────────────────────────────────────────────────────────────────
    async function loadPosts() {
        tbody.innerHTML = `<tr><td colspan="7" class="text-center py-3 text-muted">Đang tải...</td></tr>`;
        try {
            const data = await apiFetch(`${API}/get-posts?${new URLSearchParams(filters)}`);
            if (data.success) { updateStats(data.stats); renderTable(data.data); }
            else tbody.innerHTML = `<tr><td colspan="7" class="text-center py-3 text-danger">Lỗi tải dữ liệu</td></tr>`;
        } catch {
            tbody.innerHTML = `<tr><td colspan="7" class="text-center py-3 text-danger">Lỗi kết nối</td></tr>`;
        }
    }

    function renderTable(posts) {
        if (!posts.length) {
            tbody.innerHTML = `<tr><td colspan="7" class="posts-empty-state">
                <i class="bi bi-inbox" style="font-size:2rem;display:block;margin-bottom:8px;opacity:.4"></i>
                Không có bài viết nào</td></tr>`;
            return;
        }
        tbody.innerHTML = posts.map(p => {
            const preview   = p.content ? p.content.slice(0, 60) + (p.content.length > 60 ? '…' : '') : 'Không có nội dung';
            const typeBadge = p.post_type === 'event' ? '<span class="badge bg-danger">Sự kiện</span>' : '<span class="badge bg-info">Bài viết</span>';
            const visBadge  = p.visibility === 'Public' ? '<span class="badge bg-success">Công khai</span>' : '<span class="badge bg-secondary">Riêng tư</span>';
            return `<tr>
                <td>${p.id}</td>
                <td><strong>${esc(p.author_name || 'Unknown')}</strong></td>
                <td>${typeBadge}</td>
                <td class="content-preview" title="${esc(p.content)}">${esc(preview)}</td>
                <td>${visBadge}</td>
                <td>${new Date(p.created_at).toLocaleString('vi-VN')}</td>
                <td style="white-space:nowrap">
                    <button class="btn btn-sm btn-primary  btn-view"          data-id="${p.id}" title="Xem"><i class="bi bi-eye-fill"></i></button>
                    <button class="btn btn-sm btn-warning  btn-edit   ms-1"   data-id="${p.id}" title="Sửa"><i class="bi bi-pencil-fill"></i></button>
                    <button class="btn btn-sm btn-danger   btn-delete ms-1"   data-id="${p.id}" title="Xóa"><i class="bi bi-trash-fill"></i></button>
                </td></tr>`;
        }).join('');

        tbody.querySelectorAll('.btn-view').forEach(b   => b.addEventListener('click', () => viewDetail(b.dataset.id)));
        tbody.querySelectorAll('.btn-edit').forEach(b   => b.addEventListener('click', () => editPost(b.dataset.id)));
        tbody.querySelectorAll('.btn-delete').forEach(b => b.addEventListener('click', () => deletePost(b.dataset.id)));
    }

    // ── Modal dùng chung ─────────────────────────────────────────────────────
    function openModal(title, html) {
        $('detailModal').querySelector('.modal-title').innerHTML = title;
        modalBody().innerHTML = html;
        bootstrap.Modal.getOrCreateInstance($('detailModal')).show();
    }

    function closeModal() {
        bootstrap.Modal.getInstance($('detailModal'))?.hide();
    }

    // ── Xem chi tiết ─────────────────────────────────────────────────────────
    async function viewDetail(id) {
        openModal('Chi tiết bài viết', '<div class="text-center py-4">Đang tải...</div>');
        try {
            const data = await apiFetch(`${API}/get-detail?id=${id}`);
            modalBody().innerHTML = data.success ? buildDetailHtml(data.data) : '<p class="text-danger">Không thể tải chi tiết</p>';
        } catch { modalBody().innerHTML = '<p class="text-danger">Lỗi kết nối</p>'; }
    }

    function buildDetailHtml(p) {
        const row = (label, val) => `<div class="modal-detail-row"><div class="modal-detail-label">${label}</div><div>${val}</div></div>`;
        let html = row('ID', `#${p.id}`) + row('Tác giả', `<strong>${esc(p.author_name)}</strong>`) + row('Loại', p.post_type === 'event' ? 'Sự kiện' : 'Bài viết thường');

        if (p.post_type === 'event' && p.event_name)
            html += row('Sự kiện', `<strong>${esc(p.event_name)}</strong><br>
                📍 ${esc(p.event_location || 'Không có địa điểm')}<br>
                ⏰ ${p.event_time ? new Date(p.event_time).toLocaleString('vi-VN') : 'Không có thời gian'}`);

        html += `<div><div class="modal-detail-label">Nội dung</div>
            <div class="modal-content-box">${esc(p.content || 'Không có nội dung').replace(/\n/g, '<br>')}</div></div>`;

        if (p.images?.length)
            html += `<div><div class="modal-detail-label">Hình ảnh</div>
                <div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:8px">
                ${p.images.map(img => `<img src="${esc(img)}" style="max-height:200px;object-fit:contain;border-radius:6px;border:1px solid #e2e8f0" onerror="this.src='https://placehold.co/300x150?text=No+Image'">`).join('')}
                </div></div>`;

        html += `<div class="modal-stats">
            <div><i class="bi bi-heart-fill text-danger"></i> ${p.likes || 0} lượt thích</div>
            <div><i class="bi bi-chat-fill text-primary"></i> ${p.comments || 0} bình luận</div>
        </div>`;
        return html + row('Hiển thị', p.visibility === 'Public' ? 'Công khai' : 'Riêng tư') + row('Ngày đăng', new Date(p.created_at).toLocaleString('vi-VN'));
    }

    // ── Form dùng chung (thêm + sửa) ─────────────────────────────────────────
    function buildPostForm(p = {}, btnLabel = 'Đăng bài') {
        return `<form class="post-form">
            <div class="mb-3">
                <label class="form-label">Loại bài viết</label>
                <select name="post_type" class="form-select">
                    <option value="post"  ${p.post_type !== 'event' ? 'selected' : ''}>Bài viết thường</option>
                    <option value="event" ${p.post_type === 'event' ? 'selected' : ''}>Sự kiện</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Nội dung</label>
                <textarea name="content" rows="5" class="form-control" required>${esc(p.content || '')}</textarea>
            </div>
            <div class="mb-3 event-fields" style="display:${p.post_type === 'event' ? 'block' : 'none'}">
                <label class="form-label">Tên sự kiện</label>
                <input type="text" name="event_name" class="form-control" value="${esc(p.event_name || '')}">
                <label class="form-label mt-2">Địa điểm</label>
                <input type="text" name="event_location" class="form-control" value="${esc(p.event_location || '')}">
                <label class="form-label mt-2">Thời gian</label>
                <input type="datetime-local" name="event_time" class="form-control"
                    value="${p.event_time ? new Date(p.event_time).toISOString().slice(0, 16) : ''}">
            </div>
            <div class="mb-3">
                <label class="form-label">Trạng thái</label>
                <select name="visibility" class="form-select">
                    <option value="Public"  ${p.visibility !== 'Private' ? 'selected' : ''}>Công khai</option>
                    <option value="Private" ${p.visibility === 'Private' ? 'selected' : ''}>Riêng tư</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary w-100">${btnLabel}</button>
        </form>`;
    }

    function bindTypeToggle(form) {
        const sel = form.querySelector('[name="post_type"]');
        const fields = form.querySelector('.event-fields');
        sel.addEventListener('change', () => fields.style.display = sel.value === 'event' ? 'block' : 'none');
    }

    function bindFormSubmit(form, url, extraData = {}) {
        form.addEventListener('submit', async e => {
            e.preventDefault();
            const fd = new FormData(form);
            Object.entries(extraData).forEach(([k, v]) => fd.append(k, v));
            try {
                const data = await apiFetch(url, { method: 'POST', body: fd });
                alert(data.message);
                if (data.success) { closeModal(); loadPosts(); }
            } catch { alert('Lỗi kết nối'); }
        });
    }

    // ── Thêm ─────────────────────────────────────────────────────────────────
    function showAddPostModal() {
        openModal('<i class="bi bi-plus-circle"></i> Thêm bài viết mới', buildPostForm());
        const form = modalBody().querySelector('.post-form');
        bindTypeToggle(form);
        bindFormSubmit(form, `${API}/create`);
    }

    // ── Sửa ──────────────────────────────────────────────────────────────────
    async function editPost(id) {
        openModal('<i class="bi bi-pencil-fill"></i> Chỉnh sửa bài viết', '<div class="text-center py-4">Đang tải...</div>');
        try {
            const data = await apiFetch(`${API}/get-detail?id=${id}`);
            if (!data.success) { modalBody().innerHTML = '<p class="text-danger">Không thể tải dữ liệu</p>'; return; }
            modalBody().innerHTML = buildPostForm(data.data, 'Lưu thay đổi');
            const form = modalBody().querySelector('.post-form');
            bindTypeToggle(form);
            bindFormSubmit(form, `${API}/update`, { id });
        } catch { modalBody().innerHTML = '<p class="text-danger">Lỗi kết nối</p>'; }
    }

    // ── Xóa ──────────────────────────────────────────────────────────────────
    async function deletePost(id) {
        if (!confirm(`Xóa bài viết #${id}?`)) return;
        try {
            const fd = new FormData();
            fd.append('id', id);
            const data = await apiFetch(`${API}/delete`, { method: 'POST', body: fd });
            alert(data.message);
            if (data.success) loadPosts();
        } catch { alert('Lỗi kết nối'); }
    }

    // ── Bộ lọc & khởi động ───────────────────────────────────────────────────
    $('btnAddPost')?.addEventListener('click', showAddPostModal);
    $('searchKeyword')?.addEventListener('input',    e => { filters.keyword    = e.target.value; loadPosts(); });
    $('filterPostType')?.addEventListener('change',  e => { filters.type       = e.target.value; loadPosts(); });
    $('filterVisibility')?.addEventListener('change',e => { filters.visibility = e.target.value; loadPosts(); });
    $('resetFilters')?.addEventListener('click', () => {
        ['searchKeyword', 'filterPostType'].forEach(id => { const el = $(id); if (el) el.value = ''; });
        const vis = $('filterVisibility'); if (vis) vis.value = 'all';
        filters = { keyword: '', type: '', visibility: 'all' };
        loadPosts();
    });

    loadPosts();
});