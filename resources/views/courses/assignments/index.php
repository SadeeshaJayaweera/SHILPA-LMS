<?php
ob_start();
?>
<div class="assignments-faculty-container" style="max-width: 1200px; margin: 0 auto; padding-bottom: 3rem;">
    <!-- Top Breadcrumbs -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: var(--text-muted);">
            <a href="/faculty/courses" style="color: inherit; text-decoration: none;">Course Management</a>
            <span>/</span>
            <a href="/courses/<?= (int)$course->C_ID ?>/workspace" style="color: inherit; text-decoration: none;"><?= e((string)$course->C_NAME) ?></a>
            <span>/</span>
            <span style="color: var(--text-base); font-weight: 600;">Assignments</span>
        </div>

        <div style="display: flex; gap: 0.75rem;">
            <a href="/courses/<?= (int)$course->C_ID ?>/workspace" class="btn btn-secondary" style="font-size: 0.875rem;">
                <?= component('icon', ['name' => 'eye', 'size' => 16]) ?>
                <span>Student View</span>
            </a>
            <a href="/faculty/courses/<?= (int)$course->C_ID ?>/assignments/create" class="btn btn-primary" style="font-size: 0.875rem;">
                <?= component('icon', ['name' => 'plus', 'size' => 16]) ?>
                <span>Create Assignment</span>
            </a>
        </div>
    </div>

    <!-- Header Banner -->
    <div style="background: linear-gradient(135deg, var(--bg-surface-elevated) 0%, var(--bg-surface) 100%); border: 1px solid var(--border-subtle); border-radius: var(--radius-lg); padding: 1.75rem; margin-bottom: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1.5rem;">
            <div>
                <span class="badge badge-primary" style="margin-bottom: 0.5rem;">Academic Assessments</span>
                <h1 style="font-size: 1.75rem; font-weight: 800; margin: 0 0 0.5rem; font-family: var(--font-heading);">
                    <?= e((string)$course->C_NAME) ?>
                </h1>
                <p style="color: var(--text-muted); font-size: 0.9375rem; margin: 0; max-width: 680px;">
                    Author course projects, practical laboratory deliverables, and deadlines. Track incoming student submissions and grade student performance.
                </p>
            </div>

            <div style="background: var(--bg-surface); padding: 1rem 1.5rem; border-radius: var(--radius-md); border: 1px solid var(--border-subtle); text-align: center;">
                <div style="font-size: 1.75rem; font-weight: 800; color: var(--primary);"><?= count($assignments) ?></div>
                <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; font-weight: 600;">Total Assignments</div>
            </div>
        </div>
    </div>

    <!-- Assignments List -->
    <div class="card" style="padding: 1.5rem; border: 1px solid var(--border-subtle);">
        <?php if (empty($assignments)): ?>
            <div style="text-align: center; padding: 4rem 1.5rem; color: var(--text-muted);">
                <div style="width: 64px; height: 64px; border-radius: 50%; background: var(--bg-surface-elevated); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                    <?= component('icon', ['name' => 'clipboard', 'size' => 32]) ?>
                </div>
                <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem; color: var(--text-base);">No Assignments Configured</h3>
                <p style="font-size: 0.875rem; max-width: 480px; margin: 0 auto 1.5rem;">
                    Create practical coursework or project milestones with clear grading criteria and deadline enforcement.
                </p>
                <a href="/faculty/courses/<?= (int)$course->C_ID ?>/assignments/create" class="btn btn-primary">
                    <?= component('icon', ['name' => 'plus', 'size' => 16]) ?>
                    <span>Create First Assignment</span>
                </a>
            </div>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table class="table" style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--border-subtle); text-align: left;">
                            <th style="padding: 0.75rem; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase;">Assignment</th>
                            <th style="padding: 0.75rem; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase;">Max Points</th>
                            <th style="padding: 0.75rem; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase;">Due Date</th>
                            <th style="padding: 0.75rem; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase;">Submissions</th>
                            <th style="padding: 0.75rem; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase; text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($assignments as $assignment): ?>
                            <?php $subs = $assignment->submissions(); ?>
                            <tr style="border-bottom: 1px solid var(--border-subtle);">
                                <td style="padding: 1rem 0.75rem;">
                                    <div style="font-weight: 700; color: var(--text-base);"><?= e((string)$assignment->title) ?></div>
                                    <?php if (!empty($assignment->description)): ?>
                                        <div style="font-size: 0.8125rem; color: var(--text-muted); max-width: 420px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            <?= e((string)$assignment->description) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 1rem 0.75rem; font-weight: 600; color: var(--primary);">
                                    <?= (int)$assignment->max_score ?> pts
                                </td>
                                <td style="padding: 1rem 0.75rem; color: var(--text-muted); font-size: 0.8125rem;">
                                    <?php if ($assignment->due_at): ?>
                                        <div style="display: flex; align-items: center; gap: 0.35rem;">
                                            <?= component('icon', ['name' => 'clock', 'size' => 14]) ?>
                                            <span><?= date('M j, Y H:i', strtotime((string)$assignment->due_at)) ?></span>
                                        </div>
                                        <?php if ($assignment->isOverdue()): ?>
                                            <span class="badge badge-danger" style="font-size: 0.625rem; margin-top: 0.25rem;">Passed</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span>No Due Date</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 1rem 0.75rem; font-weight: 600;">
                                    <span class="badge badge-info"><?= count($subs) ?> Submitted</span>
                                </td>
                                <td style="padding: 1rem 0.75rem; text-align: right;">
                                    <a href="/courses/<?= (int)$course->C_ID ?>/assignments/<?= (int)$assignment->id ?>" class="btn btn-secondary btn-sm" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                                        <?= component('icon', ['name' => 'eye', 'size' => 12]) ?>
                                        <span>Review</span>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php
$slot = ob_get_clean();
include dirname(__DIR__, 2) . '/layouts/workspace.php';
