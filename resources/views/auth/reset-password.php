<?php
ob_start();
?>
<div>
    <div style="margin-bottom: 2rem;">
        <h1 style="font-size: 2rem; margin-bottom: 0.5rem;">Create new password</h1>
        <p style="color: var(--text-muted); font-size: 0.9375rem;">
            Your new password must satisfy strong entropy requirements (min 8 chars, uppercase, lowercase, number, symbol).
        </p>
    </div>

    <?php if ($errors = session()->get('errors')): ?>
        <div class="alert alert-danger" style="margin-bottom: 1.5rem;">
            <ul style="margin: 0; padding-left: 1.25rem;">
                <?php foreach ((array)$errors as $err): ?>
                    <li><?= e($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="/reset-password" method="POST" data-auth-form>
        <?= csrf_field() ?>
        <input type="hidden" name="token" value="<?= e($token ?? '') ?>">

        <div class="form-group">
            <label for="reset_email" class="form-label">Email address <span style="color: var(--danger-text);">*</span></label>
            <input 
                type="email" 
                id="reset_email" 
                name="email" 
                required 
                autocomplete="email" 
                placeholder="student@shilpa.lk"
                class="form-control"
                value="<?= e($email ?? '') ?>"
            >
        </div>

        <div class="form-group">
            <label for="new_password" class="form-label">New Password <span style="color: var(--danger-text);">*</span></label>
            <input 
                type="password" 
                id="new_password" 
                name="password" 
                required 
                autocomplete="new-password" 
                placeholder="••••••••" 
                class="form-control"
            >
        </div>

        <div class="form-group">
            <label for="password_confirmation" class="form-label">Confirm New Password <span style="color: var(--danger-text);">*</span></label>
            <input 
                type="password" 
                id="password_confirmation" 
                name="password_confirmation" 
                required 
                autocomplete="new-password" 
                placeholder="••••••••" 
                class="form-control"
            >
        </div>

        <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; margin-bottom: 1.5rem;">
            Update Password & Sign In
        </button>

        <p style="text-align: center; font-size: 0.875rem; color: var(--text-muted);">
            Back to <a href="/login" style="color: var(--primary-text); font-weight: 600;">Sign In</a>
        </p>
    </form>
</div>
<?php
$slot = ob_get_clean();
include dirname(__DIR__) . '/layouts/auth.php';
