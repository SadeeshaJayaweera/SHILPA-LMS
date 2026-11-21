<?php
ob_start();
?>
<!-- Hero Section -->
<section style="position: relative; overflow: hidden; padding: 6rem 0 5rem; background: radial-gradient(circle at 50% 10%, rgba(2, 132, 199, 0.18) 0%, transparent 65%);">
    <div style="max-width: 1200px; margin: 0 auto; padding: 0 1.5rem; text-align: center;">
        <div style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.35rem 0.85rem; border-radius: var(--radius-full); background: var(--primary-subtle); border: 1px solid rgba(56, 189, 248, 0.25); color: var(--primary-text); font-size: 0.8125rem; font-weight: 600; margin-bottom: 1.5rem;">
            <?= icon('zap', ['size' => 14]) ?> Open-Access Educational Platform
        </div>
        
        <h1 style="font-size: 3.75rem; font-weight: 800; line-height: 1.12; letter-spacing: -0.03em; margin-bottom: 1.5rem; color: var(--text-primary);">
            Master Modern Computing & <br>
            <span style="background: linear-gradient(135deg, var(--shilpa-blue-400) 0%, var(--shilpa-blue-600) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                Empower Your Tech Future
            </span>
        </h1>
        
        <p style="font-size: 1.1875rem; color: var(--text-muted); max-width: 720px; margin: 0 auto 2.5rem; line-height: 1.7;">
            SHILPA LMS delivers interactive, accessible, and structured IT coursework ranging from web systems and algorithmic problem solving to modern software architecture.
        </p>

        <!-- Search Bar -->
        <div style="max-width: 600px; margin: 0 auto 2.5rem;">
            <form action="/courses" method="GET" style="display: flex; gap: 0.5rem; background: var(--bg-surface); padding: 0.5rem; border-radius: var(--radius-xl); border: 1px solid var(--border-default); box-shadow: var(--shadow-lg);">
                <div style="display: flex; align-items: center; padding-left: 0.75rem; color: var(--text-tertiary);">
                    <?= icon('search', ['size' => 20]) ?>
                </div>
                <input type="text" name="q" placeholder="Search web development, algorithms, frameworks..." style="flex: 1; background: transparent; border: none; outline: none; color: var(--text-primary); font-size: 0.9375rem; padding: 0.5rem;">
                <button type="submit" class="btn btn-primary" style="padding: 0.625rem 1.25rem;">
                    Find Courses
                </button>
            </form>
        </div>

        <div style="display: flex; justify-content: center; align-items: center; gap: 2rem; flex-wrap: wrap; color: var(--text-secondary); font-size: 0.875rem; font-weight: 500;">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <?= icon('check', ['size' => 16, 'color' => 'var(--shilpa-emerald-400)']) ?> 100% Free & Open Access
            </div>
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <?= icon('check', ['size' => 16, 'color' => 'var(--shilpa-emerald-400)']) ?> Self-Paced Modules
            </div>
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <?= icon('check', ['size' => 16, 'color' => 'var(--shilpa-emerald-400)']) ?> Structured Curriculum
            </div>
        </div>
    </div>
</section>

<!-- Featured / Top Courses Section -->
<section style="padding: 4.5rem 0; border-top: 1px solid var(--border-subtle);">
    <div style="max-width: 1200px; margin: 0 auto; padding: 0 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2.5rem; flex-wrap: wrap; gap: 1rem;">
            <div>
                <div style="color: var(--primary-text); font-weight: 600; font-size: 0.8125rem; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem;">
                    Curated Excellence
                </div>
                <h2 style="font-size: 2rem;">Top Rated Courses</h2>
            </div>
            <a href="/courses?sort=top" class="btn btn-outline btn-sm">
                View All Top Courses <?= icon('chevron-right', ['size' => 14]) ?>
            </a>
        </div>

        <?php if (empty($topCourses)): ?>
            <?= component('empty-state', [
                'icon' => '🎓',
                'title' => 'No Featured Courses Yet',
                'description' => 'Courses are currently being updated by the faculty.',
            ]) ?>
        <?php else: ?>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(270px, 1fr)); gap: 1.5rem;">
                <?php foreach ($topCourses as $course): ?>
                    <a href="/courses/<?= (int)$course->C_ID ?>" style="text-decoration: none; color: inherit; display: block;">
                        <div class="card card-interactive" style="height: 100%; display: flex; flex-direction: column;">
                            <div style="height: 160px; background: var(--bg-surface-elevated); position: relative; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                                <?php if ($course->C_CARD_IMAGE): ?>
                                    <img src="<?= e($course->C_CARD_IMAGE) ?>" alt="<?= e($course->C_NAME) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                <?php else: ?>
                                    <div style="font-size: 3rem; color: var(--text-tertiary);"><?= icon('book', ['size' => 48]) ?></div>
                                <?php endif; ?>
                                <div style="position: absolute; top: 0.75rem; right: 0.75rem;">
                                    <?= component('badge', ['variant' => 'primary', 'slot' => icon('star', ['size' => 12]) . ' Top Course']) ?>
                                </div>
                            </div>
                            <div class="card-body" style="flex: 1; display: flex; flex-direction: column;">
                                <h3 style="font-size: 1.125rem; font-weight: 700; margin-bottom: 0.5rem; line-height: 1.35;">
                                    <?= e($course->C_NAME) ?>
                                </h3>
                                <p style="font-size: 0.875rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 1.25rem; flex: 1;">
                                    <?= e($course->C_CARD_DESCRIPTION ?: substr((string)$course->C_DESCRIPTION, 0, 90) . '...') ?>
                                </p>
                                <div style="display: flex; align-items: center; justify-content: space-between; font-size: 0.8125rem; color: var(--text-tertiary); padding-top: 0.75rem; border-top: 1px solid var(--border-subtle);">
                                    <span style="display: flex; align-items: center; gap: 0.35rem;">
                                        <?= icon('user', ['size' => 14]) ?> <?= e($course->C_DONE_BY ?: 'SHILPA Faculty') ?>
                                    </span>
                                    <span style="color: var(--primary-text); font-weight: 600;">Learn &rarr;</span>
                                </div>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Course Categories Section -->
<section style="padding: 4.5rem 0; background-color: var(--bg-surface-elevated); border-top: 1px solid var(--border-subtle);">
    <div style="max-width: 1200px; margin: 0 auto; padding: 0 1.5rem;">
        <div style="text-align: center; max-width: 650px; margin: 0 auto 3rem;">
            <div style="color: var(--primary-text); font-weight: 600; font-size: 0.8125rem; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem;">
                Learning Pathways
            </div>
            <h2 style="font-size: 2rem; margin-bottom: 0.75rem;">Explore Course Categories</h2>
            <p style="color: var(--text-muted); font-size: 0.9375rem;">Specialized domain streams designed to guide learners from foundations to mastery.</p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
            <a href="/courses?category=Web" style="text-decoration: none; color: inherit;">
                <div class="card card-interactive" style="padding: 1.75rem; text-align: left;">
                    <div style="width: 2.75rem; height: 2.75rem; border-radius: var(--radius-lg); background: var(--primary-subtle); color: var(--primary-text); display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                        <?= icon('zap', ['size' => 22]) ?>
                    </div>
                    <h3 style="font-size: 1.125rem; font-weight: 700; margin-bottom: 0.5rem;">Web Development</h3>
                    <p style="font-size: 0.875rem; color: var(--text-muted); line-height: 1.5;">HTML5, modern CSS architectures, JavaScript, PHP 8.2 backend services, and APIs.</p>
                </div>
            </a>

            <a href="/courses?category=Algorithms" style="text-decoration: none; color: inherit;">
                <div class="card card-interactive" style="padding: 1.75rem; text-align: left;">
                    <div style="width: 2.75rem; height: 2.75rem; border-radius: var(--radius-lg); background: var(--success-bg); color: var(--success-text); display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                        <?= icon('book', ['size' => 22]) ?>
                    </div>
                    <h3 style="font-size: 1.125rem; font-weight: 700; margin-bottom: 0.5rem;">Algorithms & Systems</h3>
                    <p style="font-size: 0.875rem; color: var(--text-muted); line-height: 1.5;">Computational thinking, data structures, optimization patterns, and system design.</p>
                </div>
            </a>

            <a href="/courses?category=Fullstack" style="text-decoration: none; color: inherit;">
                <div class="card card-interactive" style="padding: 1.75rem; text-align: left;">
                    <div style="width: 2.75rem; height: 2.75rem; border-radius: var(--radius-lg); background: var(--warning-bg); color: var(--warning-text); display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                        <?= icon('graduation-cap', ['size' => 22]) ?>
                    </div>
                    <h3 style="font-size: 1.125rem; font-weight: 700; margin-bottom: 0.5rem;">Full-Stack Engineering</h3>
                    <p style="font-size: 0.875rem; color: var(--text-muted); line-height: 1.5;">End-to-end applications, relational database design, authentication, and secure deployments.</p>
                </div>
            </a>

            <a href="/courses?category=Security" style="text-decoration: none; color: inherit;">
                <div class="card card-interactive" style="padding: 1.75rem; text-align: left;">
                    <div style="width: 2.75rem; height: 2.75rem; border-radius: var(--radius-lg); background: var(--danger-bg); color: var(--danger-text); display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                        <?= icon('shield', ['size' => 22]) ?>
                    </div>
                    <h3 style="font-size: 1.125rem; font-weight: 700; margin-bottom: 0.5rem;">Security & Reliability</h3>
                    <p style="font-size: 0.875rem; color: var(--text-muted); line-height: 1.5;">Application defense, OWASP principles, session management, and automated verification.</p>
                </div>
            </a>
        </div>
    </div>
</section>

<!-- Latest Courses Section -->
<section style="padding: 4.5rem 0; border-top: 1px solid var(--border-subtle);">
    <div style="max-width: 1200px; margin: 0 auto; padding: 0 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2.5rem; flex-wrap: wrap; gap: 1rem;">
            <div>
                <div style="color: var(--primary-text); font-weight: 600; font-size: 0.8125rem; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem;">
                    Fresh Releases
                </div>
                <h2 style="font-size: 2rem;">Recently Added Courses</h2>
            </div>
            <a href="/courses" class="btn btn-outline btn-sm">
                Browse Full Catalog <?= icon('chevron-right', ['size' => 14]) ?>
            </a>
        </div>

        <?php if (!empty($latestCourses)): ?>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(270px, 1fr)); gap: 1.5rem;">
                <?php foreach ($latestCourses as $course): ?>
                    <a href="/courses/<?= (int)$course->C_ID ?>" style="text-decoration: none; color: inherit; display: block;">
                        <div class="card card-interactive" style="height: 100%; display: flex; flex-direction: column;">
                            <div style="height: 150px; background: var(--bg-surface-elevated); position: relative; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                                <?php if ($course->C_CARD_IMAGE): ?>
                                    <img src="<?= e($course->C_CARD_IMAGE) ?>" alt="<?= e($course->C_NAME) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                <?php else: ?>
                                    <div style="font-size: 2.5rem; color: var(--text-tertiary);"><?= icon('book', ['size' => 40]) ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="card-body" style="flex: 1; display: flex; flex-direction: column;">
                                <h3 style="font-size: 1.0625rem; font-weight: 700; margin-bottom: 0.5rem; line-height: 1.35;">
                                    <?= e($course->C_NAME) ?>
                                </h3>
                                <p style="font-size: 0.8125rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 1rem; flex: 1;">
                                    <?= e($course->C_CARD_DESCRIPTION ?: substr((string)$course->C_DESCRIPTION, 0, 80) . '...') ?>
                                </p>
                                <div style="display: flex; align-items: center; justify-content: space-between; font-size: 0.75rem; color: var(--text-tertiary); padding-top: 0.75rem; border-top: 1px solid var(--border-subtle);">
                                    <span><?= e($course->C_DONE_BY ?: 'SHILPA LMS') ?></span>
                                    <span style="color: var(--primary-text); font-weight: 600;">Explore &rarr;</span>
                                </div>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Call to Action -->
<section style="padding: 5rem 0; background: radial-gradient(circle at 50% 50%, rgba(2, 132, 199, 0.12) 0%, var(--bg-surface) 100%); border-top: 1px solid var(--border-subtle); text-align: center;">
    <div style="max-width: 800px; margin: 0 auto; padding: 0 1.5rem;">
        <h2 style="font-size: 2.25rem; margin-bottom: 1rem;">Ready to Accelerate Your Learning?</h2>
        <p style="color: var(--text-muted); font-size: 1.0625rem; line-height: 1.6; margin-bottom: 2rem;">
            Join the SHILPA LMS open community. Access high-quality courses, interactive materials, and track your educational progress with zero barriers.
        </p>
        <div style="display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap;">
            <a href="/login/register.php" class="btn btn-primary btn-lg">Create Free Account</a>
            <a href="/courses" class="btn btn-secondary btn-lg">Explore All Courses</a>
        </div>
    </div>
</section>
<?php
$slot = ob_get_clean();
include dirname(__DIR__) . '/layouts/app.php';
