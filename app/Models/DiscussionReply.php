<?php
declare(strict_types=1);

namespace App\Models;

class DiscussionReply extends Model {
    protected string $table = 'discussion_replies';
    protected array $fillable = [
        'thread_id',
        'user_id',
        'body',
        'is_solution',
        'parent_reply_id',
        'created_at',
        'updated_at',
    ];

    public function thread(): ?DiscussionThread {
        $row = DiscussionThread::find((int)$this->thread_id);
        return $row ? new DiscussionThread($row->toArray()) : null;
    }

    public function author(): ?User {
        return User::find((int)$this->user_id);
    }

    public function isSolution(): bool {
        return (int)($this->is_solution ?? 0) === 1;
    }
}
