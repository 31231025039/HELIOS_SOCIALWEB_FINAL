<?php
// app/views/message_conversation.php - Danh sách hội thoại + popup tìm người
$uid = $currentUserId ?? 1;
$convs = $conversations ?? [];
$activeUser = $activeUser ?? null;

// Hàm tạo avatar từ tên hoặc ảnh
function convAvatar($name, $img) {
    if ($img) return "<img src=\"".htmlspecialchars($img)."\">";
    
    $words = explode(' ', trim($name));
    $first = mb_substr($words[0], 0, 1);        // Chữ đầu của họ
    $last = mb_substr(end($words), 0, 1);       // Chữ đầu của tên
    $initial = mb_strtoupper($first . $last);
    
    return "<span>$initial</span>";
}
?>
<aside class="msg-conversation">
    <div class="msg-conversation-header">
        <h5><i class="bi bi-chat-dots-fill me-2"></i>Tin nhắn</h5>
        <button class="msg-icon-btn" id="newMsgBtn" title="Soạn tin nhắn mới">
            <i class="bi bi-pencil-square"></i>
        </button>
    </div>
    
    <div class="msg-search-wrap">
        <i class="bi bi-search"></i>
        <input id="searchConv" class="msg-search-input" type="text" 
               placeholder="Tìm kiếm hội thoại..." autocomplete="off">
        <div id="searchDrop" class="msg-search-drop" style="display:none;"></div>
    </div>
    
    <ul id="convList" class="msg-conv-list">
        <?php if (empty($convs)): ?>
            <li class="msg-conv-empty">
                <i class="bi bi-inbox"></i>
                <p>Hộp thư trống</p>
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
        <li class="msg-conv-item <?= $isActive ? 'active' : '' ?> <?= $unread ? 'has-unread' : '' ?>" 
            data-user="<?= $c['user_id'] ?>">
            <div class="msg-conv-avatar"><?= convAvatar($name, $c['avatar'] ?? '') ?></div>
            <div class="msg-conv-meta">
                <div class="msg-conv-name"><?= $name ?></div>
                <div class="msg-conv-preview">
                    <?= $isMine ? 'Bạn: ' : '' ?><?= $preview ?>
                </div>
            </div>
            <div class="msg-conv-right">
                <span class="msg-conv-time"><?= $time ?></span>
                <?php if ($unread): ?>
                    <span class="msg-unread-badge"><?= $unread > 99 ? '99+' : $unread ?></span>
                <?php endif ?>
            </div>
        </li>
        <?php endforeach; endif ?>
    </ul>
</aside>

<!-- Popup tìm người dùng -->
<div id="findUserPopup" class="msg-popup-overlay" hidden>
    <div class="msg-popup">
        <div class="msg-popup-header">
            <span>Tin nhắn mới</span>
            <button class="msg-icon-btn msg-popup-close" id="closeFindUserPopup">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="msg-popup-body">
            <div class="msg-popup-search">
                <i class="bi bi-search"></i>
                <input id="findUserInput" type="text" placeholder="Tìm người dùng..." autocomplete="off">
            </div>
            <ul id="findUserResults" class="msg-popup-results">
                <li class="msg-popup-hint">Nhập tên để tìm người dùng</li>
            </ul>
        </div>
    </div>
</div>