<?php
declare(strict_types=1);

namespace App\Services;

use App\Database\Connection;
use App\Models\Notification;
use App\Models\NotificationPreference;
use App\Models\User;
use App\Security\SecurityAuditLog;
use RuntimeException;

class NotificationService {
    public static function notify(
        int $userId,
        string $type,
        string $title,
        string $message,
        ?string $actionUrl = null,
        string $icon = 'bell'
    ): Notification {
        $user = User::find($userId);
        if (!$user) {
            throw new RuntimeException("User not found: $userId");
        }

        $now = date('Y-m-d H:i:s');
        $notification = Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => trim($title),
            'message' => trim($message),
            'action_url' => $actionUrl,
            'icon' => $icon,
            'is_read' => 0,
            'read_at' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Check user preferences for email dispatch
        $prefs = NotificationPreference::forUser($userId);
        $category = self::extractCategory($type);

        if ($prefs->allowsCategory($category) && !empty($user->Email)) {
            $html = MailService::buildTemplate(
                $title,
                'Dear ' . htmlspecialchars((string)$user->Full_Name) . ',',
                htmlspecialchars($message),
                $actionUrl ? 'View in SHILPA LMS' : null,
                $actionUrl
            );

            MailService::send(
                (string)$user->Email,
                (string)$user->Full_Name,
                "[SHILPA LMS] $title",
                $html
            );
        }

        SecurityAuditLog::log('notification.created', 'info', $userId, null, [
            'notification_id' => $notification->id,
            'type' => $type,
        ]);

        return $notification;
    }

    public static function getUserNotifications(int $userId, bool $unreadOnly = false, int $limit = 50): array {
        return Notification::forUser($userId, $unreadOnly, $limit);
    }

    public static function markAsRead(int $notificationId, int $userId): bool {
        $notif = Notification::find($notificationId);
        if (!$notif || (int)$notif->user_id !== $userId) {
            return false;
        }

        return $notif->markAsRead();
    }

    public static function markAllAsRead(int $userId): int {
        $pdo = Connection::get();
        $now = date('Y-m-d H:i:s');
        $stmt = $pdo->prepare('
            UPDATE notifications
            SET is_read = 1, read_at = :now, updated_at = :now
            WHERE user_id = :u AND is_read = 0
        ');
        $stmt->execute([':now' => $now, ':u' => $userId]);
        return $stmt->rowCount();
    }

    public static function getUnreadCount(int $userId): int {
        $pdo = Connection::get();
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM notifications WHERE user_id = :u AND is_read = 0');
        $stmt->execute([':u' => $userId]);
        return (int)$stmt->fetchColumn();
    }

    public static function getPreferences(int $userId): NotificationPreference {
        return NotificationPreference::forUser($userId);
    }

    public static function updatePreferences(int $userId, array $data): NotificationPreference {
        $prefs = NotificationPreference::forUser($userId);
        $prefs->email_enabled = !empty($data['email_enabled']) ? 1 : 0;
        $prefs->assignment_alerts = !empty($data['assignment_alerts']) ? 1 : 0;
        $prefs->announcement_alerts = !empty($data['announcement_alerts']) ? 1 : 0;
        $prefs->grade_alerts = !empty($data['grade_alerts']) ? 1 : 0;
        $prefs->discussion_alerts = !empty($data['discussion_alerts']) ? 1 : 0;
        $prefs->updated_at = date('Y-m-d H:i:s');
        $prefs->save();

        SecurityAuditLog::log('notification.preferences.updated', 'info', $userId, null, [
            'email_enabled' => $prefs->email_enabled,
        ]);

        return $prefs;
    }

    private static function extractCategory(string $type): string {
        if (str_starts_with($type, 'assignment')) {
            return 'assignment';
        }
        if (str_starts_with($type, 'announcement')) {
            return 'announcement';
        }
        if (str_starts_with($type, 'grade') || str_starts_with($type, 'quiz')) {
            return 'grade';
        }
        if (str_starts_with($type, 'discussion')) {
            return 'discussion';
        }
        return 'general';
    }
}
