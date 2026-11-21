<?php
ob_start();
?>
<div class="discussions-page" style="max-width: 1100px; margin: 0 auto;">
    <!-- Header -->
    <div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                <h1 style="font-size: 1.875rem; font-weight: 800; margin: 0; font-family: var(--font-heading);">
                    <?= e((string)$course->C_NAME) ?> Discussions
                </h1>
                <span class="badge badge-primary">Q&amp;A Forum</span>
            </div>
            <p style="color: var(--text-muted); margin: 0; font-size: 0.9375rem;">
                Collaborate with peers, ask questions about course assignments and lectures, and share academic insights.
            </p>
        </div>

        <div style="display: flex; gap: 0.75rem;">
            <a href="/courses/<?= (int)$course->C_ID ?>/workspace" class="btn btn-secondary btn-sm">
                <?= component('icon', ['name' => 'arrow-left', 'size' => 14]) ?>
                <span>Course Workspace</span>
            </a>
            <a href="#new-thread-form" class="btn btn-primary btn-sm">
                <?= component('icon', ['name' => 'plus', 'size' => 14]) ?>
                <span>New Topic</span>
            </a>
        </div>
    </div>

    <!-- Forum Threads List -->
    <div style="display: flex; flex-direction: column; gap: 1rem; margin-bottom: 2.5rem;">
        <?php if (empty($threads)): ?>
            <div class="card" style="padding: 3.5rem 1.5rem; text-align: center; color: var(--text-muted);">
                <?= component('icon', ['name' => 'message-square', 'size' => 42]) ?>
                <div style="font-weight: 700; font-size: 1.125rem; margin-top: 1rem; color: var(--text-base);">No Discussion Topics Yet</div>
                <p style="font-size: 0.875rem; margin-top: 0.25rem; max-width: 450px; margin-left: auto; margin-right: auto;">
                    Be the first learner or instructor to start a conversation, propose a research question, or ask for guidance.
                </p>
            </div>
        <?php else: ?>
            <?php foreach ($threads as $th): ?>
                <?php $author = $th->author(); ?>
                <a href="/courses/<?= (int)$course->C_ID ?>/discussions/<?= (int)$th->id ?>" class="card" style="padding: 1.25rem 1.5rem; text-decoration: none; color: inherit; display: block; transition: transform 0.15s ease, border-color 0.15s ease; <?= $th->isPinned() ? 'border-left: 4px solid var(--primary-accent, #6366f1);' : '' ?>">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; flex-wrap: wrap;">
                        <div style="flex: 1; min-width: 280px;">
                            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.375rem; flex-wrap: wrap;">
                                <?php if ($th->isPinned()): ?>
                                    <span class="badge badge-warning" style="font-size: 0.6875rem;">Pinned</span>
                                <?php endif; ?>
                                <?php if ($th->isLocked()): ?>
                                    <span class="badge badge-secondary" style="font-size: 0.6875rem;">Locked</span>
                                <?php endif; ?>
                                <h3 style="font-size: 1.125rem; font-weight: 700; margin: 0; color: var(--text-base);">
                                    <?= e((string)$th->title) ?>
                                </h3>
                            </div>
                            <p style="color: var(--text-muted); font-size: 0.875rem; margin: 0 0 0.75rem; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                <?= e((string)$th->body) ?>
                            </p>
                            <div style="display: flex; align-items: center; gap: 1rem; font-size: 0.75rem; color: var(--text-muted); flex-wrap: wrap;">
                                <span>Author: <strong><?= e((string)($author->Full_Name ?? 'Anonymous')) ?></strong></span>
                                <span>•</span>
                                <span><?= date('M d, Y', strtotime((string)$th->created_at)) ?></span>
                            </div>
                        </div>

                        <div style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem 0.875rem; border-radius: var(--radius-md); background: var(--bg-surface-elevated); font-size: 0.875rem;">
                            <?= component('icon', ['name' => 'message-circle', 'size' => 16]) ?>
                            <span style="font-weight: 700; color: var(--text-base);"><?= (int)$th->replies_count ?></span>
                            <span style="color: var(--text-muted); font-size: 0.75rem;">replies</span>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Start New Thread Form -->
    <div id="new-thread-form" class="card" style="padding: 1.75rem;">
        <h3 style="font-size: 1.25rem; font-weight: 700; margin: 0 0 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
            <?= component('icon', ['name' => 'edit-3', 'size' => 20]) ?>
            <span>Start a Discussion Topic</span>
        </h3>
        <form action="/courses/<?= (int)$course->C_ID ?>/discussions" method="POST">
            <input type="hidden" name="_csrf" value="<?= session()->csrfToken() ?>">
            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.375rem;">Topic Title</label>
                <input type="text" name="title" required placeholder="What is your question or discussion subject?" class="form-input" style="width: 100%; padding: 0.625rem 0.875rem; border-radius: var(--radius-md); border: 1px solid var(--border-subtle); background: var(--bg-surface-elevated); color: var(--text-base);">
            </div>
            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.375rem;">Detailed Inquiry / Discussion Body</label>
                <textarea name="body" rows="5" required placeholder="Provide code snippets, concept questions, or assignment context..." class="form-input" style="width: 100%; padding: 0.625rem 0.875rem; border-radius: var(--radius-md); border: 1px solid var(--border-subtle); background: var(--bg-surface-elevated); color: var(--text-base);"></textarea>
            </div>
            <div style="display: flex; justify-content: flex-end;">
                <button type="submit" class="btn btn-primary">
                    <?= component('icon', ['name' => 'send', 'size' => 16]) ?>
                    <span>Create Discussion Thread</span>
                </button>
            </div>
        </form>
    </div>
</div>
<?php
$slot = ob_get_clean();
include dirname(__DIR__, 2) . '/layouts/workspace.php';
