<?php
ob_start();
?>
<div class="course-analytics-page" style="max-width: 1100px; margin: 0 auto;">
    <!-- Page Header -->
    <div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                <h1 style="font-size: 1.875rem; font-weight: 800; margin: 0; font-family: var(--font-heading);">
                    <?= e((string)$course->C_NAME) ?> Analytics
                </h1>
                <span class="badge badge-primary">Course Insights</span>
            </div>
            <p style="color: var(--text-muted); margin: 0; font-size: 0.9375rem;">
                Engagement velocity, retention funnels, and at-risk student academic indicators.
            </p>
        </div>

        <a href="/faculty/courses" class="btn btn-secondary btn-sm">
            <?= component('icon', ['name' => 'arrow-left', 'size' => 14]) ?>
            <span>Courses Directory</span>
        </a>
    </div>

    <!-- Stat Highlights -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
        <div class="card" style="padding: 1.5rem;">
            <div style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Enrolled Learners</div>
            <div style="font-size: 2.25rem; font-weight: 800; color: var(--text-base); margin: 0.25rem 0;">
                <?= (int)$insights['enrolledCount'] ?>
            </div>
            <div style="font-size: 0.8125rem; color: #10b981;">
                Active: <?= (int)$insights['activeLearnersCount'] ?> in past 14 days
            </div>
        </div>

        <div class="card" style="padding: 1.5rem;">
            <div style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Average Completion</div>
            <div style="font-size: 2.25rem; font-weight: 800; color: var(--primary-accent, #6366f1); margin: 0.25rem 0;">
                <?= (int)$insights['avgCompletionPercent'] ?>%
            </div>
            <div style="font-size: 0.8125rem; color: var(--text-muted);">
                Curriculum syllabus coverage
            </div>
        </div>

        <div class="card" style="padding: 1.5rem;">
            <div style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">At-Risk Learners</div>
            <div style="font-size: 2.25rem; font-weight: 800; color: <?= (int)$insights['atRiskCount'] > 0 ? '#ef4444' : '#10b981' ?>; margin: 0.25rem 0;">
                <?= (int)$insights['atRiskCount'] ?>
            </div>
            <div style="font-size: 0.8125rem; color: var(--text-muted);">
                Progress &lt; 25% or Grade &lt; 50%
            </div>
        </div>
    </div>

    <!-- Module Completion Funnel -->
    <div class="card" style="padding: 1.75rem; margin-bottom: 2rem;">
        <h3 style="font-size: 1.125rem; font-weight: 700; margin: 0 0 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
            <?= component('icon', ['name' => 'layers', 'size' => 18]) ?>
            <span>Module Completion Funnel</span>
        </h3>

        <?php if (empty($insights['moduleFunnel'])): ?>
            <p style="color: var(--text-muted); font-size: 0.875rem;">No curriculum modules published yet.</p>
        <?php else: ?>
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <?php foreach ($insights['moduleFunnel'] as $idx => $mf): ?>
                    <div>
                        <div style="display: flex; justify-content: space-between; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.25rem;">
                            <span>Module <?= $idx + 1 ?>: <?= e((string)$mf['title']) ?> (<?= $mf['lesson_count'] ?> lessons)</span>
                            <span style="color: var(--primary-accent, #6366f1);"><?= (int)$mf['completion_rate'] ?>% completed</span>
                        </div>
                        <div style="width: 100%; height: 10px; border-radius: 5px; background: var(--border-subtle); overflow: hidden;">
                            <div style="width: <?= (int)$mf['completion_rate'] ?>%; height: 100%; background: linear-gradient(90deg, #6366f1, #3b82f6);"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- At-Risk Learner Advisory -->
    <?php if (!empty($insights['atRiskLearners'])): ?>
        <div class="card" style="padding: 1.75rem; border-left: 4px solid #ef4444;">
            <h3 style="font-size: 1.125rem; font-weight: 700; margin: 0 0 1rem; color: #ef4444; display: flex; align-items: center; gap: 0.5rem;">
                <?= component('icon', ['name' => 'alert-triangle', 'size' => 18]) ?>
                <span>At-Risk Student Intervention Required (<?= count($insights['atRiskLearners']) ?>)</span>
            </h3>
            <div style="overflow-x: auto;">
                <table class="table" style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--border-subtle); text-align: left;">
                            <th style="padding: 0.5rem; color: var(--text-muted);">Student</th>
                            <th style="padding: 0.5rem; color: var(--text-muted);">Course Progress</th>
                            <th style="padding: 0.5rem; color: var(--text-muted);">Current Grade</th>
                            <th style="padding: 0.5rem; color: var(--text-muted); text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($insights['atRiskLearners'] as $ar): ?>
                            <tr style="border-bottom: 1px solid var(--border-subtle);">
                                <td style="padding: 0.75rem 0.5rem;">
                                    <div style="font-weight: 700; color: var(--text-base);"><?= e((string)$ar['student']->Full_Name) ?></div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);"><?= e((string)$ar['student']->Email) ?></div>
                                </td>
                                <td style="padding: 0.75rem 0.5rem;">
                                    <span class="badge badge-warning"><?= (int)$ar['progress'] ?>%</span>
                                </td>
                                <td style="padding: 0.75rem 0.5rem;">
                                    <span class="badge badge-secondary"><?= e((string)$ar['letterGrade']) ?> (<?= (int)$ar['gradePercent'] ?>%)</span>
                                </td>
                                <td style="padding: 0.75rem 0.5rem; text-align: right;">
                                    <a href="/messages/<?= (int)$ar['student']->id ?>" class="btn btn-secondary btn-sm" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                                        <?= component('icon', ['name' => 'mail', 'size' => 12]) ?>
                                        <span>Direct Message</span>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php
$slot = ob_get_clean();
include dirname(__DIR__) . '/layouts/workspace.php';
