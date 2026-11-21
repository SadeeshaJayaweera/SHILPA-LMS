<?php
ob_start();
?>
<div style="max-width: 1000px; margin: 0 auto; padding: 3rem 1.5rem;">
    <!-- Breadcrumb -->
    <nav style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: var(--text-muted); margin-bottom: 2rem;">
        <a href="/" style="color: inherit; text-decoration: none;">Home</a>
        <span>/</span>
        <a href="/courses" style="color: inherit; text-decoration: none;">Courses</a>
        <span>/</span>
        <span style="color: var(--text-primary);"><?= e($course->C_NAME) ?></span>
    </nav>

    <!-- Course Header Banner -->
    <div class="card" style="margin-bottom: 2.5rem; overflow: hidden; background: linear-gradient(135deg, var(--bg-surface) 0%, var(--bg-surface-elevated) 100%);">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; align-items: center; padding: 2.5rem;">
            <div>
                <?php if (strcasecmp((string)$course->TOP_COURSE, 'yes') === 0): ?>
                    <div style="margin-bottom: 1rem;">
                        <?= component('badge', ['variant' => 'primary', 'slot' => icon('star', ['size' => 12]) . ' Featured Top Course']) ?>
                    </div>
                <?php endif; ?>

                <h1 style="font-size: 2.5rem; font-weight: 800; line-height: 1.2; margin-bottom: 1rem;">
                    <?= e($course->C_NAME) ?>
                </h1>

                <p style="font-size: 1.0625rem; color: var(--text-secondary); line-height: 1.6; margin-bottom: 1.5rem;">
                    <?= e($course->C_CARD_DESCRIPTION) ?>
                </p>

                <div style="display: flex; align-items: center; gap: 1.5rem; font-size: 0.875rem; color: var(--text-muted); margin-bottom: 2rem;">
                    <span style="display: flex; align-items: center; gap: 0.5rem;">
                        <?= icon('user', ['size' => 16]) ?>
                        <strong>Instructor:</strong> <?= e($course->C_DONE_BY ?: 'SHILPA Instructor') ?>
                    </span>
                    <span style="display: flex; align-items: center; gap: 0.5rem;">
                        <?= icon('clock', ['size' => 16]) ?>
                        <strong>Added:</strong> <?= e(date('M Y', strtotime((string)$course->C_ADD_DATE ?: 'now'))) ?>
                    </span>
                </div>

                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <a href="/welcome.php" class="btn btn-primary btn-lg">
                        <?= icon('graduation-cap', ['size' => 18]) ?> Enroll Now for Free
                    </a>
                    <?php if ($course->C_LINK): ?>
                        <a href="<?= e($course->C_LINK) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-secondary btn-lg">
                            <?= icon('zap', ['size' => 18]) ?> External Materials &rarr;
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($course->C_CARD_IMAGE): ?>
                <div style="border-radius: var(--radius-xl); overflow: hidden; box-shadow: var(--shadow-xl); border: 1px solid var(--border-default); max-height: 320px;">
                    <img src="<?= e($course->C_CARD_IMAGE) ?>" alt="<?= e($course->C_NAME) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Course Content / Description -->
    <div class="card" style="padding: 2.5rem; margin-bottom: 2.5rem;">
        <h2 style="font-size: 1.5rem; margin-bottom: 1.25rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border-subtle);">
            About This Course & Syllabus Overview
        </h2>
        <div style="color: var(--text-secondary); line-height: 1.8; font-size: 1rem; white-space: pre-line;">
            <?= e($course->C_DESCRIPTION) ?>
        </div>
    </div>
</div>
<?php
$slot = ob_get_clean();
include dirname(__DIR__) . '/layouts/app.php';
