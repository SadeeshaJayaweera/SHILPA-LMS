<?php
ob_start();
?>
<div>
    <div style="margin-bottom: 2rem;">
        <h1 style="font-size: 2rem; margin-bottom: 0.5rem;">Reset your password</h1>
        <p style="color: var(--text-muted); font-size: 0.9375rem;">
            Enter the email associated with your SHILPA account to receive password reset instructions.
        </p>
    </div>

    <?php if ($status = session()->get('status')): ?>
        <?= component('alert', ['type' => 'success', 'slot' => e($status)]) ?>
    <?php endif; ?>

    <form action="/forgot-password" method="POST" data-auth-form>
        <?= csrf_field() ?>

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
            >
        </div>

        <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; margin-bottom: 1.5rem;">
            Send Password Reset Link
        </button>

        <p style="text-align: center; font-size: 0.875rem; color: var(--text-muted);">
            Remember your credentials? 
            <a href="/login" style="color: var(--primary-text); font-weight: 600; text-decoration: none;">Back to login</a>
        </p>
    </form>
</div>
<?php
$slot = ob_get_clean();
include dirname(__DIR__) . '/layouts/auth.php';
