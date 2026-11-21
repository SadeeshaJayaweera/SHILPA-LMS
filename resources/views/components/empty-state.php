<?php
$title = $title ?? 'No items found';
$description = $description ?? 'There are currently no records to display.';
$icon = $icon ?? '📁';
$action = $action ?? null;
?>
<div class="empty-state">
    <div class="empty-state-icon"><?= $icon ?></div>
    <h4 class="empty-state-title"><?= e($title) ?></h4>
    <p class="empty-state-desc"><?= e($description) ?></p>
    <?php if ($action): ?>
        <div><?= $action ?></div>
    <?php endif; ?>
</div>
