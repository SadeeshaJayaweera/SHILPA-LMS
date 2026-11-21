<?php
$type = $type ?? 'info';
$title = $title ?? null;
$class = $class ?? '';
?>
<div class="alert alert-<?= e($type) ?> <?= e($class) ?>" role="alert">
    <div>
        <?php if ($title): ?>
            <strong style="display: block; font-weight: 600; margin-bottom: 0.25rem;"><?= e($title) ?></strong>
        <?php endif; ?>
        <div><?= $slot ?? '' ?></div>
    </div>
</div>
