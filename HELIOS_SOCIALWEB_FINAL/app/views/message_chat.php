<?php
// app/views/message_chat.php - Chat phải
$baseUrl = $baseUrl ?? '/helios/public/';

if (!isset($activeUser) || !$activeUser): ?>
    <main class="msg-chat">
        <div class="msg-chat-empty">
            <div class="msg-chat-empty-icon"><i class="bi bi-chat-heart-fill"></i></div>
            <h5>Tin nhắn của bạn</h5>
            <p>Chọn hội thoại hoặc bắt đầu cuộc trò chuyện mới</p>
            <button class="btn-msg-start" id="emptyNewBtn">
                <i class="bi bi-pencil-square me-2"></i>Soạn tin nhắn
            </button>
        </div>
    </main>

<?php else: 
    $otherId = $activeUser['id'];
    $otherName = htmlspecialchars($activeUser['name']);
    
    // Lấy chữ đầu của họ + chữ đầu của tên
    $words = explode(' ', trim($otherName));
    $firstLetter = mb_substr($words[0], 0, 1);
    $lastLetter = mb_substr(end($words), 0, 1);
    $avatarInitial = mb_strtoupper($firstLetter . $lastLetter);
    
    $avatarHtml = !empty($activeUser['avatar']) 
        ? '<img src="'.htmlspecialchars($activeUser['avatar']).'">' 
        : '<span>'.$avatarInitial.'</span>';
    
    $msgs = $messages ?? [];
    $pinnedMessages = $pinnedMessages ?? [];
    $attachments = $attachments ?? [];
?>

<main class="msg-chat">
    <!-- Header -->
    <div class="msg-chat-header">
        <button class="msg-icon-btn d-lg-none" id="backBtn"><i class="bi bi-arrow-left"></i></button>
        <div class="msg-chat-avatar"><?= $avatarHtml ?></div>
        <div class="msg-chat-info">
            <div class="msg-chat-name"><?= $otherName ?></div>
            <div class="msg-chat-status"><i class="bi bi-circle-fill" style="font-size:8px; color:#22c55e;"></i> Trực tuyến</div>
        </div>
        <div class="msg-chat-actions">
            <button class="msg-icon-btn" id="showRightbarBtn" title="Chi tiết"><i class="bi bi-info-circle"></i></button>
            <button class="msg-icon-btn" id="searchMsgBtn" title="Tìm kiếm tin nhắn"><i class="bi bi-search"></i></button>
            <button class="msg-icon-btn" id="showPinnedBtn" title="Tin nhắn đã ghim"><i class="bi bi-pin-angle-fill"></i></button>
            <button class="msg-icon-btn text-danger" id="deleteConvBtn" title="Xóa hội thoại"><i class="bi bi-trash3"></i></button>
        </div>
    </div>
    
    <!-- Thanh tìm kiếm tin nhắn -->
    <div class="msg-chat-search" id="msgChatSearch" style="display:none;">
        <div class="msg-chat-search-input">
            <i class="bi bi-search"></i>
            <input type="text" id="chatSearchInput" placeholder="Tìm kiếm tin nhắn..." autocomplete="off">
            <button id="closeChatSearch"><i class="bi bi-x-lg"></i></button>
        </div>
        <div id="chatSearchResults" class="msg-chat-search-results"></div>
    </div>
    
    <!-- Danh sách tin nhắn -->
    <div id="msgList" class="msg-messages" data-with="<?= $otherId ?>">
        <?php if (empty($msgs)): ?>
            <div class="msg-messages-empty">
                <div class="msg-empty-avatar"><?= $avatarHtml ?></div>
                <p class="msg-empty-name"><?= $otherName ?></p>
                <p class="msg-empty-hint">Hãy gửi tin nhắn đầu tiên 👋</p>
            </div>
        <?php else:
            $prevDate = '';
            foreach ($msgs as $m):
                $date = date('d/m/Y', strtotime($m['time']));
                $time = date('H:i', strtotime($m['time']));
                $isMine = (bool)$m['is_mine'];
                $isPinned = !empty($m['is_pinned']);
        ?>
        <?php if ($date !== $prevDate): $prevDate = $date; ?>
            <div class="msg-date-group"><span><?= $date === date('d/m/Y') ? 'Hôm nay' : $date ?></span></div>
        <?php endif ?>
        <div class="msg-item <?= $isMine ? 'msg-item--out' : 'msg-item--in' ?>" data-id="<?= $m['id'] ?>">
            <div class="msg-bubble <?= $isMine ? 'msg-bubble--out' : 'msg-bubble--in' ?>">
                <?php if ($isPinned): ?><span class="msg-pin-badge"><i class="bi bi-pin-angle-fill"></i></span><?php endif; ?>
                <?php if (!empty($m['file_path'])): 
                    $isImage = preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $m['file_path']);
                    if ($isImage): ?>
                        <a href="<?= $baseUrl . ltrim($m['file_path'], '/') ?>" target="_blank">
                            <img src="<?= $baseUrl . ltrim($m['file_path'], '/') ?>" class="msg-image">
                        </a>
                    <?php else: ?>
                        <a href="<?= $baseUrl . ltrim($m['file_path'], '/') ?>" target="_blank" class="msg-file">
                            <i class="bi bi-file-earmark"></i> <?= basename($m['file_path']) ?>
                        </a>
                    <?php endif; ?>
                <?php else: ?>
                    <?= nl2br(htmlspecialchars($m['content'])) ?>
                <?php endif; ?>
                <div class="msg-meta">
                    <span class="msg-time"><?= $time ?></span>
                    <?php if (!$isMine): ?>
                        <button class="msg-pin" data-id="<?= $m['id'] ?>"><i class="bi bi-pin<?= $isPinned ? '-angle-fill' : '' ?>"></i></button>
                    <?php endif ?>
                    <?php if ($isMine): ?>
                        <i class="bi <?= $m['is_read'] ? 'bi-check2-all' : 'bi-check2' ?>"></i>
                        <button class="msg-delete" data-id="<?= $m['id'] ?>"><i class="bi bi-trash3"></i></button>
                    <?php endif ?>
                </div>
            </div>
        </div>
        <?php endforeach; endif ?>
    </div>
    
    <!-- Input -->
    <div class="msg-input">
        <input type="file" id="fileInput" style="display:none" accept="image/*,.pdf,.docx,.zip">
        <button class="msg-upload-btn" id="uploadBtn" title="Đính kèm file"><i class="bi bi-paperclip"></i></button>
        <div class="msg-input-wrap">
            <textarea id="msgInput" class="msg-input-field" rows="1" placeholder="Nhập tin nhắn..."></textarea>
        </div>
        <button class="msg-send" id="sendBtn" disabled><i class="bi bi-send-fill"></i></button>
    </div>
</main>

<!-- Rightbar -->
<aside class="msg-rightbar" id="msgRightbar" style="display:none;">
    <div class="msg-rightbar-header">
        <span>Chi tiết</span>
        <button class="msg-rightbar-close" id="closeRightbar"><i class="bi bi-x-lg"></i></button>
    </div>
    
    <!-- Thông tin người dùng -->
    <div class="msg-rightbar-user">
        <div class="msg-rightbar-avatar"><?= $avatarHtml ?></div>
        <div class="msg-rightbar-name"><?= $otherName ?></div>
        <div class="msg-rightbar-email"><?= $activeUser['headline'] ?? 'Chưa có thông tin' ?></div>
        <div class="msg-rightbar-actions">
            <button class="msg-rightbar-delete" id="deleteConvBtn"><i class="bi bi-trash3"></i> Xóa tin nhắn</button>
        </div>
    </div>
    
    <!-- Tệp đính kèm đã gửi -->
    <div class="msg-rightbar-section">
        <div class="msg-rightbar-title"><i class="bi bi-paperclip"></i> Tệp đính kèm đã gửi (<?= count($attachments) ?>)</div>
        <div class="msg-rightbar-files">
            <?php if (empty($attachments)): ?>
                <p class="text-muted">Chưa có tệp đính kèm</p>
            <?php else: foreach ($attachments as $file): 
                $fileName = basename($file['file_path']);
                $fileSize = file_exists(ROOT_PATH . '/public' . $file['file_path']) 
                    ? round(filesize(ROOT_PATH . '/public' . $file['file_path']) / 1024, 1) . ' KB'
                    : '? KB';
                $fileTime = date('H:i', strtotime($file['time']));
            ?>
                <div class="msg-rightbar-file" data-msg="<?= $file['id'] ?>">
                    <a href="<?= $baseUrl . ltrim($file['file_path'], '/') ?>" target="_blank" class="msg-rightbar-file-link">
                        <div class="msg-rightbar-file-info">
                            <div class="msg-rightbar-file-name"><?= htmlspecialchars($fileName) ?></div>
                            <div class="msg-rightbar-file-meta"><?= $fileSize ?> · <?= $fileTime ?></div>
                        </div>
                    </a>
                </div>
            <?php endforeach; endif ?>
        </div>
    </div>
    
    <!-- Tin nhắn đã ghim -->
    <div class="msg-rightbar-section">
        <div class="msg-rightbar-title"><i class="bi bi-pin-angle-fill"></i> Tin nhắn đã ghim (<?= count($pinnedMessages) ?>)</div>
        <div class="msg-rightbar-pinned">
            <?php if (empty($pinnedMessages)): ?>
                <p class="text-muted">Chưa có tin nhắn ghim</p>
            <?php else: foreach ($pinnedMessages as $pin): ?>
                <div class="msg-rightbar-pin" data-msg="<?= $pin['id'] ?>">
                    <div class="msg-rightbar-pin-content">
                        <?= htmlspecialchars(mb_substr($pin['content'], 0, 60)) ?>...
                    </div>
                    <div class="msg-rightbar-pin-time">
                        <?= date('d/m/Y H:i', strtotime($pin['time'])) ?>
                    </div>
                </div>
            <?php endforeach; endif ?>
        </div>
    </div>
</aside>

<!-- Popup tin nhắn đã ghim -->
<div id="pinPopup" class="msg-pin-popup" style="display:none;">
    <div class="msg-pin-popup-content">
        <div class="msg-pin-popup-header">
            <span><i class="bi bi-pin-angle-fill"></i> Tin nhắn đã ghim</span>
            <button class="msg-icon-btn" id="closePinPopup"><i class="bi bi-x-lg"></i></button>
        </div>
        <div class="msg-pin-popup-body" id="pinPopupBody">
            <p class="text-muted text-center">Đang tải...</p>
        </div>
    </div>
</div>

<?php endif ?>