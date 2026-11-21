<?php
ob_start();
?>
<div>
    <div style="margin-bottom: 2rem;">
        <h1 style="font-size: 2rem; margin-bottom: 0.5rem;">Welcome back</h1>
        <p style="color: var(--text-muted); font-size: 0.9375rem;">
            Sign in to access your course modules, assignments, and learning dashboard.
        </p>
    </div>

    <?php if ($error = session()->get('error')): ?>
        <?= component('alert', ['type' => 'danger', 'slot' => e($error)]) ?>
    <?php endif; ?>

    <?php if ($success = session()->get('success')): ?>
        <?= component('alert', ['type' => 'success', 'slot' => e($success)]) ?>
    <?php endif; ?>

    <form action="/login" method="POST" data-auth-form>
        <?= csrf_field() ?>

        <div class="form-group">
            <label for="login_email" class="form-label">Email address <span style="color: var(--danger-text);">*</span></label>
            <input 
                type="email" 
                id="login_email" 
                name="email" 
                required 
                autocomplete="email" 
                placeholder="name@university.edu"
                class="form-control"
                value="<?= e(session()->get('old')['email'] ?? '') ?>"
            >
        </div>

        <div class="form-group">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.375rem;">
                <label for="login_password" class="form-label" style="margin-bottom: 0;">Password <span style="color: var(--danger-text);">*</span></label>
                <a href="/forgot-password" style="font-size: 0.8125rem; color: var(--primary-text); text-decoration: none;">Forgot password?</a>
            </div>
            <div style="position: relative;">
                <input 
                    type="password" 
                    id="login_password" 
                    name="password" 
                    required 
                    autocomplete="current-password" 
                    placeholder="••••••••" 
                    class="form-control"
                    style="padding-right: 2.75rem;"
                >
                <button 
                    type="button" 
                    data-password-toggle="login_password" 
                    aria-label="Toggle password visibility"
                    style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; font-size: 1.1rem; color: var(--text-muted);"
                >
                    👁️
                </button>
            </div>
        </div>

        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem;">
            <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem; color: var(--text-secondary); cursor: pointer;">
                <input type="checkbox" name="remember" value="1" style="accent-color: var(--primary-default); width: 1rem; height: 1rem;">
                Keep me signed in
            </label>
        </div>

        <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; margin-bottom: 1.5rem;">
            Sign In to Dashboard
        </button>

        <p style="text-align: center; font-size: 0.875rem; color: var(--text-muted);">
            Don't have an account yet? 
            <a href="/register" style="color: var(--primary-text); font-weight: 600; text-decoration: none;">Create free account</a>
        </p>
    </form>
</div>
<?php
$slot = ob_get_clean();
include dirname(__DIR__) . '/layouts/auth.php';
