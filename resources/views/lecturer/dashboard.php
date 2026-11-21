<?php
ob_start();
?>
<div class="lecturer-dashboard">
    <!-- Faculty Welcome Header -->
    <div style="background: linear-gradient(135deg, var(--bg-surface-elevated) 0%, var(--bg-surface) 100%); border: 1px solid var(--border-subtle); border-radius: var(--radius-lg); padding: 1.75rem 2rem; margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.5rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.375rem;">
                <h1 style="font-size: 1.875rem; font-weight: 800; margin: 0; font-family: var(--font-heading);">
                    Faculty Workspace • <?= e($lecturerName) ?>
                </h1>
                <span class="badge badge-primary">Lecturer Command Center</span>
            </div>
            <p style="color: var(--text-muted); margin: 0; font-size: 0.9375rem;">
                Department of Computer Science & Software Engineering • Academic Year 2026
            </p>
        </div>

        <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
            <a href="/courses" class="btn btn-secondary btn-sm">
                <?= component('icon', ['name' => 'book', 'size' => 16]) ?>
                <span>Course Catalog</span>
            </a>
            <a href="/courseform.php" class="btn btn-primary btn-sm">
                <?= component('icon', ['name' => 'layers', 'size' => 16]) ?>
                <span>Create New Course</span>
            </a>
        </div>
    </div>

    <!-- Quick Stats Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; margin-bottom: 2.5rem;">
        <div class="card" style="padding: 1.25rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                <span style="font-size: 0.8125rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Assigned Courses</span>
                <span style="color: var(--primary-text);"><?= component('icon', ['name' => 'book', 'size' => 18]) ?></span>
            </div>
            <div style="font-size: 2rem; font-weight: 800; font-family: var(--font-heading); margin-bottom: 0.25rem;">
                <?= (int)$metrics['courses_managed'] ?>
            </div>
            <div style="font-size: 0.75rem; color: var(--success-text); display: flex; align-items: center; gap: 0.25rem;">
                <?= component('icon', ['name' => 'check', 'size' => 12]) ?>
                <span>Curriculum published & active</span>
            </div>
        </div>

        <div class="card" style="padding: 1.25rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                <span style="font-size: 0.8125rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Enrolled Students</span>
                <span style="color: var(--primary-text);"><?= component('icon', ['name' => 'user', 'size' => 18]) ?></span>
            </div>
            <div style="font-size: 2rem; font-weight: 800; font-family: var(--font-heading); margin-bottom: 0.25rem;">
                <?= (int)$metrics['total_enrolled_students'] ?>
            </div>
            <div style="font-size: 0.75rem; color: var(--text-muted);">
                Across all computing modules
            </div>
        </div>

        <div class="card" style="padding: 1.25rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                <span style="font-size: 0.8125rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Submissions to Grade</span>
                <span style="color: var(--warning-text);"><?= component('icon', ['name' => 'clipboard', 'size' => 18]) ?></span>
            </div>
            <div style="font-size: 2rem; font-weight: 800; font-family: var(--font-heading); margin-bottom: 0.25rem;">
                <?= (int)$metrics['pending_grading_count'] ?>
            </div>
            <div style="font-size: 0.75rem; color: var(--warning-text);">
                Action required this week
            </div>
        </div>

        <div class="card" style="padding: 1.25rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                <span style="font-size: 0.8125rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Cohort Average Score</span>
                <span style="color: var(--success-text);"><?= component('icon', ['name' => 'star', 'size' => 18]) ?></span>
            </div>
            <div style="font-size: 2rem; font-weight: 800; font-family: var(--font-heading); margin-bottom: 0.25rem;">
                <?= e($metrics['average_score']) ?>
            </div>
            <div style="font-size: 0.75rem; color: var(--success-text);">
                ↑ +4.2% higher than faculty average
            </div>
        </div>
    </div>

    <!-- Main Two-Column Layout -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; align-items: start;">
        
        <!-- Left / Primary Column -->
        <div style="display: flex; flex-direction: column; gap: 2.5rem; min-width: 0;">
            
            <!-- Pending Grading Workload Queue -->
            <div class="card" style="padding: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border-subtle);">
                    <div>
                        <h2 style="font-size: 1.125rem; margin: 0 0 0.25rem 0;">Pending Grading Queue</h2>
                        <p style="font-size: 0.8125rem; color: var(--text-muted); margin: 0;">Submissions awaiting evaluation, rubric scoring, and faculty feedback.</p>
                    </div>
                    <span class="badge badge-warning"><?= count($gradingQueue) ?> Submissions</span>
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.875rem;">
                    <?php foreach ($gradingQueue as $item): ?>
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.875rem 1rem; background-color: var(--bg-surface-elevated); border: 1px solid var(--border-subtle); border-radius: var(--radius-md); flex-wrap: wrap; gap: 0.75rem;">
                            <div style="display: flex; align-items: center; gap: 0.875rem;">
                                <div class="avatar avatar-sm avatar-primary" style="width: 2.25rem; height: 2.25rem; font-weight: 700; font-size: 0.875rem;">
                                    <?= e($item['student_avatar']) ?>
                                </div>
                                <div>
                                    <div style="font-weight: 600; font-size: 0.875rem; margin-bottom: 0.125rem;">
                                        <?= e($item['student_name']) ?> — <span style="font-weight: 400; color: var(--text-secondary);"><?= e($item['assignment_title']) ?></span>
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);">
                                        <?= e($item['course_name']) ?> • Submitted <?= e($item['time_ago']) ?>
                                    </div>
                                </div>
                            </div>

                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <button type="button" class="btn btn-outline-primary btn-sm">
                                    <?= component('icon', ['name' => 'clipboard', 'size' => 14]) ?>
                                    <span>Grade & Feedback</span>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Managed Courses Grid -->
            <div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                    <div>
                        <h2 style="font-size: 1.25rem; margin: 0 0 0.25rem 0;">Curriculum Management</h2>
                        <p style="font-size: 0.8125rem; color: var(--text-muted); margin: 0;">Oversee lesson modules, lab assignments, and student participation.</p>
                    </div>
                    <a href="/courseform.php" class="btn btn-secondary btn-sm">Add New Module</a>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.25rem;">
                    <?php foreach ($courses as $c): ?>
                        <div class="card" style="padding: 1.25rem; display: flex; flex-direction: column;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                                <span class="badge badge-primary"><?= e($c->C_DONE_BY ?? 'Faculty') ?></span>
                                <span style="font-size: 0.75rem; color: var(--success-text); font-weight: 600;">Active</span>
                            </div>
                            <h3 style="font-size: 1rem; margin-bottom: 0.5rem; line-height: 1.4;">
                                <?= e($c->C_NAME) ?>
                            </h3>
                            <p style="font-size: 0.8125rem; color: var(--text-secondary); margin-bottom: 1.25rem; flex: 1;">
                                <?= e($c->C_CARD_DESCRIPTION ?? 'Structured university syllabus and hands-on exercises.') ?>
                            </p>
                            <div style="display: flex; gap: 0.5rem;">
                                <a href="/updateform.php?updateid=<?= (int)$c->C_ID ?>" class="btn btn-secondary btn-sm" style="flex: 1; text-align: center;">
                                    Edit Course
                                </a>
                                <a href="/courses/<?= (int)$c->C_ID ?>" class="btn btn-outline-primary btn-sm">
                                    Preview
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Grade Distribution SVG Visualization -->
            <div class="card" style="padding: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <div>
                        <h2 style="font-size: 1.125rem; margin: 0 0 0.25rem 0;">Cohort Grade Distribution</h2>
                        <p style="font-size: 0.8125rem; color: var(--text-muted); margin: 0;">Performance spread across midterm and laboratory assessments.</p>
                    </div>
                    <span class="badge badge-success">84.6% Avg Score</span>
                </div>

                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <?php foreach ($gradeDistribution as $item): ?>
                        <div>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.375rem; font-size: 0.8125rem;">
                                <span style="font-weight: 600;"><?= e($item['grade']) ?></span>
                                <span style="font-family: var(--font-mono); color: var(--text-muted); font-weight: 700;">
                                    <?= (int)$item['count'] ?> Students (<?= (int)$item['percentage'] ?>%)
                                </span>
                            </div>
                            <div style="height: 8px; background-color: var(--bg-surface-elevated); border-radius: 4px; overflow: hidden;">
                                <div style="height: 100%; width: <?= (int)$item['percentage'] ?>%; background-color: <?= $item['color'] ?>; border-radius: 4px;"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Right / Sidebar Column -->
        <div style="display: flex; flex-direction: column; gap: 2rem;">
            
            <!-- Upcoming Scheduled Lectures -->
            <div class="card" style="padding: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                    <h2 style="font-size: 1.0625rem; margin: 0;">Upcoming Classes</h2>
                    <span class="badge badge-primary">Schedule</span>
                </div>

                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <?php foreach ($upcomingClasses as $cls): ?>
                        <div style="padding: 0.875rem; background-color: var(--bg-surface-elevated); border-radius: var(--radius-md); border: 1px solid var(--border-subtle);">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.375rem;">
                                <span class="badge <?= $cls['is_live_today'] ? 'badge-danger' : 'badge-secondary' ?>">
                                    <?= $cls['is_live_today'] ? '● Live Today' : 'Upcoming' ?>
                                </span>
                                <span style="font-size: 0.75rem; font-weight: 700; font-family: var(--font-mono); color: var(--primary-text);">
                                    <?= e($cls['course_code']) ?>
                                </span>
                            </div>
                            <div style="font-weight: 700; font-size: 0.875rem; margin-bottom: 0.25rem;">
                                <?= e($cls['title']) ?>
                            </div>
                            <div style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.75rem;">
                                <?= e($cls['room']) ?> • <?= e($cls['scheduled_time']) ?>
                            </div>
                            <button type="button" class="btn btn-secondary btn-sm" style="width: 100%;">
                                Launch Virtual Classroom (<?= (int)$cls['attendees'] ?> Attendees)
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Quick Faculty Actions -->
            <div class="card" style="padding: 1.5rem;">
                <h2 style="font-size: 1.0625rem; margin: 0 0 1rem 0;">Faculty Actions</h2>
                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <a href="/courseform.php" class="btn btn-primary btn-sm" style="justify-content: flex-start; text-align: left;">
                        <?= component('icon', ['name' => 'book', 'size' => 16]) ?>
                        <span>Add New Syllabus Course</span>
                    </a>
                    <a href="/announcements" class="btn btn-secondary btn-sm" style="justify-content: flex-start; text-align: left;">
                        <?= component('icon', ['name' => 'bell', 'size' => 16]) ?>
                        <span>Publish Announcement</span>
                    </a>
                    <a href="/assessments/manage" class="btn btn-secondary btn-sm" style="justify-content: flex-start; text-align: left;">
                        <?= component('icon', ['name' => 'help-circle', 'size' => 16]) ?>
                        <span>Assessment Question Bank</span>
                    </a>
                    <a href="/dev/developers.html" class="btn btn-secondary btn-sm" style="justify-content: flex-start; text-align: left;">
                        <?= component('icon', ['name' => 'user', 'size' => 16]) ?>
                        <span>Faculty Staff Directory</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$slot = ob_get_clean();
include dirname(__DIR__) . '/layouts/workspace.php';
