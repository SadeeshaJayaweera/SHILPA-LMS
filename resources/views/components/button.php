<?php
$variant = $variant ?? 'primary';
$size = $size ?? 'md';
$type = $type ?? 'button';
$class = $class ?? '';
$href = $href ?? null;
$disabled = $disabled ?? false;
$attributes = $attributes ?? '';

$classes = "btn btn-$variant" . ($size !== 'md' ? " btn-$size" : "") . ($class ? " $class" : "");

if ($href) {
    echo '<a href="' . e($href) . '" class="' . e($classes) . '" ' . ($disabled ? 'aria-disabled="true"' : '') . ' ' . $attributes . '>' . ($slot ?? '') . '</a>';
} else {
    echo '<button type="' . e($type) . '" class="' . e($classes) . '" ' . ($disabled ? 'disabled' : '') . ' ' . $attributes . '>' . ($slot ?? '') . '</button>';
}
