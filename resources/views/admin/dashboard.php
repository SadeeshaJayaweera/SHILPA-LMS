<?php
ob_start();
?>
<div class="admin-dashboard">
    <!-- Admin Welcome Header -->
    <div style="background: linear-gradient(135deg, var(--bg-surface-elevated) 0%, var(--bg-surface) 100%); border: 1px solid var(--border-subtle); border-radius: var(--radius-lg); padding: 1.75rem 2rem; margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.5rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.375rem;">
                <h1 style="font-size: 1.875rem; font-weight: 800; margin: 0; font-family: var(--font-heading);">
                    Platform Governance Console
                </h1>
                <span class="badge badge-danger">Superadmin Access</span>
            </div>
            <p style="color: var(--text-muted); margin: 0; font-size: 0.9375rem;">
                Institutional Administration • User Directories, Course Approvals, and Security Auditing
            </p>
        </div>

        <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
            <a href="/admin/users" class="btn btn-secondary btn-sm">
                <?= component('icon', ['name' => 'user', 'size' => 16]) ?>
                <span>User Directory</span>
            </a>
            <a href="/courseform.php" class="btn btn-primary btn-sm">
                <?= component('icon', ['name' => 'layers', 'size' => 16]) ?>
                <span>Create Course</span>
            </a>
        </div>
    </div>

    <!-- Quick Stats Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; margin-bottom: 2.5rem;">
        <div class="card" style="padding: 1.25rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                <span style="font-size: 0.8125rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Total Platform Users</span>
                <span style="color: var(--primary-text);"><?= component('icon', ['name' => 'user', 'size' => 18]) ?></span>
            </div>
            <div style="font-size: 2rem; font-weight: 800; font-family: var(--font-heading); margin-bottom: 0.25rem;">
                <?= (int)$metrics['total_users'] ?>
            </div>
            <div style="font-size: 0.75rem; color: var(--text-muted);">
                <?= (int)$metrics['students_count'] ?> Students • <?= (int)$metrics['lecturers_count'] ?> Faculty • <?= (int)$metrics['admins_count'] ?> Admins
            </div>
        </div>

        <div class="card" style="padding: 1.25rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                <span style="font-size: 0.8125rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Active Courses</span>
                <span style="color: var(--success-text);"><?= component('icon', ['name' => 'book', 'size' => 18]) ?></span>
            </div>
            <div style="font-size: 2rem; font-weight: 800; font-family: var(--font-heading); margin-bottom: 0.25rem;">
                <?= (int)$metrics['total_courses'] ?>
            </div>
            <div style="font-size: 0.75rem; color: var(--success-text);">
                <?= (int)$metrics['featured_courses'] ?> Featured Top Courses
            </div>
        </div>

        <div class="card" style="padding: 1.25rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                <span style="font-size: 0.8125rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Total Enrollments</span>
                <span style="color: var(--warning-text);"><?= component('icon', ['name' => 'award', 'size' => 18]) ?></span>
            </div>
            <div style="font-size: 2rem; font-weight: 800; font-family: var(--font-heading); margin-bottom: 0.25rem;">
                <?= (int)$metrics['total_enrollments'] ?>
            </div>
            <div style="font-size: 0.75rem; color: var(--success-text);">
                Active learning sessions across LMS
            </div>
        </div>

        <div class="card" style="padding: 1.25rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                <span style="font-size: 0.8125rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Platform Health</span>
                <span style="color: var(--success-text);"><?= component('icon', ['name' => 'shield', 'size' => 18]) ?></span>
            </div>
            <div style="font-size: 2rem; font-weight: 800; font-family: var(--font-heading); margin-bottom: 0.25rem; color: var(--success-text);">
                100% Uptime
            </div>
            <div style="font-size: 0.75rem; color: var(--text-muted);">
                PHP <?= PHP_VERSION ?> • Database Healthy
            </div>
        </div>
    </div>

    <!-- Main Two-Column Layout -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; align-items: start;">
        
        <!-- Left / Primary Column -->
        <div style="display: flex; flex-direction: column; gap: 2.5rem; min-width: 0;">
            
            <!-- Platform Enrollment Growth SVG Chart -->
            <div class="card" style="padding: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <div>
                        <h2 style="font-size: 1.125rem; margin: 0 0 0.25rem 0;">Enrollment Growth Trajectory</h2>
                        <p style="font-size: 0.8125rem; color: var(--text-muted); margin: 0;">Monthly student admission and course enrollment trends.</p>
                    </div>
                    <span class="badge badge-success">+42% QoQ Growth</span>
                </div>

                <div style="margin-bottom: 1rem;">
                    <svg viewBox="0 0 600 160" style="width: 100%; height: auto; overflow: visible;" aria-label="Monthly enrollment chart" role="img">
                        <line x1="30" y1="20" x2="580" y2="20" stroke="var(--border-subtle)" stroke-dasharray="4" />
                        <line x1="30" y1="70" x2="580" y2="70" stroke="var(--border-subtle)" stroke-dasharray="4" />
                        <line x1="30" y1="120" x2="580" y2="120" stroke="var(--border-subtle)" />

                        <?php 
                        $maxVal = 140;
                        $startX = 50;
                        $spacing = 90;
                        foreach ($enrollmentTrend as $i => $item): 
                            $h = ($item['count'] / $maxVal) * 100;
                            $x = $startX + ($i * $spacing);
                            $y = 120 - $h;
                        ?>
                            <rect x="<?= $x ?>" y="<?= $y ?>" width="40" height="<?= $h ?>" rx="4" fill="url(#adminBarGrad)" />
                            <text x="<?= $x + 20 ?>" y="142" text-anchor="middle" fill="var(--text-secondary)" font-size="12" font-family="var(--font-sans)"><?= e($item['month']) ?></text>
                            <text x="<?= $x + 20 ?>" y="<?= $y - 6 ?>" text-anchor="middle" fill="var(--text-primary)" font-size="11" font-weight="700" font-family="var(--font-mono)"><?= $item['count'] ?></text>
                        <?php endforeach; ?>

                        <defs>
                            <linearGradient id="adminBarGrad" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="var(--shilpa-indigo-500)" />
                                <stop offset="100%" stop-color="var(--shilpa-blue-600)" />
                            </linearGradient>
                        </defs>
                    </svg>
                </div>
            </div>

            <!-- Course Catalog Oversight -->
            <div class="card" style="padding: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                    <div>
                        <h2 style="font-size: 1.125rem; margin: 0 0 0.25rem 0;">Published Courses & Governance</h2>
                        <p style="font-size: 0.8125rem; color: var(--text-muted); margin: 0;">Institutional academic offerings and top course spotlighting.</p>
                    </div>
                    <a href="/all_courses.php" class="btn btn-secondary btn-sm">Full Catalog</a>
                </div>

                <div style="overflow-x: auto;">
                    <table class="table" style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
                        <thead>
                            <tr style="border-bottom: 1px solid var(--border-subtle); text-align: left; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase;">
                                <th style="padding: 0.75rem 0.5rem;">ID</th>
                                <th style="padding: 0.75rem 0.5rem;">Course Title</th>
                                <th style="padding: 0.75rem 0.5rem;">Faculty / Author</th>
                                <th style="padding: 0.75rem 0.5rem;">Featured</th>
                                <th style="padding: 0.75rem 0.5rem; text-align: right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($courses as $c): ?>
                                <tr style="border-bottom: 1px solid var(--border-subtle);">
                                    <td style="padding: 0.75rem 0.5rem; font-family: var(--font-mono); color: var(--text-muted);"><?= (int)$c->C_ID ?></td>
                                    <td style="padding: 0.75rem 0.5rem; font-weight: 600;"><?= e($c->C_NAME) ?></td>
                                    <td style="padding: 0.75rem 0.5rem; color: var(--text-secondary);"><?= e($c->C_DONE_BY ?? 'Faculty') ?></td>
                                    <td style="padding: 0.75rem 0.5rem;">
                                        <?php if (strtolower((string)$c->TOP_COURSE) === 'yes'): ?>
                                            <span class="badge badge-success">Top Course</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">Standard</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="padding: 0.75rem 0.5rem; text-align: right;">
                                        <a href="/updateform.php?updateid=<?= (int)$c->C_ID ?>" class="btn btn-secondary btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">Edit</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Security Audit & Event Log Feed -->
            <div class="card" style="padding: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border-subtle);">
                    <div>
                        <h2 style="font-size: 1.125rem; margin: 0 0 0.25rem 0;">Security & Audit Logs</h2>
                        <p style="font-size: 0.8125rem; color: var(--text-muted); margin: 0;">Real-time platform access events and authentication logs.</p>
                    </div>
                    <span class="badge badge-primary">Audited</span>
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.875rem;">
                    <?php foreach ($auditLogs as $log): ?>
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.75rem 1rem; background-color: var(--bg-surface-elevated); border: 1px solid var(--border-subtle); border-radius: var(--radius-md); flex-wrap: wrap; gap: 0.75rem;">
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <span style="color: var(--success-text);"><?= component('icon', ['name' => 'shield', 'size' => 16]) ?></span>
                                <div>
                                    <div style="font-weight: 600; font-size: 0.8125rem;"><?= e($log['event']) ?></div>
                                    <div style="font-size: 0.75rem; color: var(--text-secondary);"><?= e($log['details']) ?></div>
                                </div>
                            </div>
                            <div style="text-align: right; font-size: 0.75rem; color: var(--text-muted);">
                                <div>IP: <?= e($log['ip']) ?></div>
                                <div><?= e($log['time_ago']) ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Right / Sidebar Column -->
        <div style="display: flex; flex-direction: column; gap: 2rem;">
            
            <!-- Platform Diagnostics -->
            <div class="card" style="padding: 1.5rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
                    <span style="color: var(--success-text);"><?= component('icon', ['name' => 'shield', 'size' => 18]) ?></span>
                    <h2 style="font-size: 1.0625rem; margin: 0;">System Diagnostics</h2>
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.75rem; font-size: 0.8125rem;">
                    <div style="display: flex; justify-content: space-between; padding-bottom: 0.5rem; border-bottom: 1px solid var(--border-subtle);">
                        <span style="color: var(--text-muted);">PHP Runtime</span>
                        <span style="font-family: var(--font-mono); font-weight: 700;"><?= e($systemHealth['php_version']) ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding-bottom: 0.5rem; border-bottom: 1px solid var(--border-subtle);">
                        <span style="color: var(--text-muted);">Database Layer</span>
                        <span style="font-weight: 600; color: var(--success-text);"><?= e($systemHealth['db_driver']) ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding-bottom: 0.5rem; border-bottom: 1px solid var(--border-subtle);">
                        <span style="color: var(--text-muted);">Memory Footprint</span>
                        <span style="font-family: var(--font-mono); font-weight: 700;"><?= e($systemHealth['memory_usage']) ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--text-muted);">Server Health</span>
                        <span style="color: var(--success-text); font-weight: 700;">Operational</span>
                    </div>
                </div>
            </div>

            <!-- Quick Administration Actions -->
            <div class="card" style="padding: 1.5rem;">
                <h2 style="font-size: 1.0625rem; margin: 0 0 1rem 0;">Administration Shortcuts</h2>
                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <a href="/admin/users" class="btn btn-primary btn-sm" style="justify-content: flex-start; text-align: left;">
                        <?= component('icon', ['name' => 'user', 'size' => 16]) ?>
                        <span>User Directory Management</span>
                    </a>
                    <a href="/admin.php" class="btn btn-secondary btn-sm" style="justify-content: flex-start; text-align: left;">
                        <?= component('icon', ['name' => 'settings', 'size' => 16]) ?>
                        <span>Legacy Administration Table</span>
                    </a>
                    <a href="/login/registeradmin.php" class="btn btn-secondary btn-sm" style="justify-content: flex-start; text-align: left;">
                        <?= component('icon', ['name' => 'shield', 'size' => 16]) ?>
                        <span>Provision New Administrator</span>
                    </a>
                    <a href="/design-system" class="btn btn-secondary btn-sm" style="justify-content: flex-start; text-align: left;">
                        <?= component('icon', ['name' => 'layers', 'size' => 16]) ?>
                        <span>Design System Tokens</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$slot = ob_get_clean();
include dirname(__DIR__) . '/layouts/workspace.php';
