<?php
ob_start();
?>
<div class="notification-center-page" style="max-width: 900px; margin: 0 auto;">
    <!-- Page Header -->
    <div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                <h1 style="font-size: 1.875rem; font-weight: 800; margin: 0; font-family: var(--font-heading);">
                    Notification Center
                </h1>
                <?php if ($unreadCount > 0): ?>
                    <span class="badge badge-primary"><?= $unreadCount ?> Unread</span>
                <?php else: ?>
                    <span class="badge badge-success">All Caught Up</span>
                <?php endif; ?>
            </div>
            <p style="color: var(--text-muted); margin: 0; font-size: 0.9375rem;">
                Real-time updates on assignments, course broadcasts, grades, and peer collaboration.
            </p>
        </div>

        <div style="display: flex; gap: 0.75rem; align-items: center;">
            <a href="/notifications/settings" class="btn btn-secondary btn-sm">
                <?= component('icon', ['name' => 'settings', 'size' => 14]) ?>
                <span>Preferences</span>
            </a>
            <?php if ($unreadCount > 0): ?>
                <form action="/notifications/read-all" method="POST" style="margin: 0;">
                    <input type="hidden" name="_csrf" value="<?= session()->csrfToken() ?>">
                    <button type="submit" class="btn btn-secondary btn-sm">
                        <?= component('icon', ['name' => 'check-circle', 'size' => 14]) ?>
                        <span>Mark All as Read</span>
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <!-- Filter Toolbar -->
    <div class="card" style="padding: 0.75rem 1rem; margin-bottom: 1.5rem;">
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
            <a href="/notifications?filter=all" class="btn <?= $currentFilter === 'all' ? 'btn-primary' : 'btn-secondary' ?> btn-sm">
                All
            </a>
            <a href="/notifications?filter=unread" class="btn <?= $currentFilter === 'unread' ? 'btn-primary' : 'btn-secondary' ?> btn-sm">
                Unread (<?= $unreadCount ?>)
            </a>
            <a href="/notifications?filter=assignments" class="btn <?= $currentFilter === 'assignments' ? 'btn-primary' : 'btn-secondary' ?> btn-sm">
                Assignments
            </a>
            <a href="/notifications?filter=announcements" class="btn <?= $currentFilter === 'announcements' ? 'btn-primary' : 'btn-secondary' ?> btn-sm">
                Announcements
            </a>
            <a href="/notifications?filter=grades" class="btn <?= $currentFilter === 'grades' ? 'btn-primary' : 'btn-secondary' ?> btn-sm">
                Grades &amp; Results
            </a>
        </div>
    </div>

    <!-- Notifications Stream -->
    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
        <?php if (empty($notifications)): ?>
            <div class="card" style="padding: 3.5rem 1.5rem; text-align: center; color: var(--text-muted);">
                <?= component('icon', ['name' => 'bell-off', 'size' => 44]) ?>
                <div style="font-weight: 700; font-size: 1.125rem; margin-top: 1rem; color: var(--text-base);">No Notifications</div>
                <p style="font-size: 0.875rem; margin-top: 0.25rem;">You have no alerts matching your selected filter.</p>
            </div>
        <?php else: ?>
            <?php foreach ($notifications as $n): ?>
                <?php
                $isUnread = !$n->isRead();
                $iconColor = '#6366f1';
                if (str_starts_with((string)$n->type, 'assignment')) $iconColor = '#f59e0b';
                elseif (str_starts_with((string)$n->type, 'grade') || str_starts_with((string)$n->type, 'quiz')) $iconColor = '#10b981';
                elseif (str_starts_with((string)$n->type, 'announcement')) $iconColor = '#ec4899';
                ?>
                <div class="card" style="padding: 1.25rem; display: flex; align-items: flex-start; gap: 1rem; <?= $isUnread ? 'background: rgba(99, 102, 241, 0.04); border-left: 4px solid var(--primary-accent, #6366f1);' : '' ?>">
                    <div style="width: 40px; height: 40px; border-radius: 50%; background: <?= $iconColor ?>22; color: <?= $iconColor ?>; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 0.125rem;">
                        <?= component('icon', ['name' => $n->icon ?: 'bell', 'size' => 18]) ?>
                    </div>

                    <div style="flex: 1; min-width: 0;">
                        <div style="display: flex; justify-content: space-between; align-items: baseline; gap: 0.75rem; flex-wrap: wrap; margin-bottom: 0.25rem;">
                            <div style="font-weight: 700; font-size: 0.9375rem; color: var(--text-base);">
                                <?= e((string)$n->title) ?>
                            </div>
                            <span style="font-size: 0.75rem; color: var(--text-muted);">
                                <?= date('M d, Y • h:i A', strtotime((string)$n->created_at)) ?>
                            </span>
                        </div>

                        <p style="font-size: 0.875rem; color: var(--text-muted); margin: 0 0 0.75rem; line-height: 1.5;">
                            <?= e((string)$n->message) ?>
                        </p>

                        <div style="display: flex; justify-content: space-between; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                            <div>
                                <?php if (!empty($n->action_url)): ?>
                                    <a href="<?= e((string)$n->action_url) ?>" class="btn btn-secondary btn-sm" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                                        <span>View Details</span>
                                        <?= component('icon', ['name' => 'arrow-right', 'size' => 12]) ?>
                                    </a>
                                <?php endif; ?>
                            </div>

                            <?php if ($isUnread): ?>
                                <form action="/notifications/<?= (int)$n->id ?>/read" method="POST" style="margin: 0;">
                                    <input type="hidden" name="_csrf" value="<?= session()->csrfToken() ?>">
                                    <button type="submit" class="btn btn-secondary btn-sm" style="font-size: 0.6875rem; padding: 0.2rem 0.4rem; color: var(--text-muted);">
                                        Mark as read
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
<?php
$slot = ob_get_clean();
include dirname(__DIR__) . '/layouts/workspace.php';
