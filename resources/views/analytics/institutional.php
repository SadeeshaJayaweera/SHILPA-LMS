<?php
ob_start();
?>
<div class="institutional-analytics-page" style="max-width: 1100px; margin: 0 auto;">
    <!-- Page Header -->
    <div style="margin-bottom: 2rem;">
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
            <h1 style="font-size: 1.875rem; font-weight: 800; margin: 0; font-family: var(--font-heading);">
                Institutional Learning Analytics
            </h1>
            <span class="badge badge-primary">Executive Oversight</span>
        </div>
        <p style="color: var(--text-muted); margin: 0; font-size: 0.9375rem;">
            University-wide learning engagement, student enrollment distributions, and faculty activity.
        </p>
    </div>

    <!-- Institutional KPI Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
        <div class="card" style="padding: 1.5rem;">
            <div style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Total Students</div>
            <div style="font-size: 2.25rem; font-weight: 800; color: var(--text-base); margin: 0.25rem 0;">
                <?= (int)$data['totalStudents'] ?>
            </div>
            <div style="font-size: 0.8125rem; color: var(--text-muted);">Registered learners</div>
        </div>

        <div class="card" style="padding: 1.5rem;">
            <div style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Faculty Instructors</div>
            <div style="font-size: 2.25rem; font-weight: 800; color: var(--text-base); margin: 0.25rem 0;">
                <?= (int)$data['totalFaculty'] ?>
            </div>
            <div style="font-size: 0.8125rem; color: var(--text-muted);">Academic staff</div>
        </div>

        <div class="card" style="padding: 1.5rem;">
            <div style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Degree Courses</div>
            <div style="font-size: 2.25rem; font-weight: 800; color: var(--text-base); margin: 0.25rem 0;">
                <?= (int)$data['totalCourses'] ?>
            </div>
            <div style="font-size: 0.8125rem; color: var(--text-muted);">Active curriculum</div>
        </div>

        <div class="card" style="padding: 1.5rem;">
            <div style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Total Study Hours</div>
            <div style="font-size: 2.25rem; font-weight: 800; color: var(--primary-accent, #6366f1); margin: 0.25rem 0;">
                <?= (float)$data['totalLearningHours'] ?>
            </div>
            <div style="font-size: 0.8125rem; color: #10b981;">Telemetry validated</div>
        </div>
    </div>

    <!-- Top Performing Courses Table -->
    <div class="card" style="padding: 1.75rem;">
        <h3 style="font-size: 1.125rem; font-weight: 700; margin: 0 0 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
            <?= component('icon', ['name' => 'award', 'size' => 18]) ?>
            <span>High-Enrollment Flagship Courses</span>
        </h3>

        <?php if (empty($data['topCourses'])): ?>
            <p style="color: var(--text-muted); font-size: 0.875rem;">No course enrollments recorded.</p>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table class="table" style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--border-subtle); text-align: left;">
                            <th style="padding: 0.5rem; color: var(--text-muted);">Course Name</th>
                            <th style="padding: 0.5rem; color: var(--text-muted);">Instructor</th>
                            <th style="padding: 0.5rem; color: var(--text-muted);">Enrollments</th>
                            <th style="padding: 0.5rem; color: var(--text-muted); text-align: right;">Analytics</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['topCourses'] as $tc): ?>
                            <tr style="border-bottom: 1px solid var(--border-subtle);">
                                <td style="padding: 0.75rem 0.5rem; font-weight: 700; color: var(--text-base);">
                                    <?= e((string)$tc['course']->C_NAME) ?>
                                </td>
                                <td style="padding: 0.75rem 0.5rem; color: var(--text-muted);">
                                    <?= e((string)($tc['course']->C_DONE_BY ?: 'Faculty')) ?>
                                </td>
                                <td style="padding: 0.75rem 0.5rem;">
                                    <span class="badge badge-primary"><?= (int)$tc['enrollments'] ?> Students</span>
                                </td>
                                <td style="padding: 0.75rem 0.5rem; text-align: right;">
                                    <a href="/faculty/courses/<?= (int)$tc['course']->C_ID ?>/analytics" class="btn btn-secondary btn-sm" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                                        View Funnel
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
include dirname(__DIR__) . '/layouts/workspace.php';
