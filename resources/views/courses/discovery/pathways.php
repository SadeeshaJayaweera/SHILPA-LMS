<?php
declare(strict_types=1);

$pathways = $data['pathways'] ?? [];

ob_start();
?>
<div class="pathways-index-container" style="max-width: 1100px; margin: 0 auto; padding-bottom: 3rem;">
    <!-- Header -->
    <div style="margin-bottom: 2.5rem; text-align: center; max-width: 700px; margin-inline: auto;">
        <div style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.25rem 0.75rem; background: var(--primary-50); color: var(--primary-700); border-radius: 9999px; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.75rem;">
            <?= component('icon', ['name' => 'compass', 'size' => 14]) ?>
            <span>Structured Career Progression</span>
        </div>
        <h1 style="font-size: 2.5rem; font-weight: 800; font-family: var(--font-heading); margin: 0 0 0.75rem 0; letter-spacing: -0.025em;">
            Curated Learning Pathways
        </h1>
        <p style="color: var(--text-muted); font-size: 1.125rem; line-height: 1.6; margin: 0;">
            Follow structured, role-specific tracks designed by top educators and industry engineers to take you from fundamentals to senior mastery.
        </p>
    </div>

    <!-- Pathways Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.75rem;">
        <?php foreach ($pathways as $pw): ?>
            <?php
            $courseCount = count($pw->getCourseIds());
            ?>
            <div class="card" style="padding: 2rem; border-radius: var(--radius-xl); border: 1px solid var(--border-color); display: flex; flex-direction: column; position: relative; transition: all 0.2s;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.25rem;">
                    <div style="width: 52px; height: 52px; border-radius: var(--radius-lg); background: var(--primary-50); color: var(--primary); display: flex; align-items: center; justify-content: center;">
                        <?= component('icon', ['name' => $pw->icon ?: 'compass', 'size' => 26]) ?>
                    </div>
                    <span class="badge badge-primary" style="font-size: 0.75rem; font-weight: 700;">
                        <?= $courseCount ?> <?= $courseCount === 1 ? 'Course' : 'Courses' ?>
                    </span>
                </div>

                <span style="font-size: 0.8125rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--primary); margin-bottom: 0.5rem;">
                    <?= e($pw->target_role) ?>
                </span>

                <h2 style="font-size: 1.375rem; font-weight: 800; margin: 0 0 0.75rem 0; font-family: var(--font-heading); line-height: 1.3;">
                    <a href="/pathways/<?= e($pw->slug) ?>" style="color: var(--text-color); text-decoration: none;">
                        <?= e($pw->title) ?>
                    </a>
                </h2>

                <p style="color: var(--text-muted); font-size: 0.9375rem; line-height: 1.6; margin: 0 0 1.75rem 0; flex: 1;">
                    <?= e($pw->description) ?>
                </p>

                <div style="border-top: 1px solid var(--border-color); padding-top: 1.25rem; display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 0.8125rem; color: var(--text-muted); display: flex; align-items: center; gap: 0.375rem;">
                        <?= component('icon', ['name' => 'award', 'size' => 14]) ?>
                        <span>Role Certification Ready</span>
                    </span>
                    <a href="/pathways/<?= e($pw->slug) ?>" class="btn btn-primary btn-sm">
                        <span>View Pathway</span>
                        <?= component('icon', ['name' => 'arrow-right', 'size' => 14]) ?>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php
$slot = ob_get_clean();
include dirname(__DIR__, 2) . '/layouts/workspace.php';
