<?php
ob_start();
?>
<div class="assignment-create-container" style="max-width: 800px; margin: 0 auto; padding-bottom: 3rem;">
    <!-- Breadcrumbs -->
    <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: var(--text-muted); margin-bottom: 1.5rem;">
        <a href="/faculty/courses" style="color: inherit; text-decoration: none;">Course Management</a>
        <span>/</span>
        <a href="/faculty/courses/<?= (int)$course->C_ID ?>/assignments" style="color: inherit; text-decoration: none;">Assignments</a>
        <span>/</span>
        <span style="color: var(--text-base); font-weight: 600;">Create Assignment</span>
    </div>

    <div class="card" style="padding: 2rem; border: 1px solid var(--border-subtle);">
        <h2 style="font-size: 1.5rem; font-weight: 800; margin: 0 0 0.5rem; font-family: var(--font-heading);">
            Author New Course Assignment
        </h2>
        <p style="color: var(--text-muted); font-size: 0.875rem; margin: 0 0 1.75rem;">
            Configure project specifications, learning objectives, scoring weights, and submission cutoff dates for <?= e((string)$course->C_NAME) ?>.
        </p>

        <form method="POST" action="/faculty/courses/<?= (int)$course->C_ID ?>/assignments">
            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.35rem;">Assignment Title</label>
                <input type="text" name="title" required placeholder="e.g. Distributed Key-Value Store Implementation" class="form-input" style="width: 100%;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.25rem;">
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.35rem;">Maximum Score (Points)</label>
                    <input type="number" name="max_score" value="100" min="1" max="1000" class="form-input" style="width: 100%;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.35rem;">Due Date &amp; Time (UTC)</label>
                    <input type="datetime-local" name="due_at" class="form-input" style="width: 100%;">
                </div>
            </div>

            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.35rem;">Project Instructions &amp; Deliverables</label>
                <textarea name="description" rows="8" placeholder="Detailed requirements, architecture constraints, input/output contracts, and submission guidelines..." class="form-input" style="width: 100%;"></textarea>
            </div>

            <div style="margin-bottom: 1.75rem;">
                <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; cursor: pointer;">
                    <input type="checkbox" name="is_published" value="1" checked>
                    <span>Publish assignment immediately to enrolled students</span>
                </label>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 1rem;">
                <a href="/faculty/courses/<?= (int)$course->C_ID ?>/assignments" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">
                    <?= component('icon', ['name' => 'check', 'size' => 16]) ?>
                    <span>Create &amp; Publish Assignment</span>
                </button>
            </div>
        </form>
    </div>
</div>
<?php
$slot = ob_get_clean();
include dirname(__DIR__, 2) . '/layouts/workspace.php';
