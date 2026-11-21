<?php
$label = $label ?? null;
$name = $name ?? '';
$type = $type ?? 'text';
$value = $value ?? '';
$placeholder = $placeholder ?? '';
$hint = $hint ?? null;
$error = $error ?? null;
$required = $required ?? false;
$id = $id ?? ($name ? 'input_' . $name : 'input_' . uniqid());
?>
<div class="form-group">
    <?php if ($label): ?>
        <label for="<?= e($id) ?>" class="form-label">
            <?= e($label) ?>
            <?php if ($required): ?><span style="color: var(--danger-text);">*</span><?php endif; ?>
        </label>
    <?php endif; ?>
    
    <input 
        type="<?= e($type) ?>" 
        id="<?= e($id) ?>" 
        name="<?= e($name) ?>" 
        value="<?= e($value) ?>" 
        placeholder="<?= e($placeholder) ?>" 
        class="form-control <?= $error ? 'input-error' : '' ?>"
        <?= $required ? 'required' : '' ?>
    >

    <?php if ($hint && !$error): ?>
        <span class="form-hint"><?= e($hint) ?></span>
    <?php endif; ?>

    <?php if ($error): ?>
        <span class="form-error"><?= e($error) ?></span>
    <?php endif; ?>
</div>
