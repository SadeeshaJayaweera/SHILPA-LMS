<?php
declare(strict_types=1);

namespace App\Models;

class Announcement extends Model {
    protected string $table = 'announcements';
    protected array $fillable = [
        'course_id',
        'author_id',
        'title',
        'content',
        'is_pinned',
        'created_at',
        'updated_at',
    ];

    public static function forCourse(?int $courseId): array {
        $q = self::query();
        if ($courseId !== null) {
            $q->where('course_id', $courseId);
        }
        $rows = $q->orderBy('is_pinned', 'DESC')->orderBy('created_at', 'DESC')->get();
        return array_map(fn($r) => new self($r), $rows);
    }

    public function course(): ?Course {
        if (!$this->course_id) {
            return null;
        }
        return Course::find((int)$this->course_id);
    }

    public function author(): ?User {
        return User::find((int)$this->author_id);
    }

    public function isPinned(): bool {
        return (int)($this->is_pinned ?? 0) === 1;
    }
}
