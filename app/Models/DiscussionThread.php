<?php
declare(strict_types=1);

namespace App\Models;

class DiscussionThread extends Model {
    protected string $table = 'discussion_threads';
    protected array $fillable = [
        'course_id',
        'user_id',
        'title',
        'body',
        'is_pinned',
        'is_locked',
        'replies_count',
        'created_at',
        'updated_at',
    ];

    public static function forCourse(int $courseId): array {
        $rows = self::query()
            ->where('course_id', $courseId)
            ->orderBy('is_pinned', 'DESC')
            ->orderBy('updated_at', 'DESC')
            ->get();
        return array_map(fn($r) => new self($r), $rows);
    }

    public function course(): ?Course {
        return Course::find((int)$this->course_id);
    }

    public function author(): ?User {
        return User::find((int)$this->user_id);
    }

    public function replies(): array {
        $rows = DiscussionReply::query()
            ->where('thread_id', (int)$this->id)
            ->orderBy('created_at', 'ASC')
            ->get();
        return array_map(fn($r) => new DiscussionReply($r), $rows);
    }

    public function isPinned(): bool {
        return (int)($this->is_pinned ?? 0) === 1;
    }

    public function isLocked(): bool {
        return (int)($this->is_locked ?? 0) === 1;
    }
}
