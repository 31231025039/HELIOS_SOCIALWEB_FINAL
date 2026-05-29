<div class="modal fade" id="editPostModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square"></i> Chỉnh sửa bài viết</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <textarea id="editPostContent" class="form-control" rows="5" placeholder="Nội dung bài viết..."></textarea>
        <div class="mt-2">
          <label class="text-muted small">Trạng thái:</label>
          <select id="editPostStatus" class="form-select form-select-sm w-auto">
            <option value="Public">Công khai</option>
            <option value="Private">Chỉ mình tôi</option>
          </select>
        </div>
        <div class="mt-3">
          <label class="text-muted small fw-bold">Ảnh hiện tại:</label>
          <div id="editImagesContainer" class="d-flex flex-wrap gap-2 mt-2"></div>
          <div class="mt-2">
            <label class="btn btn-outline-secondary btn-sm">
              <i class="bi bi-plus-circle"></i> Thêm ảnh mới
              <input type="file" id="editNewImages" style="display:none;" accept="image/*" multiple>
            </label>
            <span class="small text-muted ms-2" id="editImageCountText"></span>
          </div>
          <div id="editNewPreviewContainer" class="d-flex flex-wrap gap-2 mt-2"></div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
        <button type="button" class="btn btn-primary" id="saveEditPostBtn">Lưu thay đổi</button>
      </div>
    </div>
  </div>
</div>
