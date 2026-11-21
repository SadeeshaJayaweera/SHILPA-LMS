<?php
ob_start();
$dailyActivity = $data['dailyActivity'] ?? [];
$minutesList = array_merge([1], array_map(fn($d) => (int)$d['minutes'], $dailyActivity));
$maxMinutes = max($minutesList);
?>
<div class="student-analytics-page" style="max-width: 1100px; margin: 0 auto;">
    <!-- Page Header -->
    <div style="margin-bottom: 2rem;">
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
            <h1 style="font-size: 1.875rem; font-weight: 800; margin: 0; font-family: var(--font-heading);">
                My Learning Velocity &amp; Analytics
            </h1>
            <span class="badge badge-primary">Personal Metrics</span>
        </div>
        <p style="color: var(--text-muted); margin: 0; font-size: 0.9375rem;">
            Quantify your study habits, consecutive streaks, and module completion trajectories.
        </p>
    </div>

    <!-- Metric Stat Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
        <div class="card" style="padding: 1.5rem;">
            <div style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Study Streak</div>
            <div style="font-size: 2.25rem; font-weight: 800; color: var(--primary-accent, #6366f1); margin: 0.25rem 0;">
                <?= (int)$data['streakDays'] ?> <span style="font-size: 1rem; font-weight: 600;">Days</span>
            </div>
            <div style="font-size: 0.8125rem; color: #10b981; display: flex; align-items: center; gap: 0.25rem;">
                <?= component('icon', ['name' => 'zap', 'size' => 14]) ?>
                <span>Active Learning Momentum</span>
            </div>
        </div>

        <div class="card" style="padding: 1.5rem;">
            <div style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Total Study Time</div>
            <div style="font-size: 2.25rem; font-weight: 800; color: var(--text-base); margin: 0.25rem 0;">
                <?= (float)$data['totalHours'] ?> <span style="font-size: 1rem; font-weight: 600;">Hours</span>
            </div>
            <div style="font-size: 0.8125rem; color: var(--text-muted);">
                <?= (int)$data['totalMinutes'] ?> minutes recorded
            </div>
        </div>

        <div class="card" style="padding: 1.5rem;">
            <div style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Learning Interactions</div>
            <div style="font-size: 2.25rem; font-weight: 800; color: var(--text-base); margin: 0.25rem 0;">
                <?= (int)$data['totalEvents'] ?>
            </div>
            <div style="font-size: 0.8125rem; color: var(--text-muted);">
                Lectures, quizzes &amp; submissions
            </div>
        </div>
    </div>

    <!-- 14-Day Activity Heat Bar Chart -->
    <div class="card" style="padding: 1.75rem; margin-bottom: 2rem;">
        <h3 style="font-size: 1.125rem; font-weight: 700; margin: 0 0 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
            <?= component('icon', ['name' => 'bar-chart', 'size' => 18]) ?>
            <span>Daily Study Time (Past 14 Days)</span>
        </h3>

        <div style="display: flex; align-items: flex-end; justify-content: space-between; height: 160px; padding-top: 1rem; border-bottom: 1px solid var(--border-subtle); gap: 0.5rem;">
            <?php foreach ($dailyActivity as $day): ?>
                <?php
                $pct = min(100, (int)round(($day['minutes'] / $maxMinutes) * 100));
                ?>
                <div style="flex: 1; display: flex; flex-direction: column; align-items: center; height: 100%; justify-content: flex-end;">
                    <div style="font-size: 0.6875rem; color: var(--text-muted); margin-bottom: 0.25rem;">
                        <?= $day['minutes'] > 0 ? $day['minutes'] . 'm' : '' ?>
                    </div>
                    <div style="width: 100%; max-width: 28px; height: <?= max(4, $pct) ?>%; background: <?= $day['minutes'] > 0 ? 'linear-gradient(180deg, #6366f1, #4f46e5)' : 'var(--border-subtle)' ?>; border-radius: 4px 4px 0 0;" title="<?= $day['label'] ?>: <?= $day['minutes'] ?> minutes (<?= $day['events'] ?> events)"></div>
                </div>
            <?php endforeach; ?>
        </div>

        <div style="display: flex; justify-content: space-between; margin-top: 0.5rem; font-size: 0.6875rem; color: var(--text-muted);">
            <?php foreach ($dailyActivity as $day): ?>
                <div style="flex: 1; text-align: center;"><?= e((string)$day['label']) ?></div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Course Progress Trajectories -->
    <div class="card" style="padding: 1.75rem;">
        <h3 style="font-size: 1.125rem; font-weight: 700; margin: 0 0 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
            <?= component('icon', ['name' => 'book-open', 'size' => 18]) ?>
            <span>Active Course Progression</span>
        </h3>

        <?php if (empty($data['courseBreakdown'])): ?>
            <p style="color: var(--text-muted); font-size: 0.875rem; margin: 0;">No enrolled courses found.</p>
        <?php else: ?>
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <?php foreach ($data['courseBreakdown'] as $item): ?>
                    <?php $c = $item['course']; ?>
                    <div>
                        <div style="display: flex; justify-content: space-between; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.375rem;">
                            <span><?= e((string)$c->C_NAME) ?></span>
                            <span style="color: var(--primary-accent, #6366f1);"><?= (int)$item['progress'] ?>%</span>
                        </div>
                        <div style="width: 100%; height: 8px; border-radius: 4px; background: var(--border-subtle); overflow: hidden;">
                            <div style="width: <?= (int)$item['progress'] ?>%; height: 100%; background: linear-gradient(90deg, #6366f1, #10b981);"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php
$slot = ob_get_clean();
include dirname(__DIR__) . '/layouts/workspace.php';
