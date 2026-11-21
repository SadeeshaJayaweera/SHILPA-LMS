<?php
ob_start();
?>
<div class="quiz-student-portal" style="max-width: 900px; margin: 0 auto; padding-bottom: 3rem;">
    <!-- Breadcrumbs -->
    <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: var(--text-muted); margin-bottom: 1.5rem;">
        <a href="/courses" style="color: inherit; text-decoration: none;">Courses</a>
        <span>/</span>
        <a href="/courses/<?= (int)$course->C_ID ?>/workspace" style="color: inherit; text-decoration: none;"><?= e((string)$course->C_NAME) ?></a>
        <span>/</span>
        <span style="color: var(--text-base); font-weight: 600;"><?= e((string)$quiz->title) ?></span>
    </div>

    <!-- Quiz Hero Card -->
    <div class="card" style="padding: 2.5rem; margin-bottom: 2rem; border: 1px solid var(--border-subtle); text-align: center;">
        <span class="badge badge-primary" style="margin-bottom: 0.75rem;">Online Academic Assessment</span>
        <h1 style="font-size: 2rem; font-weight: 800; margin: 0 0 0.5rem; font-family: var(--font-heading);">
            <?= e((string)$quiz->title) ?>
        </h1>
        <p style="color: var(--text-muted); font-size: 1rem; max-width: 600px; margin: 0 auto 2rem;">
            <?= e((string)($quiz->description ?: 'Test your knowledge on course architecture, design trade-offs, and practical concepts.')) ?>
        </p>

        <!-- Assessment Constraints Grid -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; max-width: 640px; margin: 0 auto 2rem; text-align: left;">
            <div style="background: var(--bg-surface-elevated); padding: 1rem; border-radius: var(--radius-md); border: 1px solid var(--border-subtle);">
                <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Duration</div>
                <div style="font-size: 1.25rem; font-weight: 800; color: var(--text-base);"><?= (int)$quiz->time_limit_minutes ?> Minutes</div>
            </div>
            <div style="background: var(--bg-surface-elevated); padding: 1rem; border-radius: var(--radius-md); border: 1px solid var(--border-subtle);">
                <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Passing Score</div>
                <div style="font-size: 1.25rem; font-weight: 800; color: #10b981;"><?= (int)$quiz->passing_score_percent ?>%</div>
            </div>
            <div style="background: var(--bg-surface-elevated); padding: 1rem; border-radius: var(--radius-md); border: 1px solid var(--border-subtle);">
                <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Attempts Allowed</div>
                <div style="font-size: 1.25rem; font-weight: 800; color: var(--text-base);"><?= count($attempts) ?> / <?= (int)$quiz->max_attempts ?></div>
            </div>
        </div>

        <?php if ($isEnrolled): ?>
            <?php if (count($attempts) < (int)$quiz->max_attempts): ?>
                <form method="POST" action="/courses/<?= (int)$course->C_ID ?>/quizzes/<?= (int)$quiz->id ?>/start">
                    <button type="submit" class="btn btn-primary" style="font-size: 1rem; padding: 0.75rem 2rem;">
                        <?= component('icon', ['name' => 'play', 'size' => 18]) ?>
                        <span>Begin Assessment Now</span>
                    </button>
                </form>
            <?php else: ?>
                <div class="badge badge-warning" style="font-size: 0.875rem; padding: 0.5rem 1rem;">
                    Maximum Attempts Exhausted
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div style="color: var(--text-muted); font-size: 0.875rem;">
                You must be actively enrolled in this course to take this assessment.
            </div>
        <?php endif; ?>
    </div>

    <!-- Previous Attempts History -->
    <?php if (!empty($attempts)): ?>
        <div class="card" style="padding: 1.5rem; border: 1px solid var(--border-subtle);">
            <h3 style="font-size: 1.125rem; font-weight: 700; margin: 0 0 1rem;">Previous Attempt History</h3>
            <table class="table" style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
                <thead>
                    <tr style="border-bottom: 1px solid var(--border-subtle); text-align: left;">
                        <th style="padding: 0.5rem; color: var(--text-muted);">Attempt</th>
                        <th style="padding: 0.5rem; color: var(--text-muted);">Score</th>
                        <th style="padding: 0.5rem; color: var(--text-muted);">Percentage</th>
                        <th style="padding: 0.5rem; color: var(--text-muted);">Outcome</th>
                        <th style="padding: 0.5rem; color: var(--text-muted); text-align: right;">Review</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($attempts as $att): ?>
                        <tr style="border-bottom: 1px solid var(--border-subtle);">
                            <td style="padding: 0.75rem 0.5rem; font-weight: 600;">Attempt #<?= (int)$att->attempt_number ?></td>
                            <td style="padding: 0.75rem 0.5rem;"><?= (int)$att->score_achieved ?> / <?= (int)$att->max_score ?></td>
                            <td style="padding: 0.75rem 0.5rem; font-weight: 700;"><?= $att->percentage() ?>%</td>
                            <td style="padding: 0.75rem 0.5rem;">
                                <?php if ($att->isPassed()): ?>
                                    <span class="badge badge-success">Passed</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Failed</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 0.75rem 0.5rem; text-align: right;">
                                <a href="/courses/<?= (int)$course->C_ID ?>/quizzes/<?= (int)$quiz->id ?>/result/<?= (int)$att->id ?>" class="btn btn-secondary btn-sm" style="font-size: 0.75rem;">
                                    View Breakdown
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
<?php
$slot = ob_get_clean();
include dirname(__DIR__, 2) . '/layouts/workspace.php';
