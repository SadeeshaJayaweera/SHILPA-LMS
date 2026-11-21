<?php
ob_start();
?>
<div class="profile-settings-page" style="max-width: 1040px; margin: 0 auto;">
    <!-- Page Header -->
    <div style="margin-bottom: 2rem;">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 0.5rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <h1 style="font-size: 1.875rem; font-weight: 800; margin: 0; font-family: var(--font-heading);">
                    Profile & Account Settings
                </h1>
                <span class="badge badge-primary"><?= e(ucfirst((string)($user->user_type ?? 'User'))) ?></span>
            </div>
            <a href="/workspace" class="btn btn-secondary btn-sm">
                <?= component('icon', ['name' => 'arrow-left', 'size' => 16]) ?>
                <span>Back to Workspace</span>
            </a>
        </div>
        <p style="color: var(--text-muted); margin: 0; font-size: 0.9375rem;">
            Manage your personal profile, credentials, security controls, and learning preferences.
        </p>
    </div>

    <!-- Flash Notifications -->
    <?php if (session()->has('success')): ?>
        <div class="alert alert-success" style="margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem; padding: 1rem 1.25rem; border-radius: var(--radius-md); background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); color: #10b981;">
            <?= component('icon', ['name' => 'check-circle', 'size' => 20]) ?>
            <span><?= e((string)session()->get('success')) ?></span>
        </div>
    <?php endif; ?>

    <?php if (session()->has('error')): ?>
        <div class="alert alert-danger" style="margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem; padding: 1rem 1.25rem; border-radius: var(--radius-md); background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); color: #ef4444;">
            <?= component('icon', ['name' => 'alert-triangle', 'size' => 20]) ?>
            <span><?= e((string)session()->get('error')) ?></span>
        </div>
    <?php endif; ?>

    <!-- Profile Grid Layout -->
    <div style="display: grid; grid-template-columns: 260px 1fr; gap: 2rem; align-items: start;">
        <!-- Sidebar Navigation Menu -->
        <div class="card" style="padding: 1rem; position: sticky; top: 1.5rem;">
            <div style="text-align: center; padding: 1rem 0 1.5rem; border-bottom: 1px solid var(--border-subtle); margin-bottom: 1rem;">
                <div style="width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, var(--primary) 0%, #3b82f6 100%); color: white; display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 700; margin: 0 auto 0.75rem; overflow: hidden; border: 3px solid var(--border-subtle);">
                    <?php if (!empty($profile->avatar_url)): ?>
                        <img src="<?= e($profile->avatar_url) ?>" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;">
                    <?php else: ?>
                        <?= strtoupper(substr((string)($user->Full_Name ?? 'U'), 0, 1)) ?>
                    <?php endif; ?>
                </div>
                <div style="font-weight: 700; font-size: 1.125rem;"><?= e((string)$user->Full_Name) ?></div>
                <div style="font-size: 0.8125rem; color: var(--text-muted);"><?= e((string)$user->Email) ?></div>
                <div style="margin-top: 0.5rem; font-size: 0.75rem; color: var(--primary-text); font-weight: 500;">
                    <?= e((string)($profile->headline ?: 'NSBM Academic Member')) ?>
                </div>
            </div>

            <nav style="display: flex; flex-direction: column; gap: 0.375rem;">
                <a href="#general" class="profile-nav-link active" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.625rem 0.875rem; border-radius: var(--radius-md); color: var(--text-base); text-decoration: none; font-size: 0.875rem; font-weight: 500; transition: background 0.15s ease;">
                    <?= component('icon', ['name' => 'user', 'size' => 16]) ?>
                    <span>General Profile</span>
                </a>
                <a href="#preferences" class="profile-nav-link" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.625rem 0.875rem; border-radius: var(--radius-md); color: var(--text-muted); text-decoration: none; font-size: 0.875rem; font-weight: 500; transition: background 0.15s ease;">
                    <?= component('icon', ['name' => 'sliders', 'size' => 16]) ?>
                    <span>Preferences & Theme</span>
                </a>
                <a href="#security" class="profile-nav-link" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.625rem 0.875rem; border-radius: var(--radius-md); color: var(--text-muted); text-decoration: none; font-size: 0.875rem; font-weight: 500; transition: background 0.15s ease;">
                    <?= component('icon', ['name' => 'shield', 'size' => 16]) ?>
                    <span>Security & Sessions</span>
                </a>
            </nav>
        </div>

        <!-- Settings Content Areas -->
        <div style="display: flex; flex-direction: column; gap: 2rem;">
            <!-- 1. General Profile Section -->
            <section id="general" class="card" style="padding: 1.75rem;">
                <div style="border-bottom: 1px solid var(--border-subtle); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0 0 0.25rem; font-family: var(--font-heading);">
                        General Profile Information
                    </h2>
                    <p style="color: var(--text-muted); margin: 0; font-size: 0.875rem;">
                        Update your public display information, biographical details, and profile photo.
                    </p>
                </div>

                <form action="/profile" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="_csrf" value="<?= session()->csrfToken() ?>">

                    <div style="margin-bottom: 1.25rem;">
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem; color: var(--text-base);">
                            Full Name
                        </label>
                        <input type="text" name="full_name" value="<?= e((string)$user->Full_Name) ?>" required class="form-input" style="width: 100%; padding: 0.625rem 0.875rem; border-radius: var(--radius-md); border: 1px solid var(--border-subtle); background: var(--bg-surface-elevated); color: var(--text-base);">
                    </div>

                    <div style="margin-bottom: 1.25rem;">
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem; color: var(--text-base);">
                            Email Address <span style="font-weight: 400; color: var(--text-muted);">(Primary Identity)</span>
                        </label>
                        <input type="email" value="<?= e((string)$user->Email) ?>" disabled class="form-input" style="width: 100%; padding: 0.625rem 0.875rem; border-radius: var(--radius-md); border: 1px solid var(--border-subtle); background: var(--bg-surface); color: var(--text-muted); cursor: not-allowed;">
                        <span style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem; display: block;">
                            Institutional emails are managed by your campus registrar.
                        </span>
                    </div>

                    <div style="margin-bottom: 1.25rem;">
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem; color: var(--text-base);">
                            Professional Headline / Title
                        </label>
                        <input type="text" name="headline" value="<?= e((string)($profile->headline ?? '')) ?>" placeholder="e.g. Undergraduate Software Engineer" class="form-input" style="width: 100%; padding: 0.625rem 0.875rem; border-radius: var(--radius-md); border: 1px solid var(--border-subtle); background: var(--bg-surface-elevated); color: var(--text-base);">
                    </div>

                    <div style="margin-bottom: 1.25rem;">
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem; color: var(--text-base);">
                            Biographical Statement
                        </label>
                        <textarea name="bio" rows="4" placeholder="Briefly describe your academic background, research interests, or learning goals..." class="form-input" style="width: 100%; padding: 0.625rem 0.875rem; border-radius: var(--radius-md); border: 1px solid var(--border-subtle); background: var(--bg-surface-elevated); color: var(--text-base); font-family: inherit;"><?= e((string)($profile->bio ?? '')) ?></textarea>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.5rem;">
                        <div>
                            <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem; color: var(--text-base);">
                                Phone Contact
                            </label>
                            <input type="text" name="phone" value="<?= e((string)($profile->phone ?? '')) ?>" placeholder="+94 77 123 4567" class="form-input" style="width: 100%; padding: 0.625rem 0.875rem; border-radius: var(--radius-md); border: 1px solid var(--border-subtle); background: var(--bg-surface-elevated); color: var(--text-base);">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem; color: var(--text-base);">
                                Avatar Photo (JPG, PNG, WebP)
                            </label>
                            <input type="file" name="avatar" accept="image/jpeg,image/png,image/webp" class="form-input" style="width: 100%; padding: 0.5rem; border-radius: var(--radius-md); border: 1px solid var(--border-subtle); background: var(--bg-surface-elevated); color: var(--text-base);">
                        </div>
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                        <button type="submit" class="btn btn-primary">
                            <?= component('icon', ['name' => 'save', 'size' => 16]) ?>
                            <span>Save Profile Changes</span>
                        </button>
                    </div>
                </form>
            </section>

            <!-- 2. Preferences & Customization Section -->
            <section id="preferences" class="card" style="padding: 1.75rem;">
                <div style="border-bottom: 1px solid var(--border-subtle); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0 0 0.25rem; font-family: var(--font-heading);">
                        Preferences & Customization
                    </h2>
                    <p style="color: var(--text-muted); margin: 0; font-size: 0.875rem;">
                        Configure platform appearance, language locale, timezone, and communication preferences.
                    </p>
                </div>

                <form action="/profile/preferences" method="POST">
                    <input type="hidden" name="_csrf" value="<?= session()->csrfToken() ?>">

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.5rem;">
                        <div>
                            <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem; color: var(--text-base);">
                                Interface Theme
                            </label>
                            <select name="theme_preference" class="form-input" style="width: 100%; padding: 0.625rem 0.875rem; border-radius: var(--radius-md); border: 1px solid var(--border-subtle); background: var(--bg-surface-elevated); color: var(--text-base);">
                                <option value="system" <?= ($profile->theme_preference ?? '') === 'system' ? 'selected' : '' ?>>System Default (Auto)</option>
                                <option value="light" <?= ($profile->theme_preference ?? '') === 'light' ? 'selected' : '' ?>>Light Theme</option>
                                <option value="dark" <?= ($profile->theme_preference ?? '') === 'dark' ? 'selected' : '' ?>>Dark Theme</option>
                            </select>
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem; color: var(--text-base);">
                                Interface Language
                            </label>
                            <select name="language" class="form-input" style="width: 100%; padding: 0.625rem 0.875rem; border-radius: var(--radius-md); border: 1px solid var(--border-subtle); background: var(--bg-surface-elevated); color: var(--text-base);">
                                <option value="en" <?= ($profile->language ?? '') === 'en' ? 'selected' : '' ?>>English (UK / US)</option>
                                <option value="si" <?= ($profile->language ?? '') === 'si' ? 'selected' : '' ?>>Sinhala (සිංහල)</option>
                                <option value="ta" <?= ($profile->language ?? '') === 'ta' ? 'selected' : '' ?>>Tamil (தமிழ்)</option>
                            </select>
                        </div>
                    </div>

                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem; color: var(--text-base);">
                            Timezone
                        </label>
                        <select name="timezone" class="form-input" style="width: 100%; padding: 0.625rem 0.875rem; border-radius: var(--radius-md); border: 1px solid var(--border-subtle); background: var(--bg-surface-elevated); color: var(--text-base);">
                            <option value="Asia/Colombo" <?= ($profile->timezone ?? '') === 'Asia/Colombo' ? 'selected' : '' ?>>Asia/Colombo (UTC +05:30 Sri Lanka Standard Time)</option>
                            <option value="UTC" <?= ($profile->timezone ?? '') === 'UTC' ? 'selected' : '' ?>>UTC (Universal Coordinated Time)</option>
                            <option value="Asia/Singapore" <?= ($profile->timezone ?? '') === 'Asia/Singapore' ? 'selected' : '' ?>>Asia/Singapore (UTC +08:00)</option>
                            <option value="Europe/London" <?= ($profile->timezone ?? '') === 'Europe/London' ? 'selected' : '' ?>>Europe/London (GMT/BST)</option>
                        </select>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 1.5rem; padding: 1rem; background: var(--bg-surface); border: 1px solid var(--border-subtle); border-radius: var(--radius-md);">
                        <label style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer;">
                            <input type="checkbox" name="email_notifications" value="1" <?= !empty($profile->email_notifications) ? 'checked' : '' ?> style="width: 1.125rem; height: 1.125rem; accent-color: var(--primary);">
                            <div>
                                <span style="font-weight: 600; font-size: 0.875rem; display: block;">Course Announcements & Assignment Deadlines</span>
                                <span style="font-size: 0.75rem; color: var(--text-muted);">Receive timely automated digest emails for upcoming tasks.</span>
                            </div>
                        </label>

                        <label style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer;">
                            <input type="checkbox" name="security_alerts" value="1" <?= !empty($profile->security_alerts) ? 'checked' : '' ?> style="width: 1.125rem; height: 1.125rem; accent-color: var(--primary);">
                            <div>
                                <span style="font-weight: 600; font-size: 0.875rem; display: block;">Security & Login Alerts</span>
                                <span style="font-size: 0.75rem; color: var(--text-muted);">Receive immediate notifications when new devices sign in.</span>
                            </div>
                        </label>
                    </div>

                    <div style="display: flex; justify-content: flex-end;">
                        <button type="submit" class="btn btn-primary">
                            <?= component('icon', ['name' => 'check', 'size' => 16]) ?>
                            <span>Update Preferences</span>
                        </button>
                    </div>
                </form>
            </section>

            <!-- 3. Security, Password & Active Devices Section -->
            <section id="security" class="card" style="padding: 1.75rem;">
                <div style="border-bottom: 1px solid var(--border-subtle); padding-bottom: 1rem; margin-bottom: 1.5rem;">
                    <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0 0 0.25rem; font-family: var(--font-heading);">
                        Security & Password Management
                    </h2>
                    <p style="color: var(--text-muted); margin: 0; font-size: 0.875rem;">
                        Update your master credentials, audit active browser sessions, and manage login authorization.
                    </p>
                </div>

                <!-- Password Update Form -->
                <form action="/profile/password" method="POST" style="margin-bottom: 2.5rem;">
                    <input type="hidden" name="_csrf" value="<?= session()->csrfToken() ?>">

                    <div style="margin-bottom: 1.25rem;">
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem; color: var(--text-base);">
                            Current Password
                        </label>
                        <input type="password" name="current_password" required placeholder="Enter your current password" class="form-input" style="width: 100%; padding: 0.625rem 0.875rem; border-radius: var(--radius-md); border: 1px solid var(--border-subtle); background: var(--bg-surface-elevated); color: var(--text-base);">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.5rem;">
                        <div>
                            <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem; color: var(--text-base);">
                                New Password
                            </label>
                            <input type="password" name="new_password" required placeholder="Minimum 8 characters" class="form-input" style="width: 100%; padding: 0.625rem 0.875rem; border-radius: var(--radius-md); border: 1px solid var(--border-subtle); background: var(--bg-surface-elevated); color: var(--text-base);">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.5rem; color: var(--text-base);">
                                Confirm New Password
                            </label>
                            <input type="password" name="confirm_password" required placeholder="Re-type new password" class="form-input" style="width: 100%; padding: 0.625rem 0.875rem; border-radius: var(--radius-md); border: 1px solid var(--border-subtle); background: var(--bg-surface-elevated); color: var(--text-base);">
                        </div>
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                        <div style="font-size: 0.75rem; color: var(--text-muted);">
                            <?= component('icon', ['name' => 'info', 'size' => 12]) ?>
                            Password must contain at least 8 characters, upper/lowercase letters, a number, and a special symbol.
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <?= component('icon', ['name' => 'key', 'size' => 16]) ?>
                            <span>Update Password</span>
                        </button>
                    </div>
                </form>

                <!-- Active Sessions & Device Tracking -->
                <div style="border-top: 1px solid var(--border-subtle); padding-top: 1.5rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 1rem;">
                        <div>
                            <h3 style="font-size: 1rem; font-weight: 700; margin: 0 0 0.25rem;">
                                Active Device Sessions
                            </h3>
                            <p style="font-size: 0.8125rem; color: var(--text-muted); margin: 0;">
                                Devices and browsers currently authenticated to your SHILPA LMS account.
                            </p>
                        </div>

                        <?php if (count($sessions) > 1): ?>
                            <form action="/profile/sessions/terminate-others" method="POST" onsubmit="return confirm('Are you sure you want to log out of all other devices?');">
                                <input type="hidden" name="_csrf" value="<?= session()->csrfToken() ?>">
                                <button type="submit" class="btn btn-secondary btn-sm" style="color: #ef4444;">
                                    <?= component('icon', ['name' => 'log-out', 'size' => 14]) ?>
                                    <span>Sign Out All Other Devices</span>
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                        <?php foreach ($sessions as $session): ?>
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem; background: var(--bg-surface); border: 1px solid var(--border-subtle); border-radius: var(--radius-md); flex-wrap: wrap; gap: 1rem;">
                                <div style="display: flex; align-items: center; gap: 1rem;">
                                    <div style="width: 40px; height: 40px; border-radius: var(--radius-md); background: var(--bg-surface-elevated); border: 1px solid var(--border-subtle); display: flex; align-items: center; justify-content: center; color: var(--primary-text);">
                                        <?php if (str_contains(strtolower((string)$session['device_type']), 'mobile')): ?>
                                            <?= component('icon', ['name' => 'smartphone', 'size' => 20]) ?>
                                        <?php else: ?>
                                            <?= component('icon', ['name' => 'monitor', 'size' => 20]) ?>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <div style="font-weight: 600; font-size: 0.875rem; display: flex; align-items: center; gap: 0.5rem;">
                                            <span><?= e((string)$session['device_type']) ?></span>
                                            <?php if (!empty($session['is_current'])): ?>
                                                <span class="badge badge-success" style="font-size: 0.6875rem; padding: 0.125rem 0.5rem;">This Device (Current)</span>
                                            <?php endif; ?>
                                        </div>
                                        <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.125rem;">
                                            IP: <?= e((string)$session['ip_address']) ?> • <?= e((string)$session['location']) ?> • Last active <?= e((string)$session['last_activity']) ?>
                                        </div>
                                    </div>
                                </div>

                                <?php if (empty($session['is_current'])): ?>
                                    <form action="/profile/sessions/terminate" method="POST">
                                        <input type="hidden" name="_csrf" value="<?= session()->csrfToken() ?>">
                                        <input type="hidden" name="session_id" value="<?= e((string)$session['id']) ?>">
                                        <button type="submit" class="btn btn-secondary btn-sm" style="font-size: 0.75rem;">
                                            Revoke Access
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
<?php
$slot = ob_get_clean();
include dirname(__DIR__) . '/layouts/workspace.php';
