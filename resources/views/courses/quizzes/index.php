<?php
ob_start();
?>
<div class="quizzes-faculty-container" style="max-width: 1200px; margin: 0 auto; padding-bottom: 3rem;">
    <!-- Top Breadcrumbs -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: var(--text-muted);">
            <a href="/faculty/courses" style="color: inherit; text-decoration: none;">Course Management</a>
            <span>/</span>
            <a href="/courses/<?= (int)$course->C_ID ?>/workspace" style="color: inherit; text-decoration: none;"><?= e((string)$course->C_NAME) ?></a>
            <span>/</span>
            <span style="color: var(--text-base); font-weight: 600;">Quizzes &amp; Exams</span>
        </div>

        <div style="display: flex; gap: 0.75rem;">
            <a href="/faculty/courses/<?= (int)$course->C_ID ?>/assignments" class="btn btn-secondary" style="font-size: 0.875rem;">
                <?= component('icon', ['name' => 'clipboard', 'size' => 16]) ?>
                <span>Assignments</span>
            </a>
            <button onclick="document.getElementById('modal-create-quiz').style.display='flex'" class="btn btn-primary" style="font-size: 0.875rem;">
                <?= component('icon', ['name' => 'plus', 'size' => 16]) ?>
                <span>Create Assessment</span>
            </button>
        </div>
    </div>

    <!-- Header Banner -->
    <div style="background: linear-gradient(135deg, var(--bg-surface-elevated) 0%, var(--bg-surface) 100%); border: 1px solid var(--border-subtle); border-radius: var(--radius-lg); padding: 1.75rem; margin-bottom: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1.5rem;">
            <div>
                <span class="badge badge-primary" style="margin-bottom: 0.5rem;">Assessment Engine</span>
                <h1 style="font-size: 1.75rem; font-weight: 800; margin: 0 0 0.5rem; font-family: var(--font-heading);">
                    <?= e((string)$course->C_NAME) ?>
                </h1>
                <p style="color: var(--text-muted); font-size: 0.9375rem; margin: 0; max-width: 680px;">
                    Author timed online examinations, module knowledge checks, and objective question banks with automated instant scoring.
                </p>
            </div>

            <div style="background: var(--bg-surface); padding: 1rem 1.5rem; border-radius: var(--radius-md); border: 1px solid var(--border-subtle); text-align: center;">
                <div style="font-size: 1.75rem; font-weight: 800; color: var(--primary);"><?= count($quizzes) ?></div>
                <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; font-weight: 600;">Active Quizzes</div>
            </div>
        </div>
    </div>

    <!-- Quizzes Table -->
    <div class="card" style="padding: 1.5rem; border: 1px solid var(--border-subtle);">
        <?php if (empty($quizzes)): ?>
            <div style="text-align: center; padding: 4rem 1.5rem; color: var(--text-muted);">
                <div style="width: 64px; height: 64px; border-radius: 50%; background: var(--bg-surface-elevated); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                    <?= component('icon', ['name' => 'help-circle', 'size' => 32]) ?>
                </div>
                <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem; color: var(--text-base);">No Quizzes Configured</h3>
                <p style="font-size: 0.875rem; max-width: 480px; margin: 0 auto 1.5rem;">
                    Create module knowledge checks or term assessments with multiple-choice, true/false, and short answer question pools.
                </p>
                <button onclick="document.getElementById('modal-create-quiz').style.display='flex'" class="btn btn-primary">
                    <?= component('icon', ['name' => 'plus', 'size' => 16]) ?>
                    <span>Create First Quiz</span>
                </button>
            </div>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table class="table" style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--border-subtle); text-align: left;">
                            <th style="padding: 0.75rem; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase;">Assessment</th>
                            <th style="padding: 0.75rem; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase;">Questions</th>
                            <th style="padding: 0.75rem; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase;">Time Limit</th>
                            <th style="padding: 0.75rem; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase;">Passing Mark</th>
                            <th style="padding: 0.75rem; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase; text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($quizzes as $quiz): ?>
                            <?php $qCount = count($quiz->questions()); ?>
                            <tr style="border-bottom: 1px solid var(--border-subtle);">
                                <td style="padding: 1rem 0.75rem;">
                                    <div style="font-weight: 700; color: var(--text-base);"><?= e((string)$quiz->title) ?></div>
                                    <?php if (!empty($quiz->description)): ?>
                                        <div style="font-size: 0.8125rem; color: var(--text-muted);"><?= e((string)$quiz->description) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 1rem 0.75rem; font-weight: 600;">
                                    <?= $qCount ?> items (<?= $quiz->totalPoints() ?> pts)
                                </td>
                                <td style="padding: 1rem 0.75rem; color: var(--text-muted);">
                                    <?= (int)$quiz->time_limit_minutes ?> mins
                                </td>
                                <td style="padding: 1rem 0.75rem;">
                                    <span class="badge badge-success"><?= (int)$quiz->passing_score_percent ?>%</span>
                                </td>
                                <td style="padding: 1rem 0.75rem; text-align: right;">
                                    <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                                        <a href="/faculty/courses/<?= (int)$course->C_ID ?>/quizzes/<?= (int)$quiz->id ?>/builder" class="btn btn-secondary btn-sm" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                                            <?= component('icon', ['name' => 'edit', 'size' => 12]) ?>
                                            <span>Question Bank</span>
                                        </a>
                                        <a href="/courses/<?= (int)$course->C_ID ?>/quizzes/<?= (int)$quiz->id ?>" class="btn btn-primary btn-sm" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                                            <span>Preview</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal: Create Quiz -->
<div id="modal-create-quiz" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; padding: 1rem;">
    <div class="card" style="width: 100%; max-width: 500px; padding: 2rem;">
        <h3 style="margin: 0 0 1rem; font-size: 1.25rem;">Create Course Assessment</h3>
        <form method="POST" action="/faculty/courses/<?= (int)$course->C_ID ?>/quizzes">
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.35rem;">Assessment Title</label>
                <input type="text" name="title" required placeholder="e.g. Midterm Knowledge Examination" class="form-input" style="width: 100%;">
            </div>
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.35rem;">Description</label>
                <textarea name="description" rows="3" placeholder="Scope, rules, and topics covered..." class="form-input" style="width: 100%;"></textarea>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.35rem;">Time Limit (mins)</label>
                    <input type="number" name="time_limit_minutes" value="30" min="5" max="300" class="form-input" style="width: 100%;">
                </div>
                <div>
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.35rem;">Passing Score (%)</label>
                    <input type="number" name="passing_score_percent" value="70" min="1" max="100" class="form-input" style="width: 100%;">
                </div>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                <button type="button" onclick="document.getElementById('modal-create-quiz').style.display='none'" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">Create &amp; Add Questions</button>
            </div>
        </form>
    </div>
</div>
<?php
$slot = ob_get_clean();
include dirname(__DIR__, 2) . '/layouts/workspace.php';
