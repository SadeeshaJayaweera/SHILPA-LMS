<?php
declare(strict_types=1);

$currentLocale = $data['currentLocale'] ?? 'en';
$supportedLocales = $data['supportedLocales'] ?? [];
$currentTheme = $data['currentTheme'] ?? 'dark';
$supportedThemes = $data['supportedThemes'] ?? [];

ob_start();
?>
<div class="preferences-container" style="max-width: 900px; margin: 0 auto; padding-bottom: 4rem;">
    <!-- Page Header -->
    <div style="margin-bottom: 2rem;">
        <h1 style="font-size: 2rem; font-weight: 800; font-family: var(--font-heading); margin: 0 0 0.5rem 0;">
            Accessibility & Preferences
        </h1>
        <p style="color: var(--text-muted); font-size: 1rem; margin: 0;">
            Customize visual theme, language localization, and WCAG accessibility standards.
        </p>
    </div>

    <!-- Section 1: Visual Theme -->
    <div class="card" style="padding: 2rem; border-radius: var(--radius-xl); border: 1px solid var(--border-color); margin-bottom: 2rem;">
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
            <?= component('icon', ['name' => 'moon', 'size' => 20, 'color' => 'var(--primary)']) ?>
            <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0;">Visual Appearance & Contrast</h2>
        </div>
        <p style="color: var(--text-muted); font-size: 0.875rem; margin: 0 0 1.5rem 0;">
            Select a color theme suited to your viewing environment.
        </p>

        <form action="/preferences/theme" method="POST" style="margin: 0;">
            <input type="hidden" name="_csrf" value="<?= session()->csrfToken() ?>">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
                <?php foreach ($supportedThemes as $key => $meta): ?>
                    <?php $isActive = $currentTheme === $key; ?>
                    <label class="card theme-choice-card" style="padding: 1.25rem; border-radius: var(--radius-lg); border: 2px solid <?= $isActive ? 'var(--primary)' : 'var(--border-color)' ?>; cursor: pointer; display: flex; flex-direction: column; background: <?= $isActive ? 'var(--surface-secondary)' : 'var(--surface)' ?>; transition: all 0.2s;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                            <input type="radio" name="theme" value="<?= e($key) ?>" <?= $isActive ? 'checked' : '' ?> onchange="this.form.submit()">
                            <?php if ($isActive): ?>
                                <span class="badge badge-primary" style="font-size: 0.6875rem;">Active</span>
                            <?php endif; ?>
                        </div>
                        <div style="font-weight: 700; font-size: 1rem; margin-bottom: 0.25rem;">
                            <?= e($meta['name']) ?>
                        </div>
                        <div style="font-size: 0.75rem; color: var(--text-muted); line-height: 1.4;">
                            <?= e($meta['description']) ?>
                        </div>
                    </label>
                <?php endforeach; ?>
            </div>
        </form>
    </div>

    <!-- Section 2: Language & Localization -->
    <div class="card" style="padding: 2rem; border-radius: var(--radius-xl); border: 1px solid var(--border-color); margin-bottom: 2rem;">
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
            <?= component('icon', ['name' => 'globe', 'size' => 20, 'color' => 'var(--primary)']) ?>
            <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0;">Language & Regional Locale</h2>
        </div>
        <p style="color: var(--text-muted); font-size: 0.875rem; margin: 0 0 1.5rem 0;">
            Choose your preferred language for the LMS user interface.
        </p>

        <form action="/preferences/locale" method="POST" style="margin: 0;">
            <input type="hidden" name="_csrf" value="<?= session()->csrfToken() ?>">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); gap: 1rem;">
                <?php foreach ($supportedLocales as $code => $loc): ?>
                    <?php $isActive = $currentLocale === $code; ?>
                    <label class="card locale-choice-card" style="padding: 1.25rem; border-radius: var(--radius-lg); border: 2px solid <?= $isActive ? 'var(--primary)' : 'var(--border-color)' ?>; cursor: pointer; display: flex; flex-direction: column; background: <?= $isActive ? 'var(--surface-secondary)' : 'var(--surface)' ?>; transition: all 0.2s;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                            <input type="radio" name="locale" value="<?= e($code) ?>" <?= $isActive ? 'checked' : '' ?> onchange="this.form.submit()">
                            <span style="font-size: 1rem; font-weight: 700; color: var(--text-muted);"><?= strtoupper($code) ?></span>
                        </div>
                        <div style="font-weight: 700; font-size: 1.125rem; margin-bottom: 0.125rem;">
                            <?= e($loc['native']) ?>
                        </div>
                        <div style="font-size: 0.75rem; color: var(--text-muted);">
                            <?= e($loc['name']) ?>
                        </div>
                    </label>
                <?php endforeach; ?>
            </div>
        </form>
    </div>

    <!-- Section 3: Accessibility Features (WCAG 2.1 AA) -->
    <div class="card" style="padding: 2rem; border-radius: var(--radius-xl); border: 1px solid var(--border-color);">
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
            <?= component('icon', ['name' => 'shield', 'size' => 20, 'color' => 'var(--primary)']) ?>
            <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0;">Accessibility Capabilities (WCAG 2.1 AA)</h2>
        </div>
        <p style="color: var(--text-muted); font-size: 0.875rem; margin: 0 0 1.5rem 0;">
            Features engineered to ensure equal educational access for all students.
        </p>

        <div style="display: flex; flex-direction: column; gap: 1rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem; background: var(--surface-secondary); border-radius: var(--radius-md);">
                <div>
                    <div style="font-weight: 600; font-size: 0.9375rem; margin-bottom: 0.25rem;">Keyboard Navigation & Focus Rings</div>
                    <div style="font-size: 0.8125rem; color: var(--text-muted);">All interactive controls have distinct :focus-visible outlines for screen readers and tab navigation.</div>
                </div>
                <span class="badge badge-success">Enabled</span>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem; background: var(--surface-secondary); border-radius: var(--radius-md);">
                <div>
                    <div style="font-weight: 600; font-size: 0.9375rem; margin-bottom: 0.25rem;">Skip-to-Content Bypass Link</div>
                    <div style="font-size: 0.8125rem; color: var(--text-muted);">Allows keyboard users to bypass top header menus and jump directly to the main content container.</div>
                </div>
                <span class="badge badge-success">Active</span>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem; background: var(--surface-secondary); border-radius: var(--radius-md);">
                <div>
                    <div style="font-weight: 600; font-size: 0.9375rem; margin-bottom: 0.25rem;">High Contrast Color Palettes</div>
                    <div style="font-size: 0.8125rem; color: var(--text-muted);">Adheres to WCAG AAA contrast ratios (>= 7:1) when High Contrast mode is enabled.</div>
                </div>
                <span class="badge badge-secondary"><?= $currentTheme === 'high-contrast' ? 'Active' : 'Standby' ?></span>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem; background: var(--surface-secondary); border-radius: var(--radius-md);">
                <div>
                    <div style="font-weight: 600; font-size: 0.9375rem; margin-bottom: 0.25rem;">Reduced Motion Respect</div>
                    <div style="font-size: 0.8125rem; color: var(--text-muted);">Automatically silences non-essential transitions when operating system prefers-reduced-motion is active.</div>
                </div>
                <span class="badge badge-success">Auto-Detect</span>
            </div>
        </div>
    </div>
</div>

<?php
$slot = ob_get_clean();
include dirname(__DIR__) . '/layouts/workspace.php';
