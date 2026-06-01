document.addEventListener('DOMContentLoaded', () => {
    const API     = '/helios/public/admin/posts';
    let   filters = { keyword: '', type: '', visibility: 'all' };

    // ── Helpers ──────────────────────────────────────────────────────────────
    const $        = id => document.getElementById(id);
    const esc      = s  => s ? s.replace(/[&<>"]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c])) : '';
    const api      = async (url, opts) => (await fetch(url, { ...opts, credentials: 'include' })).json();
    const modal    = id => bootstrap.Modal.getOrCreateInstance($(id));
    const formVal  = id => $(id).value;
    const formatDateTime = value => {
        if (!value) return '';
        const d = new Date(value);
        if (Number.isNaN(d.getTime())) return value;
        const pad = n => String(n).padStart(2, '0');
        return `${pad(d.getDate())}/${pad(d.getMonth() + 1)}/${d.getFullYear()} ${pad(d.getHours())}:${pad(d.getMinutes())}:${pad(d.getSeconds())}`;
    };

    // ── Stats ────────────────────────────────────────────────────────────────
    const updateStats = s => {
        if (!s) return;
        $('totalPosts').innerText = s.total  || 0;
        $('eventCount').innerText = s.events || 0;
        $('postCount').innerText  = s.posts  || 0;
    };

    // ── Bảng ─────────────────────────────────────────────────────────────────
    async function loadPosts() {
        $('postsTableBody').innerHTML =
            `<tr><td colspan="7" class="text-center py-3 text-muted">Đang tải...</td></tr>`;
        try {
            const d = await api(`${API}/get-posts?${new URLSearchParams(filters)}`);
            if (d.success) { updateStats(d.stats); renderTable(d.data); }
            else $('postsTableBody').innerHTML =
                `<tr><td colspan="7" class="text-center py-3 text-danger">Lỗi tải dữ liệu</td></tr>`;
        } catch {
            $('postsTableBody').innerHTML =
                `<tr><td colspan="7" class="text-center py-3 text-danger">Lỗi kết nối</td></tr>`;
        }
    }

    function renderTable(posts) {
        if (!posts.length) {
            $('postsTableBody').innerHTML =
                `<tr><td colspan="7" class="text-center py-4 text-muted">
                    <i class="bi bi-inbox fs-2 d-block mb-2 opacity-50"></i>Không có bài viết nào
                 </td></tr>`;
            return;
        }
        $('postsTableBody').innerHTML = posts.map(p => `<tr>
            <td class="col-id">${p.id}</td>
            <td class="col-author">${esc(p.author_name || 'Unknown')}</td>
            <td class="col-type">${p.post_type === 'event'
                ? '<span class="badge bg-danger">Sự kiện</span>'
                : '<span class="badge bg-info">Bài viết</span>'}</td>
            <td class="content-preview col-content col-content-large" title="${esc(p.content)}">
                <div class="cell-clamp">${esc(p.content || '')}</div>
            </td>
            <td class="col-visibility">${p.visibility === 'Public'
                ? '<span class="badge bg-success">Công khai</span>'
                : '<span class="badge bg-secondary">Riêng tư</span>'}</td>
            <td class="col-date">${formatDateTime(p.created_at)}</td>
            <td style="white-space:nowrap">
                <button class="btn btn-sm btn-primary  btn-view"        data-id="${p.id}"><i class="bi bi-eye-fill"></i></button>
                <button class="btn btn-sm btn-warning  btn-edit   ms-1" data-id="${p.id}"><i class="bi bi-pencil-fill"></i></button>
                <button class="btn btn-sm btn-danger   btn-delete ms-1" data-id="${p.id}"><i class="bi bi-trash-fill"></i></button>
            </td></tr>`).join('');

        $('postsTableBody').querySelectorAll('.btn-view')  .forEach(b => b.addEventListener('click', () => viewDetail(b.dataset.id)));
        $('postsTableBody').querySelectorAll('.btn-edit')  .forEach(b => b.addEventListener('click', () => openEdit(b.dataset.id)));
        $('postsTableBody').querySelectorAll('.btn-delete').forEach(b => b.addEventListener('click', () => deletePost(b.dataset.id)));
    }

    // ── Xem chi tiết ─────────────────────────────────────────────────────────
    async function viewDetail(id) {
        $('detailModalBody').innerHTML = '<div class="text-center py-4">Đang tải...</div>';
        modal('detailModal').show();
        try {
            const d = await api(`${API}/get-detail?id=${id}`);
            $('detailModalBody').innerHTML = d.success ? buildDetailHtml(d.data) : '<p class="text-danger">Không thể tải</p>';
        } catch { $('detailModalBody').innerHTML = '<p class="text-danger">Lỗi kết nối</p>'; }
    }

    function buildDetailHtml(p) {
        const row = (label, val) =>
            `<div class="modal-detail-row"><div class="modal-detail-label">${label}</div><div>${val}</div></div>`;

        let h = row('ID', `#${p.id}`)
              + row('Tác giả', `<strong>${esc(p.author_name)}</strong>`)
              + row('Loại', p.post_type === 'event' ? 'Sự kiện' : 'Bài viết thường');

        if (p.post_type === 'event' && p.event_name)
            h += row('Sự kiện', `<strong>${esc(p.event_name)}</strong><br>
                📍 ${esc(p.event_location || 'Không có')}<br>
                ⏰ ${p.event_time ? new Date(p.event_time).toLocaleString('vi-VN') : 'Không có'}`);

        h += `<div><div class="modal-detail-label">Nội dung</div>
              <div class="modal-content-box">${esc(p.content || '').replace(/\n/g,'<br>')}</div></div>`;

        if (p.images?.length)
            h += `<div><div class="modal-detail-label">Hình ảnh</div>
                  <div class="d-flex flex-wrap gap-2 mt-2">
                  ${p.images.map(img =>
                      `<img src="${esc(img)}" style="max-height:200px;object-fit:contain;border-radius:6px;border:1px solid #e2e8f0"
                            onerror="this.src='https://placehold.co/300x150?text=No+Image'">`
                  ).join('')}</div></div>`;

        h += `<div class="modal-stats">
                <div><i class="bi bi-heart-fill text-danger"></i> ${p.likes || 0} lượt tương tác</div>
                <div><i class="bi bi-chat-fill text-primary"></i>  ${p.comments || 0} bình luận</div>
              </div>`;

        return h
            + row('Hiển thị', p.visibility === 'Public' ? 'Công khai' : 'Riêng tư')
            + row('Ngày đăng', formatDateTime(p.created_at));
    }

    // ── Form: reset ───────────────────────────────────────────────────────────
    function resetForm() {
        ['formPostId','formContent','formEventName','formEventLocation','formEventTime']
            .forEach(id => $(id).value = '');
        $('formPostType').value   = 'post';
        $('formVisibility').value = 'Public';
        $('formImages').value     = '';
        $('imagePreview').innerHTML      = '';
        $('existingImages').innerHTML    = '';
        $('existingImagesWrap').style.display = 'none';
        $('eventFields').style.display        = 'none';
    }

    // ── Form: mở thêm mới ────────────────────────────────────────────────────
    function openAdd() {
        resetForm();
        $('formModalTitle').innerHTML  = '<i class="bi bi-plus-circle me-2"></i>Thêm bài viết mới';
        $('btnSubmitLabel').textContent = 'Đăng bài';
        modal('formModal').show();
    }

    // ── Form: mở sửa ─────────────────────────────────────────────────────────
    async function openEdit(id) {
        resetForm();
        $('formModalTitle').innerHTML  = '<i class="bi bi-pencil-fill me-2"></i>Chỉnh sửa bài viết';
        $('btnSubmitLabel').textContent = 'Lưu thay đổi';
        $('formContent').disabled = true;
        $('btnSubmitPost').disabled = true;
        modal('formModal').show();
        try {
            const d = await api(`${API}/get-detail?id=${id}`);
            if (!d.success) { alert('Không thể tải dữ liệu'); modal('formModal').hide(); return; }
            fillForm(d.data);
        } catch { alert('Lỗi kết nối'); modal('formModal').hide(); }
        finally { $('formContent').disabled = false; $('btnSubmitPost').disabled = false; }
    }

    function fillForm(p) {
        $('formPostId').value        = p.id;
        $('formPostType').value      = p.post_type      || 'post';
        $('formContent').value       = p.content        || '';
        $('formVisibility').value    = p.visibility     || 'Public';
        $('formEventName').value     = p.event_name     || '';
        $('formEventLocation').value = p.event_location || '';
        $('formEventTime').value     = p.event_time
            ? new Date(p.event_time).toISOString().slice(0, 16) : '';
        $('eventFields').style.display = p.post_type === 'event' ? 'block' : 'none';

        if (p.images?.length) {
            $('existingImagesWrap').style.display = 'block';
            $('existingImages').innerHTML = p.images.map(img =>
                `<img src="${esc(img)}" style="height:80px;width:80px;object-fit:cover;border-radius:6px;border:1px solid #e2e8f0"
                      onerror="this.src='https://placehold.co/80x80?text=No+Image'">`
            ).join('');
        }
    }

    // ── Toggle event fields ───────────────────────────────────────────────────
    $('formPostType').addEventListener('change', () => {
        $('eventFields').style.display = $('formPostType').value === 'event' ? 'block' : 'none';
    });

    // ── Preview ảnh ───────────────────────────────────────────────────────────
    $('formImages').addEventListener('change', () => {
        $('imagePreview').innerHTML = '';
        [...$('formImages').files].forEach(file => {
            if (file.size > 5 * 1024 * 1024) {
                $('imagePreview').insertAdjacentHTML('beforeend',
                    `<div class="text-danger small">"${esc(file.name)}" vượt quá 5MB</div>`);
                return;
            }
            const wrap = Object.assign(document.createElement('div'), { style: 'text-align:center' });
            const img  = Object.assign(document.createElement('img'), {
                style: 'height:80px;width:80px;object-fit:cover;border-radius:6px;border:1px solid #e2e8f0;display:block'
            });
            wrap.insertAdjacentHTML('beforeend',
                `<div style="font-size:10px;max-width:80px;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;color:#666;margin-top:2px">${esc(file.name)}</div>`);
            const reader = new FileReader();
            reader.onload = e => img.src = e.target.result;
            reader.readAsDataURL(file);
            wrap.prepend(img);
            $('imagePreview').appendChild(wrap);
        });
    });

    // ── Submit ────────────────────────────────────────────────────────────────
    $('btnSubmitPost').addEventListener('click', async () => {
        const content = $('formContent').value.trim();
        if (!content) { alert('Nội dung không được để trống'); $('formContent').focus(); return; }

        const id     = $('formPostId').value;
        const isEdit = !!id;
        const origLabel = $('btnSubmitLabel').textContent;

        $('btnSubmitPost').disabled   = true;
        $('btnSubmitLabel').textContent = 'Đang lưu...';

        const fd = new FormData();
        if (isEdit) fd.append('id', id);
        fd.append('post_type',      $('formPostType').value);
        fd.append('content',        content);
        fd.append('visibility',     $('formVisibility').value);
        fd.append('event_name',     $('formEventName').value.trim());
        fd.append('event_location', $('formEventLocation').value.trim());
        fd.append('event_time',     $('formEventTime').value);

        try {
            const d = await api(`${API}/${isEdit ? 'update' : 'create'}`, { method: 'POST', body: fd });
            if (!d.success) {
                alert(d.message);
                $('btnSubmitPost').disabled   = false;
                $('btnSubmitLabel').textContent = origLabel;
                return;
            }

            // Upload ảnh nếu có
            const files = [...$('formImages').files];
            if (files.length && d.post_id) {
                $('btnSubmitLabel').textContent = 'Đang tải ảnh...';
                const imgFd = new FormData();
                imgFd.append('post_id', d.post_id);
                files.forEach(f => imgFd.append('images[]', f));
                try {
                    const ir = await api(`${API}/upload-images`, { method: 'POST', body: imgFd });
                    alert(d.message + '\n' + ir.message + (ir.errors?.length ? '\nLỗi: ' + ir.errors.join(', ') : ''));
                } catch { alert(d.message + '\n(Upload ảnh thất bại)'); }
            } else {
                alert(d.message);
            }

            modal('formModal').hide();
            loadPosts();
        } catch {
            alert('Lỗi kết nối');
            $('btnSubmitPost').disabled   = false;
            $('btnSubmitLabel').textContent = origLabel;
        }
    });

    $('formModal').addEventListener('hidden.bs.modal', resetForm);

    // ── Xóa ──────────────────────────────────────────────────────────────────
    async function deletePost(id) {
        if (!confirm(`Xóa bài viết #${id}?`)) return;
        const fd = new FormData();
        fd.append('id', id);
        try {
            const d = await api(`${API}/delete`, { method: 'POST', body: fd });
            alert(d.message);
            if (d.success) loadPosts();
        } catch { alert('Lỗi kết nối'); }
    }

    // ── Bộ lọc & boot ────────────────────────────────────────────────────────
    $('btnAddPost')       .addEventListener('click',  openAdd);
    $('searchKeyword')    .addEventListener('input',  e => { filters.keyword    = e.target.value; loadPosts(); });
    $('filterPostType')   .addEventListener('change', e => { filters.type       = e.target.value; loadPosts(); });
    $('filterVisibility') .addEventListener('change', e => { filters.visibility = e.target.value; loadPosts(); });
    $('resetFilters')     .addEventListener('click',  () => {
        $('searchKeyword').value = $('filterPostType').value = '';
        $('filterVisibility').value = 'all';
        filters = { keyword: '', type: '', visibility: 'all' };
        loadPosts();
    });

    loadPosts();
});