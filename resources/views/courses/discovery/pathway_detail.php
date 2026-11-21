<?php
declare(strict_types=1);

/** @var \App\Models\LearningPathway $pathway */
$pathway = $data['pathway'];
$courses = $data['courses'] ?? [];
$userId = $data['userId'] ?? 0;

ob_start();
?>
<div class="pathway-detail-container" style="max-width: 1000px; margin: 0 auto; padding-bottom: 4rem;">
    <!-- Breadcrumb -->
    <div style="margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: var(--text-muted);">
        <a href="/pathways" style="color: var(--text-muted); text-decoration: none;">Pathways</a>
        <span>/</span>
        <span style="color: var(--text-color); font-weight: 600;"><?= e($pathway->title) ?></span>
    </div>

    <!-- Pathway Header Hero -->
    <div class="card" style="padding: 2.5rem; border-radius: var(--radius-xl); border: 1px solid var(--border-color); margin-bottom: 2.5rem; background: var(--surface);">
        <div style="display: flex; gap: 2rem; align-items: flex-start; flex-wrap: wrap;">
            <div style="width: 72px; height: 72px; border-radius: var(--radius-xl); background: var(--primary-50); color: var(--primary); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <?= component('icon', ['name' => $pathway->icon ?: 'compass', 'size' => 36]) ?>
            </div>
            <div style="flex: 1; min-width: 280px;">
                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem; flex-wrap: wrap;">
                    <span class="badge badge-primary"><?= e($pathway->target_role) ?></span>
                    <span class="badge badge-secondary"><?= count($courses) ?> Milestone Courses</span>
                </div>
                <h1 style="font-size: 2.25rem; font-weight: 800; font-family: var(--font-heading); margin: 0 0 1rem 0; line-height: 1.2;">
                    <?= e($pathway->title) ?>
                </h1>
                <p style="color: var(--text-muted); font-size: 1.0625rem; line-height: 1.6; margin: 0;">
                    <?= e($pathway->description) ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Curriculum Sequence -->
    <div style="margin-bottom: 2rem;">
        <h2 style="font-size: 1.375rem; font-weight: 800; font-family: var(--font-heading); margin: 0 0 1.5rem 0;">
            Curriculum Sequence & Milestones
        </h2>

        <?php if (empty($courses)): ?>
            <div class="card" style="padding: 3rem; text-align: center; color: var(--text-muted);">
                No courses assigned to this pathway yet.
            </div>
        <?php else: ?>
            <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                <?php foreach ($courses as $index => $course): ?>
                    <?php
                    $stepNum = $index + 1;
                    $isEnrolled = $userId > 0 ? \App\Models\Enrollment::isEnrolled($userId, (int)$course->C_ID) : false;
                    ?>
                    <div class="card" style="padding: 1.5rem; border-radius: var(--radius-lg); border: 1px solid var(--border-color); display: flex; align-items: center; gap: 1.5rem; flex-wrap: wrap;">
                        <!-- Step Indicator -->
                        <div style="width: 44px; height: 44px; border-radius: 50%; background: <?= $isEnrolled ? 'var(--success-50)' : 'var(--surface-secondary)' ?>; color: <?= $isEnrolled ? 'var(--success)' : 'var(--text-muted)' ?>; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.125rem; flex-shrink: 0; border: 2px solid <?= $isEnrolled ? 'var(--success)' : 'var(--border-color)' ?>;">
                            <?php if ($isEnrolled): ?>
                                <?= component('icon', ['name' => 'check', 'size' => 20]) ?>
                            <?php else: ?>
                                <?= $stepNum ?>
                            <?php endif; ?>
                        </div>

                        <!-- Course Details -->
                        <div style="flex: 1; min-width: 240px;">
                            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                                <span style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted);">
                                    Step <?= $stepNum ?>
                                </span>
                                <?php if ($isEnrolled): ?>
                                    <span class="badge badge-success" style="font-size: 0.6875rem;">Enrolled</span>
                                <?php endif; ?>
                            </div>
                            <h3 style="font-size: 1.125rem; font-weight: 700; margin: 0 0 0.375rem 0;">
                                <a href="/courses/<?= (int)$course->C_ID ?>" style="color: var(--text-color); text-decoration: none;">
                                    <?= e($course->C_NAME) ?>
                                </a>
                            </h3>
                            <p style="color: var(--text-muted); font-size: 0.875rem; margin: 0; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                <?= e($course->C_CARD_DESCRIPTION ?: $course->C_DESCRIPTION) ?>
                            </p>
                        </div>

                        <!-- Action Button -->
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <a href="/courses/<?= (int)$course->C_ID ?>" class="btn <?= $isEnrolled ? 'btn-secondary' : 'btn-primary' ?> btn-sm">
                                <span><?= $isEnrolled ? 'Continue Course' : 'View Course' ?></span>
                                <?= component('icon', ['name' => 'arrow-right', 'size' => 14]) ?>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$slot = ob_get_clean();
include dirname(__DIR__, 2) . '/layouts/workspace.php';
