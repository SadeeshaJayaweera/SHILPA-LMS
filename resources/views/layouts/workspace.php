<?php
use App\Services\NavigationService;

$userRole = session()->get('user_role', 'student');
$navSections = NavigationService::getNavigationForUser();
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? config('app.name', 'SHILPA LMS')) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/design-system.css">
    <link rel="stylesheet" href="/css/navigation.css">
</head>
<body class="workspace-shell">

    <!-- Mobile Drawer Overlay Backdrop -->
    <div class="mobile-drawer-overlay" id="mobile-drawer-overlay" aria-hidden="true"></div>

    <!-- Workspace Sidebar Navigation -->
    <aside class="workspace-sidebar" role="navigation" aria-label="Sidebar Navigation">
        <div class="sidebar-header">
            <a href="/workspace" class="sidebar-brand" aria-label="SHILPA LMS Workspace">
                <span class="sidebar-brand-avatar">S</span>
                <span class="sidebar-brand-text">SHILPA <span style="color: var(--primary-text); font-weight: 500;">LMS</span></span>
            </a>
            <button type="button" class="sidebar-toggle-btn" id="sidebar-toggle-btn" aria-label="Toggle sidebar collapse" aria-expanded="true">
                <?= component('icon', ['name' => 'sidebar', 'size' => 18]) ?>
            </button>
            <button type="button" class="sidebar-toggle-btn" id="sidebar-close-btn" style="display: none; @media(max-width: 1024px){display: flex;}" aria-label="Close menu">
                <?= component('icon', ['name' => 'x', 'size' => 18]) ?>
            </button>
        </div>

        <div class="sidebar-content">
            <?php foreach ($navSections as $sectionTitle => $items): ?>
                <div class="sidebar-section">
                    <div class="sidebar-section-title"><?= e($sectionTitle) ?></div>
                    <?php foreach ($items as $item): ?>
                        <a href="<?= e($item['url']) ?>" class="nav-item-link">
                            <span class="nav-item-icon">
                                <?= component('icon', ['name' => $item['icon'], 'size' => 18]) ?>
                            </span>
                            <span class="nav-item-label"><?= e($item['label']) ?></span>
                            <?php if (!empty($item['badge'])): ?>
                                <span class="nav-item-badge"><?= e($item['badge']) ?></span>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="sidebar-footer">
            <div style="font-size: 0.6875rem; color: var(--text-muted); display: flex; align-items: center; justify-content: space-between;">
                <span>v2.4 Modernized</span>
                <span style="display: inline-block; width: 6px; height: 6px; border-radius: 50%; background-color: var(--success-text);" title="All systems operational"></span>
            </div>
        </div>
    </aside>

    <!-- Workspace Main Viewport -->
    <div class="workspace-main">
        <!-- Top Application Header -->
        <header class="workspace-header" role="banner">
            <div class="header-left">
                <button type="button" class="mobile-menu-toggle" id="mobile-menu-toggle" aria-label="Open mobile navigation menu" aria-expanded="false">
                    <?= component('icon', ['name' => 'menu', 'size' => 22]) ?>
                </button>

                <div class="global-search-container">
                    <button type="button" class="global-search-trigger" id="global-search-trigger" aria-label="Open global search">
                        <?= component('icon', ['name' => 'search', 'size' => 16, 'color' => 'var(--text-muted)']) ?>
                        <span>Search courses, modules, assignments...</span>
                        <kbd class="search-shortcut-badge">⌘K</kbd>
                    </button>
                </div>
            </div>

            <div class="header-right">
                <button type="button" class="header-action-btn" onclick="toggleTheme()" aria-label="Toggle visual theme">
                    <span data-theme-icon>🌙</span>
                </button>

                <?= component('notification-dropdown') ?>
                <?= component('user-menu') ?>
            </div>
        </header>

        <!-- Dynamic Breadcrumb Navigation -->
        <?= component('breadcrumbs') ?>

        <!-- Content Viewport -->
        <main class="workspace-content" id="main-content" role="main">
            <?php if ($flashSuccess = session()->get('success')): ?>
                <?= component('alert', ['type' => 'success', 'slot' => e($flashSuccess)]) ?>
            <?php endif; ?>

            <?php if ($flashError = session()->get('error')): ?>
                <?= component('alert', ['type' => 'danger', 'slot' => e($flashError)]) ?>
            <?php endif; ?>

            <?= $slot ?? '' ?>
        </main>
    </div>

    <!-- Mobile Bottom Navigation Bar (< 768px) -->
    <nav class="mobile-bottom-nav" aria-label="Mobile Navigation">
        <a href="/workspace" class="mobile-bottom-tab active">
            <?= component('icon', ['name' => 'home', 'size' => 18]) ?>
            <span>Home</span>
        </a>
        <a href="/courses" class="mobile-bottom-tab">
            <?= component('icon', ['name' => 'book', 'size' => 18]) ?>
            <span>Courses</span>
        </a>
        <a href="/assignments" class="mobile-bottom-tab">
            <?= component('icon', ['name' => 'clipboard', 'size' => 18]) ?>
            <span>Tasks</span>
        </a>
        <a href="/profile" class="mobile-bottom-tab">
            <?= component('icon', ['name' => 'user', 'size' => 18]) ?>
            <span>Profile</span>
        </a>
    </nav>

    <script src="/js/design-system.js"></script>
    <script src="/js/navigation.js"></script>
</body>
</html>
