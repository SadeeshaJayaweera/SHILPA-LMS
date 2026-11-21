<?php
declare(strict_types=1);

namespace App\Models;

class Notification extends Model {
    protected string $table = 'notifications';
    protected array $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'action_url',
        'icon',
        'is_read',
        'read_at',
        'created_at',
        'updated_at',
    ];

    public static function forUser(int $userId, bool $unreadOnly = false, int $limit = 50): array {
        $q = self::query()->where('user_id', $userId);
        if ($unreadOnly) {
            $q->where('is_read', 0);
        }
        $rows = $q->orderBy('created_at', 'DESC')->limit($limit)->get();
        return array_map(fn($r) => new self($r), $rows);
    }

    public function user(): ?User {
        return User::find((int)$this->user_id);
    }

    public function isRead(): bool {
        return (int)($this->is_read ?? 0) === 1;
    }

    public function markAsRead(): bool {
        $this->is_read = 1;
        $this->read_at = date('Y-m-d H:i:s');
        $this->updated_at = $this->read_at;
        return $this->save();
    }
}
