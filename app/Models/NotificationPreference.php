<?php
declare(strict_types=1);

namespace App\Models;

class NotificationPreference extends Model {
    protected string $table = 'notification_preferences';
    protected array $fillable = [
        'user_id',
        'email_enabled',
        'assignment_alerts',
        'announcement_alerts',
        'grade_alerts',
        'discussion_alerts',
        'created_at',
        'updated_at',
    ];

    public static function forUser(int $userId): self {
        $row = self::query()->where('user_id', $userId)->first();
        if ($row) {
            return new self($row);
        }

        $now = date('Y-m-d H:i:s');
        return self::create([
            'user_id' => $userId,
            'email_enabled' => 1,
            'assignment_alerts' => 1,
            'announcement_alerts' => 1,
            'grade_alerts' => 1,
            'discussion_alerts' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    public function allowsEmail(): bool {
        return (int)($this->email_enabled ?? 1) === 1;
    }

    public function allowsCategory(string $category): bool {
        if (!$this->allowsEmail()) {
            return false;
        }

        return match ($category) {
            'assignment' => (int)($this->assignment_alerts ?? 1) === 1,
            'announcement' => (int)($this->announcement_alerts ?? 1) === 1,
            'grade' => (int)($this->grade_alerts ?? 1) === 1,
            'discussion' => (int)($this->discussion_alerts ?? 1) === 1,
            default => true,
        };
    }
}
