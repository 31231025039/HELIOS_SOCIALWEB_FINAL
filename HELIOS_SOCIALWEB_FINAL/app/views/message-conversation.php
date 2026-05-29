<?php
// app/views/message-conversation.php - Danh sách hội thoại + popup tìm người
$uid = $currentUserId ?? 1;
$convs = $conversations ?? [];
$activeUser = $activeUser ?? null;
$baseUrl = $baseUrl ?? '/helios/public/';

function convAvatar($name, $img, $baseUrl) {
    if ($img) return "<img src=\"".htmlspecialchars($baseUrl . ltrim($img, '/'))."\" loading=\"eager\" decoding=\"async\" class=\"msg-conv-avatar-img\">";
    $words = explode(' ', trim($name));
    $initial = mb_strtoupper(mb_substr($words[0], 0, 1) . mb_substr(end($words), 0, 1));
    return "<span>$initial</span>";
}
?>

<aside class="msg-conversation d-flex flex-column border-end">

    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between px-3 py-3 border-bottom">
        <h5 class="fw-bold mb-0 text-helios-navy">
            <i class="bi bi-chat-dots-fill me-2"></i>Tin nhắn
        </h5>
        <button class="msg-icon-btn btn btn-helios-navy text-white rounded-circle" id="newMsgBtn" title="Soạn tin nhắn mới">
            <i class="bi bi-pencil-square"></i>
        </button>
    </div>

    <!-- Search -->
    <div class="px-3 py-2 border-bottom position-relative">
        <div class="input-group input-group-sm">
            <span class="input-group-text bg-light border-end-0 rounded-start-pill">
                <i class="bi bi-search text-muted"></i>
            </span>
            <input id="searchConv" type="text"
                   class="form-control bg-light border-start-0 rounded-end-pill"
                   placeholder="Tìm kiếm hội thoại..." autocomplete="off">
        </div>
        <div id="searchDrop" class="msg-search-drop" style="display:none;"></div>
    </div>

    <!-- Conversation list -->
    <ul id="convList" class="list-unstyled mb-0 overflow-y-auto flex-fill">
        <?php if (empty($convs)): ?>
            <li class="text-center text-muted py-5">
                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                <p class="mb-1">Hộp thư trống</p>
                <small>Chưa có hội thoại nào</small>
            </li>
        <?php else: foreach ($convs as $c):
            $isActive = $activeUser && $activeUser['id'] == $c['user_id'];
            $unread = (int)($c['unread'] ?? 0);
            $preview = htmlspecialchars($c['last_msg'] ?? 'Bắt đầu trò chuyện');
            $time = !empty($c['last_time']) ? date('H:i', strtotime($c['last_time'])) : '';
            $isMine = ($c['last_sender'] ?? 0) == $uid;
            $name = htmlspecialchars($c['name'] ?? 'Người dùng');
        ?>
        <li class="msg-conv-item <?= $isActive ? 'msg-conv-item--active' : '' ?> <?= $unread ? 'msg-conv-item--unread' : '' ?>"
            data-user="<?= $c['user_id'] ?>" role="button">

            <div class="msg-conv-avatar flex-shrink-0">
                <?= convAvatar($c['name'] ?? 'Người dùng', $c['avatar'] ?? '', $baseUrl) ?>
            </div>

            <div class="msg-conv-meta">
                <div class="msg-conv-name"><?= $name ?></div>
                <div class="msg-conv-preview">
                    <?= $isMine ? 'Bạn: ' : '' ?><?= $preview ?>
                </div>
            </div>

            <div class="msg-conv-right text-end flex-shrink-0">
                <span class="msg-conv-time d-block"><?= $time ?></span>
                <?php if ($unread): ?>
                    <span class="msg-unread-badge"><?= $unread > 99 ? '99+' : $unread ?></span>
                <?php endif ?>
            </div>
        </li>
        <?php endforeach; endif ?>
    </ul>
</aside>

<!-- Modal: Tin nhắn mới -->
<div id="findUserPopup" class="msg-popup-overlay" hidden>
    <div class="bg-white rounded-3 shadow-lg overflow-hidden" style="width:500px;max-width:90vw;">

        <div class="d-flex align-items-center justify-content-between px-4 py-3 border-bottom">
            <span class="fw-bold fs-5 text-helios-navy">Tin nhắn mới</span>
            <button class="msg-icon-btn" id="closeFindUserPopup">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <div class="p-4">
            <div class="input-group mb-3">
                <span class="input-group-text bg-light border-end-0 rounded-start-pill">
                    <i class="bi bi-search text-muted"></i>
                </span>
                <input id="findUserInput" type="text"
                       class="form-control bg-light border-start-0 rounded-end-pill"
                       placeholder="Tìm người dùng..." autocomplete="off">
            </div>

            <ul id="findUserResults" class="list-unstyled mb-0 overflow-y-auto" style="max-height:320px;">
                <li class="text-center text-muted py-4">Nhập tên để tìm người dùng</li>
            </ul>
        </div>
    </div>
</div>
