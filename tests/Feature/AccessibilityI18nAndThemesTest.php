<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserProfile;
use App\Services\LocalizationService;
use App\Services\ThemeService;
use App\Testing\TestCase;

class AccessibilityI18nAndThemesTest extends TestCase {
    public function testLocalizationServiceAndHelperFunctions(): void {
        // Reset locale to English
        LocalizationService::setLocale('en');
        assert(LocalizationService::getLocale() === 'en');

        // English translation
        assert(__('common.courses') === 'Courses');
        assert(__('common.dashboard') === 'Dashboard');

        // Parameter replacement
        $welcome = __('common.welcome_back', ['name' => 'Ada Lovelace']);
        assert($welcome === 'Welcome back, Ada Lovelace!');

        // Sinhala translation
        LocalizationService::setLocale('si');
        assert(LocalizationService::getLocale() === 'si');
        assert(__('common.courses') === 'පාඨමාලා');
        assert(__('common.dashboard') === 'පාලක පුවරුව');

        // Tamil translation
        LocalizationService::setLocale('ta');
        assert(LocalizationService::getLocale() === 'ta');
        assert(__('common.courses') === 'பாடநெறிகள்');
        assert(__('common.dashboard') === 'டாஷ்போர்டு');

        // Spanish translation
        LocalizationService::setLocale('es');
        assert(LocalizationService::getLocale() === 'es');
        assert(__('common.courses') === 'Cursos');
        assert(__('common.dashboard') === 'Panel de Control');

        // Fallback test
        $fallback = LocalizationService::trans('common.app_name', [], 'es');
        assert($fallback === 'SHILPA LMS');

        // Invalid locale rejected
        $invalid = LocalizationService::setLocale('xx');
        assert($invalid === false);
        assert(LocalizationService::getLocale() === 'es');

        // Restore default
        LocalizationService::setLocale('en');
    }

    public function testThemeServiceAndPreferences(): void {
        // Reset to dark
        ThemeService::setTheme('dark');
        assert(ThemeService::getTheme() === 'dark');

        // Switch to light
        $light = ThemeService::setTheme('light');
        assert($light === true);
        assert(ThemeService::getTheme() === 'light');

        // Switch to high-contrast (WCAG AAA)
        $hc = ThemeService::setTheme('high-contrast');
        assert($hc === true);
        assert(ThemeService::getTheme() === 'high-contrast');

        // Invalid theme rejected
        $invalid = ThemeService::setTheme('neon-green');
        assert($invalid === false);
        assert(ThemeService::getTheme() === 'high-contrast');

        // Supported themes inventory
        $themes = ThemeService::getSupportedThemes();
        assert(isset($themes['dark']));
        assert(isset($themes['light']));
        assert(isset($themes['high-contrast']));
        assert(isset($themes['system']));

        // Restore default
        ThemeService::setTheme('dark');
    }

    public function testAccessibilityCssAndMarkupCompliance(): void {
        $cssPath = dirname(__DIR__, 2) . '/public/css/accessibility.css';
        assert(file_exists($cssPath), 'Accessibility CSS file must exist');

        $cssContent = file_get_contents($cssPath);
        assert(str_contains($cssContent, ':focus-visible'), 'Must define visible focus indicators');
        assert(str_contains($cssContent, '.skip-link'), 'Must provide skip-to-content mechanism');
        assert(str_contains($cssContent, '.sr-only'), 'Must include screen reader utilities');
        assert(str_contains($cssContent, '[data-theme="high-contrast"]'), 'Must support high contrast theme variables');
        assert(str_contains($cssContent, 'prefers-reduced-motion'), 'Must support reduced motion queries');
    }

    public function testPreferencesHttpEndpoints(): void {
        // Preferences page renders
        $res = $this->get('/preferences');
        assert($res->getStatusCode() === 200, 'Preferences page should return 200');
        assert(str_contains($res->getContent(), 'Accessibility & Preferences'));
        assert(str_contains($res->getContent(), 'Visual Appearance & Contrast'));

        // Post theme switch
        $resTheme = $this->post('/preferences/theme', [
            'theme' => 'light',
            '_csrf' => session()->csrfToken(),
        ]);
        assert($resTheme->getStatusCode() === 302, 'Should redirect after theme change');
        assert(session()->get('theme') === 'light');

        // Post locale switch
        $resLocale = $this->post('/preferences/locale', [
            'locale' => 'es',
            '_csrf' => session()->csrfToken(),
        ]);
        assert($resLocale->getStatusCode() === 302, 'Should redirect after locale change');
        assert(session()->get('locale') === 'es');

        // API Preferences update
        $resApi = $this->post('/api/preferences', [
            'theme' => 'high-contrast',
            'locale' => 'en',
        ]);
        assert($resApi->getStatusCode() === 200, 'API preferences update should return 200');
        $json = json_decode($resApi->getContent(), true);
        assert($json['status'] === 'success');
        assert($json['theme'] === 'high-contrast');
        assert($json['locale'] === 'en');
    }
}
