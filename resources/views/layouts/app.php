<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-900 text-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? config('app.name', 'SHILPA LMS')) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --font-display: 'Plus Jakarta Sans', system-ui, sans-serif;
            --font-body: 'Inter', system-ui, sans-serif;
            --font-mono: 'JetBrains Mono', monospace;
            --color-primary: #0284c7;
            --color-primary-hover: #0369a1;
            --color-accent: #38bdf8;
            --color-bg-dark: #090d16;
            --color-surface-dark: #0f172a;
            --color-border-dark: rgba(255, 255, 255, 0.08);
            --color-text-main: #f8fafc;
            --color-text-muted: #94a3b8;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: var(--font-body); background-color: var(--color-bg-dark); color: var(--color-text-main); line-height: 1.5; min-height: 100vh; display: flex; flex-direction: column; }
        a { color: inherit; text-decoration: none; }
        .container { width: 100%; max-width: 1280px; margin: 0 auto; padding: 0 1.5rem; }
        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; font-family: var(--font-display); font-weight: 600; font-size: 0.875rem; padding: 0.625rem 1.25rem; border-radius: 0.5rem; border: none; cursor: pointer; transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1); }
        .btn-primary { background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); color: #ffffff; box-shadow: 0 4px 14px rgba(2, 132, 199, 0.35); }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(2, 132, 199, 0.45); }
        .btn-secondary { background: rgba(255, 255, 255, 0.05); color: #f8fafc; border: 1px solid var(--color-border-dark); }
        .btn-secondary:hover { background: rgba(255, 255, 255, 0.08); border-color: rgba(255, 255, 255, 0.15); }
        .header { border-bottom: 1px solid var(--color-border-dark); background: rgba(9, 13, 22, 0.8); backdrop-filter: blur(12px); position: sticky; top: 0; z-index: 50; }
        .nav-inner { display: flex; align-items: center; justify-content: space-between; height: 4.5rem; }
        .logo-wrap { display: flex; align-items: center; gap: 0.75rem; font-family: var(--font-display); font-weight: 800; font-size: 1.25rem; letter-spacing: -0.025em; }
        .logo-badge { background: linear-gradient(135deg, #38bdf8 0%, #0284c7 100%); color: #fff; width: 2.25rem; height: 2.25rem; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.125rem; }
        .nav-links { display: flex; align-items: center; gap: 2rem; font-size: 0.875rem; font-weight: 500; color: var(--color-text-muted); }
        .nav-links a:hover { color: var(--color-text-main); }
        .main-content { flex: 1; }
        .footer { border-top: 1px solid var(--color-border-dark); background: var(--color-surface-dark); padding: 3rem 0; margin-top: auto; font-size: 0.875rem; color: var(--color-text-muted); }
    </style>
</head>
<body>
    <header class="header">
        <div class="container nav-inner">
            <a href="/" class="logo-wrap">
                <span class="logo-badge">S</span>
                <span>SHILPA <span style="color: var(--color-accent); font-weight: 500;">LMS</span></span>
            </a>
            <nav class="nav-links">
                <a href="/all_courses.php">Explore Courses</a>
                <a href="/welcome.php">Student Portal</a>
                <a href="/admin.php">Administration</a>
                <a href="/dev/developers.html">Developers</a>
            </nav>
            <div style="display: flex; gap: 0.75rem;">
                <a href="/login/login.php" class="btn btn-secondary">Sign In</a>
                <a href="/login/register.php" class="btn btn-primary">Get Started</a>
            </div>
        </div>
    </header>

    <main class="main-content">
        <?= $slot ?? '' ?>
    </main>

    <footer class="footer">
        <div class="container" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div>&copy; <?= date('Y') ?> SHILPA LMS. Empowering IT Education. All rights reserved.</div>
            <div style="display: flex; gap: 1.5rem;">
                <a href="/all_courses.php">Courses</a>
                <a href="/login/terms.html">Terms</a>
                <a href="/dev/developers.html">Team</a>
            </div>
        </div>
    </footer>
</body>
</html>
