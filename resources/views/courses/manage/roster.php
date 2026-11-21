<?php
ob_start();
$activeCount = count(array_filter($roster, fn($r) => $r['status'] === 'active'));
$completedCount = count(array_filter($roster, fn($r) => $r['status'] === 'completed'));
?>
<div class="course-roster-page" style="max-width: 1200px; margin: 0 auto;">
    <!-- Page Header -->
    <div style="margin-bottom: 2rem;">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 0.5rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <h1 style="font-size: 1.875rem; font-weight: 800; margin: 0; font-family: var(--font-heading);">
                    Student Enrollment Roster
                </h1>
                <span class="badge badge-primary"><?= e((string)$course->C_NAME) ?></span>
            </div>
            <a href="/faculty/courses" class="btn btn-secondary btn-sm">
                <?= component('icon', ['name' => 'arrow-left', 'size' => 16]) ?>
                <span>Back to Modules</span>
            </a>
        </div>
        <p style="color: var(--text-muted); margin: 0; font-size: 0.9375rem;">
            Track enrolled student learners, monitor completion velocity, and audit registration states.
        </p>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->has('success')): ?>
        <div class="alert alert-success" style="margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem; padding: 1rem 1.25rem; border-radius: var(--radius-md); background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); color: #10b981;">
            <?= component('icon', ['name' => 'check-circle', 'size' => 20]) ?>
            <span><?= e((string)session()->get('success')) ?></span>
        </div>
    <?php endif; ?>

    <!-- Roster KPI Metrics -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
        <div class="card" style="padding: 1.25rem;">
            <div style="font-size: 0.8125rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Total Registrations</div>
            <div style="font-size: 1.875rem; font-weight: 800; margin: 0.25rem 0;"><?= count($roster) ?></div>
            <div style="font-size: 0.75rem; color: var(--text-muted);">All recorded enrollments</div>
        </div>
        <div class="card" style="padding: 1.25rem;">
            <div style="font-size: 0.8125rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Active Learners</div>
            <div style="font-size: 1.875rem; font-weight: 800; margin: 0.25rem 0; color: #10b981;"><?= $activeCount ?></div>
            <div style="font-size: 0.75rem; color: var(--text-muted);">Current semester learners</div>
        </div>
        <div class="card" style="padding: 1.25rem;">
            <div style="font-size: 0.8125rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Graduated / Completed</div>
            <div style="font-size: 1.875rem; font-weight: 800; margin: 0.25rem 0; color: var(--primary-text);"><?= $completedCount ?></div>
            <div style="font-size: 0.75rem; color: var(--text-muted);">100% curriculum completion</div>
        </div>
    </div>

    <!-- Student Roster Table -->
    <div class="card" style="padding: 1.5rem;">
        <?php if (empty($roster)): ?>
            <div style="text-align: center; padding: 3rem 1rem; color: var(--text-muted);">
                <?= component('icon', ['name' => 'users', 'size' => 40]) ?>
                <div style="font-weight: 700; font-size: 1.125rem; margin-top: 1rem;">No enrolled students</div>
                <p style="font-size: 0.875rem; margin-top: 0.25rem;">No students have registered for this course module yet.</p>
            </div>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table class="table" style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--border-subtle); text-align: left;">
                            <th style="padding: 0.75rem; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase;">Student</th>
                            <th style="padding: 0.75rem; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase;">Enrolled Date</th>
                            <th style="padding: 0.75rem; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase;">Progress</th>
                            <th style="padding: 0.75rem; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase;">Status</th>
                            <th style="padding: 0.75rem; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase; text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($roster as $item): ?>
                            <tr style="border-bottom: 1px solid var(--border-subtle);">
                                <td style="padding: 0.875rem 0.75rem;">
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <div style="width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, var(--primary) 0%, #3b82f6 100%); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.875rem;">
                                            <?= strtoupper(substr((string)$item['name'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div style="font-weight: 600; color: var(--text-base);"><?= e((string)$item['name']) ?></div>
                                            <div style="font-size: 0.75rem; color: var(--text-muted);"><?= e((string)$item['email']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 0.875rem 0.75rem; color: var(--text-muted); font-size: 0.8125rem;">
                                    <?= e((string)$item['enrolled_at']) ?>
                                </td>
                                <td style="padding: 0.875rem 0.75rem;">
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <div style="width: 100px; height: 8px; border-radius: 4px; background: var(--border-subtle); overflow: hidden;">
                                            <div style="width: <?= (int)$item['progress'] ?>%; height: 100%; background: var(--primary);"></div>
                                        </div>
                                        <span style="font-size: 0.75rem; font-weight: 600;"><?= (int)$item['progress'] ?>%</span>
                                    </div>
                                </td>
                                <td style="padding: 0.875rem 0.75rem;">
                                    <?php
                                    $st = (string)$item['status'];
                                    $badge = match ($st) {
                                        'active' => 'badge-success',
                                        'completed' => 'badge-primary',
                                        'cancelled' => 'badge-danger',
                                        default => 'badge-secondary',
                                    };
                                    ?>
                                    <span class="badge <?= $badge ?>"><?= ucfirst(e($st)) ?></span>
                                </td>
                                <td style="padding: 0.875rem 0.75rem; text-align: right;">
                                    <form action="/faculty/enrollments/<?= (int)$item['enrollment']->id ?>/status" method="POST" style="display: inline-flex; gap: 0.375rem;">
                                        <input type="hidden" name="_csrf" value="<?= session()->csrfToken() ?>">
                                        <?php if ($item['status'] === 'active'): ?>
                                            <button type="submit" name="status" value="completed" class="btn btn-secondary btn-sm" style="font-size: 0.6875rem; padding: 0.25rem 0.5rem;">
                                                Mark Completed
                                            </button>
                                        <?php else: ?>
                                            <button type="submit" name="status" value="active" class="btn btn-secondary btn-sm" style="font-size: 0.6875rem; padding: 0.25rem 0.5rem;">
                                                Activate
                                            </button>
                                        <?php endif; ?>
                                    </form>
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
include dirname(__DIR__, 2) . '/layouts/workspace.php';
