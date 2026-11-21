<?php
ob_start();
?>
<div class="gradebook-faculty-container" style="max-width: 1400px; margin: 0 auto; padding-bottom: 3rem;">
    <!-- Top Breadcrumbs -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: var(--text-muted);">
            <a href="/faculty/courses" style="color: inherit; text-decoration: none;">Course Management</a>
            <span>/</span>
            <a href="/courses/<?= (int)$course->C_ID ?>/workspace" style="color: inherit; text-decoration: none;"><?= e((string)$course->C_NAME) ?></a>
            <span>/</span>
            <span style="color: var(--text-base); font-weight: 600;">Gradebook</span>
        </div>

        <div style="display: flex; gap: 0.75rem;">
            <a href="/faculty/courses/<?= (int)$course->C_ID ?>/assignments" class="btn btn-secondary" style="font-size: 0.875rem;">
                <?= component('icon', ['name' => 'clipboard', 'size' => 16]) ?>
                <span>Assignments</span>
            </a>
            <form method="POST" action="/faculty/courses/<?= (int)$course->C_ID ?>/gradebook/publish" onsubmit="return confirm('Publish all finalized marks to student official transcripts?');" style="margin: 0;">
                <button type="submit" class="btn btn-primary" style="font-size: 0.875rem;">
                    <?= component('icon', ['name' => 'award', 'size' => 16]) ?>
                    <span>Publish Official Grades</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Header Banner -->
    <div style="background: linear-gradient(135deg, var(--bg-surface-elevated) 0%, var(--bg-surface) 100%); border: 1px solid var(--border-subtle); border-radius: var(--radius-lg); padding: 1.75rem; margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <span class="badge badge-primary">Evaluation Matrix</span>
            <h1 style="font-size: 1.75rem; font-weight: 800; margin: 0.25rem 0; font-family: var(--font-heading);">
                <?= e((string)$course->C_NAME) ?> — Gradebook
            </h1>
            <div style="font-size: 0.875rem; color: var(--text-muted);">
                <?= count($studentsData) ?> Enrolled Students • <?= count($assignments) ?> Course Projects • <?= count($quizzes) ?> Online Exams
            </div>
        </div>
    </div>

    <!-- Gradebook Matrix Card -->
    <div class="card" style="padding: 1.5rem; border: 1px solid var(--border-subtle); overflow-x: auto;">
        <?php if (empty($studentsData)): ?>
            <div style="text-align: center; padding: 3rem 1.5rem; color: var(--text-muted);">
                <div style="width: 64px; height: 64px; border-radius: 50%; background: var(--bg-surface-elevated); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                    <?= component('icon', ['name' => 'users', 'size' => 32]) ?>
                </div>
                <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 0.5rem; color: var(--text-base);">No Enrolled Students</h3>
                <p style="font-size: 0.875rem; margin: 0;">Students who enroll in this course will appear in this gradebook matrix automatically.</p>
            </div>
        <?php else: ?>
            <table class="table" style="width: 100%; border-collapse: collapse; font-size: 0.8125rem;">
                <thead>
                    <tr style="border-bottom: 2px solid var(--border-subtle); text-align: left;">
                        <th style="padding: 0.75rem; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase; min-width: 200px;">Student Learner</th>
                        <?php foreach ($assignments as $a): ?>
                            <th style="padding: 0.75rem; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase; text-align: center;">
                                <div><?= e((string)$a->title) ?></div>
                                <div style="font-weight: 400; opacity: 0.7;">(<?= (int)$a->max_score ?> pts)</div>
                            </th>
                        <?php endforeach; ?>
                        <?php foreach ($quizzes as $q): ?>
                            <th style="padding: 0.75rem; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase; text-align: center;">
                                <div><?= e((string)$q->title) ?></div>
                                <div style="font-weight: 400; opacity: 0.7;">(<?= $q->totalPoints() ?> pts)</div>
                            </th>
                        <?php endforeach; ?>
                        <th style="padding: 0.75rem; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase; text-align: center;">Total %</th>
                        <th style="padding: 0.75rem; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase; text-align: center;">Letter</th>
                        <th style="padding: 0.75rem; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase; text-align: center;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($studentsData as $row): ?>
                        <?php
                        $st = $row['student'];
                        $gr = $row['grade'];
                        ?>
                        <tr style="border-bottom: 1px solid var(--border-subtle);">
                            <td style="padding: 1rem 0.75rem;">
                                <div style="font-weight: 700; color: var(--text-base);"><?= e((string)$st->Full_Name) ?></div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);"><?= e((string)$st->Email) ?></div>
                            </td>

                            <!-- Assignments Columns -->
                            <?php foreach ($assignments as $a): ?>
                                <?php $aScore = $row['assignmentScores'][(int)$a->id] ?? null; ?>
                                <td style="padding: 1rem 0.75rem; text-align: center;">
                                    <?php if ($aScore && $aScore['is_graded']): ?>
                                        <button onclick="openGradeModal(<?= (int)$aScore['submission_id'] ?>, '<?= e(addslashes((string)$st->Full_Name)) ?>', '<?= e(addslashes((string)$a->title)) ?>', <?= (int)$aScore['score'] ?>, '<?= e(addslashes((string)($aScore['feedback'] ?? ''))) ?>')" class="btn btn-secondary btn-sm" style="font-size: 0.75rem; padding: 0.2rem 0.5rem; font-weight: 700;">
                                            <?= (int)$aScore['score'] ?> / <?= (int)$a->max_score ?>
                                        </button>
                                    <?php elseif ($aScore): ?>
                                        <button onclick="openGradeModal(<?= (int)$aScore['submission_id'] ?>, '<?= e(addslashes((string)$st->Full_Name)) ?>', '<?= e(addslashes((string)$a->title)) ?>', 0, '')" class="btn btn-primary btn-sm" style="font-size: 0.75rem; padding: 0.2rem 0.5rem;">
                                            Grade
                                        </button>
                                    <?php else: ?>
                                        <span style="color: var(--text-muted);">-</span>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>

                            <!-- Quizzes Columns -->
                            <?php foreach ($quizzes as $q): ?>
                                <?php $qScore = $row['quizScores'][(int)$q->id] ?? null; ?>
                                <td style="padding: 1rem 0.75rem; text-align: center;">
                                    <?php if ($qScore && $qScore['attempts'] > 0): ?>
                                        <span style="font-weight: 700; color: var(--primary);">
                                            <?= (int)$qScore['best_score'] ?> / <?= (int)$qScore['max_score'] ?>
                                        </span>
                                    <?php else: ?>
                                        <span style="color: var(--text-muted);">-</span>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>

                            <!-- Overall Total -->
                            <td style="padding: 1rem 0.75rem; text-align: center; font-weight: 800; font-size: 0.9375rem;">
                                <?= (int)$gr->percentage ?>%
                            </td>
                            <td style="padding: 1rem 0.75rem; text-align: center;">
                                <span class="badge badge-primary" style="font-weight: 800;"><?= e((string)$gr->letter_grade) ?></span>
                            </td>
                            <td style="padding: 1rem 0.75rem; text-align: center;">
                                <?php if ($gr->isPublished()): ?>
                                    <span class="badge badge-success" style="font-size: 0.6875rem;">Published</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary" style="font-size: 0.6875rem;">Draft</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<!-- Modal: Grade Submission -->
<div id="modal-grade-submission" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; padding: 1rem;">
    <div class="card" style="width: 100%; max-width: 500px; padding: 2rem;">
        <h3 style="margin: 0 0 0.5rem; font-size: 1.25rem;">Grade Assignment Submission</h3>
        <div id="grade-modal-subtitle" style="font-size: 0.8125rem; color: var(--text-muted); margin-bottom: 1.25rem;"></div>

        <form id="form-grade-submission" method="POST" action="">
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.35rem;">Score Achieved</label>
                <input type="number" id="grade-modal-score" name="score" required min="0" max="1000" class="form-input" style="width: 100%;">
            </div>
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.35rem;">Instructor Feedback</label>
                <textarea id="grade-modal-feedback" name="feedback" rows="4" placeholder="Feedback notes and rubrics..." class="form-input" style="width: 100%;"></textarea>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                <button type="button" onclick="document.getElementById('modal-grade-submission').style.display='none'" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Grade</button>
            </div>
        </form>
    </div>
</div>

<script>
function openGradeModal(submissionId, studentName, asgnTitle, score, feedback) {
    document.getElementById('grade-modal-subtitle').innerText = studentName + ' — ' + asgnTitle;
    document.getElementById('form-grade-submission').action = '/faculty/submissions/' + submissionId + '/grade';
    document.getElementById('grade-modal-score').value = score;
    document.getElementById('grade-modal-feedback').value = feedback;
    document.getElementById('modal-grade-submission').style.display = 'flex';
}
</script>
<?php
$slot = ob_get_clean();
include dirname(__DIR__, 2) . '/layouts/workspace.php';
