<?php
$title = $title ?? null;
$subtitle = $subtitle ?? null;
$footer = $footer ?? null;
$interactive = $interactive ?? false;
$class = $class ?? '';
$headerActions = $headerActions ?? null;

$cardClasses = "card" . ($interactive ? " card-interactive" : "") . ($class ? " $class" : "");
?>
<div class="<?= e($cardClasses) ?>">
    <?php if ($title || $headerActions): ?>
        <div class="card-header">
            <div>
                <?php if ($title): ?>
                    <h3 style="font-size: 1.125rem; font-weight: 700;"><?= e($title) ?></h3>
                <?php endif; ?>
                <?php if ($subtitle): ?>
                    <p style="font-size: 0.8125rem; color: var(--text-muted); margin-top: 0.25rem;"><?= e($subtitle) ?></p>
                <?php endif; ?>
            </div>
            <?php if ($headerActions): ?>
                <div><?= $headerActions ?></div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="card-body">
        <?= $slot ?? '' ?>
    </div>

    <?php if ($footer): ?>
        <div class="card-footer">
            <?= $footer ?>
        </div>
    <?php endif; ?>
</div>
