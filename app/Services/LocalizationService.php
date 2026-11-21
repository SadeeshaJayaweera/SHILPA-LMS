<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Models\UserProfile;

class LocalizationService {
    protected static array $supportedLocales = [
        'en' => ['name' => 'English', 'native' => 'English', 'flag' => 'US', 'dir' => 'ltr'],
        'si' => ['name' => 'Sinhala', 'native' => 'සිංහල', 'flag' => 'LK', 'dir' => 'ltr'],
        'ta' => ['name' => 'Tamil', 'native' => 'தமிழ்', 'flag' => 'LK', 'dir' => 'ltr'],
        'es' => ['name' => 'Spanish', 'native' => 'Español', 'flag' => 'ES', 'dir' => 'ltr'],
    ];

    protected static array $loadedLines = []; // [locale][group] => array

    /**
     * Get current active locale (from session, user profile, or default 'en')
     */
    public static function getLocale(): string {
        if (session()->has('locale')) {
            $loc = (string)session()->get('locale');
            if (isset(self::$supportedLocales[$loc])) {
                return $loc;
            }
        }

        // Check authenticated user profile preference if available
        $userId = (int)session()->get('user_id', 0);
        if ($userId > 0) {
            $profile = UserProfile::forUser($userId);
            if (!empty($profile->preferred_language) && isset(self::$supportedLocales[$profile->preferred_language])) {
                session()->set('locale', $profile->preferred_language);
                return $profile->preferred_language;
            }
        }

        return 'en';
    }

    /**
     * Set active locale
     */
    public static function setLocale(string $locale): bool {
        if (!isset(self::$supportedLocales[$locale])) {
            return false;
        }

        session()->set('locale', $locale);

        $userId = (int)session()->get('user_id', 0);
        if ($userId > 0) {
            $profile = UserProfile::forUser($userId);
            $profile->preferred_language = $locale;
            $profile->save();
        }

        return true;
    }

    /**
     * Get all supported locales
     */
    public static function getSupportedLocales(): array {
        return self::$supportedLocales;
    }

    /**
     * Translate key with optional parameter replacements and fallback to English
     */
    public static function trans(string $key, array $replace = [], ?string $locale = null): string {
        $locale = $locale ?? self::getLocale();
        if (!isset(self::$supportedLocales[$locale])) {
            $locale = 'en';
        }

        $parts = explode('.', $key, 2);
        if (count($parts) < 2) {
            return $key;
        }

        [$group, $item] = $parts;

        // Try target locale
        $translation = self::getLine($locale, $group, $item);

        // Fallback to English if missing
        if ($translation === null && $locale !== 'en') {
            $translation = self::getLine('en', $group, $item);
        }

        if ($translation === null) {
            return $key;
        }

        // Replace parameters :param
        foreach ($replace as $placeholder => $value) {
            $translation = str_replace(
                [':' . $placeholder, ':' . strtoupper($placeholder), ':' . ucfirst($placeholder)],
                [(string)$value, strtoupper((string)$value), ucfirst((string)$value)],
                $translation
            );
        }

        return $translation;
    }

    /**
     * Helper to load and retrieve translation line
     */
    protected static function getLine(string $locale, string $group, string $item): ?string {
        if (!isset(self::$loadedLines[$locale][$group])) {
            $path = dirname(__DIR__, 2) . "/resources/lang/{$locale}/{$group}.php";
            if (file_exists($path)) {
                self::$loadedLines[$locale][$group] = require $path;
            } else {
                self::$loadedLines[$locale][$group] = [];
            }
        }

        return self::$loadedLines[$locale][$group][$item] ?? null;
    }
}
