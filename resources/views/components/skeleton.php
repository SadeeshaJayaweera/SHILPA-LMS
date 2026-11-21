<?php
$width = $width ?? '100%';
$height = $height ?? '1rem';
$radius = $radius ?? 'var(--radius-sm)';
$class = $class ?? '';
?>
<div class="skeleton <?= e($class) ?>" style="width: <?= e($width) ?>; height: <?= e($height) ?>; border-radius: <?= e($radius) ?>;"></div>
