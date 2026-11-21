<?php
ob_start();
?>
<div class="announcements-page" style="max-width: 1000px; margin: 0 auto;">
    <!-- Header -->
    <div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                <h1 style="font-size: 1.875rem; font-weight: 800; margin: 0; font-family: var(--font-heading);">
                    <?= $course ? e((string)$course->C_NAME) . ' Announcements' : 'Campus Announcements' ?>
                </h1>
                <span class="badge badge-primary">Broadcasts</span>
            </div>
            <p style="color: var(--text-muted); margin: 0; font-size: 0.9375rem;">
                <?= $course ? 'Official course bulletins, schedule modifications, and academic advisories from your instructors.' : 'Institutional notices, semester milestones, and university-wide updates.' ?>
            </p>
        </div>

        <?php if ($course): ?>
            <a href="/courses/<?= (int)$course->C_ID ?>/workspace" class="btn btn-secondary btn-sm">
                <?= component('icon', ['name' => 'arrow-left', 'size' => 14]) ?>
                <span>Back to Workspace</span>
            </a>
        <?php endif; ?>
    </div>

    <!-- Instructor Publishing Form (if faculty/admin and inside a course) -->
    <?php if ($isFacultyOrAdmin && $course): ?>
        <div class="card" style="padding: 1.5rem; margin-bottom: 2rem; border-left: 4px solid var(--primary-accent, #6366f1);">
            <h3 style="font-size: 1.125rem; font-weight: 700; margin: 0 0 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <?= component('icon', ['name' => 'bell', 'size' => 18]) ?>
                <span>Post Faculty Announcement</span>
            </h3>
            <form action="/faculty/courses/<?= (int)$course->C_ID ?>/announcements" method="POST">
                <input type="hidden" name="_csrf" value="<?= session()->csrfToken() ?>">
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.375rem;">Announcement Title</label>
                    <input type="text" name="title" required placeholder="e.g. Schedule Update for Lab Practical..." class="form-input" style="width: 100%; padding: 0.625rem 0.875rem; border-radius: var(--radius-md); border: 1px solid var(--border-subtle); background: var(--bg-surface-elevated); color: var(--text-base);">
                </div>
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.375rem;">Content / Notice</label>
                    <textarea name="content" rows="4" required placeholder="Write your announcement details here..." class="form-input" style="width: 100%; padding: 0.625rem 0.875rem; border-radius: var(--radius-md); border: 1px solid var(--border-subtle); background: var(--bg-surface-elevated); color: var(--text-base);"></textarea>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                    <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; cursor: pointer;">
                        <input type="checkbox" name="is_pinned" value="1">
                        <span>Pin to top of course feed</span>
                    </label>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <?= component('icon', ['name' => 'send', 'size' => 14]) ?>
                        <span>Publish Announcement</span>
                    </button>
                </div>
            </form>
        </div>
    <?php endif; ?>

    <!-- Announcements Feed -->
    <div style="display: flex; flex-direction: column; gap: 1.25rem;">
        <?php if (empty($announcements)): ?>
            <div class="card" style="padding: 3.5rem 1.5rem; text-align: center; color: var(--text-muted);">
                <?= component('icon', ['name' => 'bell-off', 'size' => 42]) ?>
                <div style="font-weight: 700; font-size: 1.125rem; margin-top: 1rem; color: var(--text-base);">No Announcements Posted</div>
                <p style="font-size: 0.875rem; margin-top: 0.25rem; max-width: 450px; margin-left: auto; margin-right: auto;">
                    There are no current advisories or updates broadcast for this module. Important notices will appear here automatically.
                </p>
            </div>
        <?php else: ?>
            <?php foreach ($announcements as $ann): ?>
                <?php $author = $ann->author(); ?>
                <div class="card" style="padding: 1.5rem; <?= $ann->isPinned() ? 'border: 1px solid rgba(99, 102, 241, 0.4); background: rgba(99, 102, 241, 0.03);' : '' ?>">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; margin-bottom: 0.75rem;">
                        <div style="display: flex; align-items: center; gap: 0.625rem; flex-wrap: wrap;">
                            <?php if ($ann->isPinned()): ?>
                                <span class="badge badge-warning" style="display: flex; align-items: center; gap: 0.25rem;">
                                    <?= component('icon', ['name' => 'bookmark', 'size' => 12]) ?>
                                    <span>Pinned Notice</span>
                                </span>
                            <?php endif; ?>
                            <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0; color: var(--text-base);">
                                <?= e((string)$ann->title) ?>
                            </h2>
                        </div>
                        <?php if ($isFacultyOrAdmin): ?>
                            <form action="/faculty/announcements/<?= (int)$ann->id ?>/delete" method="POST" onsubmit="return confirm('Delete this announcement?');" style="margin: 0;">
                                <input type="hidden" name="_csrf" value="<?= session()->csrfToken() ?>">
                                <button type="submit" class="btn btn-secondary btn-sm" style="color: #ef4444; padding: 0.25rem 0.5rem; font-size: 0.75rem;">
                                    <?= component('icon', ['name' => 'trash-2', 'size' => 12]) ?>
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>

                    <div style="font-size: 0.9375rem; color: var(--text-base); line-height: 1.6; margin-bottom: 1.25rem; white-space: pre-line;">
                        <?= e((string)$ann->content) ?>
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.8125rem; color: var(--text-muted); border-top: 1px solid var(--border-subtle); padding-top: 0.75rem; flex-wrap: wrap; gap: 0.5rem;">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <?= component('icon', ['name' => 'user', 'size' => 14]) ?>
                            <span>Posted by <strong><?= e((string)($author->Full_Name ?? 'Faculty Instructor')) ?></strong></span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.375rem;">
                            <?= component('icon', ['name' => 'clock', 'size' => 14]) ?>
                            <span><?= date('M d, Y • h:i A', strtotime((string)$ann->created_at)) ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
<?php
$slot = ob_get_clean();
include dirname(__DIR__, 2) . '/layouts/workspace.php';
