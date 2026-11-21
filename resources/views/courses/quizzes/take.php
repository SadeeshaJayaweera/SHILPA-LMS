<?php
ob_start();
?>
<div class="quiz-take-session" style="max-width: 900px; margin: 0 auto; padding-bottom: 3rem;">
    <!-- Active Exam Header -->
    <div style="background: var(--bg-surface-elevated); border: 1px solid var(--border-subtle); border-radius: var(--radius-lg); padding: 1.25rem 1.75rem; margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; position: sticky; top: 1rem; z-index: 10; box-shadow: var(--shadow-md);">
        <div>
            <div style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">
                Attempt #<?= (int)$attempt->attempt_number ?> • <?= e((string)$course->C_NAME) ?>
            </div>
            <h2 style="font-size: 1.25rem; font-weight: 800; margin: 0;">
                <?= e((string)$quiz->title) ?>
            </h2>
        </div>

        <div style="display: flex; align-items: center; gap: 0.5rem; background: var(--bg-surface); padding: 0.5rem 1rem; border-radius: var(--radius-md); border: 1px solid var(--border-subtle);">
            <?= component('icon', ['name' => 'clock', 'size' => 16]) ?>
            <span style="font-weight: 700; font-family: var(--font-mono, monospace);"><?= (int)$quiz->time_limit_minutes ?>:00</span>
        </div>
    </div>

    <!-- Exam Questions Form -->
    <form method="POST" action="/courses/<?= (int)$course->C_ID ?>/quizzes/<?= (int)$quiz->id ?>/take/<?= (int)$attempt->id ?>" onsubmit="return confirm('Ready to finalize and submit your assessment?');">
        <div style="display: flex; flex-direction: column; gap: 1.5rem; margin-bottom: 2rem;">
            <?php foreach ($questions as $qIndex => $q): ?>
                <div class="card" style="padding: 1.75rem; border: 1px solid var(--border-subtle);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                        <span style="font-weight: 700; color: var(--primary);">Question <?= $qIndex + 1 ?> of <?= count($questions) ?></span>
                        <span style="font-size: 0.75rem; color: var(--text-muted);"><?= (int)$q->points ?> Points</span>
                    </div>

                    <div style="font-size: 1rem; font-weight: 600; line-height: 1.5; margin-bottom: 1.25rem;">
                        <?= nl2br(e((string)$q->question_text)) ?>
                    </div>

                    <?php $type = (string)$q->question_type; ?>
                    <?php $opts = $q->options(); ?>

                    <?php if ($type === 'single_choice' && !empty($opts)): ?>
                        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                            <?php foreach ($opts as $optKey => $optVal): ?>
                                <label style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem; border-radius: var(--radius-md); border: 1px solid var(--border-subtle); background: var(--bg-surface-elevated); cursor: pointer;">
                                    <input type="radio" name="answers[<?= (int)$q->id ?>]" value="<?= e((string)$optKey) ?>" style="accent-color: var(--primary);">
                                    <span><strong><?= e((string)$optKey) ?>)</strong> <?= e((string)$optVal) ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    <?php elseif ($type === 'true_false'): ?>
                        <div style="display: flex; gap: 1rem;">
                            <label style="flex: 1; display: flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.75rem; border-radius: var(--radius-md); border: 1px solid var(--border-subtle); background: var(--bg-surface-elevated); cursor: pointer;">
                                <input type="radio" name="answers[<?= (int)$q->id ?>]" value="True" style="accent-color: var(--primary);">
                                <span style="font-weight: 600;">True</span>
                            </label>
                            <label style="flex: 1; display: flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.75rem; border-radius: var(--radius-md); border: 1px solid var(--border-subtle); background: var(--bg-surface-elevated); cursor: pointer;">
                                <input type="radio" name="answers[<?= (int)$q->id ?>]" value="False" style="accent-color: var(--primary);">
                                <span style="font-weight: 600;">False</span>
                            </label>
                        </div>
                    <?php else: ?>
                        <div>
                            <input type="text" name="answers[<?= (int)$q->id ?>]" placeholder="Enter exact answer..." class="form-input" style="width: 100%;">
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 1rem;">
            <button type="submit" class="btn btn-primary" style="font-size: 1rem; padding: 0.75rem 2.5rem;">
                <?= component('icon', ['name' => 'check-circle', 'size' => 18]) ?>
                <span>Submit Assessment</span>
            </button>
        </div>
    </form>
</div>
<?php
$slot = ob_get_clean();
include dirname(__DIR__, 2) . '/layouts/workspace.php';
