<?php
ob_start();
?>
<div style="max-width: 1200px; margin: 0 auto; padding: 3rem 1.5rem;">
    <!-- Page Header -->
    <div style="margin-bottom: 3.5rem;">
        <div style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.25rem 0.75rem; border-radius: var(--radius-full); background: var(--primary-subtle); color: var(--primary-text); font-size: 0.8125rem; font-weight: 600; margin-bottom: 1rem;">
            <?= icon('zap', ['size' => 14]) ?> SHILPA Design System v2.0
        </div>
        <h1 style="font-size: 2.75rem; margin-bottom: 0.75rem;">Premium Visual Foundation & Components</h1>
        <p style="font-size: 1.125rem; color: var(--text-muted); max-width: 700px;">
            A unified design token and component library engineered for high visual fidelity, seamless dark/light mode switching, accessible contrast (WCAG 2.2 AA), and consistent SaaS learning workflows.
        </p>
    </div>

    <!-- 1. Color Tokens -->
    <section style="margin-bottom: 4rem;">
        <h2 style="font-size: 1.5rem; margin-bottom: 1.5rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border-subtle);">
            1. Color System & Semantic Palette
        </h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem;">
            <div style="background: var(--bg-surface); border: 1px solid var(--border-default); border-radius: var(--radius-lg); padding: 1.25rem;">
                <div style="height: 3rem; background: var(--primary-default); border-radius: var(--radius-sm); margin-bottom: 0.75rem;"></div>
                <div style="font-weight: 600; font-size: 0.875rem;">Primary Brand</div>
                <div style="font-size: 0.75rem; color: var(--text-muted); font-family: var(--font-mono);">#0284c7 (Sky 600)</div>
            </div>
            <div style="background: var(--bg-surface); border: 1px solid var(--border-default); border-radius: var(--radius-lg); padding: 1.25rem;">
                <div style="height: 3rem; background: var(--shilpa-blue-400); border-radius: var(--radius-sm); margin-bottom: 0.75rem;"></div>
                <div style="font-weight: 600; font-size: 0.875rem;">Electric Accent</div>
                <div style="font-size: 0.75rem; color: var(--text-muted); font-family: var(--font-mono);">#38bdf8 (Sky 400)</div>
            </div>
            <div style="background: var(--bg-surface); border: 1px solid var(--border-default); border-radius: var(--radius-lg); padding: 1.25rem;">
                <div style="height: 3rem; background: var(--shilpa-emerald-500); border-radius: var(--radius-sm); margin-bottom: 0.75rem;"></div>
                <div style="font-weight: 600; font-size: 0.875rem;">Success Emerald</div>
                <div style="font-size: 0.75rem; color: var(--text-muted); font-family: var(--font-mono);">#10b981 (Emerald)</div>
            </div>
            <div style="background: var(--bg-surface); border: 1px solid var(--border-default); border-radius: var(--radius-lg); padding: 1.25rem;">
                <div style="height: 3rem; background: var(--shilpa-amber-500); border-radius: var(--radius-sm); margin-bottom: 0.75rem;"></div>
                <div style="font-weight: 600; font-size: 0.875rem;">Warning Amber</div>
                <div style="font-size: 0.75rem; color: var(--text-muted); font-family: var(--font-mono);">#f59e0b (Amber)</div>
            </div>
            <div style="background: var(--bg-surface); border: 1px solid var(--border-default); border-radius: var(--radius-lg); padding: 1.25rem;">
                <div style="height: 3rem; background: var(--shilpa-rose-500); border-radius: var(--radius-sm); margin-bottom: 0.75rem;"></div>
                <div style="font-weight: 600; font-size: 0.875rem;">Danger Rose</div>
                <div style="font-size: 0.75rem; color: var(--text-muted); font-family: var(--font-mono);">#ef4444 (Rose)</div>
            </div>
        </div>
    </section>

    <!-- 2. Buttons & Actions -->
    <section style="margin-bottom: 4rem;">
        <h2 style="font-size: 1.5rem; margin-bottom: 1.5rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border-subtle);">
            2. Button Matrix & Interactive Controls
        </h2>
        <div style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: center; margin-bottom: 1.5rem;">
            <?= component('button', ['variant' => 'primary', 'slot' => icon('play', ['size' => 16]) . ' Primary Button']) ?>
            <?= component('button', ['variant' => 'secondary', 'slot' => 'Secondary Action']) ?>
            <?= component('button', ['variant' => 'outline', 'slot' => 'Outline Button']) ?>
            <?= component('button', ['variant' => 'ghost', 'slot' => 'Ghost Button']) ?>
            <?= component('button', ['variant' => 'danger', 'slot' => 'Danger Action']) ?>
            <?= component('button', ['variant' => 'primary', 'disabled' => true, 'slot' => 'Disabled State']) ?>
        </div>
        <div style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: center;">
            <?= component('button', ['variant' => 'primary', 'size' => 'sm', 'slot' => 'Small Button']) ?>
            <?= component('button', ['variant' => 'primary', 'size' => 'md', 'slot' => 'Medium (Default)']) ?>
            <?= component('button', ['variant' => 'primary', 'size' => 'lg', 'slot' => 'Large Hero Button']) ?>
            <button type="button" class="btn btn-primary" onclick="openModal('demoModal')">
                <?= icon('shield', ['size' => 16]) ?> Open Live Modal Dialog
            </button>
        </div>
    </section>

    <!-- 3. Form Controls & Validation -->
    <section style="margin-bottom: 4rem;">
        <h2 style="font-size: 1.5rem; margin-bottom: 1.5rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border-subtle);">
            3. Form Inputs, Validation & Feedback
        </h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem;">
            <div style="background: var(--bg-surface); padding: 1.5rem; border-radius: var(--radius-xl); border: 1px solid var(--border-subtle);">
                <?= component('input', [
                    'label' => 'Full Name',
                    'name' => 'fullname',
                    'placeholder' => 'e.g. Sadeesha Jayaweera',
                    'hint' => 'Enter your institutional or formal legal name.',
                    'required' => true,
                ]) ?>
                <?= component('input', [
                    'label' => 'Academic Email',
                    'name' => 'email',
                    'type' => 'email',
                    'placeholder' => 'student@shilpa.lk',
                    'value' => 'invalid-email-format',
                    'error' => 'Please provide a valid institutional email address.',
                    'required' => true,
                ]) ?>
            </div>
            <div style="background: var(--bg-surface); padding: 1.5rem; border-radius: var(--radius-xl); border: 1px solid var(--border-subtle);">
                <div class="form-group">
                    <label class="form-label">Course Category</label>
                    <select class="form-select">
                        <option>Web Engineering & Architecture</option>
                        <option>Algorithms & Data Structures</option>
                        <option>Cloud Infrastructure & Security</option>
                        <option>Artificial Intelligence & Data</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Short Description</label>
                    <textarea class="form-textarea" rows="3" placeholder="Brief executive summary of module outcomes..."></textarea>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. Badges, Tags & Alerts -->
    <section style="margin-bottom: 4rem;">
        <h2 style="font-size: 1.5rem; margin-bottom: 1.5rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border-subtle);">
            4. Badges, Tags & System Alerts
        </h2>
        <div style="display: flex; flex-wrap: wrap; gap: 0.75rem; margin-bottom: 1.5rem;">
            <?= component('badge', ['variant' => 'primary', 'slot' => icon('zap', ['size' => 12]) . ' Active Course']) ?>
            <?= component('badge', ['variant' => 'success', 'slot' => icon('check', ['size' => 12]) . ' Verified Grade']) ?>
            <?= component('badge', ['variant' => 'warning', 'slot' => icon('clock', ['size' => 12]) . ' Deadline Pending']) ?>
            <?= component('badge', ['variant' => 'danger', 'slot' => icon('alert', ['size' => 12]) . ' Action Required']) ?>
            <?= component('badge', ['variant' => 'neutral', 'slot' => 'Draft Content']) ?>
        </div>
        <div style="display: grid; gap: 1rem;">
            <?= component('alert', ['type' => 'info', 'title' => 'System Update Notice', 'slot' => 'The SHILPA LMS modern architecture runs on PHP 8.2+ with parameterized security queries.']) ?>
            <?= component('alert', ['type' => 'success', 'title' => 'Enrollment Confirmed', 'slot' => 'You have successfully registered for the Advanced Web Architecture course.']) ?>
            <?= component('alert', ['type' => 'warning', 'title' => 'Assignment Deadline', 'slot' => 'Module 4 practical assessment submission closes in 48 hours.']) ?>
            <?= component('alert', ['type' => 'danger', 'title' => 'Authentication Error', 'slot' => 'Session expired. Please re-authenticate to continue grading.']) ?>
        </div>
    </section>

    <!-- 5. Data Tables -->
    <section style="margin-bottom: 4rem;">
        <h2 style="font-size: 1.5rem; margin-bottom: 1.5rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border-subtle);">
            5. Clean Data Tables
        </h2>
        <?= component('table', [
            'headers' => ['Course ID', 'Course Title', 'Lead Instructor', 'Enrollments', 'Status', 'Actions'],
            'rows' => [
                [
                    '<code>CRS-101</code>',
                    '<strong>Fullstack Modern PHP</strong>',
                    'Sadeesha Jayaweera',
                    '1,248 students',
                    component('badge', ['variant' => 'success', 'slot' => 'Published']),
                    '<a href="#" class="btn btn-outline btn-sm">Manage</a>',
                ],
                [
                    '<code>CRS-102</code>',
                    '<strong>Algorithms & Complexity</strong>',
                    'Faculty of Computing',
                    '892 students',
                    component('badge', ['variant' => 'primary', 'slot' => 'Top Course']),
                    '<a href="#" class="btn btn-outline btn-sm">Manage</a>',
                ],
                [
                    '<code>CRS-103</code>',
                    '<strong>Cloud Infrastructure & DevOps</strong>',
                    'Hansana & Team',
                    '640 students',
                    component('badge', ['variant' => 'neutral', 'slot' => 'In Review']),
                    '<a href="#" class="btn btn-outline btn-sm">Manage</a>',
                ],
            ]
        ]) ?>
    </section>

    <!-- 6. Empty States & Skeletons -->
    <section style="margin-bottom: 4rem;">
        <h2 style="font-size: 1.5rem; margin-bottom: 1.5rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border-subtle);">
            6. Skeletons & Empty States
        </h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 2rem;">
            <?= component('empty-state', [
                'icon' => '📚',
                'title' => 'No Enrolled Courses Found',
                'description' => 'You have not enrolled in any learning courses yet. Browse the open catalog to get started.',
                'action' => '<a href="/all_courses.php" class="btn btn-primary btn-sm">Browse Catalog</a>',
            ]) ?>
            <div style="background: var(--bg-surface); padding: 2rem; border-radius: var(--radius-xl); border: 1px solid var(--border-subtle);">
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem;">
                    <?= component('skeleton', ['width' => '3rem', 'height' => '3rem', 'radius' => 'var(--radius-full)']) ?>
                    <div style="flex: 1;">
                        <?= component('skeleton', ['width' => '60%', 'height' => '1.25rem', 'radius' => 'var(--radius-sm)']) ?>
                        <div style="height: 0.5rem;"></div>
                        <?= component('skeleton', ['width' => '40%', 'height' => '0.875rem', 'radius' => 'var(--radius-sm)']) ?>
                    </div>
                </div>
                <?= component('skeleton', ['width' => '100%', 'height' => '1rem', 'radius' => 'var(--radius-sm)']) ?>
                <div style="height: 0.5rem;"></div>
                <?= component('skeleton', ['width' => '85%', 'height' => '1rem', 'radius' => 'var(--radius-sm)']) ?>
                <div style="height: 0.5rem;"></div>
                <?= component('skeleton', ['width' => '70%', 'height' => '1rem', 'radius' => 'var(--radius-sm)']) ?>
            </div>
        </div>
    </section>
</div>

<!-- Demo Modal Dialog -->
<?= component('modal', [
    'id' => 'demoModal',
    'title' => 'Enterprise Security Verification',
    'slot' => '<p style="color: var(--text-secondary); line-height: 1.6;">This dialog demonstrates the accessible modal system with backdrop blur, smooth entrance animation, escape key listener, and keyboard trap support.</p>',
    'footer' => '<button type="button" class="btn btn-secondary" onclick="closeModal(\'demoModal\')">Cancel</button><button type="button" class="btn btn-primary" onclick="closeModal(\'demoModal\')">Confirm Action</button>',
]) ?>

<?php
$slot = ob_get_clean();
include dirname(__DIR__) . '/layouts/app.php';
