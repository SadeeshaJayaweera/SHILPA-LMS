<?php
ob_start();
?>
<div>
    <div style="margin-bottom: 2rem;">
        <h1 style="font-size: 2rem; margin-bottom: 0.5rem;">Create your account</h1>
        <p style="color: var(--text-muted); font-size: 0.9375rem;">
            Join SHILPA LMS for unrestricted open access to university-grade computing modules.
        </p>
    </div>

    <?php if ($errors = session()->get('errors')): ?>
        <div class="alert alert-danger">
            <ul style="margin-left: 1rem; list-style: square;">
                <?php foreach ($errors as $field => $fieldErrors): ?>
                    <?php foreach ((array)$fieldErrors as $msg): ?>
                        <li><?= e($msg) ?></li>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="/register" method="POST" data-auth-form>
        <?= csrf_field() ?>

        <div class="form-group">
            <label for="fullname" class="form-label">Full Name <span style="color: var(--danger-text);">*</span></label>
            <input 
                type="text" 
                id="fullname" 
                name="fullname" 
                required 
                autocomplete="name" 
                placeholder="e.g. Sadeesha Jayaweera"
                class="form-control"
                value="<?= e(session()->get('old')['fullname'] ?? '') ?>"
            >
        </div>

        <div class="form-group">
            <label for="email" class="form-label">Academic / Personal Email <span style="color: var(--danger-text);">*</span></label>
            <input 
                type="email" 
                id="email" 
                name="email" 
                required 
                autocomplete="email" 
                placeholder="student@shilpa.lk"
                class="form-control"
                value="<?= e(session()->get('old')['email'] ?? '') ?>"
            >
        </div>

        <div class="form-group">
            <label for="role" class="form-label">I am joining as <span style="color: var(--danger-text);">*</span></label>
            <select id="role" name="role" class="form-select">
                <option value="student">Student / Independent Learner</option>
                <option value="lecturer">Lecturer / Instructor</option>
            </select>
        </div>

        <div class="form-group">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <label for="reg_password" class="form-label">Password (min 8 characters) <span style="color: var(--danger-text);">*</span></label>
                <span id="password_strength_text" style="font-size: 0.75rem; font-weight: 600;"></span>
            </div>
            <div style="position: relative;">
                <input 
                    type="password" 
                    id="reg_password" 
                    name="password" 
                    required 
                    autocomplete="new-password" 
                    placeholder="••••••••" 
                    class="form-control"
                    style="padding-right: 2.75rem;"
                >
                <button 
                    type="button" 
                    data-password-toggle="reg_password" 
                    aria-label="Toggle password visibility"
                    style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; font-size: 1.1rem; color: var(--text-muted);"
                >
                    👁️
                </button>
            </div>
            <!-- Dynamic Strength Bar -->
            <div style="height: 4px; background: var(--bg-surface-elevated); border-radius: 2px; margin-top: 0.5rem; overflow: hidden;">
                <div id="password_strength_bar" style="height: 100%; width: 0%; transition: all var(--transition-fast);"></div>
            </div>
        </div>

        <div class="form-group">
            <label for="repeat_password" class="form-label">Confirm Password <span style="color: var(--danger-text);">*</span></label>
            <div style="position: relative;">
                <input 
                    type="password" 
                    id="repeat_password" 
                    name="repeat_password" 
                    required 
                    autocomplete="new-password" 
                    placeholder="••••••••" 
                    class="form-control"
                    style="padding-right: 2.75rem;"
                >
                <button 
                    type="button" 
                    data-password-toggle="repeat_password" 
                    aria-label="Toggle confirm password visibility"
                    style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; font-size: 1.1rem; color: var(--text-muted);"
                >
                    👁️
                </button>
            </div>
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label style="display: flex; align-items: flex-start; gap: 0.625rem; font-size: 0.8125rem; color: var(--text-secondary); cursor: pointer; line-height: 1.5;">
                <input type="checkbox" name="terms" required value="1" style="accent-color: var(--primary-default); margin-top: 0.15rem;">
                <span>I agree to the <a href="/login/terms.html" target="_blank" style="color: var(--primary-text); text-decoration: underline;">Terms of Service</a> and academic integrity guidelines.</span>
            </label>
        </div>

        <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; margin-bottom: 1.5rem;">
            Register & Continue
        </button>

        <p style="text-align: center; font-size: 0.875rem; color: var(--text-muted);">
            Already have an account? 
            <a href="/login" style="color: var(--primary-text); font-weight: 600; text-decoration: none;">Sign in here</a>
        </p>
    </form>
</div>
<?php
$slot = ob_get_clean();
include dirname(__DIR__) . '/layouts/auth.php';
