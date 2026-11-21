<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\ActiveSession;
use App\Models\User;
use App\Models\UserProfile;
use App\Security\PasswordService;

class ProfileService {
    public static function getProfileData(int $userId, string $currentSessionId): array {
        $user = User::find($userId);
        $profile = UserProfile::forUser($userId);
        $sessions = ActiveSession::forUser($userId);

        // Mark current session
        foreach ($sessions as &$s) {
            $s['is_current'] = ($s['id'] === $currentSessionId);
        }
        unset($s);

        return [
            'user' => $user,
            'profile' => $profile,
            'sessions' => $sessions,
        ];
    }

    public static function updateGeneralProfile(int $userId, array $data, ?array $file = null): array {
        $user = User::find($userId);
        if (!$user) {
            return ['success' => false, 'error' => 'User not found.'];
        }

        $fullName = trim((string)($data['full_name'] ?? $data['Full_Name'] ?? ''));
        if (empty($fullName)) {
            return ['success' => false, 'error' => 'Full name cannot be empty.'];
        }

        $user->Full_Name = $fullName;
        $user->save();

        $profileData = [
            'headline' => trim((string)($data['headline'] ?? '')),
            'bio' => trim((string)($data['bio'] ?? '')),
            'phone' => trim((string)($data['phone'] ?? '')),
        ];

        // Avatar handling if uploaded
        if ($file && isset($file['tmp_name']) && is_uploaded_file($file['tmp_name'])) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            if (in_array($mime, $allowedTypes, true) && $file['size'] <= 2 * 1024 * 1024) {
                $ext = $mime === 'image/png' ? 'png' : ($mime === 'image/webp' ? 'webp' : 'jpg');
                $filename = 'avatar_' . $userId . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
                $uploadDir = __DIR__ . '/../../public/uploads/avatars';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                $target = $uploadDir . '/' . $filename;
                if (move_uploaded_file($file['tmp_name'], $target)) {
                    $profileData['avatar_url'] = '/uploads/avatars/' . $filename;
                }
            }
        } elseif (!empty($data['avatar_url'])) {
            $profileData['avatar_url'] = filter_var($data['avatar_url'], FILTER_SANITIZE_URL);
        }

        $profile = UserProfile::updateForUser($userId, $profileData);

        return [
            'success' => true,
            'message' => 'Profile updated successfully.',
            'profile' => $profile,
            'user' => $user,
        ];
    }

    public static function updatePreferences(int $userId, array $data): array {
        $allowedThemes = ['light', 'dark', 'system'];
        $theme = in_array($data['theme_preference'] ?? '', $allowedThemes, true)
            ? (string)$data['theme_preference']
            : 'system';

        $allowedLanguages = ['en', 'si', 'ta'];
        $language = in_array($data['language'] ?? '', $allowedLanguages, true)
            ? (string)$data['language']
            : 'en';

        $timezone = trim((string)($data['timezone'] ?? 'Asia/Colombo'));
        $emailNotifications = !empty($data['email_notifications']) ? 1 : 0;
        $securityAlerts = !empty($data['security_alerts']) ? 1 : 0;

        $profile = UserProfile::updateForUser($userId, [
            'theme_preference' => $theme,
            'language' => $language,
            'timezone' => $timezone,
            'email_notifications' => $emailNotifications,
            'security_alerts' => $securityAlerts,
        ]);

        return [
            'success' => true,
            'message' => 'Account preferences saved.',
            'profile' => $profile,
        ];
    }

    public static function updatePassword(
        int $userId,
        string $currentPassword,
        string $newPassword,
        string $confirmPassword,
        string $currentSessionId = ''
    ): array {
        $user = User::find($userId);
        if (!$user) {
            return ['success' => false, 'error' => 'User not found.'];
        }

        if (!PasswordService::verify($currentPassword, (string)$user->Password)) {
            return ['success' => false, 'error' => 'The current password you provided is incorrect.'];
        }

        if ($newPassword !== $confirmPassword) {
            return ['success' => false, 'error' => 'New password and confirmation do not match.'];
        }

        $strengthErrors = PasswordService::validateStrength($newPassword);
        if (!empty($strengthErrors)) {
            return ['success' => false, 'error' => implode(' ', $strengthErrors)];
        }

        $user->Password = PasswordService::hash($newPassword);
        $user->save();

        // Security best practice: terminate other active sessions upon password reset
        if ($currentSessionId !== '') {
            ActiveSession::terminateOthers($userId, $currentSessionId);
        }

        return [
            'success' => true,
            'message' => 'Password changed successfully. All other active sessions have been signed out.',
        ];
    }

    public static function terminateSession(int $userId, string $sessionId): bool {
        return ActiveSession::terminate($userId, $sessionId);
    }

    public static function terminateOtherSessions(int $userId, string $currentSessionId): int {
        return ActiveSession::terminateOthers($userId, $currentSessionId);
    }
}
