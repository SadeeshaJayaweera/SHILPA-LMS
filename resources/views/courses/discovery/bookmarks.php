<?php
declare(strict_types=1);

$bookmarks = $data['bookmarks'] ?? [];

ob_start();
?>
<div class="bookmarks-container" style="max-width: 1000px; margin: 0 auto; padding-bottom: 3rem;">
    <!-- Header -->
    <div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 style="font-size: 2rem; font-weight: 800; font-family: var(--font-heading); margin: 0 0 0.5rem 0;">
                My Saved Courses
            </h1>
            <p style="color: var(--text-muted); font-size: 0.9375rem; margin: 0;">
                Review courses bookmarked for your future study plan.
            </p>
        </div>
        <a href="/courses/recommended" class="btn btn-secondary">
            <?= component('icon', ['name' => 'sparkles', 'size' => 16]) ?>
            <span>Discover More Courses</span>
        </a>
    </div>

    <?php if (empty($bookmarks)): ?>
        <div class="card" style="padding: 3.5rem 2rem; text-align: center; border: 1px dashed var(--border-color);">
            <div style="width: 56px; height: 56px; border-radius: 50%; background: var(--surface-secondary); color: var(--text-muted); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                <?= component('icon', ['name' => 'bookmark', 'size' => 28]) ?>
            </div>
            <h3 style="font-size: 1.125rem; font-weight: 700; margin: 0 0 0.5rem 0;">No bookmarked courses yet</h3>
            <p style="color: var(--text-muted); margin: 0 0 1.5rem 0; max-width: 450px; margin-inline: auto;">
                When you find an interesting course, click the bookmark icon to save it here for later.
            </p>
            <a href="/courses" class="btn btn-primary">
                <span>Browse Course Catalog</span>
            </a>
        </div>
    <?php else: ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem;">
            <?php foreach ($bookmarks as $item): ?>
                <?php
                /** @var \App\Models\Course $course */
                $course = $item['course'];
                /** @var \App\Models\CourseBookmark $bm */
                $bm = $item['bookmark'];
                $image = $course->C_CARD_IMAGE ?: '/img/courses/default.jpg';
                ?>
                <div class="card" style="display: flex; flex-direction: column; overflow: hidden; border-radius: var(--radius-lg); border: 1px solid var(--border-color);">
                    <div style="position: relative; height: 160px; background: var(--surface-secondary); overflow: hidden;">
                        <img src="<?= e($image) ?>" alt="<?= e($course->C_NAME) ?>" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='/img/courses/default.jpg'; this.onerror=null;">
                        
                        <form action="/courses/<?= (int)$course->C_ID ?>/bookmark" method="POST" style="position: absolute; top: 0.75rem; right: 0.75rem; margin: 0;">
                            <input type="hidden" name="_csrf" value="<?= session()->csrfToken() ?>">
                            <button type="submit" class="btn btn-secondary btn-icon" style="width: 32px; height: 32px; border-radius: 50%; background: rgba(255,255,255,0.95); color: var(--primary);" title="Remove Bookmark">
                                <?= component('icon', ['name' => 'x', 'size' => 14]) ?>
                            </button>
                        </form>
                    </div>

                    <div style="padding: 1.25rem; flex: 1; display: flex; flex-direction: column;">
                        <h3 style="font-size: 1.0625rem; font-weight: 700; margin: 0 0 0.5rem 0; line-height: 1.4;">
                            <a href="/courses/<?= (int)$course->C_ID ?>" style="color: var(--text-color); text-decoration: none;">
                                <?= e($course->C_NAME) ?>
                            </a>
                        </h3>
                        <p style="color: var(--text-muted); font-size: 0.8125rem; line-height: 1.5; margin: 0 0 1rem 0; flex: 1; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            <?= e($course->C_CARD_DESCRIPTION ?: $course->C_DESCRIPTION) ?>
                        </p>

                        <div style="display: flex; align-items: center; justify-content: space-between; padding-top: 0.75rem; border-top: 1px solid var(--border-color);">
                            <span style="font-size: 0.75rem; color: var(--text-muted);">
                                Saved <?= date('M j, Y', strtotime((string)$bm->created_at)) ?>
                            </span>
                            <a href="/courses/<?= (int)$course->C_ID ?>" class="btn btn-primary btn-sm">
                                <span>View</span>
                                <?= component('icon', ['name' => 'arrow-right', 'size' => 12]) ?>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php
$slot = ob_get_clean();
include dirname(__DIR__, 2) . '/layouts/workspace.php';
