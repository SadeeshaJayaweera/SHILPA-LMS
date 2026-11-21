<?php
ob_start();
?>
<div class="student-dashboard">
    <!-- Top Welcome Banner -->
    <div style="background: linear-gradient(135deg, var(--bg-surface-elevated) 0%, var(--bg-surface) 100%); border: 1px solid var(--border-subtle); border-radius: var(--radius-lg); padding: 1.75rem 2rem; margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.5rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.375rem;">
                <h1 style="font-size: 1.875rem; font-weight: 800; margin: 0; font-family: var(--font-heading);">
                    Good day, <?= e($userName) ?>!
                </h1>
                <span class="badge badge-primary">Student Portal</span>
            </div>
            <p style="color: var(--text-muted); margin: 0; font-size: 0.9375rem;">
                Faculty of Computing • Software Engineering Track • NSBM Green University
            </p>
        </div>

        <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
            <a href="/courses" class="btn btn-secondary btn-sm">
                <?= component('icon', ['name' => 'book', 'size' => 16]) ?>
                <span>Browse Catalog</span>
            </a>
            <?php if (!empty($enrolledCourses)): ?>
                <a href="/courses/<?= (int)($enrolledCourses[0]['course']->C_ID ?? 1) ?>" class="btn btn-primary btn-sm">
                    <?= component('icon', ['name' => 'play', 'size' => 16]) ?>
                    <span>Resume Last Lecture</span>
                </a>
            <?php else: ?>
                <a href="/courses" class="btn btn-primary btn-sm">
                    <?= component('icon', ['name' => 'graduation-cap', 'size' => 16]) ?>
                    <span>Enroll in Your First Course</span>
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Quick Stats Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; margin-bottom: 2.5rem;">
        <div class="card" style="padding: 1.25rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                <span style="font-size: 0.8125rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Enrolled Courses</span>
                <span style="color: var(--primary-text);"><?= component('icon', ['name' => 'book', 'size' => 18]) ?></span>
            </div>
            <div style="font-size: 2rem; font-weight: 800; font-family: var(--font-heading); margin-bottom: 0.25rem;">
                <?= (int)$metrics['enrolled_courses'] ?>
            </div>
            <div style="font-size: 0.75rem; color: var(--success-text); display: flex; align-items: center; gap: 0.25rem;">
                <?= component('icon', ['name' => 'check', 'size' => 12]) ?>
                <span>Active semester enrollment</span>
            </div>
        </div>

        <div class="card" style="padding: 1.25rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                <span style="font-size: 0.8125rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Curriculum Completion</span>
                <span style="color: var(--success-text);"><?= component('icon', ['name' => 'award', 'size' => 18]) ?></span>
            </div>
            <div style="font-size: 2rem; font-weight: 800; font-family: var(--font-heading); margin-bottom: 0.25rem;">
                <?= (int)$metrics['completion_percent'] ?>%
            </div>
            <div style="font-size: 0.75rem; color: var(--text-muted);">
                +14% completed this month
            </div>
        </div>

        <div class="card" style="padding: 1.25rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                <span style="font-size: 0.8125rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Study Time This Week</span>
                <span style="color: var(--warning-text);"><?= component('icon', ['name' => 'clock', 'size' => 18]) ?></span>
            </div>
            <div style="font-size: 2rem; font-weight: 800; font-family: var(--font-heading); margin-bottom: 0.25rem;">
                <?= $metrics['study_hours_week'] ?> hrs
            </div>
            <div style="font-size: 0.75rem; color: var(--success-text);">
                ↑ +3.2 hrs above peer average
            </div>
        </div>

        <div class="card" style="padding: 1.25rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                <span style="font-size: 0.8125rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Academic Standing</span>
                <span style="color: var(--primary-text);"><?= component('icon', ['name' => 'star', 'size' => 18]) ?></span>
            </div>
            <div style="font-size: 2rem; font-weight: 800; font-family: var(--font-heading); margin-bottom: 0.25rem;">
                <?= e($metrics['gpa']) ?>
            </div>
            <div style="font-size: 0.75rem; color: var(--success-text);">
                First Class Honors Distinction
            </div>
        </div>
    </div>

    <!-- Main Two-Column Layout -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; align-items: start;">
        
        <!-- Left / Primary Column -->
        <div style="display: flex; flex-direction: column; gap: 2.5rem; min-width: 0;">
            
            <!-- In-Progress Enrolled Courses -->
            <div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                    <div>
                        <h2 style="font-size: 1.25rem; margin: 0 0 0.25rem 0;">My Active Courses</h2>
                        <p style="font-size: 0.8125rem; color: var(--text-muted); margin: 0;">Continue your structured modules and coursework.</p>
                    </div>
                    <a href="/courses" style="font-size: 0.875rem; color: var(--primary-text); text-decoration: none; font-weight: 600;">
                        View All (<?= count($enrolledCourses) ?>) →
                    </a>
                </div>

                <?php if (empty($enrolledCourses)): ?>
                    <!-- Graceful Empty State -->
                    <div class="card" style="padding: 2.5rem 1.5rem; text-align: center;">
                        <div style="display: inline-flex; align-items: center; justify-content: center; width: 3.5rem; height: 3.5rem; border-radius: var(--radius-full); background: var(--bg-surface-elevated); color: var(--primary-text); margin-bottom: 1rem;">
                            <?= component('icon', ['name' => 'graduation-cap', 'size' => 28]) ?>
                        </div>
                        <h3 style="font-size: 1.125rem; margin-bottom: 0.5rem;">No Enrolled Courses Yet</h3>
                        <p style="color: var(--text-muted); font-size: 0.875rem; max-width: 440px; margin: 0 auto 1.5rem auto;">
                            You have not enrolled in any academic modules yet. Explore our open curriculum to jumpstart your software engineering journey.
                        </p>
                        <a href="/courses" class="btn btn-primary btn-md">
                            Browse Open Course Catalog
                        </a>
                    </div>
                <?php else: ?>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.25rem;">
                        <?php foreach ($enrolledCourses as $item): ?>
                            <?php 
                            $c = $item['course']; 
                            $progress = (int)$item['progress_percent'];
                            ?>
                            <div class="card" style="display: flex; flex-direction: column; overflow: hidden; transition: transform var(--transition-fast);">
                                <div style="height: 100px; background: linear-gradient(135deg, var(--shilpa-blue-900) 0%, var(--bg-surface-elevated) 100%); padding: 1rem; display: flex; align-items: flex-start; justify-content: space-between;">
                                    <span class="badge badge-primary"><?= e($c->C_DONE_BY ?? 'Faculty of IT') ?></span>
                                    <span style="font-size: 0.75rem; font-weight: 700; color: #fff; background: rgba(0,0,0,0.4); padding: 0.2rem 0.5rem; border-radius: 9999px;">
                                        <?= $progress ?>% Complete
                                    </span>
                                </div>
                                <div style="padding: 1.25rem; flex: 1; display: flex; flex-direction: column;">
                                    <h3 style="font-size: 1rem; margin-bottom: 0.5rem; line-height: 1.4;">
                                        <?= e($c->C_NAME) ?>
                                    </h3>
                                    <p style="font-size: 0.8125rem; color: var(--text-secondary); margin-bottom: 1rem; line-height: 1.5; flex: 1;">
                                        <?= e($c->C_CARD_DESCRIPTION ?? 'Advanced computing curriculum module with interactive practical coursework.') ?>
                                    </p>

                                    <!-- Progress Bar -->
                                    <div style="margin-bottom: 1.25rem;">
                                        <div style="height: 6px; background-color: var(--bg-surface-elevated); border-radius: 3px; overflow: hidden;">
                                            <div style="height: 100%; width: <?= $progress ?>%; background-color: var(--primary-default); border-radius: 3px;"></div>
                                        </div>
                                    </div>

                                    <a href="/courses/<?= (int)$c->C_ID ?>" class="btn btn-secondary btn-sm" style="width: 100%;">
                                        <?= component('icon', ['name' => 'play', 'size' => 14]) ?>
                                        <span>Resume Learning</span>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Upcoming Deadlines & Exam Schedule -->
            <div class="card" style="padding: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border-subtle);">
                    <div>
                        <h2 style="font-size: 1.125rem; margin: 0 0 0.25rem 0;">Upcoming Deadlines & Examinations</h2>
                        <p style="font-size: 0.8125rem; color: var(--text-muted); margin: 0;">Stay ahead of assessments, exams, and milestones.</p>
                    </div>
                    <span class="badge badge-warning"><?= count($deadlines) ?> Pending</span>
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <?php foreach ($deadlines as $d): ?>
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.875rem 1rem; background-color: var(--bg-surface-elevated); border: 1px solid var(--border-subtle); border-radius: var(--radius-md); flex-wrap: wrap; gap: 0.75rem;">
                            <div style="display: flex; align-items: center; gap: 0.875rem;">
                                <div style="display: flex; align-items: center; justify-content: center; width: 2.25rem; height: 2.25rem; border-radius: var(--radius-sm); background-color: var(--primary-subtle); color: var(--primary-text);">
                                    <?php if (($d['type'] ?? '') === 'exam'): ?>
                                        <?= component('icon', ['name' => 'award', 'size' => 18]) ?>
                                    <?php else: ?>
                                        <?= component('icon', ['name' => 'clipboard', 'size' => 18]) ?>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <div style="font-weight: 600; font-size: 0.875rem; margin-bottom: 0.125rem;">
                                        <?= e($d['title']) ?>
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);">
                                        <?= e($d['course_name'] ?? 'Faculty of IT') ?> • Weight: <?= (int)($d['weight_percent'] ?? 10) ?>%
                                    </div>
                                </div>
                            </div>

                            <div style="display: flex; align-items: center; gap: 1rem;">
                                <div style="text-align: right;">
                                    <div style="font-size: 0.75rem; font-weight: 700; color: <?= ($d['urgency_color'] ?? '') === 'danger' ? 'var(--danger-text)' : 'var(--warning-text)' ?>;">
                                        <?= e($d['urgency'] ?? 'Due Soon') ?>
                                    </div>
                                    <div style="font-size: 0.6875rem; color: var(--text-muted);">
                                        <?= date('M j, Y h:i A', strtotime($d['due_date'])) ?>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-outline-primary btn-sm">Submit</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Learning Analytics SVG Visualizations -->
            <div class="card" style="padding: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <div>
                        <h2 style="font-size: 1.125rem; margin: 0 0 0.25rem 0;">Weekly Study Velocity</h2>
                        <p style="font-size: 0.8125rem; color: var(--text-muted); margin: 0;">Interactive hours logged across computing laboratories.</p>
                    </div>
                    <span class="badge badge-success">Target Achieved</span>
                </div>

                <!-- SVG Activity Bar Chart -->
                <div style="margin-bottom: 2rem;">
                    <svg viewBox="0 0 700 160" style="width: 100%; height: auto; overflow: visible;" aria-label="Weekly study hours chart" role="img">
                        <!-- Horizontal grid lines -->
                        <line x1="40" y1="20" x2="680" y2="20" stroke="var(--border-subtle)" stroke-dasharray="4" />
                        <text x="10" y="24" fill="var(--text-muted)" font-size="11" font-family="var(--font-mono)">5h</text>
                        <line x1="40" y1="70" x2="680" y2="70" stroke="var(--border-subtle)" stroke-dasharray="4" />
                        <text x="10" y="74" fill="var(--text-muted)" font-size="11" font-family="var(--font-mono)">2.5h</text>
                        <line x1="40" y1="120" x2="680" y2="120" stroke="var(--border-subtle)" />
                        <text x="10" y="124" fill="var(--text-muted)" font-size="11" font-family="var(--font-mono)">0h</text>

                        <!-- Bars -->
                        <?php 
                        $maxHours = 5.0;
                        $chartHeight = 100;
                        $startX = 60;
                        $spacing = 90;
                        foreach ($weeklyActivity as $idx => $act): 
                            $barHeight = ($act['hours'] / $maxHours) * $chartHeight;
                            $x = $startX + ($idx * $spacing);
                            $y = 120 - $barHeight;
                        ?>
                            <g class="chart-bar-group">
                                <rect 
                                    x="<?= $x ?>" 
                                    y="<?= $y ?>" 
                                    width="42" 
                                    height="<?= $barHeight ?>" 
                                    rx="4" 
                                    fill="url(#barGradient)"
                                    style="transition: all 0.3s ease;"
                                />
                                <text x="<?= $x + 21 ?>" y="142" text-anchor="middle" fill="var(--text-secondary)" font-size="12" font-family="var(--font-sans)">
                                    <?= e($act['day']) ?>
                                </text>
                                <text x="<?= $x + 21 ?>" y="<?= $y - 6 ?>" text-anchor="middle" fill="var(--text-primary)" font-size="11" font-weight="600" font-family="var(--font-mono)">
                                    <?= $act['hours'] ?>h
                                </text>
                            </g>
                        <?php endforeach; ?>

                        <defs>
                            <linearGradient id="barGradient" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="var(--shilpa-blue-400)" />
                                <stop offset="100%" stop-color="var(--shilpa-blue-600)" />
                            </linearGradient>
                        </defs>
                    </svg>
                </div>

                <!-- Subject Mastery Progress -->
                <div>
                    <h3 style="font-size: 0.9375rem; margin-bottom: 1rem;">Subject Mastery Index</h3>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem;">
                        <?php foreach ($subjectMastery as $sm): ?>
                            <div style="padding: 0.75rem 1rem; background-color: var(--bg-surface-elevated); border: 1px solid var(--border-subtle); border-radius: var(--radius-md);">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; font-size: 0.8125rem;">
                                    <span style="font-weight: 600;"><?= e($sm['subject']) ?></span>
                                    <span style="font-family: var(--font-mono); font-weight: 700; color: var(--primary-text);"><?= (int)$sm['mastery'] ?>%</span>
                                </div>
                                <div style="height: 6px; background-color: var(--bg-surface); border-radius: 3px; overflow: hidden;">
                                    <div style="height: 100%; width: <?= (int)$sm['mastery'] ?>%; background-color: var(--primary-default); border-radius: 3px;"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right / Sidebar Column -->
        <div style="display: flex; flex-direction: column; gap: 2rem;">
            
            <!-- Contextual Study Recommendations -->
            <div class="card" style="padding: 1.5rem; border-left: 3px solid var(--primary-default);">
                <div style="display: flex; align-items: center; gap: 0.625rem; margin-bottom: 1rem;">
                    <span style="color: var(--primary-text);"><?= component('icon', ['name' => 'zap', 'size' => 20]) ?></span>
                    <h2 style="font-size: 1.0625rem; margin: 0;">Study Recommendations</h2>
                </div>

                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <?php foreach ($recommendations as $rec): ?>
                        <div style="padding: 0.875rem; background-color: var(--bg-surface-elevated); border-radius: var(--radius-md); border: 1px solid var(--border-subtle);">
                            <div style="font-weight: 700; font-size: 0.875rem; margin-bottom: 0.25rem;">
                                <?= e($rec['title']) ?>
                            </div>
                            <div style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.75rem; line-height: 1.4;">
                                <?= e($rec['reason']) ?>
                            </div>
                            <a href="<?= e($rec['url']) ?>" class="btn btn-secondary btn-sm" style="width: 100%; text-align: center;">
                                <?= e($rec['action_label']) ?> →
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Recent Academic Activity Feed -->
            <div class="card" style="padding: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                    <h2 style="font-size: 1.0625rem; margin: 0;">Recent Activities</h2>
                    <span style="font-size: 0.75rem; color: var(--text-muted);">Live timeline</span>
                </div>

                <div style="position: relative; padding-left: 1rem; border-left: 2px solid var(--border-subtle); display: flex; flex-direction: column; gap: 1.5rem;">
                    <?php foreach ($activities as $act): ?>
                        <div style="position: relative;">
                            <!-- Timeline dot -->
                            <span style="position: absolute; left: calc(-1rem - 5px); top: 0.25rem; width: 8px; height: 8px; border-radius: 50%; background-color: var(--primary-default); border: 2px solid var(--bg-surface);"></span>

                            <div style="font-weight: 600; font-size: 0.8125rem; margin-bottom: 0.125rem;">
                                <?= e($act['title']) ?>
                            </div>
                            <div style="font-size: 0.75rem; color: var(--text-secondary); line-height: 1.4; margin-bottom: 0.25rem;">
                                <?= e($act['description'] ?? '') ?>
                            </div>
                            <div style="font-size: 0.6875rem; color: var(--text-muted);">
                                <?= e($act['time_ago'] ?? 'Recent') ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Quick Academic Links & Tools -->
            <div class="card" style="padding: 1.5rem;">
                <h2 style="font-size: 1.0625rem; margin: 0 0 1rem 0;">Academic Resources</h2>
                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <a href="/courses" class="btn btn-secondary btn-sm" style="justify-content: flex-start; text-align: left;">
                        <?= component('icon', ['name' => 'book', 'size' => 16]) ?>
                        <span>Course Catalog & Handouts</span>
                    </a>
                    <a href="/assessments" class="btn btn-secondary btn-sm" style="justify-content: flex-start; text-align: left;">
                        <?= component('icon', ['name' => 'help-circle', 'size' => 16]) ?>
                        <span>Practice Question Bank</span>
                    </a>
                    <a href="/discussions" class="btn btn-secondary btn-sm" style="justify-content: flex-start; text-align: left;">
                        <?= component('icon', ['name' => 'message-square', 'size' => 16]) ?>
                        <span>Student Collaboration Forum</span>
                    </a>
                    <a href="https://www.nsbm.ac.lk" target="_blank" class="btn btn-secondary btn-sm" style="justify-content: flex-start; text-align: left;">
                        <?= component('icon', ['name' => 'graduation-cap', 'size' => 16]) ?>
                        <span>NSBM University Portal ↗</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$slot = ob_get_clean();
include dirname(__DIR__) . '/layouts/workspace.php';
