<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\UserProfile;

class ThemeService {
    protected static array $supportedThemes = ['dark', 'light', 'high-contrast', 'system'];

    /**
     * Get active visual theme
     */
    public static function getTheme(): string {
        if (session()->has('theme')) {
            $th = (string)session()->get('theme');
            if (in_array($th, self::$supportedThemes, true)) {
                return $th;
            }
        }

        $userId = (int)session()->get('user_id', 0);
        if ($userId > 0) {
            $profile = UserProfile::forUser($userId);
            if (!empty($profile->theme_preference) && in_array($profile->theme_preference, self::$supportedThemes, true)) {
                session()->set('theme', $profile->theme_preference);
                return $profile->theme_preference;
            }
        }

        return 'dark';
    }

    /**
     * Set active visual theme
     */
    public static function setTheme(string $theme): bool {
        if (!in_array($theme, self::$supportedThemes, true)) {
            return false;
        }

        session()->set('theme', $theme);

        $userId = (int)session()->get('user_id', 0);
        if ($userId > 0) {
            $profile = UserProfile::forUser($userId);
            $profile->theme_preference = $theme;
            $profile->save();
        }

        return true;
    }

    /**
     * Supported visual themes
     */
    public static function getSupportedThemes(): array {
        return [
            'dark' => ['name' => 'Dark Modern', 'icon' => 'moon', 'description' => 'Default sleek dark interface optimized for low glare'],
            'light' => ['name' => 'Light Clean', 'icon' => 'sun', 'description' => 'Bright, high-clarity daylight theme'],
            'high-contrast' => ['name' => 'High Contrast', 'icon' => 'eye', 'description' => 'WCAG AAA certified high-contrast palette for maximum readability'],
            'system' => ['name' => 'System Automatic', 'icon' => 'monitor', 'description' => 'Follows your operating system color scheme'],
        ];
    }
}
