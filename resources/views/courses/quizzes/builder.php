<?php
ob_start();
?>
<div class="quiz-builder-container" style="max-width: 1100px; margin: 0 auto; padding-bottom: 3rem;">
    <!-- Breadcrumbs -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: var(--text-muted);">
            <a href="/faculty/courses" style="color: inherit; text-decoration: none;">Courses</a>
            <span>/</span>
            <a href="/faculty/courses/<?= (int)$course->C_ID ?>/quizzes" style="color: inherit; text-decoration: none;">Quizzes</a>
            <span>/</span>
            <span style="color: var(--text-base); font-weight: 600;"><?= e((string)$quiz->title) ?></span>
        </div>

        <a href="/courses/<?= (int)$course->C_ID ?>/quizzes/<?= (int)$quiz->id ?>" class="btn btn-secondary btn-sm">
            <?= component('icon', ['name' => 'eye', 'size' => 14]) ?>
            <span>Preview Exam</span>
        </a>
    </div>

    <!-- Header Banner -->
    <div style="background: linear-gradient(135deg, var(--bg-surface-elevated) 0%, var(--bg-surface) 100%); border: 1px solid var(--border-subtle); border-radius: var(--radius-lg); padding: 1.75rem; margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <span class="badge badge-primary">Question Bank Authoring</span>
            <h1 style="font-size: 1.75rem; font-weight: 800; margin: 0.25rem 0; font-family: var(--font-heading);">
                <?= e((string)$quiz->title) ?>
            </h1>
            <div style="font-size: 0.875rem; color: var(--text-muted);">
                <?= count($questions) ?> Questions • <?= $quiz->totalPoints() ?> Total Points • Passing Grade: <?= (int)$quiz->passing_score_percent ?>%
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 420px; gap: 2rem; align-items: start;">
        <!-- Left: Questions List -->
        <div>
            <h3 style="font-size: 1.25rem; font-weight: 700; margin: 0 0 1rem;">Questions in this Assessment</h3>

            <?php if (empty($questions)): ?>
                <div class="card" style="padding: 3rem 1.5rem; text-align: center; border: 1px dashed var(--border-subtle); color: var(--text-muted);">
                    <?= component('icon', ['name' => 'help-circle', 'size' => 32]) ?>
                    <h4 style="margin: 0.75rem 0 0.25rem; color: var(--text-base);">No Questions Added Yet</h4>
                    <p style="font-size: 0.875rem; margin: 0;">Use the form on the right to add multiple choice, true/false, or short answer questions.</p>
                </div>
            <?php else: ?>
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <?php foreach ($questions as $qIndex => $q): ?>
                        <div class="card" style="padding: 1.5rem; border: 1px solid var(--border-subtle);">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem;">
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <span style="font-weight: 700; color: var(--text-muted);">#<?= $qIndex + 1 ?></span>
                                    <span class="badge badge-secondary" style="font-size: 0.6875rem; text-transform: uppercase;">
                                        <?= str_replace('_', ' ', (string)$q->question_type) ?>
                                    </span>
                                </div>
                                <span class="badge badge-primary"><?= (int)$q->points ?> Points</span>
                            </div>

                            <div style="font-weight: 600; margin-bottom: 1rem; font-size: 0.9375rem;">
                                <?= nl2br(e((string)$q->question_text)) ?>
                            </div>

                            <?php $opts = $q->options(); ?>
                            <?php if (!empty($opts)): ?>
                                <div style="display: flex; flex-direction: column; gap: 0.35rem; font-size: 0.8125rem;">
                                    <?php foreach ($opts as $optKey => $optVal): ?>
                                        <div style="padding: 0.35rem 0.5rem; background: var(--bg-surface-elevated); border-radius: 4px; display: flex; align-items: center; gap: 0.5rem;">
                                            <span style="font-weight: 600; color: var(--text-muted);"><?= e((string)$optKey) ?>)</span>
                                            <span><?= e((string)$optVal) ?></span>
                                            <?php if ((string)$q->correctAnswer() === (string)$optKey): ?>
                                                <span class="badge badge-success" style="font-size: 0.625rem; margin-left: auto;">Correct Answer</span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Right: Add Question Form -->
        <div class="card" style="padding: 1.75rem; border: 1px solid var(--border-subtle); position: sticky; top: 1.5rem;">
            <h3 style="font-size: 1.125rem; font-weight: 700; margin: 0 0 1rem;">Add New Question</h3>

            <form method="POST" action="/faculty/courses/<?= (int)$course->C_ID ?>/quizzes/<?= (int)$quiz->id ?>/questions">
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.35rem;">Question Type</label>
                    <select name="question_type" class="form-input" style="width: 100%;">
                        <option value="single_choice">Multiple Choice (Single Answer)</option>
                        <option value="true_false">True / False</option>
                        <option value="short_answer">Short Answer / Exact Match</option>
                    </select>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.35rem;">Question Prompt</label>
                    <textarea name="question_text" required rows="3" placeholder="What is the time complexity of Raft leader election?" class="form-input" style="width: 100%;"></textarea>
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.35rem;">Points</label>
                    <input type="number" name="points" value="10" min="1" max="100" class="form-input" style="width: 100%;">
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.35rem;">Options (JSON for multiple choice)</label>
                    <input type="text" name="options_json" placeholder='{"A": "O(1)", "B": "O(N)", "C": "O(N^2)"}' class="form-input" style="width: 100%; font-family: var(--font-mono, monospace); font-size: 0.8125rem;">
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.35rem;">Correct Answer (Key or Text)</label>
                    <input type="text" name="correct_answer" required placeholder="e.g. B or True" class="form-input" style="width: 100%;">
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <?= component('icon', ['name' => 'plus', 'size' => 16]) ?>
                    <span>Append Question</span>
                </button>
            </form>
        </div>
    </div>
</div>
<?php
$slot = ob_get_clean();
include dirname(__DIR__, 2) . '/layouts/workspace.php';
