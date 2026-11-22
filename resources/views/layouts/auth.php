<!DOCTYPE html>
<html lang="en" data-theme="light">
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
            background: linear-gradient(135deg, rgba(32, 81, 84, 0.92) 0%, rgba(15, 23, 42, 0.94) 100%), url('/login/register_background.jpg') center/cover no-repeat;
            border-right: 1px solid var(--border-subtle);
            padding: 4rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            color: #ffffff;
        }
        .auth-brand-pane h2 {
            color: #ffffff;
        }
        .auth-brand-pane p {
            color: #e2e8f0;
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
                <a href="/" style="display: inline-flex; align-items: center; text-decoration: none;">
                    <img src="/Images/index/shilpaLogo.png" alt="SHILPA LMS" style="height: 48px; width: auto; object-fit: contain;">
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
