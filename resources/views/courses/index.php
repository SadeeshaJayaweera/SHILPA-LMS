<?php
ob_start();
?>
<div style="max-width: 1240px; margin: 0 auto; padding: 2.5rem 1.5rem;">
    <!-- Authentic Hero Banner with kiddo.png -->
    <div style="background: #205154 url('/Images/allCourses/kiddo.png') no-repeat right 40px center / contain; padding: 3rem 2.5rem; border-radius: 14px; margin-bottom: 2.5rem; box-shadow: 0 4px 15px rgba(32, 81, 84, 0.25); color: #ffffff;">
        <h1 style="font-family: 'Inter', sans-serif; font-size: 2.75rem; font-weight: 800; margin-bottom: 0.5rem; color: #ffffff; letter-spacing: -0.02em;">
            All Courses in Shilpa!
        </h1>
        <p style="font-size: 1.1rem; color: #e2e8f0; max-width: 600px; margin-bottom: 0;">
            Course Catalog & Discovery — Empowering students with open, verified computing and technical education. Showing <?= (int)$totalCount ?> verified course<?= $totalCount === 1 ? '' : 's' ?>.
        </p>
    </div>

    <!-- Filter / Search Toolbar -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem; background: #ffffff; padding: 1.25rem 1.5rem; border-radius: 12px; border: 1px solid rgba(0,0,0,0.08); box-shadow: 0 2px 6px rgba(0,0,0,0.04);">
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center;">
            <a href="/courses" class="btn btn-sm <?= empty($activeCategory) ? 'btn-primary' : 'btn-outline-secondary' ?>" style="border-radius: 20px; font-weight: 600;">
                All Disciplines
            </a>
            <a href="/courses?category=Software" class="btn btn-sm <?= ($activeCategory === 'Software') ? 'btn-primary' : 'btn-outline-secondary' ?>" style="border-radius: 20px; font-weight: 600;">
                Software Engineering
            </a>
            <a href="/courses?category=Machine" class="btn btn-sm <?= ($activeCategory === 'Machine') ? 'btn-primary' : 'btn-outline-secondary' ?>" style="border-radius: 20px; font-weight: 600;">
                AI &amp; Data Systems
            </a>
            <a href="/courses?category=Cloud" class="btn btn-sm <?= ($activeCategory === 'Cloud') ? 'btn-primary' : 'btn-outline-secondary' ?>" style="border-radius: 20px; font-weight: 600;">
                Cloud &amp; DevOps
            </a>
            <a href="/courses?category=Cybersecurity" class="btn btn-sm <?= ($activeCategory === 'Cybersecurity') ? 'btn-primary' : 'btn-outline-secondary' ?>" style="border-radius: 20px; font-weight: 600;">
                Cybersecurity
            </a>
        </div>

        <!-- Filter / Search Form -->
        <form action="/courses" method="GET" style="display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center;">
            <div style="position: relative;">
                <input 
                    type="text" 
                    name="q" 
                    value="<?= e($searchTerm) ?>" 
                    placeholder="Search by title, instructor..." 
                    class="form-control form-control-sm"
                    style="padding-left: 2.25rem; min-width: 220px; border-radius: 8px;"
                >
                <div style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: var(--text-tertiary);">
                    <?= icon('search', ['size' => 14]) ?>
                </div>
            </div>

            <select name="sort" class="form-select form-select-sm" style="width: auto; border-radius: 8px;" onchange="this.form.submit()">
                <option value="top" <?= $activeSort === 'top' ? 'selected' : '' ?>>Sort: Top Courses</option>
                <option value="latest" <?= $activeSort === 'latest' ? 'selected' : '' ?>>Sort: All Courses</option>
            </select>

            <button type="submit" class="btn btn-sm btn-primary" style="border-radius: 8px;">Search</button>
        </form>
    </div>

    <!-- Course Grid with Authentic SHILPA Card Styling -->
    <?php if (empty($courses)): ?>
        <?= component('empty-state', [
            'icon' => '🔍',
            'title' => 'No Courses Matched Your Criteria',
            'description' => 'Try searching with different keywords or clearing active filters to view all available courses.',
            'action' => '<a href="/courses" class="btn btn-secondary btn-sm">Clear All Filters</a>',
        ]) ?>
    <?php else: ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(270px, 1fr)); gap: 1.75rem;">
            <?php foreach ($courses as $course): 
                $cover = $course->C_CARD_IMAGE ?: '/Images/allCourses/kiddo.png';
                if (!str_starts_with($cover, 'http') && !str_starts_with($cover, '/')) {
                    $cover = '/' . $cover;
                }
            ?>
                <a href="/courses/<?= (int)$course->C_ID ?>" style="text-decoration: none; color: inherit; display: block;">
                    <div style="background: #F8FAFC; border: 1px solid rgba(0,0,0,0.08); border-radius: 12px; overflow: hidden; height: 100%; display: flex; flex-direction: column; box-shadow: 0 4px 12px rgba(0,0,0,0.06); transition: transform 0.15s ease, box-shadow 0.15s ease;" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 8px 20px rgba(218, 19, 19, 0.25)';" onmouseout="this.style.transform='none'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.06)';">
                        <div style="height: 155px; background: #e2e8f0; position: relative; overflow: hidden;">
                            <img src="<?= e($cover) ?>" alt="<?= e($course->C_NAME) ?>" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.onerror=null; this.src='/Images/allCourses/kiddo.png';">

                            <?php if (strcasecmp((string)$course->TOP_COURSE, 'yes') === 0): ?>
                                <div style="position: absolute; top: 0.75rem; right: 0.75rem;">
                                    <span style="background: #da1313; color: #fff; font-size: 0.7rem; font-weight: 700; padding: 0.25rem 0.6rem; border-radius: 20px; letter-spacing: 0.05em; text-transform: uppercase;">
                                        ★ Top Course
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div style="padding: 1.25rem; flex: 1; display: flex; flex-direction: column;">
                            <h3 style="font-family: 'Inter', sans-serif; font-size: 1.0625rem; font-weight: 700; color: #111827; margin-bottom: 0.4rem; line-height: 1.35;">
                                <?= e($course->C_NAME) ?>
                            </h3>
                            <p style="font-size: 0.825rem; color: #4b5563; line-height: 1.5; margin-bottom: 1.25rem; flex: 1;">
                                <?= e($course->C_CARD_DESCRIPTION ?: substr((string)$course->C_DESCRIPTION, 0, 95) . '...') ?>
                            </p>
                            <div style="border-top: 1px solid rgba(0,0,0,0.06); padding-top: 0.75rem; display: flex; align-items: center; justify-content: space-between; font-size: 0.8125rem;">
                                <span style="font-weight: 700; color: #111827;">
                                    Done By: <?= e($course->C_DONE_BY ?: 'SHILPA Instructor') ?>
                                </span>
                                <span style="color: #da1313; font-weight: 600;">View &rarr;</span>
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
