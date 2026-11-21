<?php
ob_start();
?>
<div class="workspace-overview">
    <!-- Hero / Welcome Header -->
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem; margin-bottom: 2rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                <h1 style="font-size: 1.875rem; margin: 0;">Welcome, <?= e($userName) ?></h1>
                <?php if (in_array($userRole, ['a', 'admin', 'administrator', 'superadmin'], true)): ?>
                    <span class="badge badge-danger">Administrator Console</span>
                <?php elseif (in_array($userRole, ['l', 'lecturer', 'instructor'], true)): ?>
                    <span class="badge badge-primary">Lecturer Workspace</span>
                <?php else: ?>
                    <span class="badge badge-secondary">Student Workspace</span>
                <?php endif; ?>
            </div>
            <p style="color: var(--text-muted); margin: 0; font-size: 0.9375rem;">
                You have 2 pending assignments and 1 unread announcement across your enrolled modules.
            </p>
        </div>

        <div style="display: flex; gap: 0.75rem;">
            <a href="/courses" class="btn btn-secondary btn-sm">
                <?= component('icon', ['name' => 'book', 'size' => 16]) ?>
                <span>Browse Courses</span>
            </a>
            <a href="/assignments" class="btn btn-primary btn-sm">
                <?= component('icon', ['name' => 'clipboard', 'size' => 16]) ?>
                <span>View Tasks</span>
            </a>
        </div>
    </div>

    <!-- Quick Stats Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; margin-bottom: 2.5rem;">
        <div class="card" style="padding: 1.25rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                <span style="font-size: 0.8125rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Active Courses</span>
                <span style="color: var(--primary-text);"><?= component('icon', ['name' => 'book', 'size' => 18]) ?></span>
            </div>
            <div style="font-size: 1.75rem; font-weight: 800; font-family: var(--font-heading); margin-bottom: 0.25rem;">
                <?= count($courses) ?>
            </div>
            <div style="font-size: 0.75rem; color: var(--success-text); display: flex; align-items: center; gap: 0.25rem;">
                <?= component('icon', ['name' => 'check', 'size' => 12]) ?>
                <span>2 modules in progress</span>
            </div>
        </div>

        <div class="card" style="padding: 1.25rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                <span style="font-size: 0.8125rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Completion Rate</span>
                <span style="color: var(--success-text);"><?= component('icon', ['name' => 'award', 'size' => 18]) ?></span>
            </div>
            <div style="font-size: 1.75rem; font-weight: 800; font-family: var(--font-heading); margin-bottom: 0.25rem;">
                78%
            </div>
            <div style="font-size: 0.75rem; color: var(--text-muted);">
                +12% from last week
            </div>
        </div>

        <div class="card" style="padding: 1.25rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                <span style="font-size: 0.8125rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Pending Deadlines</span>
                <span style="color: var(--warning-text);"><?= component('icon', ['name' => 'clock', 'size' => 18]) ?></span>
            </div>
            <div style="font-size: 1.75rem; font-weight: 800; font-family: var(--font-heading); margin-bottom: 0.25rem;">
                2 Due
            </div>
            <div style="font-size: 0.75rem; color: var(--warning-text);">
                Next: Tomorrow at 23:59
            </div>
        </div>

        <div class="card" style="padding: 1.25rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                <span style="font-size: 0.8125rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Average Grade</span>
                <span style="color: var(--primary-text);"><?= component('icon', ['name' => 'star', 'size' => 18]) ?></span>
            </div>
            <div style="font-size: 1.75rem; font-weight: 800; font-family: var(--font-heading); margin-bottom: 0.25rem;">
                3.82 / 4.0
            </div>
            <div style="font-size: 0.75rem; color: var(--success-text);">
                First Class Honors
            </div>
        </div>
    </div>

    <!-- Active Courses Grid -->
    <div style="margin-bottom: 2.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
            <h2 style="font-size: 1.25rem; margin: 0;">Continue Learning</h2>
            <a href="/courses" style="font-size: 0.875rem; color: var(--primary-text); text-decoration: none; font-weight: 600;">
                All Courses →
            </a>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem;">
            <?php foreach ($courses as $c): ?>
                <?php
                $courseName = is_object($c) ? ($c->C_NAME ?? '') : ($c['C_NAME'] ?? '');
                $courseBy = is_object($c) ? ($c->C_DONE_BY ?? 'Faculty') : ($c['C_DONE_BY'] ?? 'Faculty');
                $courseDesc = is_object($c) ? ($c->C_CARD_DESCRIPTION ?? '') : ($c['C_CARD_DESCRIPTION'] ?? '');
                $courseId = is_object($c) ? (int)($c->C_ID ?? 0) : (int)($c['C_ID'] ?? 0);
                ?>
                <div class="card" style="display: flex; flex-direction: column; overflow: hidden;">
                    <div style="height: 120px; background: linear-gradient(135deg, var(--bg-surface-elevated) 0%, var(--bg-surface) 100%); display: flex; align-items: center; justify-content: center; border-bottom: 1px solid var(--border-subtle);">
                        <span class="badge badge-primary"><?= e($courseBy) ?></span>
                    </div>
                    <div style="padding: 1.25rem; flex: 1; display: flex; flex-direction: column;">
                        <h3 style="font-size: 1rem; margin-bottom: 0.5rem;"><?= e($courseName) ?></h3>
                        <p style="font-size: 0.8125rem; color: var(--text-secondary); margin-bottom: 1rem; line-height: 1.5; flex: 1;">
                            <?= e($courseDesc ?: 'Structured university curriculum with hands-on practical exercises.') ?>
                        </p>
                        
                        <!-- Progress bar -->
                        <div style="margin-bottom: 1rem;">
                            <div style="display: flex; justify-content: space-between; font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.375rem;">
                                <span>Module Progress</span>
                                <span>65%</span>
                            </div>
                            <div style="height: 6px; background-color: var(--bg-surface-elevated); border-radius: 3px; overflow: hidden;">
                                <div style="height: 100%; width: 65%; background-color: var(--primary-default); border-radius: 3px;"></div>
                            </div>
                        </div>

                        <a href="/courses/<?= $courseId ?>" class="btn btn-secondary btn-sm" style="width: 100%;">
                            Resume Module
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php
$slot = ob_get_clean();
include dirname(__DIR__) . '/layouts/workspace.php';
