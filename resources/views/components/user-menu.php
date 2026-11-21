<?php
/**
 * Modern User Profile Menu Component
 */
$userName = session()->get('user_name', 'Student User');
$userEmail = session()->get('email', 'student@shilpa.lk');
$userRole = session()->get('user_role', 'student');

$roleLabel = match ($userRole) {
    'a', 'admin', 'administrator', 'superadmin' => 'Administrator',
    'l', 'lecturer', 'instructor' => 'Faculty / Lecturer',
    default => 'Student Learner',
};

$roleBadgeClass = match ($userRole) {
    'a', 'admin', 'administrator', 'superadmin' => 'badge-danger',
    'l', 'lecturer', 'instructor' => 'badge-primary',
    default => 'badge-secondary',
};

$initial = strtoupper(substr($userName, 0, 1));
?>
<div class="dropdown-wrapper">
    <button 
        type="button" 
        class="header-action-btn" 
        id="user-menu-toggle-btn"
        data-dropdown-toggle="user-dropdown-menu" 
        aria-label="User account menu"
        aria-expanded="false"
        aria-haspopup="true"
        style="display: flex; align-items: center; gap: 0.625rem; padding: 0.25rem 0.5rem 0.25rem 0.25rem; border-radius: var(--radius-full);"
    >
        <span class="avatar avatar-sm avatar-primary" style="width: 2rem; height: 2rem; font-size: 0.8125rem;">
            <?= e($initial) ?>
        </span>
        <span style="font-size: 0.8125rem; font-weight: 600; color: var(--text-primary); max-width: 120px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: none; @media(min-width: 640px){display: inline-block;}">
            <?= e($userName) ?>
        </span>
        <span style="color: var(--text-muted); display: flex;">
            <?= component('icon', ['name' => 'chevron-down', 'size' => 14]) ?>
        </span>
    </button>

    <div class="dropdown-panel user-menu-dropdown-panel" id="user-dropdown-menu" role="menu">
        <div class="user-menu-header">
            <div style="font-weight: 700; font-size: 0.9375rem; color: var(--text-primary); margin-bottom: 0.125rem;">
                <?= e($userName) ?>
            </div>
            <div style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.5rem; word-break: break-all;">
                <?= e($userEmail) ?>
            </div>
            <span class="badge <?= $roleBadgeClass ?>"><?= $roleLabel ?></span>
        </div>

        <div style="display: flex; flex-direction: column; gap: 0.125rem;">
            <a href="/profile" class="user-menu-link" role="menuitem">
                <?= component('icon', ['name' => 'user', 'size' => 16]) ?>
                <span>My Profile</span>
            </a>
            <a href="/settings" class="user-menu-link" role="menuitem">
                <?= component('icon', ['name' => 'settings', 'size' => 16]) ?>
                <span>Account Settings</span>
            </a>
            <a href="/design-system" class="user-menu-link" role="menuitem">
                <?= component('icon', ['name' => 'layers', 'size' => 16]) ?>
                <span>Design System</span>
            </a>
            <button type="button" class="user-menu-link" style="width: 100%; border: none; background: none; cursor: pointer; text-align: left;" onclick="toggleTheme()" role="menuitem">
                <?= component('icon', ['name' => 'moon', 'size' => 16]) ?>
                <span data-theme-label>Toggle Theme</span>
            </button>
        </div>

        <div style="border-top: 1px solid var(--border-subtle); margin-top: 0.375rem; padding-top: 0.375rem;">
            <a href="/logout" class="user-menu-link" style="color: var(--danger-text);" role="menuitem">
                <?= component('icon', ['name' => 'log-out', 'size' => 16]) ?>
                <span>Sign Out</span>
            </a>
        </div>
    </div>
</div>
