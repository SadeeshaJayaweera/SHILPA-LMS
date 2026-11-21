<?php
$variant = $variant ?? 'primary';
$class = $class ?? '';
$classes = "badge badge-$variant" . ($class ? " $class" : "");
?>
<span class="<?= e($classes) ?>">
    <?= $slot ?? '' ?>
</span>
