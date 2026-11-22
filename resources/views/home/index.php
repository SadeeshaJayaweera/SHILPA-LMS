<?php
ob_start();
?>
<!-- Ambient Hero Section -->
<section style="position: relative; overflow: hidden; padding: 4.5rem 0 3.5rem; background: radial-gradient(1200px circle at 50% -10%, rgba(2, 132, 199, 0.25), transparent 70%), radial-gradient(800px circle at 85% 20%, rgba(139, 92, 246, 0.15), transparent 60%), var(--bg-canvas);">
    <div style="max-width: 1240px; margin: 0 auto; padding: 0 1.5rem; text-align: center;">
        
        <!-- Announcement Pill -->
        <div style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.35rem 0.95rem; border-radius: var(--radius-full); background: rgba(56, 189, 248, 0.12); border: 1px solid rgba(56, 189, 248, 0.3); color: var(--primary-text); font-size: 0.8125rem; font-weight: 600; margin-bottom: 1.25rem; backdrop-filter: blur(8px);">
            <?= icon('zap', ['size' => 14]) ?> <span>SHILPA LMS 2.4 Enterprise &bull; Modular Architecture &amp; Interactive Workspaces</span>
        </div>
        
        <!-- Hero Headline -->
        <h1 style="font-size: 3.5rem; font-weight: 800; line-height: 1.15; letter-spacing: -0.03em; margin-bottom: 1.25rem; color: var(--text-primary); font-family: var(--font-heading);">
            Master Modern Computing &amp; <br>
            <span style="background: linear-gradient(135deg, #38bdf8 0%, #818cf8 50%, #c084fc 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                Empower Your Tech Future
            </span>
        </h1>
        
        <!-- Hero Subtitle -->
        <p style="font-size: 1.125rem; color: var(--text-muted); max-width: 760px; margin: 0 auto 2rem; line-height: 1.65;">
            An enterprise-grade educational ecosystem featuring interactive course workspaces, real-time code environments, cryptographic audit logging, and automated institutional governance.
        </p>

        <!-- CTA & Quick Actions -->
        <div style="display: flex; justify-content: center; align-items: center; gap: 1rem; flex-wrap: wrap; margin-bottom: 2rem;">
            <a href="/courses" class="btn btn-primary btn-lg" style="box-shadow: 0 4px 20px rgba(2, 132, 199, 0.4);">
                <?= icon('book', ['size' => 18]) ?>
                <span>Explore 60+ Courses</span>
                <?= icon('chevron-right', ['size' => 16]) ?>
            </a>
            <a href="/workspace" class="btn btn-secondary btn-lg" style="background: var(--bg-surface-glass); backdrop-filter: blur(10px); border: 1px solid var(--border-default);">
                <?= icon('play', ['size' => 18]) ?>
                <span>Launch Student Workspace</span>
            </a>
            <a href="/login" class="btn btn-outline btn-lg">
                <?= icon('shield', ['size' => 18]) ?>
                <span>Institutional Sign In</span>
            </a>
        </div>

        <!-- Inline Search Bar -->
        <div style="max-width: 620px; margin: 0 auto 2.5rem;">
            <form action="/courses" method="GET" style="display: flex; gap: 0.5rem; background: var(--bg-surface-glass); padding: 0.4rem 0.5rem 0.4rem 1rem; border-radius: var(--radius-xl); border: 1px solid var(--border-default); box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.5); backdrop-filter: blur(16px);">
                <div style="display: flex; align-items: center; color: var(--text-tertiary);">
                    <?= icon('search', ['size' => 18]) ?>
                </div>
                <input type="text" name="q" placeholder="Search full-stack web, algorithms, Kubernetes, AI..." style="flex: 1; background: transparent; border: none; outline: none; color: var(--text-primary); font-size: 0.9375rem; padding: 0.5rem;">
                <button type="submit" class="btn btn-primary" style="padding: 0.5rem 1.25rem;">
                    Find Courses
                </button>
            </form>
        </div>

        <!-- Trust & Value Proposition Pills -->
        <div style="display: flex; justify-content: center; align-items: center; gap: 1.75rem; flex-wrap: wrap; color: var(--text-secondary); font-size: 0.8125rem; font-weight: 500; margin-bottom: 3rem;">
            <div style="display: flex; align-items: center; gap: 0.4rem;">
                <?= icon('check', ['size' => 16, 'color' => 'var(--shilpa-emerald-400)']) ?> 100% Free &amp; Open Access
            </div>
            <div style="display: flex; align-items: center; gap: 0.4rem;">
                <?= icon('check', ['size' => 16, 'color' => 'var(--shilpa-emerald-400)']) ?> Full-Stack PHP 8.2 &amp; TypeScript
            </div>
            <div style="display: flex; align-items: center; gap: 0.4rem;">
                <?= icon('check', ['size' => 16, 'color' => 'var(--shilpa-emerald-400)']) ?> Zero-Trust RBAC &amp; ASVS Security
            </div>
            <div style="display: flex; align-items: center; gap: 0.4rem;">
                <?= icon('check', ['size' => 16, 'color' => 'var(--shilpa-emerald-400)']) ?> Real-Time Learning Analytics
            </div>
        </div>

        <!-- Product Preview Window Mockup (Linear / Vercel Aesthetic) -->
        <div style="background: rgba(14, 21, 38, 0.85); border: 1px solid rgba(56, 189, 248, 0.25); border-radius: var(--radius-2xl); box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.8), 0 0 40px rgba(2, 132, 199, 0.15); overflow: hidden; backdrop-filter: blur(20px); text-align: left; margin-bottom: 4rem;">
            <!-- Window Titlebar -->
            <div style="background: rgba(10, 15, 29, 0.95); padding: 0.875rem 1.25rem; border-bottom: 1px solid var(--border-subtle); display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <span style="width: 11px; height: 11px; border-radius: 50%; background: #ef4444; display: inline-block;"></span>
                    <span style="width: 11px; height: 11px; border-radius: 50%; background: #f59e0b; display: inline-block;"></span>
                    <span style="width: 11px; height: 11px; border-radius: 50%; background: #10b981; display: inline-block;"></span>
                    <span style="font-size: 0.75rem; color: var(--text-muted); margin-left: 0.75rem; font-family: var(--font-mono);">shilpa-lms.workspace &bull; Full-Stack Web Engineering &bull; Module 1.2</span>
                </div>
                <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.75rem; color: #34d399; font-family: var(--font-mono);">
                    <span style="width: 7px; height: 7px; border-radius: 50%; background: #34d399;"></span>
                    <span>152/152 Tests Passing (100%)</span>
                </div>
            </div>

            <!-- Window Body (3 Columns) -->
            <div style="display: grid; grid-template-columns: 240px 1fr 280px; min-height: 280px; background: var(--bg-surface);">
                <!-- Left Syllabus Tree -->
                <div style="border-right: 1px solid var(--border-subtle); padding: 1.25rem 1rem; background: rgba(7, 11, 20, 0.5);">
                    <div style="font-size: 0.6875rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted); letter-spacing: 0.05em; margin-bottom: 0.75rem;">
                        Curriculum Syllabus
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 0.5rem; font-size: 0.8125rem;">
                        <div style="padding: 0.5rem 0.625rem; border-radius: 6px; background: rgba(16, 185, 129, 0.12); color: #34d399; display: flex; align-items: center; gap: 0.5rem;">
                            <?= icon('check', ['size' => 14, 'color' => '#34d399']) ?> <span>1.1 Architecture Setup</span>
                        </div>
                        <div style="padding: 0.5rem 0.625rem; border-radius: 6px; background: var(--primary-subtle); color: var(--primary-text); font-weight: 600; display: flex; align-items: center; gap: 0.5rem;">
                            <?= icon('play', ['size' => 14, 'color' => 'var(--primary-text)']) ?> <span>1.2 Decoupled MVC</span>
                        </div>
                        <div style="padding: 0.5rem 0.625rem; border-radius: 6px; color: var(--text-muted); display: flex; align-items: center; gap: 0.5rem;">
                            <?= icon('clock', ['size' => 14]) ?> <span>1.3 Query Builders</span>
                        </div>
                        <div style="padding: 0.5rem 0.625rem; border-radius: 6px; color: var(--text-muted); display: flex; align-items: center; gap: 0.5rem;">
                            <?= icon('shield', ['size' => 14]) ?> <span>2.1 Zero-Trust Defense</span>
                        </div>
                    </div>
                </div>

                <!-- Center Code / Architecture Terminal -->
                <div style="padding: 1.5rem; font-family: var(--font-mono); font-size: 0.875rem; line-height: 1.7; color: var(--text-secondary); overflow-x: auto; background: rgba(6, 9, 17, 0.8);">
                    <div style="color: #64748b;">// Modern Decoupled Controller with Dependency Injection</div>
                    <div><span style="color: #38bdf8;">namespace</span> App\Controllers;</div>
                    <br>
                    <div><span style="color: #f43f5e;">readonly class</span> <span style="color: #a78bfa; font-weight: 700;">CourseWorkspaceController</span> {</div>
                    <div style="padding-left: 1.5rem;">
                        <span style="color: #38bdf8;">public function</span> <span style="color: #34d399;">show</span>(Request <span style="color: #f8fafc;">$request</span>, <span style="color: #f43f5e;">int</span> <span style="color: #f8fafc;">$id</span>): Response {
                    </div>
                    <div style="padding-left: 3rem;">
                        <span style="color: #f8fafc;">$course</span> = Course::<span style="color: #fbbf24;">findOrFail</span>($id);
                    </div>
                    <div style="padding-left: 3rem;">
                        <span style="color: #f8fafc;">$audit</span> = SecurityAuditLog::<span style="color: #34d399;">verifyChainIntegrity</span>();
                    </div>
                    <div style="padding-left: 3rem; color: #38bdf8;">
                        return <span style="color: #f8fafc;">$this</span>-><span style="color: #34d399;">view</span>(<span style="color: #a7f3d0;">'courses.workspace.show'</span>, compact(<span style="color: #a7f3d0;">'course'</span>, <span style="color: #a7f3d0;">'audit'</span>));
                    </div>
                    <div style="padding-left: 1.5rem;">}</div>
                    <div>}</div>
                </div>

                <!-- Right Student Progress & AI Widget -->
                <div style="border-left: 1px solid var(--border-subtle); padding: 1.25rem; display: flex; flex-direction: column; gap: 1rem; background: rgba(7, 11, 20, 0.5);">
                    <div>
                        <div style="display: flex; justify-content: space-between; font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.25rem;">
                            <span>Course Progress</span>
                            <span style="color: #34d399; font-weight: 700;">68%</span>
                        </div>
                        <div style="width: 100%; height: 6px; border-radius: 3px; background: var(--bg-surface-elevated); overflow: hidden;">
                            <div style="width: 68%; height: 100%; background: linear-gradient(90deg, #38bdf8, #10b981);"></div>
                        </div>
                    </div>

                    <div style="padding: 0.75rem; border-radius: 8px; background: rgba(139, 92, 246, 0.12); border: 1px solid rgba(139, 92, 246, 0.25);">
                        <div style="font-size: 0.6875rem; font-weight: 700; color: #c084fc; text-transform: uppercase; margin-bottom: 0.25rem;">
                            <?= icon('zap', ['size' => 12]) ?> AI Learning Tutor
                        </div>
                        <div style="font-size: 0.75rem; color: var(--text-primary); line-height: 1.4;">
                            "Great job on MVC decoupling. Ready to test indexing on SQLite &amp; MySQL?"
                        </div>
                    </div>

                    <div style="padding: 0.75rem; border-radius: 8px; background: var(--bg-surface-elevated); border: 1px solid var(--border-subtle);">
                        <div style="font-size: 0.6875rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.25rem;">
                            Academic Metric
                        </div>
                        <div style="font-size: 1.125rem; font-weight: 800; color: var(--text-primary);">
                            3.86 <span style="font-size: 0.75rem; color: #34d399; font-weight: 600;">First Class</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<!-- Live Platform Metrics Banner -->
<section style="padding: 2.5rem 0; background: var(--bg-surface-elevated); border-top: 1px solid var(--border-subtle); border-bottom: 1px solid var(--border-subtle);">
    <div style="max-width: 1200px; margin: 0 auto; padding: 0 1.5rem;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem; text-align: center;">
            <div class="card" style="padding: 1.25rem; border-color: var(--border-default);">
                <div style="font-size: 2.25rem; font-weight: 800; color: var(--primary-text); font-family: var(--font-heading); margin-bottom: 0.25rem;">
                    60+
                </div>
                <div style="font-size: 0.8125rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">
                    Accredited IT Courses
                </div>
            </div>

            <div class="card" style="padding: 1.25rem; border-color: var(--border-default);">
                <div style="font-size: 2.25rem; font-weight: 800; color: #34d399; font-family: var(--font-heading); margin-bottom: 0.25rem;">
                    78+
                </div>
                <div style="font-size: 0.8125rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">
                    Scholars &amp; Faculty
                </div>
            </div>

            <div class="card" style="padding: 1.25rem; border-color: var(--border-default);">
                <div style="font-size: 2.25rem; font-weight: 800; color: #fbbf24; font-family: var(--font-heading); margin-bottom: 0.25rem;">
                    100%
                </div>
                <div style="font-size: 0.8125rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">
                    Verified Uptime &amp; Health
                </div>
            </div>

            <div class="card" style="padding: 1.25rem; border-color: var(--border-default);">
                <div style="font-size: 2.25rem; font-weight: 800; color: #a855f7; font-family: var(--font-heading); margin-bottom: 0.25rem;">
                    &lt; 25ms
                </div>
                <div style="font-size: 0.8125rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">
                    P99 Fast Database Latency
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured / Top Courses Section -->
<section style="padding: 4.5rem 0;">
    <div style="max-width: 1200px; margin: 0 auto; padding: 0 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2.5rem; flex-wrap: wrap; gap: 1rem;">
            <div>
                <div style="color: var(--primary-text); font-weight: 600; font-size: 0.8125rem; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem;">
                    Curated Excellence
                </div>
                <h2 style="font-size: 2.25rem; font-family: var(--font-heading); font-weight: 800;">Top Rated Courses</h2>
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
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.75rem;">
                <?php foreach (array_slice($topCourses, 0, 4) as $course): 
                    $cover = $course->C_CARD_IMAGE ?: '/img/courses/fullstack.svg';
                    if (!str_starts_with($cover, 'http') && !str_starts_with($cover, '/')) {
                        $cover = '/' . $cover;
                    }
                ?>
                    <a href="/courses/<?= (int)$course->C_ID ?>" style="text-decoration: none; color: inherit; display: block;">
                        <div class="card card-interactive" style="height: 100%; display: flex; flex-direction: column; border-color: var(--border-default);">
                            <div style="height: 170px; background: var(--bg-surface-elevated); position: relative; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                                <img src="<?= e($cover) ?>" alt="<?= e($course->C_NAME) ?>" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.onerror=null; this.src='/img/courses/fullstack.svg';">
                                <div style="position: absolute; top: 0.75rem; right: 0.75rem;">
                                    <?= component('badge', ['variant' => 'primary', 'slot' => icon('star', ['size' => 12]) . ' Top Course']) ?>
                                </div>
                            </div>
                            <div class="card-body" style="flex: 1; display: flex; flex-direction: column;">
                                <h3 style="font-size: 1.125rem; font-weight: 700; margin-bottom: 0.5rem; line-height: 1.35; font-family: var(--font-heading);">
                                    <?= e($course->C_NAME) ?>
                                </h3>
                                <p style="font-size: 0.875rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 1.25rem; flex: 1;">
                                    <?= e($course->C_CARD_DESCRIPTION ?: substr((string)$course->C_DESCRIPTION, 0, 95) . '...') ?>
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
            <h2 style="font-size: 2.25rem; margin-bottom: 0.75rem; font-family: var(--font-heading); font-weight: 800;">Explore Course Categories</h2>
            <p style="color: var(--text-muted); font-size: 0.9375rem;">Structured curriculum tracks designed to guide learners from foundations to mastery.</p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.5rem;">
            <a href="/courses?category=Software" style="text-decoration: none; color: inherit;">
                <div class="card card-interactive" style="padding: 1.75rem; text-align: left; border-color: var(--border-default);">
                    <div style="width: 2.75rem; height: 2.75rem; border-radius: var(--radius-lg); background: var(--primary-subtle); color: var(--primary-text); display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                        <?= icon('zap', ['size' => 22]) ?>
                    </div>
                    <h3 style="font-size: 1.125rem; font-weight: 700; margin-bottom: 0.5rem; font-family: var(--font-heading);">Software Engineering</h3>
                    <p style="font-size: 0.875rem; color: var(--text-muted); line-height: 1.5;">Clean MVC architecture, SOLID principles, design patterns, and PHP 8.2+ backend services.</p>
                </div>
            </a>

            <a href="/courses?category=Machine" style="text-decoration: none; color: inherit;">
                <div class="card card-interactive" style="padding: 1.75rem; text-align: left; border-color: var(--border-default);">
                    <div style="width: 2.75rem; height: 2.75rem; border-radius: var(--radius-lg); background: rgba(139, 92, 246, 0.15); color: #c084fc; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                        <?= icon('award', ['size' => 22]) ?>
                    </div>
                    <h3 style="font-size: 1.125rem; font-weight: 700; margin-bottom: 0.5rem; font-family: var(--font-heading);">AI &amp; Data Systems</h3>
                    <p style="font-size: 0.875rem; color: var(--text-muted); line-height: 1.5;">Machine learning pipelines, LLM orchestration, vector search, and model serving at scale.</p>
                </div>
            </a>

            <a href="/courses?category=Cloud" style="text-decoration: none; color: inherit;">
                <div class="card card-interactive" style="padding: 1.75rem; text-align: left; border-color: var(--border-default);">
                    <div style="width: 2.75rem; height: 2.75rem; border-radius: var(--radius-lg); background: var(--success-bg); color: var(--success-text); display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                        <?= icon('book', ['size' => 22]) ?>
                    </div>
                    <h3 style="font-size: 1.125rem; font-weight: 700; margin-bottom: 0.5rem; font-family: var(--font-heading);">Cloud &amp; DevOps</h3>
                    <p style="font-size: 0.875rem; color: var(--text-muted); line-height: 1.5;">Docker containerization, Kubernetes orchestration, CI/CD automation, and zero-downtime releases.</p>
                </div>
            </a>

            <a href="/courses?category=Cybersecurity" style="text-decoration: none; color: inherit;">
                <div class="card card-interactive" style="padding: 1.75rem; text-align: left; border-color: var(--border-default);">
                    <div style="width: 2.75rem; height: 2.75rem; border-radius: var(--radius-lg); background: var(--danger-bg); color: var(--danger-text); display: flex; align-items: center; justify-content: center; margin-bottom: 1rem;">
                        <?= icon('shield', ['size' => 22]) ?>
                    </div>
                    <h3 style="font-size: 1.125rem; font-weight: 700; margin-bottom: 0.5rem; font-family: var(--font-heading);">Cybersecurity &amp; Defense</h3>
                    <p style="font-size: 0.875rem; color: var(--text-muted); line-height: 1.5;">Zero-trust authentication, OWASP Top 10 prevention, and HMAC cryptographic audit chains.</p>
                </div>
            </a>
        </div>
    </div>
</section>

<!-- Call to Action Banner -->
<section style="padding: 5rem 0; background: radial-gradient(circle at 50% 50%, rgba(2, 132, 199, 0.18) 0%, var(--bg-surface) 100%); border-top: 1px solid var(--border-subtle); text-align: center;">
    <div style="max-width: 800px; margin: 0 auto; padding: 0 1.5rem;">
        <h2 style="font-size: 2.5rem; margin-bottom: 1rem; font-family: var(--font-heading); font-weight: 800;">Ready to Accelerate Your Learning?</h2>
        <p style="color: var(--text-muted); font-size: 1.0625rem; line-height: 1.6; margin-bottom: 2rem;">
            Join the SHILPA LMS open community. Access high-quality courses, interactive materials, and track your educational progress with zero barriers.
        </p>
        <div style="display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap;">
            <a href="/register" class="btn btn-primary btn-lg" style="box-shadow: 0 4px 20px rgba(2, 132, 199, 0.4);">
                <?= icon('user', ['size' => 18]) ?>
                <span>Create Free Student Account</span>
            </a>
            <a href="/courses" class="btn btn-secondary btn-lg">
                <span>Explore All Courses</span>
            </a>
        </div>
    </div>
</section>
<?php
$slot = ob_get_clean();
include dirname(__DIR__) . '/layouts/app.php';