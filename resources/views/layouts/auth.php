<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Authentication — ' . config('app.name', 'SHILPA LMS')) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/design-system.css">
    <style>
        .auth-split-wrapper {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }
        .auth-brand-pane {
            flex: 1.1;
            background: radial-gradient(circle at 20% 30%, rgba(2, 132, 199, 0.25) 0%, rgba(6, 9, 17, 0.98) 70%);
            border-right: 1px solid var(--border-subtle);
            padding: 4rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
        }
        .auth-form-pane {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 3rem 2rem;
            background-color: var(--bg-app);
            position: relative;
        }
        .auth-form-card {
            width: 100%;
            max-width: 440px;
        }
        .auth-top-nav {
            position: absolute;
            top: 2rem;
            right: 2rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        @media (max-width: 900px) {
            .auth-brand-pane { display: none; }
            .auth-form-pane { padding: 4rem 1.5rem; }
        }
    </style>
</head>
<body>
    <div class="auth-split-wrapper">
        <!-- Left Brand Pane -->
        <div class="auth-brand-pane">
            <div>
                <a href="/" style="display: inline-flex; align-items: center; gap: 0.75rem; text-decoration: none; color: var(--text-primary); font-family: var(--font-heading); font-weight: 800; font-size: 1.35rem;">
                    <span style="width: 2.5rem; height: 2.5rem; border-radius: var(--radius-md); background: linear-gradient(135deg, var(--shilpa-blue-400) 0%, var(--shilpa-blue-600) 100%); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 800;">S</span>
                    <span>SHILPA <span style="color: var(--primary-text); font-weight: 500;">LMS</span></span>
                </a>
            </div>

            <div style="max-width: 520px;">
                <div style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.3rem 0.75rem; border-radius: var(--radius-full); background: var(--primary-subtle); border: 1px solid rgba(56,189,248,0.3); color: var(--primary-text); font-size: 0.75rem; font-weight: 600; margin-bottom: 1.5rem;">
                    <?= icon('shield', ['size' => 14]) ?> Enterprise-Grade Security
                </div>
                <h2 style="font-size: 2.5rem; font-weight: 800; line-height: 1.2; margin-bottom: 1.25rem;">
                    Transforming Technical Education with Rigor and Openness.
                </h2>
                <p style="color: var(--text-muted); font-size: 1.0625rem; line-height: 1.6; margin-bottom: 2rem;">
                    Empowering students, lecturers, and educational administrators with intuitive course workspaces, verifiable assessments, and transparent learning insights.
                </p>

                <div style="display: grid; gap: 1rem; border-top: 1px solid var(--border-subtle); padding-top: 2rem;">
                    <div style="display: flex; align-items: center; gap: 0.75rem; font-size: 0.875rem; color: var(--text-secondary);">
                        <?= icon('check', ['size' => 16, 'color' => 'var(--shilpa-emerald-400)']) ?> Secure Session Protection & Role-Based Workspaces
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.75rem; font-size: 0.875rem; color: var(--text-secondary);">
                        <?= icon('check', ['size' => 16, 'color' => 'var(--shilpa-emerald-400)']) ?> Transparent Academic Evaluations & Real-Time Grading
                    </div>
                </div>
            </div>

            <div style="color: var(--text-tertiary); font-size: 0.8125rem;">
                &copy; <?= date('Y') ?> SHILPA LMS. Empowering IT Education.
            </div>
        </div>

        <!-- Right Form Pane -->
        <div class="auth-form-pane">
            <div class="auth-top-nav">
                <button type="button" class="theme-toggle-btn" onclick="toggleTheme()" aria-label="Toggle theme">
                    <span data-theme-icon>🌙</span>
                </button>
                <a href="/" class="btn btn-ghost btn-sm">&larr; Return Home</a>
            </div>

            <div class="auth-form-card">
                <?= $slot ?? '' ?>
            </div>
        </div>
    </div>

    <script src="/js/design-system.js"></script>
    <script src="/js/auth.js"></script>
</body>
</html>
