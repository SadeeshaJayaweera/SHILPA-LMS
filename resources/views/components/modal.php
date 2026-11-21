<?php
$id = $id ?? 'modal_' . uniqid();
$title = $title ?? 'Modal Title';
$footer = $footer ?? null;
?>
<div id="<?= e($id) ?>" class="modal-backdrop" aria-hidden="true" role="dialog" aria-modal="true">
    <div class="modal-content">
        <div class="card-header">
            <h3 style="font-size: 1.125rem; font-weight: 700;"><?= e($title) ?></h3>
            <button type="button" class="btn btn-ghost btn-sm" onclick="closeModal('<?= e($id) ?>')" aria-label="Close dialog">✕</button>
        </div>
        <div class="card-body">
            <?= $slot ?? '' ?>
        </div>
        <?php if ($footer): ?>
            <div class="card-footer" style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                <?= $footer ?>
            </div>
        <?php endif; ?>
    </div>
</div>
