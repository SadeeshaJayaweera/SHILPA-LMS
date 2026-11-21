<?php
declare(strict_types=1);

$recommendations = $data['recommendations'] ?? [];
$pathways = $data['pathways'] ?? [];
$userId = $data['userId'] ?? 0;

ob_start();
?>
<div class="recommendations-container" style="max-width: 1200px; margin: 0 auto; padding-bottom: 3rem;">
    <!-- Top Hero Header -->
    <div style="margin-bottom: 2.5rem; display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 1.5rem;">
        <div>
            <div style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.25rem 0.75rem; background: var(--primary-50); color: var(--primary-700); border-radius: 9999px; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.75rem;">
                <?= component('icon', ['name' => 'sparkles', 'size' => 14]) ?>
                <span>Intelligent Discovery Engine</span>
            </div>
            <h1 style="font-size: 2.25rem; font-weight: 800; font-family: var(--font-heading); margin: 0 0 0.5rem 0; letter-spacing: -0.025em;">
                Personalized Recommendations
            </h1>
            <p style="color: var(--text-muted); font-size: 1.0625rem; margin: 0; max-width: 650px;">
                Hand-picked courses matching your enrollment patterns, peer group selections, and professional goals.
            </p>
        </div>

        <div style="display: flex; gap: 0.75rem; align-items: center;">
            <a href="/pathways" class="btn btn-secondary">
                <?= component('icon', ['name' => 'compass', 'size' => 16]) ?>
                <span>Career Pathways</span>
            </a>
            <a href="/my-bookmarks" class="btn btn-secondary">
                <?= component('icon', ['name' => 'bookmark', 'size' => 16]) ?>
                <span>My Saved Courses</span>
            </a>
        </div>
    </div>

    <!-- Recommendations Grid -->
    <div style="margin-bottom: 3.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0; font-family: var(--font-heading);">
                Curated for Your Profile
            </h2>
            <span style="font-size: 0.875rem; color: var(--text-muted);">
                <?= count($recommendations) ?> courses recommended
            </span>
        </div>

        <?php if (empty($recommendations)): ?>
            <div class="card" style="padding: 3.5rem 2rem; text-align: center; border: 1px dashed var(--border-color);">
                <div style="width: 56px; height: 56px; border-radius: 50%; background: var(--primary-50); color: var(--primary); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                    <?= component('icon', ['name' => 'book-open', 'size' => 28]) ?>
                </div>
                <h3 style="font-size: 1.125rem; font-weight: 700; margin: 0 0 0.5rem 0;">No recommendations yet</h3>
                <p style="color: var(--text-muted); margin: 0 0 1.5rem 0; max-width: 450px; margin-inline: auto;">
                    Start browsing our course catalog or enroll in courses to activate intelligent matching.
                </p>
                <a href="/courses" class="btn btn-primary">
                    <span>Browse All Courses</span>
                </a>
            </div>
        <?php else: ?>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 1.5rem;">
                <?php foreach ($recommendations as $item): ?>
                    <?php
                    /** @var \App\Models\Course $course */
                    $course = $item['course'];
                    $reason = $item['reason'];
                    $score = round((float)$item['score']);
                    $isBookmarked = (bool)$item['is_bookmarked'];
                    $image = $course->C_CARD_IMAGE ?: '/img/courses/default.jpg';
                    ?>
                    <div class="card course-rec-card" style="display: flex; flex-direction: column; overflow: hidden; position: relative; border-radius: var(--radius-lg); border: 1px solid var(--border-color); transition: transform 0.2s, box-shadow 0.2s;">
                        <!-- Recommendation Reason Ribbon -->
                        <div style="background: var(--surface-secondary); padding: 0.5rem 1rem; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between; gap: 0.5rem;">
                            <span style="font-size: 0.75rem; font-weight: 600; color: var(--primary-700); display: flex; align-items: center; gap: 0.375rem;">
                                <?= component('icon', ['name' => 'sparkles', 'size' => 12]) ?>
                                <?= e($reason) ?>
                            </span>
                            <span class="badge badge-success" style="font-size: 0.6875rem; font-weight: 700;">
                                <?= $score ?>% Match
                            </span>
                        </div>

                        <!-- Course Image & Bookmark -->
                        <div style="position: relative; height: 180px; background: var(--surface-secondary); overflow: hidden;">
                            <img src="<?= e($image) ?>" alt="<?= e($course->C_NAME) ?>" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='/img/courses/default.jpg'; this.onerror=null;">
                            
                            <!-- Bookmark button -->
                            <form action="/courses/<?= (int)$course->C_ID ?>/bookmark" method="POST" style="position: absolute; top: 0.75rem; right: 0.75rem; margin: 0; z-index: 2;">
                                <input type="hidden" name="_csrf" value="<?= session()->csrfToken() ?>">
                                <button type="submit" class="btn btn-secondary btn-icon" style="width: 36px; height: 36px; border-radius: 50%; background: rgba(255,255,255,0.9); backdrop-filter: blur(4px); box-shadow: 0 2px 4px rgba(0,0,0,0.1); color: <?= $isBookmarked ? 'var(--primary)' : 'var(--text-muted)' ?>;" title="<?= $isBookmarked ? 'Remove Bookmark' : 'Bookmark Course' ?>">
                                    <?= component('icon', ['name' => $isBookmarked ? 'bookmark-check' : 'bookmark', 'size' => 16]) ?>
                                </button>
                            </form>
                        </div>

                        <!-- Card Body -->
                        <div style="padding: 1.25rem; flex: 1; display: flex; flex-direction: column;">
                            <h3 style="font-size: 1.125rem; font-weight: 700; margin: 0 0 0.5rem 0; line-height: 1.4;">
                                <a href="/courses/<?= (int)$course->C_ID ?>" style="color: var(--text-color); text-decoration: none;">
                                    <?= e($course->C_NAME) ?>
                                </a>
                            </h3>

                            <p style="color: var(--text-muted); font-size: 0.875rem; line-height: 1.5; margin: 0 0 1rem 0; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; flex: 1;">
                                <?= e($course->C_CARD_DESCRIPTION ?: $course->C_DESCRIPTION) ?>
                            </p>

                            <div style="display: flex; align-items: center; justify-content: space-between; margin-top: auto; padding-top: 1rem; border-top: 1px solid var(--border-color); font-size: 0.8125rem;">
                                <div style="display: flex; align-items: center; gap: 0.5rem; color: var(--text-muted);">
                                    <?= component('icon', ['name' => 'user', 'size' => 14]) ?>
                                    <span><?= e($course->C_DONE_BY ?: 'Instructor') ?></span>
                                </div>
                                <a href="/courses/<?= (int)$course->C_ID ?>" class="btn btn-primary btn-sm">
                                    <span>View Course</span>
                                    <?= component('icon', ['name' => 'arrow-right', 'size' => 14]) ?>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Career Pathways Teaser Section -->
    <?php if (!empty($pathways)): ?>
        <div style="background: var(--surface-secondary); border-radius: var(--radius-xl); padding: 2.5rem; border: 1px solid var(--border-color);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.75rem; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <h2 style="font-size: 1.5rem; font-weight: 800; margin: 0 0 0.25rem 0; font-family: var(--font-heading);">
                        Structured Career Learning Pathways
                    </h2>
                    <p style="color: var(--text-muted); margin: 0; font-size: 0.9375rem;">
                        Step-by-step master tracks designed by industry experts to advance your engineering career.
                    </p>
                </div>
                <a href="/pathways" class="btn btn-secondary">
                    <span>Explore All Tracks</span>
                    <?= component('icon', ['name' => 'arrow-right', 'size' => 14]) ?>
                </a>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.25rem;">
                <?php foreach ($pathways as $pw): ?>
                    <div class="card" style="padding: 1.5rem; background: var(--surface); border: 1px solid var(--border-color); border-radius: var(--radius-lg); display: flex; flex-direction: column;">
                        <div style="width: 44px; height: 44px; border-radius: var(--radius-md); background: var(--primary-50); color: var(--primary); display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                            <?= component('icon', ['name' => $pw->icon ?: 'compass', 'size' => 22]) ?>
                        </div>
                        <span style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--primary); margin-bottom: 0.375rem;">
                            <?= e($pw->target_role) ?>
                        </span>
                        <h3 style="font-size: 1.125rem; font-weight: 700; margin: 0 0 0.5rem 0;">
                            <?= e($pw->title) ?>
                        </h3>
                        <p style="color: var(--text-muted); font-size: 0.875rem; margin: 0 0 1.25rem 0; line-height: 1.5; flex: 1;">
                            <?= e($pw->description) ?>
                        </p>
                        <a href="/pathways/<?= e($pw->slug) ?>" class="btn btn-secondary btn-sm" style="width: 100%; justify-content: center;">
                            <span>View Pathway</span>
                            <?= component('icon', ['name' => 'arrow-right', 'size' => 14]) ?>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.course-rec-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
}
</style>

<?php
$slot = ob_get_clean();
include dirname(__DIR__, 2) . '/layouts/workspace.php';
