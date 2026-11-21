<?php
ob_start();
$userRole = (string)session()->get('user_role', 'student');
$isFacultyOrAdmin = in_array(strtolower($userRole), ['l', 'lecturer', 'instructor', 'teacher', 'a', 'admin', 'administrator', 'superadmin'], true);
?>
<div class="discussion-thread-detail" style="max-width: 1000px; margin: 0 auto;">
    <!-- Breadcrumb & Nav -->
    <div style="margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
        <a href="/courses/<?= (int)$course->C_ID ?>/discussions" class="btn btn-secondary btn-sm">
            <?= component('icon', ['name' => 'arrow-left', 'size' => 14]) ?>
            <span>Back to All Topics</span>
        </a>

        <?php if ($isFacultyOrAdmin): ?>
            <div style="display: flex; gap: 0.5rem;">
                <form action="/faculty/discussions/<?= (int)$thread->id ?>/pin" method="POST" style="margin: 0;">
                    <input type="hidden" name="_csrf" value="<?= session()->csrfToken() ?>">
                    <button type="submit" class="btn btn-secondary btn-sm">
                        <?= $thread->isPinned() ? 'Unpin Topic' : 'Pin Topic' ?>
                    </button>
                </form>
                <form action="/faculty/discussions/<?= (int)$thread->id ?>/lock" method="POST" style="margin: 0;">
                    <input type="hidden" name="_csrf" value="<?= session()->csrfToken() ?>">
                    <button type="submit" class="btn btn-secondary btn-sm" style="color: <?= $thread->isLocked() ? '#10b981' : '#ef4444' ?>;">
                        <?= $thread->isLocked() ? 'Unlock Topic' : 'Lock Topic' ?>
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <!-- Main Thread Card -->
    <div class="card" style="padding: 2rem; margin-bottom: 2rem; border-left: 4px solid var(--primary-accent, #6366f1);">
        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem; flex-wrap: wrap;">
            <?php if ($thread->isPinned()): ?>
                <span class="badge badge-warning">Pinned</span>
            <?php endif; ?>
            <?php if ($thread->isLocked()): ?>
                <span class="badge badge-secondary">Locked</span>
            <?php endif; ?>
            <h1 style="font-size: 1.75rem; font-weight: 800; margin: 0; color: var(--text-base); font-family: var(--font-heading);">
                <?= e((string)$thread->title) ?>
            </h1>
        </div>

        <div style="font-size: 1rem; color: var(--text-base); line-height: 1.6; margin: 1.25rem 0; white-space: pre-line;">
            <?= e((string)$thread->body) ?>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.8125rem; color: var(--text-muted); border-top: 1px solid var(--border-subtle); padding-top: 1rem; flex-wrap: wrap; gap: 0.5rem;">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <?= component('icon', ['name' => 'user', 'size' => 16]) ?>
                <span>Author: <strong><?= e((string)($author->Full_Name ?? 'Unknown User')) ?></strong></span>
            </div>
            <div style="display: flex; align-items: center; gap: 0.375rem;">
                <?= component('icon', ['name' => 'clock', 'size' => 14]) ?>
                <span><?= date('M d, Y • h:i A', strtotime((string)$thread->created_at)) ?></span>
            </div>
        </div>
    </div>

    <!-- Replies Section -->
    <div style="margin-bottom: 2.5rem;">
        <h3 style="font-size: 1.25rem; font-weight: 700; margin: 0 0 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
            <?= component('icon', ['name' => 'message-circle', 'size' => 20]) ?>
            <span>Responses &amp; Peer Answers (<?= count($replies) ?>)</span>
        </h3>

        <?php if (empty($replies)): ?>
            <div class="card" style="padding: 2.5rem; text-align: center; color: var(--text-muted); margin-bottom: 1.5rem;">
                <p style="margin: 0; font-size: 0.875rem;">No answers have been posted yet. Contribute your solution below.</p>
            </div>
        <?php else: ?>
            <div style="display: flex; flex-direction: column; gap: 1rem; margin-bottom: 2rem;">
                <?php foreach ($replies as $rep): ?>
                    <?php $repAuthor = $rep->author(); ?>
                    <div class="card" style="padding: 1.5rem; background: var(--bg-surface-elevated);">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; font-size: 0.8125rem; color: var(--text-muted);">
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <?= component('icon', ['name' => 'user', 'size' => 14]) ?>
                                <strong style="color: var(--text-base);"><?= e((string)($repAuthor->Full_Name ?? 'Learner')) ?></strong>
                                <span class="badge badge-secondary" style="font-size: 0.625rem;"><?= ucfirst(e((string)($repAuthor->user_type ?? 'student'))) ?></span>
                            </div>
                            <span><?= date('M d, Y • h:i A', strtotime((string)$rep->created_at)) ?></span>
                        </div>
                        <div style="font-size: 0.9375rem; color: var(--text-base); line-height: 1.5; white-space: pre-line;">
                            <?= e((string)$rep->body) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Post a Reply -->
    <?php if ($thread->isLocked()): ?>
        <div class="alert alert-secondary" style="padding: 1rem 1.25rem; border-radius: var(--radius-md); background: var(--bg-surface-elevated); border: 1px solid var(--border-subtle); display: flex; align-items: center; gap: 0.75rem;">
            <?= component('icon', ['name' => 'lock', 'size' => 18]) ?>
            <span>This thread has been locked by faculty. No further replies can be submitted.</span>
        </div>
    <?php else: ?>
        <div class="card" style="padding: 1.75rem;">
            <h3 style="font-size: 1.125rem; font-weight: 700; margin: 0 0 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <?= component('icon', ['name' => 'corner-down-right', 'size' => 18]) ?>
                <span>Post Your Response</span>
            </h3>
            <form action="/courses/<?= (int)$course->C_ID ?>/discussions/<?= (int)$thread->id ?>/reply" method="POST">
                <input type="hidden" name="_csrf" value="<?= session()->csrfToken() ?>">
                <div style="margin-bottom: 1rem;">
                    <textarea name="body" rows="4" required placeholder="Type your response, explanation, or question..." class="form-input" style="width: 100%; padding: 0.625rem 0.875rem; border-radius: var(--radius-md); border: 1px solid var(--border-subtle); background: var(--bg-surface-elevated); color: var(--text-base);"></textarea>
                </div>
                <div style="display: flex; justify-content: flex-end;">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <?= component('icon', ['name' => 'send', 'size' => 14]) ?>
                        <span>Submit Response</span>
                    </button>
                </div>
            </form>
        </div>
    <?php endif; ?>
</div>
<?php
$slot = ob_get_clean();
include dirname(__DIR__, 2) . '/layouts/workspace.php';
