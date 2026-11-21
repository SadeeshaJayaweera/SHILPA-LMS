<?php
ob_start();
?>
<div class="assignment-student-view" style="max-width: 1100px; margin: 0 auto; padding-bottom: 3rem;">
    <!-- Breadcrumbs -->
    <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: var(--text-muted); margin-bottom: 1.5rem;">
        <a href="/courses" style="color: inherit; text-decoration: none;">Courses</a>
        <span>/</span>
        <a href="/courses/<?= (int)$course->C_ID ?>/workspace" style="color: inherit; text-decoration: none;"><?= e((string)$course->C_NAME) ?></a>
        <span>/</span>
        <span style="color: var(--text-base); font-weight: 600;"><?= e((string)$assignment->title) ?></span>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 360px; gap: 2rem; align-items: start;">
        <!-- Left: Assignment Overview & Instructions -->
        <div>
            <div class="card" style="padding: 2rem; margin-bottom: 2rem; border: 1px solid var(--border-subtle);">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <span class="badge badge-primary">Course Assignment</span>
                        <h1 style="font-size: 1.75rem; font-weight: 800; margin: 0.5rem 0 0.25rem; font-family: var(--font-heading);">
                            <?= e((string)$assignment->title) ?>
                        </h1>
                        <div style="font-size: 0.875rem; color: var(--text-muted);">
                            Maximum Evaluation: <strong><?= (int)$assignment->max_score ?> Points</strong>
                        </div>
                    </div>

                    <div>
                        <?php if ($submission): ?>
                            <?php if ($submission->isGraded()): ?>
                                <span class="badge badge-success" style="font-size: 0.8125rem;">Graded: <?= (int)$submission->score ?> / <?= (int)$assignment->max_score ?></span>
                            <?php elseif ($submission->isLate()): ?>
                                <span class="badge badge-warning" style="font-size: 0.8125rem;">Submitted (Late)</span>
                            <?php else: ?>
                                <span class="badge badge-info" style="font-size: 0.8125rem;">Submitted</span>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="badge badge-secondary" style="font-size: 0.8125rem;">Not Submitted</span>
                        <?php endif; ?>
                    </div>
                </div>

                <div style="border-top: 1px solid var(--border-subtle); padding-top: 1.5rem; font-size: 0.9375rem; line-height: 1.6; color: var(--text-base);">
                    <?= nl2br(e((string)$assignment->description)) ?>
                </div>
            </div>

            <!-- Submission Details / Feedback (if submitted) -->
            <?php if ($submission): ?>
                <div class="card" style="padding: 2rem; border: 1px solid var(--border-subtle); margin-bottom: 2rem;">
                    <h3 style="font-size: 1.25rem; font-weight: 700; margin: 0 0 1rem;">Your Submission</h3>
                    <div style="font-size: 0.8125rem; color: var(--text-muted); margin-bottom: 1rem;">
                        Submitted on: <strong><?= date('M j, Y H:i', strtotime((string)$submission->submitted_at)) ?></strong>
                    </div>

                    <?php if (!empty($submission->submission_text)): ?>
                        <div style="background: var(--bg-surface-elevated); padding: 1rem; border-radius: var(--radius-md); font-size: 0.875rem; margin-bottom: 1rem; white-space: pre-wrap;">
                            <?= e((string)$submission->submission_text) ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($submission->attachment_path)): ?>
                        <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: var(--primary);">
                            <?= component('icon', ['name' => 'file', 'size' => 16]) ?>
                            <span>Attachment uploaded successfully</span>
                        </div>
                    <?php endif; ?>

                    <?php if ($submission->isGraded()): ?>
                        <div style="margin-top: 1.5rem; padding: 1.25rem; background: var(--bg-surface-elevated); border-left: 4px solid var(--primary); border-radius: 4px;">
                            <div style="font-weight: 700; margin-bottom: 0.25rem;">Faculty Feedback:</div>
                            <div style="font-size: 0.875rem; color: var(--text-base);"><?= nl2br(e((string)$submission->feedback)) ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Right: Submission Form & Deadline Card -->
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <!-- Deadline Info -->
            <div class="card" style="padding: 1.5rem; border: 1px solid var(--border-subtle);">
                <div style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 0.5rem;">
                    Submission Timeline
                </div>
                <div style="font-size: 1.125rem; font-weight: 700; margin-bottom: 0.5rem;">
                    <?= $assignment->due_at ? date('F j, Y — H:i', strtotime((string)$assignment->due_at)) : 'No Strict Deadline' ?>
                </div>
                <?php if ($assignment->isOverdue()): ?>
                    <span class="badge badge-danger">Past Deadline (Submissions marked as Late)</span>
                <?php else: ?>
                    <span class="badge badge-success">Accepting Submissions</span>
                <?php endif; ?>
            </div>

            <!-- Student Submission Form -->
            <?php if ($isEnrolled): ?>
                <div class="card" style="padding: 1.5rem; border: 1px solid var(--border-subtle);">
                    <h3 style="font-size: 1.125rem; font-weight: 700; margin: 0 0 1rem;">
                        <?= $submission ? 'Update Submission' : 'Submit Assignment' ?>
                    </h3>
                    <form method="POST" action="/courses/<?= (int)$course->C_ID ?>/assignments/<?= (int)$assignment->id ?>/submit" enctype="multipart/form-data">
                        <div style="margin-bottom: 1rem;">
                            <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.35rem;">Submission Notes / Answer</label>
                            <textarea name="submission_text" rows="4" placeholder="Enter online response or GitHub repository URL..." class="form-input" style="width: 100%;"><?= e((string)($submission->submission_text ?? '')) ?></textarea>
                        </div>
                        <div style="margin-bottom: 1.5rem;">
                            <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.35rem;">Upload Deliverable (PDF, ZIP, Code)</label>
                            <input type="file" name="attachment" class="form-input" style="width: 100%; padding: 0.5rem;">
                        </div>
                        <button type="submit" class="btn btn-primary" style="width: 100%;">
                            <?= component('icon', ['name' => 'send', 'size' => 16]) ?>
                            <span><?= $submission ? 'Re-submit Deliverable' : 'Turn In Assignment' ?></span>
                        </button>
                    </form>
                </div>
            <?php else: ?>
                <div class="card" style="padding: 1.5rem; text-align: center; border: 1px dashed var(--border-subtle); color: var(--text-muted);">
                    <?= component('icon', ['name' => 'lock', 'size' => 32]) ?>
                    <h4 style="margin: 0.5rem 0 0.25rem; font-size: 1rem; color: var(--text-base);">Enrollment Required</h4>
                    <p style="font-size: 0.8125rem; margin-bottom: 1rem;">You must be actively enrolled in this course to submit projects and receive grades.</p>
                    <a href="/courses/<?= (int)$course->C_ID ?>/workspace" class="btn btn-primary btn-sm">Go to Course</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php
$slot = ob_get_clean();
include dirname(__DIR__, 2) . '/layouts/workspace.php';
