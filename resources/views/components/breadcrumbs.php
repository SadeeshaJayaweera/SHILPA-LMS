<?php
/**
 * Modern Breadcrumbs Navigation Component
 */
use App\Services\NavigationService;

$crumbs = $items ?? NavigationService::getBreadcrumbs($_SERVER['REQUEST_URI'] ?? '/');
if (empty($crumbs)) return;
?>
<nav class="breadcrumbs-bar" aria-label="Breadcrumb">
    <ol style="display: flex; align-items: center; gap: 0.5rem; list-style: none; margin: 0; padding: 0;">
        <?php foreach ($crumbs as $index => $crumb): ?>
            <?php $isLast = ($index === count($crumbs) - 1); ?>
            <li style="display: flex; align-items: center; gap: 0.5rem;">
                <?php if ($index > 0): ?>
                    <span style="color: var(--text-muted); display: flex; align-items: center;" aria-hidden="true">
                        <?= component('icon', ['name' => 'chevron-right', 'size' => 14]) ?>
                    </span>
                <?php endif; ?>

                <?php if ($isLast): ?>
                    <span class="breadcrumb-current" aria-current="page"><?= e($crumb['label']) ?></span>
                <?php else: ?>
                    <a href="<?= e($crumb['url']) ?>" class="breadcrumb-link"><?= e($crumb['label']) ?></a>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ol>
</nav>
