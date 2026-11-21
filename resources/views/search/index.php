<?php
declare(strict_types=1);

$query = $data['query'] ?? '';
$type = $data['type'] ?? null;
$results = $data['results'] ?? [];

$total = $results['total'] ?? 0;
$courses = $results['courses'] ?? [];
$lessons = $results['lessons'] ?? [];
$assignments = $results['assignments'] ?? [];
$discussions = $results['discussions'] ?? [];
$pathways = $results['pathways'] ?? [];
$commands = $results['commands'] ?? [];

ob_start();
?>
<div class="search-page-container" style="max-width: 1000px; margin: 0 auto; padding-bottom: 4rem;">
    <!-- Search Bar Hero Header -->
    <div style="margin-bottom: 2rem;">
        <h1 style="font-size: 2rem; font-weight: 800; font-family: var(--font-heading); margin: 0 0 0.5rem 0;">
            Global Search
        </h1>
        <p style="color: var(--text-muted); font-size: 1rem; margin: 0 0 1.5rem 0;">
            Instantly query courses, curriculum lessons, homework tasks, peer discussions, and system commands.
        </p>

        <!-- Search Input Form -->
        <form action="/search" method="GET" style="display: flex; gap: 0.75rem; margin: 0;">
            <div style="position: relative; flex: 1;">
                <span style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-muted); pointer-events: none; display: flex; align-items: center;">
                    <?= component('icon', ['name' => 'search', 'size' => 18]) ?>
                </span>
                <input type="text" name="q" value="<?= e($query) ?>" placeholder="Type keywords (e.g. Python, Algorithms, Assignment 1, Grades)..." autofocus class="form-input" style="width: 100%; padding-left: 2.75rem; padding-right: 4.5rem; height: 50px; font-size: 1rem; border-radius: var(--radius-lg);">
                <span style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); font-size: 0.75rem; background: var(--surface-secondary); padding: 0.25rem 0.5rem; border-radius: 4px; color: var(--text-muted); border: 1px solid var(--border-color); font-family: monospace;">
                    ⌘K
                </span>
            </div>
            <button type="submit" class="btn btn-primary" style="height: 50px; padding: 0 1.75rem; font-weight: 600;">
                <span>Search</span>
            </button>
        </form>
    </div>

    <!-- Filter Category Tabs -->
    <div style="display: flex; gap: 0.5rem; overflow-x: auto; padding-bottom: 0.5rem; margin-bottom: 2rem; border-bottom: 1px solid var(--border-color);">
        <?php
        $buildTabUrl = function(?string $t) use ($query) {
            $params = [];
            if ($query !== '') $params['q'] = $query;
            if ($t !== null) $params['type'] = $t;
            return '/search' . (!empty($params) ? '?' . http_build_query($params) : '');
        };
        ?>
        <a href="<?= $buildTabUrl(null) ?>" class="btn <?= $type === null ? 'btn-primary' : 'btn-ghost' ?> btn-sm" style="border-radius: 9999px;">
            <span>All Results (<?= $total ?>)</span>
        </a>
        <a href="<?= $buildTabUrl('courses') ?>" class="btn <?= $type === 'courses' ? 'btn-primary' : 'btn-ghost' ?> btn-sm" style="border-radius: 9999px;">
            <span>Courses (<?= count($courses) ?>)</span>
        </a>
        <a href="<?= $buildTabUrl('lessons') ?>" class="btn <?= $type === 'lessons' ? 'btn-primary' : 'btn-ghost' ?> btn-sm" style="border-radius: 9999px;">
            <span>Lessons (<?= count($lessons) ?>)</span>
        </a>
        <a href="<?= $buildTabUrl('assignments') ?>" class="btn <?= $type === 'assignments' ? 'btn-primary' : 'btn-ghost' ?> btn-sm" style="border-radius: 9999px;">
            <span>Assignments (<?= count($assignments) ?>)</span>
        </a>
        <a href="<?= $buildTabUrl('discussions') ?>" class="btn <?= $type === 'discussions' ? 'btn-primary' : 'btn-ghost' ?> btn-sm" style="border-radius: 9999px;">
            <span>Discussions (<?= count($discussions) ?>)</span>
        </a>
        <a href="<?= $buildTabUrl('pathways') ?>" class="btn <?= $type === 'pathways' ? 'btn-primary' : 'btn-ghost' ?> btn-sm" style="border-radius: 9999px;">
            <span>Pathways (<?= count($pathways) ?>)</span>
        </a>
        <a href="<?= $buildTabUrl('commands') ?>" class="btn <?= $type === 'commands' ? 'btn-primary' : 'btn-ghost' ?> btn-sm" style="border-radius: 9999px;">
            <span>Actions & Navigation (<?= count($commands) ?>)</span>
        </a>
    </div>

    <!-- Results Display -->
    <?php if ($total === 0): ?>
        <div class="card" style="padding: 4rem 2rem; text-align: center; border: 1px dashed var(--border-color);">
            <div style="width: 56px; height: 56px; border-radius: 50%; background: var(--surface-secondary); color: var(--text-muted); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                <?= component('icon', ['name' => 'search', 'size' => 28]) ?>
            </div>
            <h3 style="font-size: 1.25rem; font-weight: 700; margin: 0 0 0.5rem 0;">No matching results found</h3>
            <p style="color: var(--text-muted); margin: 0 0 1.5rem 0; max-width: 450px; margin-inline: auto;">
                <?= $query !== '' ? 'No items matched "' . e($query) . '". Try broader terms or check spelling.' : 'Enter a query above to search across the entire LMS platform.' ?>
            </p>
            <div style="display: flex; gap: 0.75rem; justify-content: center;">
                <a href="/courses" class="btn btn-secondary btn-sm">Explore Courses</a>
                <a href="/pathways" class="btn btn-secondary btn-sm">Explore Pathways</a>
            </div>
        </div>
    <?php else: ?>
        <div style="display: flex; flex-direction: column; gap: 2rem;">

            <!-- Commands / Quick Actions -->
            <?php if (!empty($commands)): ?>
                <div>
                    <h2 style="font-size: 1.125rem; font-weight: 700; margin: 0 0 1rem 0; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; font-size: 0.75rem;">
                        Platform Actions & Commands
                    </h2>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 0.75rem;">
                        <?php foreach ($commands as $cmd): ?>
                            <a href="<?= e($cmd['url']) ?>" class="card" style="padding: 1rem 1.25rem; display: flex; align-items: center; gap: 0.875rem; text-decoration: none; border-radius: var(--radius-md); border: 1px solid var(--border-color); transition: all 0.15s;">
                                <div style="width: 36px; height: 36px; border-radius: var(--radius-sm); background: var(--primary-50); color: var(--primary); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <?= component('icon', ['name' => $cmd['icon'] ?? 'command', 'size' => 18]) ?>
                                </div>
                                <div style="flex: 1; min-width: 0;">
                                    <div style="font-weight: 600; font-size: 0.9375rem; color: var(--text-color); margin-bottom: 0.125rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        <?= e($cmd['title']) ?>
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        <?= e($cmd['description'] ?? '') ?>
                                    </div>
                                </div>
                                <?= component('icon', ['name' => 'arrow-right', 'size' => 14, 'color' => 'var(--text-muted)']) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Courses Section -->
            <?php if (!empty($courses)): ?>
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                        <h2 style="font-size: 1.125rem; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                            <?= component('icon', ['name' => 'book', 'size' => 18, 'color' => 'var(--primary)']) ?>
                            <span>Courses (<?= count($courses) ?>)</span>
                        </h2>
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                        <?php foreach ($courses as $c): ?>
                            <div class="card" style="padding: 1.25rem; display: flex; gap: 1.25rem; align-items: center; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                                <div style="width: 80px; height: 60px; border-radius: var(--radius-sm); overflow: hidden; background: var(--surface-secondary); flex-shrink: 0;">
                                    <img src="<?= e($c['image']) ?>" alt="<?= e($c['title']) ?>" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='/img/courses/default.jpg'; this.onerror=null;">
                                </div>
                                <div style="flex: 1; min-width: 0;">
                                    <h3 style="font-size: 1.0625rem; font-weight: 700; margin: 0 0 0.25rem 0;">
                                        <a href="<?= e($c['url']) ?>" style="color: var(--text-color); text-decoration: none;">
                                            <?= e($c['title']) ?>
                                        </a>
                                    </h3>
                                    <p style="font-size: 0.8125rem; color: var(--text-muted); margin: 0 0 0.375rem 0;">
                                        <?= e($c['snippet']) ?>
                                    </p>
                                    <span style="font-size: 0.75rem; color: var(--text-muted);">Instructor: <?= e($c['instructor']) ?></span>
                                </div>
                                <a href="<?= e($c['url']) ?>" class="btn btn-secondary btn-sm" style="flex-shrink: 0;">
                                    <span>View</span>
                                    <?= component('icon', ['name' => 'arrow-right', 'size' => 12]) ?>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Lessons Section -->
            <?php if (!empty($lessons)): ?>
                <div>
                    <h2 style="font-size: 1.125rem; font-weight: 700; margin: 0 0 1rem 0; display: flex; align-items: center; gap: 0.5rem;">
                        <?= component('icon', ['name' => 'play-circle', 'size' => 18, 'color' => 'var(--primary)']) ?>
                        <span>Curriculum Lessons (<?= count($lessons) ?>)</span>
                    </h2>
                    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                        <?php foreach ($lessons as $l): ?>
                            <div class="card" style="padding: 1rem 1.25rem; display: flex; justify-content: space-between; align-items: center; gap: 1rem; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                                <div style="flex: 1; min-width: 0;">
                                    <div style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.25rem;">
                                        Course: <span style="font-weight: 600; color: var(--text-color);"><?= e($l['course_title']) ?></span>
                                    </div>
                                    <h3 style="font-size: 1rem; font-weight: 700; margin: 0 0 0.25rem 0;">
                                        <a href="<?= e($l['url']) ?>" style="color: var(--text-color); text-decoration: none;">
                                            <?= e($l['title']) ?>
                                        </a>
                                    </h3>
                                    <p style="font-size: 0.8125rem; color: var(--text-muted); margin: 0;">
                                        <?= e($l['snippet']) ?>
                                    </p>
                                </div>
                                <a href="<?= e($l['url']) ?>" class="btn btn-primary btn-sm" style="flex-shrink: 0;">
                                    <span>Study Lesson</span>
                                    <?= component('icon', ['name' => 'arrow-right', 'size' => 12]) ?>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Assignments Section -->
            <?php if (!empty($assignments)): ?>
                <div>
                    <h2 style="font-size: 1.125rem; font-weight: 700; margin: 0 0 1rem 0; display: flex; align-items: center; gap: 0.5rem;">
                        <?= component('icon', ['name' => 'clipboard', 'size' => 18, 'color' => 'var(--primary)']) ?>
                        <span>Assignments & Tasks (<?= count($assignments) ?>)</span>
                    </h2>
                    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                        <?php foreach ($assignments as $a): ?>
                            <div class="card" style="padding: 1rem 1.25rem; display: flex; justify-content: space-between; align-items: center; gap: 1rem; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                                <div style="flex: 1; min-width: 0;">
                                    <div style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.25rem;">
                                        Course: <span style="font-weight: 600; color: var(--text-color);"><?= e($a['course_title']) ?></span>
                                    </div>
                                    <h3 style="font-size: 1rem; font-weight: 700; margin: 0 0 0.25rem 0;">
                                        <a href="<?= e($a['url']) ?>" style="color: var(--text-color); text-decoration: none;">
                                            <?= e($a['title']) ?>
                                        </a>
                                    </h3>
                                    <p style="font-size: 0.8125rem; color: var(--text-muted); margin: 0;">
                                        <?= e($a['snippet']) ?>
                                    </p>
                                </div>
                                <a href="<?= e($a['url']) ?>" class="btn btn-secondary btn-sm" style="flex-shrink: 0;">
                                    <span>Open Assignment</span>
                                    <?= component('icon', ['name' => 'arrow-right', 'size' => 12]) ?>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Pathways Section -->
            <?php if (!empty($pathways)): ?>
                <div>
                    <h2 style="font-size: 1.125rem; font-weight: 700; margin: 0 0 1rem 0; display: flex; align-items: center; gap: 0.5rem;">
                        <?= component('icon', ['name' => 'compass', 'size' => 18, 'color' => 'var(--primary)']) ?>
                        <span>Career Pathways (<?= count($pathways) ?>)</span>
                    </h2>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem;">
                        <?php foreach ($pathways as $pw): ?>
                            <div class="card" style="padding: 1.25rem; border-radius: var(--radius-md); border: 1px solid var(--border-color); display: flex; flex-direction: column;">
                                <div style="font-size: 0.6875rem; font-weight: 700; text-transform: uppercase; color: var(--primary); margin-bottom: 0.25rem;">
                                    <?= e($pw['target_role']) ?>
                                </div>
                                <h3 style="font-size: 1.0625rem; font-weight: 700; margin: 0 0 0.5rem 0;">
                                    <a href="<?= e($pw['url']) ?>" style="color: var(--text-color); text-decoration: none;">
                                        <?= e($pw['title']) ?>
                                    </a>
                                </h3>
                                <p style="font-size: 0.8125rem; color: var(--text-muted); margin: 0 0 1rem 0; flex: 1;">
                                    <?= e($pw['snippet']) ?>
                                </p>
                                <a href="<?= e($pw['url']) ?>" class="btn btn-secondary btn-sm" style="width: 100%; justify-content: center;">
                                    <span>Explore Track</span>
                                    <?= component('icon', ['name' => 'arrow-right', 'size' => 12]) ?>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Discussions Section -->
            <?php if (!empty($discussions)): ?>
                <div>
                    <h2 style="font-size: 1.125rem; font-weight: 700; margin: 0 0 1rem 0; display: flex; align-items: center; gap: 0.5rem;">
                        <?= component('icon', ['name' => 'message-square', 'size' => 18, 'color' => 'var(--primary)']) ?>
                        <span>Peer Discussions (<?= count($discussions) ?>)</span>
                    </h2>
                    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                        <?php foreach ($discussions as $d): ?>
                            <div class="card" style="padding: 1rem 1.25rem; display: flex; justify-content: space-between; align-items: center; gap: 1rem; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                                <div style="flex: 1; min-width: 0;">
                                    <div style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.25rem;">
                                        Course: <span style="font-weight: 600; color: var(--text-color);"><?= e($d['course_title']) ?></span> • <?= $d['replies_count'] ?> replies
                                    </div>
                                    <h3 style="font-size: 1rem; font-weight: 700; margin: 0 0 0.25rem 0;">
                                        <a href="<?= e($d['url']) ?>" style="color: var(--text-color); text-decoration: none;">
                                            <?= e($d['title']) ?>
                                        </a>
                                    </h3>
                                    <p style="font-size: 0.8125rem; color: var(--text-muted); margin: 0;">
                                        <?= e($d['snippet']) ?>
                                    </p>
                                </div>
                                <a href="<?= e($d['url']) ?>" class="btn btn-secondary btn-sm" style="flex-shrink: 0;">
                                    <span>Join Thread</span>
                                    <?= component('icon', ['name' => 'arrow-right', 'size' => 12]) ?>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    <?php endif; ?>
</div>

<?php
$slot = ob_get_clean();
include dirname(__DIR__) . '/layouts/workspace.php';
