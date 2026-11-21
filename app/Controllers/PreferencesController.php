<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Models\User;
use App\Models\UserProfile;
use App\Services\LocalizationService;
use App\Services\ThemeService;

class PreferencesController extends Controller {
    /**
     * Show Preferences & Accessibility Center
     */
    public function index(Request $request): Response {
        $userId = (int)session()->get('user_id', 0);
        $profile = $userId > 0 ? UserProfile::forUser($userId) : null;

        $currentLocale = LocalizationService::getLocale();
        $supportedLocales = LocalizationService::getSupportedLocales();

        $currentTheme = ThemeService::getTheme();
        $supportedThemes = ThemeService::getSupportedThemes();

        $data = [
            'currentLocale' => $currentLocale,
            'supportedLocales' => $supportedLocales,
            'currentTheme' => $currentTheme,
            'supportedThemes' => $supportedThemes,
            'profile' => $profile,
            'title' => 'Preferences, Accessibility & Language — SHILPA LMS',
        ];

        return $this->render('preferences/index', array_merge($data, ['data' => $data]));
    }

    /**
     * Update visual theme preference
     */
    public function setTheme(Request $request): Response {
        $theme = (string)($request->input('theme') ?? 'dark');
        $success = ThemeService::setTheme($theme);

        if ($request->isJson() || $request->header('Accept') === 'application/json') {
            return $this->json([
                'success' => $success,
                'theme' => ThemeService::getTheme(),
            ]);
        }

        session()->flash('success', 'Theme preference updated successfully.');
        return $this->redirect('/preferences');
    }

    /**
     * Update language / locale preference
     */
    public function setLocale(Request $request): Response {
        $locale = (string)($request->input('locale') ?? 'en');
        $success = LocalizationService::setLocale($locale);

        if ($request->isJson() || $request->header('Accept') === 'application/json') {
            return $this->json([
                'success' => $success,
                'locale' => LocalizationService::getLocale(),
            ]);
        }

        session()->flash('success', 'Language preference updated successfully.');
        return $this->redirect('/preferences');
    }

    /**
     * API endpoint to update all preferences asynchronously
     */
    public function apiUpdate(Request $request): Response {
        $theme = $request->input('theme');
        if ($theme !== null) {
            ThemeService::setTheme((string)$theme);
        }

        $locale = $request->input('locale');
        if ($locale !== null) {
            LocalizationService::setLocale((string)$locale);
        }

        $userId = (int)session()->get('user_id', 0);
        if ($userId > 0) {
            $profile = UserProfile::forUser($userId);
            if ($request->has('reduced_motion')) {
                // Store in profile metadata or session
                session()->set('reduced_motion', (bool)$request->input('reduced_motion'));
            }
            if ($request->has('text_size')) {
                session()->set('text_size', (string)$request->input('text_size'));
            }
        }

        return $this->json([
            'status' => 'success',
            'theme' => ThemeService::getTheme(),
            'locale' => LocalizationService::getLocale(),
        ]);
    }
}
