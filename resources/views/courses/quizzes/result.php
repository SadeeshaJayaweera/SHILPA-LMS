<?php
ob_start();
$isPassed = $attempt->isPassed();
?>
<div class="quiz-result-view" style="max-width: 900px; margin: 0 auto; padding-bottom: 3rem;">
    <!-- Breadcrumbs -->
    <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: var(--text-muted); margin-bottom: 1.5rem;">
        <a href="/courses" style="color: inherit; text-decoration: none;">Courses</a>
        <span>/</span>
        <a href="/courses/<?= (int)$course->C_ID ?>/quizzes/<?= (int)$quiz->id ?>" style="color: inherit; text-decoration: none;"><?= e((string)$quiz->title) ?></a>
        <span>/</span>
        <span style="color: var(--text-base); font-weight: 600;">Assessment Result</span>
    </div>

    <!-- Score Outcome Card -->
    <div class="card" style="padding: 2.5rem; text-align: center; margin-bottom: 2rem; border: 1px solid var(--border-subtle);">
        <div style="width: 80px; height: 80px; border-radius: 50%; background: <?= $isPassed ? '#10b98120' : '#ef444420' ?>; color: <?= $isPassed ? '#10b981' : '#ef4444' ?>; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
            <?= component('icon', ['name' => $isPassed ? 'check-circle' : 'x-circle', 'size' => 48]) ?>
        </div>

        <h1 style="font-size: 2rem; font-weight: 800; margin: 0 0 0.5rem; font-family: var(--font-heading);">
            <?= $isPassed ? 'Assessment Passed!' : 'Assessment Not Passed' ?>
        </h1>
        <p style="color: var(--text-muted); font-size: 1rem; margin: 0 0 2rem;">
            <?= $isPassed ? 'Congratulations! You met the passing standard for this academic evaluation.' : 'Review your incorrect answers below and retake the assessment when ready.' ?>
        </p>

        <!-- Stats Bar -->
        <div style="display: flex; justify-content: center; gap: 2rem; flex-wrap: wrap;">
            <div>
                <div style="font-size: 2rem; font-weight: 800; color: <?= $isPassed ? '#10b981' : 'var(--primary)' ?>;"><?= $attempt->percentage() ?>%</div>
                <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Score Achieved</div>
            </div>
            <div style="width: 1px; background: var(--border-subtle);"></div>
            <div>
                <div style="font-size: 2rem; font-weight: 800; color: var(--text-base);"><?= (int)$attempt->score_achieved ?> / <?= (int)$attempt->max_score ?></div>
                <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Points Total</div>
            </div>
            <div style="width: 1px; background: var(--border-subtle);"></div>
            <div>
                <div style="font-size: 2rem; font-weight: 800; color: var(--text-muted);"><?= (int)$quiz->passing_score_percent ?>%</div>
                <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Passing Benchmark</div>
            </div>
        </div>
    </div>

    <!-- Questions Breakdown -->
    <div class="card" style="padding: 1.75rem; border: 1px solid var(--border-subtle);">
        <h3 style="font-size: 1.25rem; font-weight: 700; margin: 0 0 1.25rem;">Question-by-Question Breakdown</h3>

        <?php $answers = $attempt->answers(); ?>
        <div style="display: flex; flex-direction: column; gap: 1rem;">
            <?php foreach ($questions as $qIdx => $q): ?>
                <?php
                $given = $answers[(int)$q->id] ?? null;
                $correct = $q->isCorrect($given);
                ?>
                <div style="padding: 1rem; border-radius: var(--radius-md); border: 1px solid <?= $correct ? '#10b98140' : '#ef444440' ?>; background: var(--bg-surface-elevated);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                        <span style="font-weight: 700; font-size: 0.875rem;">Question <?= $qIdx + 1 ?></span>
                        <?php if ($correct): ?>
                            <span class="badge badge-success">+<?= (int)$q->points ?> pts</span>
                        <?php else: ?>
                            <span class="badge badge-danger">0 pts</span>
                        <?php endif; ?>
                    </div>
                    <div style="font-size: 0.9375rem; font-weight: 600; margin-bottom: 0.5rem;">
                        <?= nl2br(e((string)$q->question_text)) ?>
                    </div>
                    <div style="font-size: 0.8125rem; color: var(--text-muted);">
                        Your Answer: <strong><?= e((string)(is_array($given) ? json_encode($given) : ($given ?: 'No Answer'))) ?></strong>
                        <?php if (!$correct): ?>
                            • Correct: <strong style="color: #10b981;"><?= e((string)(is_array($q->correctAnswer()) ? json_encode($q->correctAnswer()) : $q->correctAnswer())) ?></strong>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div style="margin-top: 1.5rem; text-align: center;">
            <a href="/courses/<?= (int)$course->C_ID ?>/quizzes/<?= (int)$quiz->id ?>" class="btn btn-secondary">
                Return to Assessment Overview
            </a>
        </div>
    </div>
</div>
<?php
$slot = ob_get_clean();
include dirname(__DIR__, 2) . '/layouts/workspace.php';
