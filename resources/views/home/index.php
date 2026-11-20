<?php
ob_start();
?>
<section style="padding: 5rem 0; background: radial-gradient(circle at 50% 0%, rgba(2, 132, 199, 0.15) 0%, transparent 60%);">
    <div class="container" style="text-align: center; max-width: 800px;">
        <div style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.35rem 0.85rem; border-radius: 9999px; background: rgba(56, 189, 248, 0.1); border: 1px solid rgba(56, 189, 248, 0.2); color: #38bdf8; font-size: 0.8125rem; font-weight: 600; margin-bottom: 1.5rem;">
            <span>🚀</span> Modern Enterprise Learning Architecture
        </div>
        <h1 style="font-family: var(--font-display); font-size: 3.5rem; font-weight: 800; line-height: 1.15; letter-spacing: -0.03em; margin-bottom: 1.5rem;">
            Empowering the Next Generation of <span style="background: linear-gradient(135deg, #38bdf8 0%, #0284c7 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">IT Innovators</span>
        </h1>
        <p style="font-size: 1.125rem; color: var(--color-text-muted); line-height: 1.7; margin-bottom: 2.5rem;">
            SHILPA LMS provides an open, modern, and accessible learning management platform engineered to deliver world-class technical education, interactive coursework, and comprehensive learning management.
        </p>
        <div style="display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap;">
            <a href="/all_courses.php" class="btn btn-primary" style="font-size: 1rem; padding: 0.75rem 1.75rem;">Browse Course Catalog</a>
            <a href="/login/register.php" class="btn btn-secondary" style="font-size: 1rem; padding: 0.75rem 1.75rem;">Create Free Account</a>
        </div>
    </div>
</section>

<section style="padding: 4rem 0; border-top: 1px solid var(--color-border-dark);">
    <div class="container">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
            <div style="background: var(--color-surface-dark); border: 1px solid var(--color-border-dark); border-radius: 1rem; padding: 2rem;">
                <div style="font-size: 2rem; margin-bottom: 1rem;">💻</div>
                <h3 style="font-family: var(--font-display); font-size: 1.25rem; font-weight: 700; margin-bottom: 0.75rem;">Diverse Course Collection</h3>
                <p style="color: var(--color-text-muted); font-size: 0.9375rem; line-height: 1.6;">From full-stack web engineering and algorithms to systems architecture and machine learning.</p>
            </div>
            <div style="background: var(--color-surface-dark); border: 1px solid var(--color-border-dark); border-radius: 1rem; padding: 2rem;">
                <div style="font-size: 2rem; margin-bottom: 1rem;">⚡</div>
                <h3 style="font-family: var(--font-display); font-size: 1.25rem; font-weight: 700; margin-bottom: 0.75rem;">Enterprise Modern Core</h3>
                <p style="color: var(--color-text-muted); font-size: 0.9375rem; line-height: 1.6;">Powered by PHP 8.2+, modular routing, parameterized secure queries, and responsive modern layouts.</p>
            </div>
            <div style="background: var(--color-surface-dark); border: 1px solid var(--color-border-dark); border-radius: 1rem; padding: 2rem;">
                <div style="font-size: 2rem; margin-bottom: 1rem;">🔒</div>
                <h3 style="font-family: var(--font-display); font-size: 1.25rem; font-weight: 700; margin-bottom: 0.75rem;">Secure & Reliable</h3>
                <p style="color: var(--color-text-muted); font-size: 0.9375rem; line-height: 1.6;">Role-based access controls, session hardening, CSRF defense, and automated testing built into every layer.</p>
            </div>
        </div>
    </div>
</section>
<?php
$slot = ob_get_clean();
include dirname(__DIR__) . '/layouts/app.php';
