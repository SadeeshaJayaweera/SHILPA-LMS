<?php
ob_start();
?>
<div class="container" style="text-align: center; padding: 8rem 0;">
    <h1 style="font-family: var(--font-display); font-size: 5rem; font-weight: 800; color: #38bdf8; margin-bottom: 1rem;">404</h1>
    <h2 style="font-family: var(--font-display); font-size: 1.75rem; font-weight: 700; margin-bottom: 1rem;">Page Not Found</h2>
    <p style="color: var(--color-text-muted); max-width: 500px; margin: 0 auto 2rem; line-height: 1.6;">
        The requested resource at <code style="background: rgba(255,255,255,0.08); padding: 0.2rem 0.4rem; border-radius: 0.25rem; color: #f8fafc;"><?= e($path ?? '') ?></code> does not exist or has been moved.
    </p>
    <a href="/" class="btn btn-primary">Return to Shilpa LMS</a>
</div>
<?php
$slot = ob_get_clean();
include dirname(__DIR__) . '/layouts/app.php';
