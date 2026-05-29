(function () {
  "use strict";

  const BASE = "/helios/public/home";
  const reactions = {
    "Thích": ["bi-hand-thumbs-up-fill", "text-primary", "reaction-like"],
    "Quan tâm": ["bi-heart-fill", "text-danger", "reaction-care"],
    "Hữu ích": ["bi-lightbulb-fill", "text-warning", "reaction-useful"],
    "Chúc mừng": ["bi-trophy-fill", "text-success", "reaction-congrats"]
  };

  let editPostId = null;
  let editImages = [];
  let editNewFiles = [];
  let createFiles = [];

  const $ = (selector, root = document) => root.querySelector(selector);
  const $$ = (selector, root = document) => [...root.querySelectorAll(selector)];
  const fd = (data) => {
    const form = new FormData();
    Object.entries(data).forEach(([key, value]) => {
      if (value !== null && value !== undefined && value !== "") form.append(key, value);
    });
    return form;
  };
  const post = (url, data) => fetch(url, { method: "POST", body: fd(data) }).then(r => r.json());
  const esc = (text) => {
    const div = document.createElement("div");
    div.textContent = text ?? "";
    return div.innerHTML;
  };
  const attr = (text) => esc(text).replace(/"/g, "&quot;");

  function toast(text, type = "success") {
    const el = document.createElement("div");
    el.className = `copy-toast bg-${type}`;
    el.textContent = text;
    document.body.appendChild(el);
    setTimeout(() => el.remove(), 2000);
  }

  function dateText(value) {
    const date = new Date(value);
    const diff = Math.floor((new Date() - date) / 1000);
    if (Number.isNaN(diff)) return "";
    if (diff < 60) return "vừa xong";
    if (diff < 3600) return `${Math.floor(diff / 60)} phút trước`;
    if (diff < 86400) return `${Math.floor(diff / 3600)} giờ trước`;
    return date.toLocaleDateString("vi-VN");
  }

  function renderLike(btn, type) {
    btn.classList.toggle("is-reacted", Boolean(type));
    if (!type || !reactions[type]) {
      btn.innerHTML = '<i class="bi bi-hand-thumbs-up"></i> <span>Thích</span>';
      return;
    }
    const [icon, color] = reactions[type];
    btn.innerHTML = `<i class="bi ${icon} ${color}"></i> <span>${esc(type)}</span>`;
  }

  async function refreshReactions(article) {
    const postId = article.dataset.postId;
    const box = $(".reactions-row", article);
    if (!box) return;

    const data = await post(`${BASE}/get-reaction-counts`, { post_id: postId });
    if (!data.success) return;

    const html = Object.entries(data.counts).map(([type, count]) => {
      const [icon, color, cls] = reactions[type] || reactions["Thích"];
      return `<span class="reaction-pill ${cls}"><i class="bi ${icon} ${color}"></i> ${count}</span>`;
    }).join("");
    box.innerHTML = html || '<span class="text-muted">0 phản ứng</span>';
  }

  function setCommentCount(postId, count) {
    const el = $(`article[data-post-id="${postId}"] .comment-count`);
    if (el) el.textContent = Math.max(0, Number(count) || 0);
  }

  function clearReply(section) {
    const input = $(".reply-parent-id", section);
    const label = $("[data-reply-label]", section);
    if (input) input.value = "";
    if (label) {
      $("strong", label).textContent = "";
      label.classList.add("d-none");
    }
  }

  async function loadComments(postId) {
    const box = $(`#comments-${postId}`);
    if (!box) return 0;

    const data = await post(`${BASE}/get-comments`, { post_id: postId });
    if (!data.success || !data.comments.length) {
      box.innerHTML = '<div class="text-muted small">Chưa có bình luận nào</div>';
      return 0;
    }

    box.innerHTML = renderComments(data.comments);
    return data.comments.length;
  }

  function renderComments(comments) {
    const groups = {};
    comments.forEach(c => {
      const key = c.MaBinhLuanCha || "root";
      groups[key] ||= [];
      groups[key].push(c);
    });

    const list = (parent = "root", level = 0) => (groups[parent] || []).map(c => {
      const actions = [
        `<button class="btn btn-link btn-sm p-0 btn-reply-comment" data-comment-id="${c.MaBinhLuan}" data-comment-author="${attr(c.HoTen)}">Trả lời</button>`
      ];
      if (c.can_edit) {
        actions.push(`<button class="btn btn-link btn-sm p-0 btn-edit-comment" data-comment-id="${c.MaBinhLuan}">Sửa</button>`);
        actions.push(`<button class="btn btn-link btn-sm p-0 text-danger btn-delete-comment" data-comment-id="${c.MaBinhLuan}">Xóa</button>`);
      }
      if (c.can_hide) {
        actions.push(`<button class="btn btn-link btn-sm p-0 text-danger btn-hide-comment" data-comment-id="${c.MaBinhLuan}">Ẩn</button>`);
      }

      return `
        <div class="comment-item d-flex gap-2 mb-2 ${level ? "comment-reply" : ""}" data-comment-id="${c.MaBinhLuan}">
          <div class="comment-avatar-small" style="background:#6c5ce7;">${esc((c.HoTen || "?").charAt(0))}</div>
          <div class="flex-grow-1">
            <div class="comment-bubble">
              <strong class="small">${esc(c.HoTen)}</strong>
              <p class="comment-content mb-0 small">${esc(c.NoiDung).replace(/\n/g, "<br>")}</p>
            </div>
            <div class="comment-actions text-muted">
              <small>${dateText(c.ThoiGianDang)}</small>${actions.join('<span class="mx-1">·</span>')}
            </div>
            ${list(c.MaBinhLuan, level + 1)}
          </div>
        </div>
      `;
    }).join("");

    return list();
  }

  async function submitComment(btn) {
    const postId = btn.dataset.postId;
    const section = $(`#comment-section-${postId}`);
    const input = $(".comment-input", section);
    const parent = $(".reply-parent-id", section);
    const content = input.value.trim();
    if (!content) return;

    const data = await post(`${BASE}/add-comment`, {
      post_id: postId,
      content,
      parent_comment_id: parent.value
    });
    if (!data.success) return;

    input.value = "";
    clearReply(section);
    setCommentCount(postId, await loadComments(postId));
  }

  function previewFiles(input, box, counter, filesStore, removable = false) {
    const files = [...input.files];
    if (files.length > 10) {
      alert("Chỉ được chọn tối đa 10 ảnh!");
      input.value = "";
      return [];
    }

    filesStore.splice(0, filesStore.length, ...files);
    if (counter) counter.textContent = `${files.length}/10 ảnh`;
    box.innerHTML = "";

    files.forEach((file, index) => {
      const reader = new FileReader();
      reader.onload = (e) => {
        const item = document.createElement("div");
        item.className = "position-relative";
        item.style.cssText = "width:100px;height:100px";
        item.innerHTML = `
          <img src="${e.target.result}" class="img-fluid rounded" style="width:100%;height:100%;object-fit:cover;" alt="Ảnh xem trước">
          ${removable ? `<button type="button" class="btn-close position-absolute top-0 end-0 bg-white rounded-circle m-1 btn-remove-preview-image" data-index="${index}" style="width:20px;height:20px"></button>` : ""}
        `;
        box.appendChild(item);
      };
      reader.readAsDataURL(file);
    });
    return files;
  }

  function removeCreatePreview(index) {
    createFiles.splice(index, 1);
    const input = $("#postImagesInput");
    const transfer = new DataTransfer();
    createFiles.forEach(file => transfer.items.add(file));
    input.files = transfer.files;
    input.dispatchEvent(new Event("change"));
  }

  async function openEdit(postId) {
    const modalEl = $("#editPostModal");
    if (!modalEl || !window.bootstrap) return;

    const data = await fetch(`${BASE}/get-post-edit?post_id=${postId}`).then(r => r.json());
    if (!data.success || !data.post) return;

    editPostId = postId;
    editImages = data.images || [];
    editNewFiles = [];
    $("#editPostContent").value = data.post.NoiDung || "";
    $("#editPostStatus").value = data.post.TrangThai;
    $("#editNewImages").value = "";
    $("#editNewPreviewContainer").innerHTML = "";
    $("#editImageCountText").textContent = "";
    renderEditImages();
    bootstrap.Modal.getOrCreateInstance(modalEl).show();
  }

  function renderEditImages() {
    const box = $("#editImagesContainer");
    if (!box) return;
    if (!editImages.length) {
      box.innerHTML = '<div class="text-muted small">Chưa có ảnh nào</div>';
      return;
    }

    box.innerHTML = editImages.map(img => `
      <div class="position-relative" style="width:100px;height:100px">
        <img src="${img.DuongDanURL}" class="img-fluid rounded" style="width:100%;height:100%;object-fit:cover;" alt="Ảnh bài viết">
        <button type="button" class="btn-close position-absolute top-0 end-0 bg-white rounded-circle m-1 btn-delete-edit-image" data-image-id="${img.MaHinhAnh}" style="width:20px;height:20px"></button>
      </div>
    `).join("");
  }

  async function saveEdit() {
    const data = await post(`${BASE}/update-post`, {
      post_id: editPostId,
      content: $("#editPostContent").value,
      status: $("#editPostStatus").value
    });
    if (!data.success) {
      alert("Không thể cập nhật");
      return;
    }

    if (editNewFiles.length) {
      const form = new FormData();
      form.append("post_id", editPostId);
      editNewFiles.forEach(file => form.append("new_images[]", file));
      await fetch(`${BASE}/add-post-images`, { method: "POST", body: form });
    }
    location.reload();
  }

  async function handleClick(e) {
    const target = e.target;
    const article = target.closest("article");
    const reactionBtn = target.closest(".reaction-opt");
    const toggleComments = target.closest(".btn-toggle-comments");
    const submit = target.closest(".btn-submit-comment");
    const reply = target.closest(".btn-reply-comment");
    const cancelReply = target.closest(".btn-cancel-reply");
    const editComment = target.closest(".btn-edit-comment");
    const deleteComment = target.closest(".btn-delete-comment");
    const hideComment = target.closest(".btn-hide-comment");
    const deletePost = target.closest(".btn-delete-post");
    const editPost = target.closest(".btn-edit-post");
    const share = target.closest(".btn-share");
    const removePreview = target.closest(".btn-remove-preview-image");
    const deleteEditImage = target.closest(".btn-delete-edit-image");
    const saveEditBtn = target.closest("#saveEditPostBtn");
    const postImage = target.closest(".post-image");

    if (reactionBtn && article) {
      e.preventDefault();
      const data = await post(`${BASE}/react`, {
        post_id: article.dataset.postId,
        reaction_type: reactionBtn.dataset.reaction
      });
      if (data.success) {
        renderLike($(".btn-like", article), data.action === "removed" ? null : data.new_reaction);
        await refreshReactions(article);
      }
      return;
    }

    if (toggleComments) {
      const postId = toggleComments.dataset.postId;
      const section = $(`#comment-section-${postId}`);
      section.classList.toggle("d-none");
      if (!section.classList.contains("d-none")) {
        await loadComments(postId);
        section.scrollIntoView({ behavior: "smooth", block: "nearest" });
      }
      return;
    }

    if (submit) return submitComment(submit);

    if (reply && article) {
      const section = $(".comment-section", article);
      $(".reply-parent-id", section).value = reply.dataset.commentId;
      $("[data-reply-label] strong", section).textContent = reply.dataset.commentAuthor || "bình luận";
      $("[data-reply-label]", section).classList.remove("d-none");
      $(".comment-input", section).focus();
      return;
    }

    if (cancelReply) return clearReply(cancelReply.closest(".comment-section"));

    if (editComment) {
      const item = editComment.closest(".comment-item");
      const content = $(".comment-content", item);
      const next = prompt("Sửa bình luận:", content.innerText);
      if (!next || next === content.innerText) return;
      const data = await post(`${BASE}/update-comment`, { comment_id: editComment.dataset.commentId, content: next });
      if (data.success) content.innerHTML = esc(next).replace(/\n/g, "<br>");
      return;
    }

    if ((deleteComment || hideComment) && article) {
      const btn = deleteComment || hideComment;
      const action = deleteComment ? "delete-comment" : "hide-comment";
      const message = deleteComment ? "Xóa bình luận này?" : "Ẩn bình luận này khỏi bài viết?";
      if (!confirm(message)) return;
      const data = await post(`${BASE}/${action}`, { comment_id: btn.dataset.commentId });
      if (data.success) setCommentCount(article.dataset.postId, await loadComments(article.dataset.postId));
      return;
    }

    if (deletePost) {
      if (!confirm("Xóa bài viết này?")) return;
      const data = await post(`${BASE}/delete-post`, { post_id: deletePost.dataset.postId });
      if (data.success) deletePost.closest("article").remove();
      return;
    }

    if (editPost) return openEdit(editPost.dataset.postId);
    if (saveEditBtn) return saveEdit();
    if (removePreview) return removeCreatePreview(Number(removePreview.dataset.index));

    if (deleteEditImage) {
      if (!confirm("Xóa ảnh này?")) return;
      const data = await post(`${BASE}/delete-post-image`, { image_id: deleteEditImage.dataset.imageId });
      if (data.success) {
        editImages = editImages.filter(img => String(img.MaHinhAnh) !== String(deleteEditImage.dataset.imageId));
        renderEditImages();
      }
      return;
    }

    if (share) {
      const input = $("#shareLinkInput");
      const modal = $("#shareModal");
      if (!input || !modal || !window.bootstrap) return;
      input.value = `${window.location.origin}/helios/public/home?post=${share.dataset.postId}`;
      $("#copySuccessMsg")?.classList.add("d-none");
      bootstrap.Modal.getOrCreateInstance(modal).show();
      return;
    }

    if (target.closest("#copyShareLinkBtn")) {
      const input = $("#shareLinkInput");
      await navigator.clipboard.writeText(input.value);
      $("#copySuccessMsg")?.classList.remove("d-none");
      toast("Đã sao chép liên kết");
      return;
    }

    if (postImage && window.bootstrap) {
      document.body.insertAdjacentHTML("beforeend", `
        <div class="modal fade" id="imgModal" tabindex="-1">
          <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content bg-transparent border-0">
              <div class="modal-body text-center">
                <img src="${postImage.src}" class="img-fluid rounded" style="max-height:90vh;" alt="Ảnh bài viết">
                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal"></button>
              </div>
            </div>
          </div>
        </div>
      `);
      const modal = $("#imgModal");
      modal.addEventListener("hidden.bs.modal", () => modal.remove());
      bootstrap.Modal.getOrCreateInstance(modal).show();
    }
  }

  function init() {
    const searchBar = $("#searchBar");
    const searchTooltip = $("#searchTooltip");
    if (searchBar && searchTooltip) {
      searchBar.addEventListener("mouseenter", () => searchTooltip.style.display = "block");
      searchBar.addEventListener("mouseleave", () => searchTooltip.style.display = "none");
    }

    $$("#composeModal .nav-link").forEach(tab => {
      tab.addEventListener("click", () => {
        const input = $("#postTypeInput");
        if (input) input.value = tab.dataset.bsTarget === "#postTab" ? "post" : "event";
      });
    });

    $("#postImagesInput")?.addEventListener("change", e => {
      previewFiles(e.target, $("#imagePreviewContainer"), $("#imageCountText"), createFiles, true);
    });

    $("#editNewImages")?.addEventListener("change", e => {
      const files = [...e.target.files];
      if (files.length + editImages.length > 10) {
        alert("Tổng số ảnh không được vượt quá 10!");
        e.target.value = "";
        return;
      }
      editNewFiles = files;
      $("#editImageCountText").textContent = files.length ? `+${files.length} ảnh mới` : "";
      previewFiles(e.target, $("#editNewPreviewContainer"), null, editNewFiles);
    });

    document.addEventListener("click", (e) => {
      handleClick(e).catch(() => toast("Có lỗi xảy ra, thử lại giúp mình", "danger"));
    });
  }
  
  document.readyState === "loading" ? document.addEventListener("DOMContentLoaded", init) : init();
})();
