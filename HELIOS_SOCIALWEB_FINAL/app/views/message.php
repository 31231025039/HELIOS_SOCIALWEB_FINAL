<?php
// app/views/message.php - Khung chính
$uid = $currentUserId ?? 1;
$convs = $conversations ?? [];
$activeUser = $activeUser ?? null;
$msgs = $messages ?? [];
$pinnedMessages = $pinnedMessages ?? [];
$images = $images ?? [];
?>
<div class="msg-shell container-xl px-2 py-3">
    <div class="msg-layout h-card" id="msgLayout">
        <?php include VIEW_PATH_APP . '/message_conversation.php'; ?>
        <?php include VIEW_PATH_APP . '/message_chat.php'; ?>
    </div>
</div>

<script>
window.MSG_CONFIG = {
    baseUrl: '<?= $baseUrl ?>',
    uid: <?= $uid ?>,
    with: <?= $activeUser ? $activeUser['id'] : 'null' ?>,
    pollInterval: 3000
};
</script>