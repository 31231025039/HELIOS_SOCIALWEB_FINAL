<?php
// app/views/message-chat.php - Chat phải
$baseUrl = $baseUrl ?? '/helios/public/';

if (!isset($activeUser) || !$activeUser): ?>

    <main class="msg-chat d-flex flex-column flex-fill" style="background:var(--helios-bg,#f8fafc);">
        <div class="flex-fill d-flex flex-column align-items-center justify-content-center gap-3 text-center p-4">
            <div class="msg-avatar msg-avatar--lg text-white" style="font-size:36px;">
                <i class="bi bi-chat-heart"></i>
            </div>
            <h5 class="fw-bold mb-0 text-helios-navy">Tin nhắn của bạn</h5>
            <p class="text-muted mb-0">Chọn hội thoại hoặc bắt đầu cuộc trò chuyện mới</p>
            <button class="btn btn-helios-navy rounded-pill px-4 text-white" id="emptyNewBtn">
                <i class="bi bi-pencil-square me-2"></i>Soạn tin nhắn
            </button>
        </div>
    </main>

<?php else:
    $otherId       = $activeUser['id'];
    $otherName     = $activeUser['name'];
    $otherNameHtml = htmlspecialchars($otherName);

    $words = explode(' ', trim($otherName));
    $avatarInitial = mb_strtoupper(mb_substr($words[0], 0, 1) . mb_substr(end($words), 0, 1));

    $avatarInner = !empty($activeUser['avatar'])
        ? '<img src="'.htmlspecialchars($baseUrl . ltrim($activeUser['avatar'], '/'))
          .'" loading="eager" class="w-100 h-100 object-fit-cover">'
        : '<span>'.$avatarInitial.'</span>';

    $msgs           = $messages ?? [];
    $pinnedMessages = $pinnedMessages ?? [];
    $attachments    = $attachments ?? [];
?>

<main class="msg-chat d-flex flex-column flex-fill overflow-hidden" style="background:var(--helios-bg,#f8fafc);">

    <!-- ── Header ── -->
    <div class="d-flex align-items-center gap-3 px-3 py-2 bg-white border-bottom">
        <button class="msg-icon-btn d-lg-none" id="backBtn"><i class="bi bi-arrow-left"></i></button>

        <div class="msg-avatar flex-shrink-0"><?= $avatarInner ?></div>

        <div class="flex-fill" style="min-width:0;">
            <div class="fw-bold text-helios-navy" style="font-size:15px;"><?= $otherNameHtml ?></div>
            <div class="d-flex align-items-center gap-1 text-muted" style="font-size:11px;">
                <i class="bi bi-circle-fill text-success" style="font-size:8px;"></i> Trực tuyến
            </div>
        </div>

        <div class="d-flex gap-1 flex-shrink-0">
            <button class="msg-icon-btn" id="showRightbarBtn" title="Chi tiết"><i class="bi bi-info-circle"></i></button>
            <button class="msg-icon-btn" id="searchMsgBtn"    title="Tìm kiếm"><i class="bi bi-search"></i></button>
            <button class="msg-icon-btn" id="showPinnedBtn"   title="Đã ghim"><i class="bi bi-pin-angle-fill"></i></button>
            <button class="msg-icon-btn text-danger" id="deleteConvBtn" title="Xóa"><i class="bi bi-trash3"></i></button>
        </div>
    </div>

    <!-- ── Thanh tìm kiếm trong chat ── -->
    <div class="bg-white border-bottom px-3 py-2" id="msgChatSearch" style="display:none;">
        <div class="d-flex align-items-center gap-2 rounded-pill px-3 py-1" style="background:var(--helios-bg,#f1f5f9);">
            <i class="bi bi-search text-muted" style="font-size:13px;"></i>
            <input type="text" id="chatSearchInput" class="msg-input-field flex-fill"
                   placeholder="Tìm kiếm tin nhắn..." autocomplete="off">
            <button class="msg-icon-btn" style="width:28px;height:28px;" id="closeChatSearch">
                <i class="bi bi-x-lg" style="font-size:12px;"></i>
            </button>
        </div>
        <div id="chatSearchResults" class="overflow-y-auto mt-2" style="max-height:200px;"></div>
    </div>

    <!-- ── Danh sách tin nhắn ── -->
    <div id="msgList" class="flex-fill overflow-y-auto p-3 d-flex flex-column gap-2"
         data-with="<?= $otherId ?>">

        <?php if (empty($msgs)): ?>
            <div class="flex-fill d-flex flex-column align-items-center justify-content-center gap-3 text-center py-5">
                <div class="msg-avatar msg-avatar--lg"><?= $avatarInner ?></div>
                <p class="fw-bold mb-0 text-helios-navy" style="font-size:18px;"><?= $otherNameHtml ?></p>
                <p class="text-muted mb-0">Hãy gửi tin nhắn đầu tiên 👋</p>
            </div>

        <?php else:
            $prevDate = '';
            foreach ($msgs as $m):
                $date     = date('d/m/Y', strtotime($m['time']));
                $time     = date('H:i', strtotime($m['time']));
                $isMine   = (bool)$m['is_mine'];
                $isPinned = !empty($m['is_pinned']);
        ?>

        <?php if ($date !== $prevDate): $prevDate = $date; ?>
            <div class="text-center my-1">
                <span class="px-3 py-1 rounded-pill text-muted"
                      style="background:var(--helios-bg,#f1f5f9);font-size:11px;">
                    <?= $date === date('d/m/Y') ? 'Hôm nay' : $date ?>
                </span>
            </div>
        <?php endif ?>

        <div class="msg-item <?= $isMine ? 'msg-item--out' : 'msg-item--in' ?>" data-id="<?= $m['id'] ?>">
            <div class="msg-bubble <?= $isMine ? 'msg-bubble--out' : 'msg-bubble--in' ?>">

                <?php if ($isPinned): ?>
                    <span class="msg-pin-badge"><i class="bi bi-pin-angle-fill"></i></span>
                <?php endif ?>

                <?php if (!empty($m['file_path'])):
                    $isImage = preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $m['file_path']);
                    if ($isImage): ?>
                        <a href="<?= $baseUrl . ltrim($m['file_path'], '/') ?>" target="_blank">
                            <img src="<?= $baseUrl . ltrim($m['file_path'], '/') ?>"
                                 class="rounded-2" style="max-width:200px;max-height:150px;">
                        </a>
                    <?php else: ?>
                        <a href="<?= $baseUrl . ltrim($m['file_path'], '/') ?>" target="_blank" class="msg-file">
                            <i class="bi bi-file-earmark"></i> <?= basename($m['file_path']) ?>
                        </a>
                    <?php endif ?>
                <?php else: ?>
                    <?= nl2br(htmlspecialchars($m['content'])) ?>
                <?php endif ?>

                <div class="msg-meta">
                    <span class="msg-time"><?= $time ?></span>
                    <?php if (!$isMine): ?>
                        <button class="msg-pin" data-id="<?= $m['id'] ?>">
                            <i class="bi bi-pin<?= $isPinned ? '-angle-fill' : '' ?>"></i>
                        </button>
                    <?php endif ?>
                    <?php if ($isMine): ?>
                        <i class="bi <?= $m['is_read'] ? 'bi-check2-all' : 'bi-check2' ?>"></i>
                        <button class="msg-delete" data-id="<?= $m['id'] ?>">
                            <i class="bi bi-trash3"></i>
                        </button>
                    <?php endif ?>
                </div>
            </div>
        </div>
        <?php endforeach; endif ?>
    </div>

    <!-- ── Input ── -->
    <div class="d-flex align-items-center gap-2 px-3 py-2 bg-white border-top">
        <input type="file" id="fileInput" style="display:none" accept="image/*,.pdf,.docx,.zip">
        <button class="msg-icon-btn btn btn-helios-navy text-white rounded-circle" id="uploadBtn" title="Đính kèm file">
            <i class="bi bi-paperclip"></i>
        </button>
        <div class="flex-fill rounded-pill px-3 py-2" style="background:var(--helios-bg,#f1f5f9);">
            <textarea id="msgInput" class="msg-input-field w-100" rows="1"
                      placeholder="Nhập tin nhắn..."></textarea>
        </div>
        <button class="msg-send-btn" id="sendBtn" disabled>
            <i class="bi bi-send-fill"></i>
        </button>
    </div>
</main>

<!-- ── Rightbar ── -->
<aside class="msg-rightbar bg-white border-start overflow-y-auto position-absolute top-0 bottom-0 end-0"
       id="msgRightbar" style="width:300px;display:none;z-index:10;box-shadow:-2px 0 8px rgba(0,0,0,.05);">

    <div class="d-flex align-items-center justify-content-between px-3 py-3 border-bottom fw-semibold">
        <span>Chi tiết</span>
        <button class="msg-icon-btn" id="closeRightbar"><i class="bi bi-x-lg"></i></button>
    </div>

    <div class="text-center px-3 py-4 border-bottom">
        <div class="msg-avatar msg-avatar--lg mx-auto mb-3"><?= $avatarInner ?></div>
        <div class="fw-bold mb-1 text-helios-navy" style="font-size:18px;"><?= $otherNameHtml ?></div>
        <div class="text-muted mb-3" style="font-size:12px;">
            <?= htmlspecialchars($activeUser['headline'] ?? 'Chưa có thông tin') ?>
        </div>
        <button class="btn btn-sm btn-outline-danger" id="deleteConvBtn">
            <i class="bi bi-trash3 me-1"></i>Xóa tin nhắn
        </button>
    </div>

    <div class="px-3 py-3 border-bottom">
        <div class="fw-semibold mb-3 text-muted" style="font-size:13px;">
            <i class="bi bi-paperclip me-1"></i>Tệp đính kèm (<?= count($attachments) ?>)
        </div>
        <?php if (empty($attachments)): ?>
            <p class="text-muted small mb-0">Chưa có tệp đính kèm</p>
        <?php else: foreach ($attachments as $file):
            $fileName = basename($file['file_path']);
            $fileSize = file_exists(ROOT_PATH . '/public' . $file['file_path'])
                ? round(filesize(ROOT_PATH . '/public' . $file['file_path']) / 1024, 1) . ' KB' : '? KB';
            $fileTime = date('H:i', strtotime($file['time']));
        ?>
            <div class="msg-rightbar-file py-2" data-msg="<?= $file['id'] ?>">
                <a href="<?= $baseUrl . ltrim($file['file_path'], '/') ?>" target="_blank"
                   class="text-decoration-none text-body d-block">
                    <div class="fw-medium" style="font-size:13px;"><?= htmlspecialchars($fileName) ?></div>
                    <div class="text-muted" style="font-size:11px;"><?= $fileSize ?> · <?= $fileTime ?></div>
                </a>
            </div>
        <?php endforeach; endif ?>
    </div>

    <div class="px-3 py-3">
        <div class="fw-semibold mb-3 text-muted" style="font-size:13px;">
            <i class="bi bi-pin-angle-fill me-1"></i>Tin nhắn đã ghim (<?= count($pinnedMessages) ?>)
        </div>
        <?php if (empty($pinnedMessages)): ?>
            <p class="text-muted small mb-0">Chưa có tin nhắn ghim</p>
        <?php else: foreach ($pinnedMessages as $pin): ?>
            <div class="msg-rightbar-pin rounded-2 p-3 mb-2" data-msg="<?= $pin['id'] ?>" role="button">
                <div style="font-size:13px;line-height:1.4;">
                    <?= htmlspecialchars(mb_substr($pin['content'], 0, 60)) ?>...
                </div>
                <div class="text-muted mt-1" style="font-size:10px;">
                    <?= date('d/m/Y H:i', strtotime($pin['time'])) ?>
                </div>
            </div>
        <?php endforeach; endif ?>
    </div>
</aside>

<!-- ── Popup tin nhắn đã ghim ── -->
<div id="pinPopup" class="msg-popup-overlay" style="display:none;">
    <div class="bg-white rounded-3 overflow-hidden shadow-lg" style="width:400px;max-width:90vw;max-height:80vh;">
        <div class="d-flex align-items-center justify-content-between px-3 py-3 border-bottom fw-semibold">
            <span class="text-helios-navy">
                <i class="bi bi-pin-angle-fill me-2"></i>Tin nhắn đã ghim
            </span>
            <button class="msg-icon-btn" id="closePinPopup"><i class="bi bi-x-lg"></i></button>
        </div>
        <div class="p-3 overflow-y-auto" id="pinPopupBody" style="max-height:calc(80vh - 58px);">
            <p class="text-muted text-center">Đang tải...</p>
        </div>
    </div>
</div>

<?php endif ?>