<?php
ob_start();
?>
<div style="max-width: 1200px; margin: 0 auto; padding: 3rem 1.5rem;">
    <!-- Discovery Header -->
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2rem; flex-wrap: wrap; gap: 1.5rem;">
        <div>
            <div style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.35rem 0.85rem; border-radius: var(--radius-full); background: var(--primary-subtle); border: 1px solid rgba(56, 189, 248, 0.25); color: var(--primary-text); font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.75rem;">
                <?= icon('book', ['size' => 14]) ?> Open Curriculum
            </div>
            <h1 style="font-size: 2.5rem; margin-bottom: 0.5rem; font-weight: 800; font-family: var(--font-heading);">Course Catalog & Discovery</h1>
            <p style="color: var(--text-muted); font-size: 1rem;">
                Showing <?= (int)$totalCount ?> verified course<?= $totalCount === 1 ? '' : 's' ?> available for immediate open study.
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
                <option value="top" <?= $activeSort === 'top' ? 'selected' : '' ?>>Sort: Featured</option>
                <option value="latest" <?= $activeSort === 'latest' ? 'selected' : '' ?>>Sort: All Courses</option>
            </select>

            <button type="submit" class="btn btn-primary">Filter</button>
        </form>
    </div>

    <!-- Category Pills -->
    <div style="display: flex; gap: 0.625rem; overflow-x: auto; padding-bottom: 1.25rem; margin-bottom: 2rem; border-bottom: 1px solid var(--border-subtle);">
        <a href="/courses" class="badge <?= empty($activeCategory) ? 'badge-primary' : 'badge-neutral' ?>" style="padding: 0.5rem 1rem; font-size: 0.8125rem; text-decoration: none;">
            All Disciplines
        </a>
        <a href="/courses?category=Software" class="badge <?= ($activeCategory === 'Software') ? 'badge-primary' : 'badge-neutral' ?>" style="padding: 0.5rem 1rem; font-size: 0.8125rem; text-decoration: none;">
            Software Engineering
        </a>
        <a href="/courses?category=Machine" class="badge <?= ($activeCategory === 'Machine') ? 'badge-primary' : 'badge-neutral' ?>" style="padding: 0.5rem 1rem; font-size: 0.8125rem; text-decoration: none;">
            AI &amp; Data Systems
        </a>
        <a href="/courses?category=Cloud" class="badge <?= ($activeCategory === 'Cloud') ? 'badge-primary' : 'badge-neutral' ?>" style="padding: 0.5rem 1rem; font-size: 0.8125rem; text-decoration: none;">
            Cloud &amp; DevOps
        </a>
        <a href="/courses?category=Cybersecurity" class="badge <?= ($activeCategory === 'Cybersecurity') ? 'badge-primary' : 'badge-neutral' ?>" style="padding: 0.5rem 1rem; font-size: 0.8125rem; text-decoration: none;">
            Cybersecurity
        </a>
        <a href="/courses?category=Distributed" class="badge <?= ($activeCategory === 'Distributed') ? 'badge-primary' : 'badge-neutral' ?>" style="padding: 0.5rem 1rem; font-size: 0.8125rem; text-decoration: none;">
            Distributed Systems
        </a>
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
            <?php foreach ($courses as $course): 
                $cover = $course->C_CARD_IMAGE ?: '/img/courses/fullstack.svg';
                if (!str_starts_with($cover, 'http') && !str_starts_with($cover, '/')) {
                    $cover = '/' . $cover;
                }
            ?>
                <a href="/courses/<?= (int)$course->C_ID ?>" style="text-decoration: none; color: inherit; display: block;">
                    <div class="card card-interactive" style="height: 100%; display: flex; flex-direction: column; border-color: var(--border-default);">
                        <div style="height: 165px; background: var(--bg-surface-elevated); position: relative; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                            <img src="<?= e($cover) ?>" alt="<?= e($course->C_NAME) ?>" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.onerror=null; this.src='/img/courses/fullstack.svg';">

                            <?php if (strcasecmp((string)$course->TOP_COURSE, 'yes') === 0): ?>
                                <div style="position: absolute; top: 0.75rem; right: 0.75rem;">
                                    <?= component('badge', ['variant' => 'primary', 'slot' => icon('star', ['size' => 12]) . ' Top Course']) ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="card-body" style="flex: 1; display: flex; flex-direction: column;">
                            <h3 style="font-size: 1.125rem; font-weight: 700; margin-bottom: 0.5rem; line-height: 1.35; font-family: var(--font-heading);">
                                <?= e($course->C_NAME) ?>
                            </h3>
                            <p style="font-size: 0.875rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 1.25rem; flex: 1;">
                                <?= e($course->C_CARD_DESCRIPTION ?: substr((string)$course->C_DESCRIPTION, 0, 100) . '...') ?>
                            </p>
                            <div style="display: flex; align-items: center; justify-content: space-between; font-size: 0.8125rem; color: var(--text-tertiary); padding-top: 0.75rem; border-top: 1px solid var(--border-subtle);">
                                <span style="display: flex; align-items: center; gap: 0.35rem;">
                                    <?= icon('user', ['size' => 14]) ?> <?= e($course->C_DONE_BY ?: 'SHILPA Instructor') ?>
                                </span>
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
