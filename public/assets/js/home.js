/**
 * Trang chủ Helios
 */
(function() {
  "use strict";

  let currentEditPostId = null;
  let currentEditImages = [];

  // ===== SEARCH TOOLTIP =====
  function initSearchTooltip() {
    const searchBar = document.getElementById("searchBar");
    const searchTooltip = document.getElementById("searchTooltip");
    if (searchBar && searchTooltip) {
      searchBar.addEventListener("mouseenter", () => searchTooltip.style.display = "block");
      searchBar.addEventListener("mouseleave", () => searchTooltip.style.display = "none");
    }
  }

  // ===== SHARE MODAL (có link + nút sao chép) =====
  function initShare() {
    const shareModal = new bootstrap.Modal(document.getElementById('shareModal'));
    const shareLinkInput = document.getElementById('shareLinkInput');
    const copyBtn = document.getElementById('copyShareLinkBtn');
    const copyMsg = document.getElementById('copySuccessMsg');

    document.addEventListener('click', (e) => {
      const shareBtn = e.target.closest('.btn-share');
      if (shareBtn) {
        e.preventDefault();
        const postId = shareBtn.dataset.postId;
        const link = window.location.origin + "/helios/public/home?post=" + postId;
        shareLinkInput.value = link;
        if (copyMsg) copyMsg.classList.add('d-none');
        shareModal.show();
      }
    });

    if (copyBtn) {
      copyBtn.addEventListener('click', () => {
        shareLinkInput.select();
        navigator.clipboard.writeText(shareLinkInput.value);
        if (copyMsg) {
          copyMsg.classList.remove('d-none');
          setTimeout(() => copyMsg.classList.add('d-none'), 2000);
        }
        showToast("✓ Đã sao chép liên kết", "success");
      });
    }
  }

  function showToast(msg, type = "success") {
    const toast = document.createElement('div');
    toast.className = `copy-toast bg-${type}`;
    toast.innerHTML = msg;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 2000);
  }

  // ===== TAB CHANGE =====
  function initTabChange() {
    const tabs = document.querySelectorAll('#composeModal .nav-link');
    const postTypeInput = document.getElementById('postTypeInput');
    tabs.forEach(tab => {
      tab.addEventListener('click', function() {
        postTypeInput.value = this.getAttribute('data-bs-target') === '#postTab' ? 'post' : 'event';
      });
    });
  }

  // ===== REACTION (1 người 1 loại) =====
  function initReactions() {
    document.addEventListener('click', async (e) => {
      const opt = e.target.closest('.reaction-opt');
      if (!opt) return;
      e.preventDefault();
      const reaction = opt.getAttribute('data-reaction');
      const article = opt.closest('article');
      const postId = article.dataset.postId;
      const likeBtn = article.querySelector('.btn-like');
      const fd = new FormData();
      fd.append('post_id', postId);
      fd.append('reaction_type', reaction);
      const res = await fetch('/helios/public/home/react', { method: 'POST', body: fd });
      const data = await res.json();
      if (data.success) {
        if (data.action === 'removed') {
          likeBtn.innerHTML = `<i class="bi bi-hand-thumbs-up"></i> Thích`;
        } else {
          let icon = '';
          switch (data.new_reaction) {
            case 'Thích': icon = 'bi-hand-thumbs-up-fill'; break;
            case 'Quan tâm': icon = 'bi-heart-fill'; break;
            case 'Hữu ích': icon = 'bi-lightbulb-fill'; break;
            case 'Chúc mừng': icon = 'bi-trophy-fill'; break;
            default: icon = 'bi-hand-thumbs-up-fill';
          }
          likeBtn.innerHTML = `<i class="bi ${icon}"></i> ${data.new_reaction}`;
        }
        const reactionsDiv = article.querySelector('.reactions-row');
        if (reactionsDiv) {
          const fdCount = new FormData();
          fdCount.append('post_id', postId);
          const countRes = await fetch('/helios/public/home/get-reaction-counts', { method: 'POST', body: fdCount });
          const countData = await countRes.json();
          if (countData.success) {
            let html = '';
            const icons = {
              'Thích': '<i class="bi bi-hand-thumbs-up-fill"></i>',
              'Quan tâm': '<i class="bi bi-heart-fill"></i>',
              'Hữu ích': '<i class="bi bi-lightbulb-fill"></i>',
              'Chúc mừng': '<i class="bi bi-trophy-fill"></i>'
            };
            for (let [type, count] of Object.entries(countData.counts)) {
              html += `<span class="reaction-pill r-${type.toLowerCase().replace(/ /g, '')}">${icons[type]} ${count}</span>`;
            }
            if (!html) html = '<span class="text-muted">0 phản ứng</span>';
            reactionsDiv.innerHTML = html;
          }
        }
      }
    });
  }

  // ===== BÌNH LUẬN =====
  function initComments() {
    document.addEventListener('click', (e) => {
      const btn = e.target.closest('.btn-toggle-comments');
      if (btn) {
        e.preventDefault();
        const postId = btn.dataset.postId;
        const section = document.getElementById(`comment-section-${postId}`);
        if (section.classList.contains('d-none')) {
          section.classList.remove('d-none');
          loadComments(postId);
          section.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        } else {
          section.classList.add('d-none');
        }
      }
    });
    
    document.addEventListener('click', async (e) => {
      const btn = e.target.closest('.btn-submit-comment');
      if (btn) {
        const postId = btn.dataset.postId;
        const textarea = document.querySelector(`#comment-section-${postId} .comment-input`);
        const content = textarea.value.trim();
        if (!content) return;
        const fd = new FormData();
        fd.append('post_id', postId);
        fd.append('content', content);
        const res = await fetch('/helios/public/home/add-comment', { method: 'POST', body: fd });
        const data = await res.json();
        if (data.success) {
          textarea.value = '';
          loadComments(postId);
          updateCommentCount(postId, 1);
        }
      }
    });
    
    document.addEventListener('click', async (e) => {
      const btn = e.target.closest('.btn-edit-comment');
      if (btn) {
        const commentId = btn.dataset.commentId;
        const commentDiv = btn.closest('.comment-item');
        const contentP = commentDiv.querySelector('.comment-content');
        const oldContent = contentP.innerText;
        const newContent = prompt('Sửa bình luận:', oldContent);
        if (newContent && newContent !== oldContent) {
          const fd = new FormData();
          fd.append('comment_id', commentId);
          fd.append('content', newContent);
          const res = await fetch('/helios/public/home/update-comment', { method: 'POST', body: fd });
          const data = await res.json();
          if (data.success) {
            contentP.innerHTML = newContent.replace(/\n/g, '<br>');
            showToast("Đã cập nhật bình luận", "success");
          }
        }
      }
    });
    
    document.addEventListener('click', async (e) => {
      const btn = e.target.closest('.btn-delete-comment');
      if (btn && confirm('Xóa bình luận này?')) {
        const commentId = btn.dataset.commentId;
        const fd = new FormData();
        fd.append('comment_id', commentId);
        const res = await fetch('/helios/public/home/delete-comment', { method: 'POST', body: fd });
        const data = await res.json();
        if (data.success) {
          btn.closest('.comment-item').remove();
          const postId = btn.closest('article').dataset.postId;
          updateCommentCount(postId, -1);
          showToast("Đã xóa bình luận", "success");
        }
      }
    });
  }

  async function loadComments(postId) {
    const container = document.getElementById(`comments-${postId}`);
    const fd = new FormData();
    fd.append('post_id', postId);
    const res = await fetch('/helios/public/home/get-comments', { method: 'POST', body: fd });
    const data = await res.json();
    if (data.success && data.comments.length) {
      let html = '';
      data.comments.forEach(c => {
        const isOwner = (c.MaNguoiDung == 1);
        html += `
          <div class="comment-item d-flex gap-2 mb-2" data-comment-id="${c.MaBinhLuan}">
            <div class="comment-avatar-small" style="background:#6c5ce7;">${escapeHtml(c.HoTen.charAt(0))}</div>
            <div class="flex-grow-1">
              <div class="bg-light rounded p-2">
                <div class="d-flex justify-content-between align-items-start">
                  <strong class="small">${escapeHtml(c.HoTen)}</strong>
                  ${isOwner ? `
                    <div class="dropdown">
                      <button class="btn btn-sm btn-link text-muted p-0" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
                      <ul class="dropdown-menu dropdown-menu-end">
                        <li><button class="dropdown-item btn-edit-comment" data-comment-id="${c.MaBinhLuan}"><i class="bi bi-pencil me-2"></i>Sửa</button></li>
                        <li><button class="dropdown-item text-danger btn-delete-comment" data-comment-id="${c.MaBinhLuan}"><i class="bi bi-trash me-2"></i>Xóa</button></li>
                      </ul>
                    </div>
                  ` : ''}
                </div>
                <p class="comment-content mb-0 small">${escapeHtml(c.NoiDung).replace(/\n/g, '<br>')}</p>
              </div>
              <small class="text-muted" style="font-size:10px;">${formatDate(c.ThoiGianDang)}</small>
            </div>
          </div>
        `;
      });
      container.innerHTML = html;
    } else {
      container.innerHTML = '<div class="text-muted small">Chưa có bình luận nào</div>';
    }
  }

  function updateCommentCount(postId, delta) {
    const countSpan = document.querySelector(`article[data-post-id="${postId}"] .comment-count`);
    if (countSpan) {
      let cur = parseInt(countSpan.textContent) || 0;
      countSpan.textContent = cur + delta;
    }
  }

  // ===== XÓA BÀI VIẾT =====
  function initDeletePost() {
    document.addEventListener('click', async (e) => {
      const btn = e.target.closest('.btn-delete-post');
      if (btn && confirm('Xóa bài viết này?')) {
        const postId = btn.dataset.postId;
        const fd = new FormData();
        fd.append('post_id', postId);
        const res = await fetch('/helios/public/home/delete-post', { method: 'POST', body: fd });
        const data = await res.json();
        if (data.success) {
          document.querySelector(`article[data-post-id="${postId}"]`).remove();
          showToast("Đã xóa bài viết", "success");
        } else {
          alert('Không thể xóa');
        }
      }
    });
  }

  // ===== SỬA BÀI VIẾT (có quản lý ảnh) =====
  function initEditPost() {
    const editModal = new bootstrap.Modal(document.getElementById('editPostModal'));
    const editContent = document.getElementById('editPostContent');
    const editStatus = document.getElementById('editPostStatus');
    const saveBtn = document.getElementById('saveEditPostBtn');
    const editImagesContainer = document.getElementById('editImagesContainer');
    const editNewImages = document.getElementById('editNewImages');
    const editNewPreviewContainer = document.getElementById('editNewPreviewContainer');
    const editImageCountText = document.getElementById('editImageCountText');
    
    let newImageFiles = [];
    let currentPostId = null;

    document.addEventListener('click', async (e) => {
      const btn = e.target.closest('.btn-edit-post');
      if (btn) {
        currentPostId = btn.dataset.postId;
        const res = await fetch(`/helios/public/home/get-post-edit?post_id=${currentPostId}`);
        const data = await res.json();
        if (data.success && data.post) {
          editContent.value = data.post.NoiDung;
          editStatus.value = data.post.TrangThai;
          currentEditImages = data.images || [];
          renderEditImages();
          editModal.show();
        }
      }
    });

    function renderEditImages() {
      if (!editImagesContainer) return;
      editImagesContainer.innerHTML = '';
      if (currentEditImages.length === 0) {
        editImagesContainer.innerHTML = '<div class="text-muted small">Chưa có ảnh nào</div>';
        return;
      }
      currentEditImages.forEach(img => {
        const div = document.createElement('div');
        div.className = 'position-relative';
        div.style.width = '100px';
        div.style.height = '100px';
        div.innerHTML = `
          <img src="${img.DuongDanURL}" class="img-fluid rounded" style="width:100%; height:100%; object-fit:cover;">
          <button type="button" class="btn-close position-absolute top-0 end-0 bg-white rounded-circle m-1" style="width:20px; height:20px;" data-image-id="${img.MaHinhAnh}" onclick="deleteEditImage(${img.MaHinhAnh})"></button>
        `;
        editImagesContainer.appendChild(div);
      });
    }

    window.deleteEditImage = async (imageId) => {
      if (!confirm('Xóa ảnh này?')) return;
      const fd = new FormData();
      fd.append('image_id', imageId);
      const res = await fetch('/helios/public/home/delete-post-image', { method: 'POST', body: fd });
      const data = await res.json();
      if (data.success) {
        currentEditImages = currentEditImages.filter(img => img.MaHinhAnh != imageId);
        renderEditImages();
        showToast("Đã xóa ảnh", "success");
      }
    };

    if (editNewImages) {
      editNewImages.addEventListener('change', (e) => {
        const files = Array.from(e.target.files);
        if (files.length + currentEditImages.length > 10) {
          alert('Tổng số ảnh không được vượt quá 10!');
          editNewImages.value = '';
          return;
        }
        newImageFiles = files;
        editImageCountText.textContent = `+${files.length} ảnh mới`;
        editNewPreviewContainer.innerHTML = '';
        files.forEach((file, idx) => {
          const reader = new FileReader();
          reader.onload = (ev) => {
            const div = document.createElement('div');
            div.className = 'position-relative';
            div.style.width = '100px';
            div.style.height = '100px';
            div.innerHTML = `
              <img src="${ev.target.result}" class="img-fluid rounded" style="width:100%; height:100%; object-fit:cover;">
              <button type="button" class="btn-close position-absolute top-0 end-0 bg-white rounded-circle m-1" style="width:20px; height:20px;" onclick="this.parentElement.remove(); newImageFiles = newImageFiles.filter((_, i) => i != ${idx})">
            `;
            editNewPreviewContainer.appendChild(div);
          };
          reader.readAsDataURL(file);
        });
      });
    }

    saveBtn.addEventListener('click', async () => {
      const fd = new FormData();
      fd.append('post_id', currentPostId);
      fd.append('content', editContent.value);
      fd.append('status', editStatus.value);
      const res = await fetch('/helios/public/home/update-post', { method: 'POST', body: fd });
      const data = await res.json();
      if (data.success) {
        if (newImageFiles.length > 0) {
          const imgFd = new FormData();
          imgFd.append('post_id', currentPostId);
          newImageFiles.forEach(file => {
            imgFd.append('new_images[]', file);
          });
          await fetch('/helios/public/home/add-post-images', { method: 'POST', body: imgFd });
        }
        location.reload();
      } else {
        alert('Không thể cập nhật');
      }
    });
  }

  // ===== HELPER =====
  function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
  }

  function formatDate(dateStr) {
    const d = new Date(dateStr);
    const now = new Date();
    const diff = Math.floor((now - d) / 1000);
    if (diff < 60) return 'vừa xong';
    if (diff < 3600) return Math.floor(diff / 60) + ' phút trước';
    if (diff < 86400) return Math.floor(diff / 3600) + ' giờ trước';
    return d.toLocaleDateString('vi-VN');
  }

  // ===== KHỞI TẠO =====
  function init() {
    initSearchTooltip();
    initShare();
    initTabChange();
    initReactions();
    initComments();
    initDeletePost();
    initEditPost();
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }

})();