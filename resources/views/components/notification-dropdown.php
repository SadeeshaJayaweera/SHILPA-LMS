<?php
/**
 * Modern Notifications Dropdown Component
 */
use App\Services\NavigationService;

$notifications = NavigationService::getQuickNotifications();
$unreadCount = count(array_filter($notifications, fn($n) => $n['unread']));
?>
<div class="dropdown-wrapper">
    <button 
        type="button" 
        class="header-action-btn" 
        id="notification-toggle-btn"
        data-dropdown-toggle="notifications-dropdown-menu" 
        aria-label="View notifications (<?= $unreadCount ?> unread)"
        aria-expanded="false"
        aria-haspopup="true"
    >
        <?= component('icon', ['name' => 'bell', 'size' => 19]) ?>
        <?php if ($unreadCount > 0): ?>
            <span class="notification-badge-dot" aria-hidden="true"></span>
        <?php endif; ?>
    </button>

    <div class="dropdown-panel notification-dropdown-panel" id="notifications-dropdown-menu" role="menu">
        <div class="notification-header">
            <div style="font-weight: 700; font-size: 0.9375rem;">Notifications</div>
            <?php if ($unreadCount > 0): ?>
                <span class="badge badge-primary"><?= $unreadCount ?> New</span>
            <?php endif; ?>
        </div>

        <div class="notification-list">
            <?php foreach ($notifications as $item): ?>
                <a href="/notifications" class="notification-item <?= $item['unread'] ? 'unread' : '' ?>" role="menuitem">
                    <div style="margin-top: 0.125rem;">
                        <?php if ($item['type'] === 'warning'): ?>
                            <span style="color: var(--warning-text);"><?= component('icon', ['name' => 'clock', 'size' => 16]) ?></span>
                        <?php elseif ($item['type'] === 'success'): ?>
                            <span style="color: var(--success-text);"><?= component('icon', ['name' => 'check', 'size' => 16]) ?></span>
                        <?php else: ?>
                            <span style="color: var(--primary-text);"><?= component('icon', ['name' => 'book', 'size' => 16]) ?></span>
                        <?php endif; ?>
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <div style="display: flex; justify-content: space-between; align-items: baseline; gap: 0.5rem; margin-bottom: 0.25rem;">
                            <div style="font-weight: 600; font-size: 0.8125rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                <?= e($item['title']) ?>
                            </div>
                            <span style="font-size: 0.6875rem; color: var(--text-muted); flex-shrink: 0;"><?= e($item['time']) ?></span>
                        </div>
                        <div style="font-size: 0.75rem; color: var(--text-secondary); line-height: 1.4;">
                            <?= e($item['description']) ?>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>

        <div style="padding: 0.625rem 1.25rem; border-top: 1px solid var(--border-subtle); text-align: center; background-color: var(--bg-surface);">
            <a href="/notifications" style="font-size: 0.75rem; color: var(--primary-text); font-weight: 600; text-decoration: none;">
                View All Notifications →
            </a>
        </div>
    </div>
</div>
