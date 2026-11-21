<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? config('app.name', 'SHILPA LMS')) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/design-system.css">
    <style>
        .app-shell { display: flex; flex-direction: column; min-height: 100vh; }
        .site-header { position: sticky; top: 0; z-index: 50; border-bottom: 1px solid var(--border-subtle); background: var(--bg-surface-glass); backdrop-filter: blur(12px); }
        .nav-container { display: flex; align-items: center; justify-content: space-between; height: 4.5rem; max-width: 1280px; margin: 0 auto; padding: 0 1.5rem; }
        .brand-link { display: flex; align-items: center; gap: 0.75rem; text-decoration: none; color: var(--text-primary); font-family: var(--font-heading); font-weight: 800; font-size: 1.25rem; }
        .brand-avatar { width: 2.25rem; height: 2.25rem; border-radius: var(--radius-md); background: linear-gradient(135deg, var(--shilpa-blue-400) 0%, var(--shilpa-blue-600) 100%); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.125rem; }
        .nav-menu { display: flex; align-items: center; gap: 1.75rem; font-size: 0.875rem; font-weight: 500; list-style: none; }
        .nav-item a { color: var(--text-secondary); text-decoration: none; transition: color var(--transition-fast); }
        .nav-item a:hover { color: var(--text-primary); }
        .nav-actions { display: flex; align-items: center; gap: 0.75rem; }
        .main-container { flex: 1; }
        .site-footer { border-top: 1px solid var(--border-subtle); background-color: var(--bg-surface); padding: 3rem 0; margin-top: auto; font-size: 0.875rem; color: var(--text-muted); }
        .footer-inner { max-width: 1280px; margin: 0 auto; padding: 0 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; }
        .footer-links { display: flex; gap: 1.5rem; }
        .footer-links a { color: var(--text-muted); text-decoration: none; }
        .footer-links a:hover { color: var(--text-primary); }
    </style>
</head>
<body class="app-shell">
    <header class="site-header" role="banner">
        <div class="nav-container">
            <a href="/" class="brand-link" aria-label="SHILPA LMS Home">
                <span class="brand-avatar">S</span>
                <span>SHILPA <span style="color: var(--primary-text); font-weight: 500;">LMS</span></span>
            </a>

            <nav class="nav-menu" role="navigation" aria-label="Main Navigation">
                <li class="nav-item"><a href="/all_courses.php">Explore Courses</a></li>
                <li class="nav-item"><a href="/welcome.php">Student Workspace</a></li>
                <li class="nav-item"><a href="/admin.php">Administration</a></li>
                <li class="nav-item"><a href="/design-system">Design System</a></li>
                <li class="nav-item"><a href="/dev/developers.html">Developers</a></li>
            </nav>

            <div class="nav-actions">
                <button type="button" class="theme-toggle-btn" onclick="toggleTheme()" aria-label="Toggle theme">
                    <span data-theme-icon>🌙</span>
                    <span data-theme-label style="display: none;">Dark Mode</span>
                </button>
                <a href="/login/login.php" class="btn btn-secondary btn-sm">Sign In</a>
                <a href="/login/register.php" class="btn btn-primary btn-sm">Get Started</a>
            </div>
        </div>
    </header>

    <main class="main-container" id="main-content" role="main">
        <?= $slot ?? '' ?>
    </main>

    <footer class="site-footer" role="contentinfo">
        <div class="footer-inner">
            <div>&copy; <?= date('Y') ?> SHILPA LMS — Empowering Open IT Education Worldwide.</div>
            <div class="footer-links">
                <a href="/all_courses.php">Courses</a>
                <a href="/design-system">Design System</a>
                <a href="/login/terms.html">Terms</a>
                <a href="/dev/developers.html">Team</a>
            </div>
        </div>
    </footer>

    <script src="/js/design-system.js"></script>
</body>
</html>
