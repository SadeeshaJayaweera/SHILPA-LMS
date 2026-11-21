<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? '403 Forbidden — SHILPA LMS') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/design-system.css">
</head>
<body style="min-height: 100vh; display: flex; align-items: center; justify-content: center; background-color: var(--bg-canvas); color: var(--text-primary); padding: 1.5rem;">
    <div style="max-width: 480px; width: 100%; text-align: center; background-color: var(--bg-surface); border: 1px solid var(--border-subtle); border-radius: var(--radius-xl); padding: 2.5rem 2rem; box-shadow: var(--shadow-xl);">
        <div style="display: inline-flex; align-items: center; justify-content: center; width: 4.5rem; height: 4.5rem; border-radius: var(--radius-full); background: rgba(239, 68, 68, 0.1); color: var(--danger-text); margin-bottom: 1.5rem;">
            <?= component('icon', ['name' => 'shield', 'size' => 36]) ?>
        </div>

        <div style="font-family: var(--font-mono); font-size: 0.875rem; font-weight: 700; color: var(--danger-text); text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 0.5rem;">
            403 • Access Forbidden
        </div>

        <h1 style="font-size: 1.75rem; font-weight: 800; margin: 0 0 0.75rem 0; font-family: var(--font-heading);">
            Insufficient Permissions
        </h1>

        <p style="color: var(--text-secondary); font-size: 0.9375rem; line-height: 1.5; margin-bottom: 1.5rem;">
            Your account role is not authorized to access this resource or perform this action.
        </p>

        <?php if (!empty($permission)): ?>
            <div style="background-color: var(--bg-surface-elevated); border: 1px solid var(--border-subtle); border-radius: var(--radius-md); padding: 0.75rem 1rem; margin-bottom: 2rem; font-size: 0.8125rem;">
                <span style="color: var(--text-muted);">Required permission: </span>
                <code style="font-family: var(--font-mono); color: var(--primary-text); font-weight: 700;"><?= e($permission) ?></code>
            </div>
        <?php endif; ?>

        <div style="display: flex; gap: 0.75rem; justify-content: center; flex-wrap: wrap;">
            <a href="/workspace" class="btn btn-primary btn-md">
                Return to Workspace
            </a>
            <a href="/logout" class="btn btn-secondary btn-md">
                Switch Account
            </a>
        </div>
    </div>
</body>
</html>
