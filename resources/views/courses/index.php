<?php
ob_start();
?>
<div style="max-width: 1200px; margin: 0 auto; padding: 3rem 1.5rem;">
    <!-- Discovery Header -->
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2.5rem; flex-wrap: wrap; gap: 1.5rem;">
        <div>
            <h1 style="font-size: 2.5rem; margin-bottom: 0.5rem;">Course Catalog & Discovery</h1>
            <p style="color: var(--text-muted); font-size: 1rem;">
                Showing <?= (int)$totalCount ?> course<?= $totalCount === 1 ? '' : 's' ?> available for immediate open study.
            </p>
        </div>

        <!-- Filter / Search Form -->
        <form action="/courses" method="GET" style="display: flex; gap: 0.75rem; flex-wrap: wrap; width: 100%; max-width: 600px;">
            <div style="flex: 1; min-width: 220px; position: relative;">
                <input 
                    type="text" 
                    name="q" 
                    value="<?= e($searchTerm) ?>" 
                    placeholder="Search by title, topic, or instructor..." 
                    class="form-control"
                    style="padding-left: 2.5rem;"
                >
                <div style="position: absolute; left: 0.875rem; top: 50%; transform: translateY(-50%); color: var(--text-tertiary);">
                    <?= icon('search', ['size' => 16]) ?>
                </div>
            </div>

            <select name="sort" class="form-select" style="width: auto;" onchange="this.form.submit()">
                <option value="latest" <?= $activeSort === 'latest' ? 'selected' : '' ?>>Sort: Newest</option>
                <option value="top" <?= $activeSort === 'top' ? 'selected' : '' ?>>Sort: Top Courses</option>
            </select>

            <button type="submit" class="btn btn-primary">Filter</button>
        </form>
    </div>

    <!-- Course Grid -->
    <?php if (empty($courses)): ?>
        <?= component('empty-state', [
            'icon' => '🔍',
            'title' => 'No Courses Matched Your Criteria',
            'description' => 'Try searching with different keywords or clearing active filters to view all available courses.',
            'action' => '<a href="/courses" class="btn btn-secondary btn-sm">Clear All Filters</a>',
        ]) ?>
    <?php else: ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.75rem;">
            <?php foreach ($courses as $course): ?>
                <a href="/courses/<?= (int)$course->C_ID ?>" style="text-decoration: none; color: inherit; display: block;">
                    <div class="card card-interactive" style="height: 100%; display: flex; flex-direction: column;">
                        <div style="height: 160px; background: var(--bg-surface-elevated); position: relative; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                            <?php if ($course->C_CARD_IMAGE): ?>
                                <img src="<?= e($course->C_CARD_IMAGE) ?>" alt="<?= e($course->C_NAME) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                            <?php else: ?>
                                <div style="font-size: 3rem; color: var(--text-tertiary);"><?= icon('book', ['size' => 48]) ?></div>
                            <?php endif; ?>

                            <?php if (strcasecmp((string)$course->TOP_COURSE, 'yes') === 0): ?>
                                <div style="position: absolute; top: 0.75rem; right: 0.75rem;">
                                    <?= component('badge', ['variant' => 'primary', 'slot' => icon('star', ['size' => 12]) . ' Top Course']) ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="card-body" style="flex: 1; display: flex; flex-direction: column;">
                            <h3 style="font-size: 1.125rem; font-weight: 700; margin-bottom: 0.5rem; line-height: 1.35;">
                                <?= e($course->C_NAME) ?>
                            </h3>
                            <p style="font-size: 0.875rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 1.25rem; flex: 1;">
                                <?= e($course->C_CARD_DESCRIPTION ?: substr((string)$course->C_DESCRIPTION, 0, 100) . '...') ?>
                            </p>
                            <div style="display: flex; align-items: center; justify-content: space-between; font-size: 0.8125rem; color: var(--text-tertiary); padding-top: 0.75rem; border-top: 1px solid var(--border-subtle);">
                                <span><?= icon('user', ['size' => 14]) ?> <?= e($course->C_DONE_BY ?: 'SHILPA Instructor') ?></span>
                                <span style="color: var(--primary-text); font-weight: 600;">View Course &rarr;</span>
                            </div>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?php
$slot = ob_get_clean();
include dirname(__DIR__) . '/layouts/app.php';
