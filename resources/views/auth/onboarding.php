<?php
ob_start();
?>
<div style="max-width: 640px; margin: 0 auto;">
    <div style="text-align: center; margin-bottom: 2.5rem;">
        <div style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.25rem 0.75rem; border-radius: var(--radius-full); background: var(--primary-subtle); color: var(--primary-text); font-size: 0.8125rem; font-weight: 600; margin-bottom: 1rem;">
            <?= icon('zap', ['size' => 14]) ?> Welcome to SHILPA
        </div>
        <h1 style="font-size: 2.25rem; margin-bottom: 0.5rem;">Personalize Your Learning Journey</h1>
        <p style="color: var(--text-muted); font-size: 1rem;">
            Select your learning focus areas so we can tailor course recommendations and deadlines.
        </p>
    </div>

    <form action="/onboarding" method="POST" data-auth-form>
        <?= csrf_field() ?>

        <div class="card" style="padding: 2rem; margin-bottom: 2rem;">
            <h3 style="font-size: 1.125rem; font-weight: 700; margin-bottom: 1.25rem;">Select Primary Study Focus</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1rem;">
                <label class="card card-interactive" style="padding: 1.25rem; cursor: pointer; display: flex; align-items: flex-start; gap: 0.75rem;">
                    <input type="checkbox" name="interests[]" value="web" checked style="accent-color: var(--primary-default); margin-top: 0.25rem;">
                    <div>
                        <strong style="display: block; font-size: 0.9375rem;">Web Engineering</strong>
                        <span style="font-size: 0.8125rem; color: var(--text-muted);">HTML, CSS, modern JS, PHP 8.2 backend architectures.</span>
                    </div>
                </label>

                <label class="card card-interactive" style="padding: 1.25rem; cursor: pointer; display: flex; align-items: flex-start; gap: 0.75rem;">
                    <input type="checkbox" name="interests[]" value="algorithms" checked style="accent-color: var(--primary-default); margin-top: 0.25rem;">
                    <div>
                        <strong style="display: block; font-size: 0.9375rem;">Algorithms & Systems</strong>
                        <span style="font-size: 0.8125rem; color: var(--text-muted);">Data structures, complexity, optimization.</span>
                    </div>
                </label>

                <label class="card card-interactive" style="padding: 1.25rem; cursor: pointer; display: flex; align-items: flex-start; gap: 0.75rem;">
                    <input type="checkbox" name="interests[]" value="cloud" style="accent-color: var(--primary-default); margin-top: 0.25rem;">
                    <div>
                        <strong style="display: block; font-size: 0.9375rem;">Cloud & DevOps</strong>
                        <span style="font-size: 0.8125rem; color: var(--text-muted);">Deployment pipelines, containers, observability.</span>
                    </div>
                </label>

                <label class="card card-interactive" style="padding: 1.25rem; cursor: pointer; display: flex; align-items: flex-start; gap: 0.75rem;">
                    <input type="checkbox" name="interests[]" value="security" style="accent-color: var(--primary-default); margin-top: 0.25rem;">
                    <div>
                        <strong style="display: block; font-size: 0.9375rem;">Security & Defense</strong>
                        <span style="font-size: 0.8125rem; color: var(--text-muted);">Application security, RBAC, session protection.</span>
                    </div>
                </label>
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">
            Enter My Student Workspace &rarr;
        </button>
    </form>
</div>
<?php
$slot = ob_get_clean();
include dirname(__DIR__) . '/layouts/app.php';
