<?php
declare(strict_types=1);

namespace App\Models;

class DirectMessage extends Model {
    protected string $table = 'direct_messages';
    protected array $fillable = [
        'sender_id',
        'recipient_id',
        'message',
        'is_read',
        'read_at',
        'created_at',
        'updated_at',
    ];

    public function sender(): ?User {
        return User::find((int)$this->sender_id);
    }

    public function recipient(): ?User {
        return User::find((int)$this->recipient_id);
    }

    public function isRead(): bool {
        return (int)($this->is_read ?? 0) === 1;
    }
}
