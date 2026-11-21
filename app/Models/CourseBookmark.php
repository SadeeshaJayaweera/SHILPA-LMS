<?php
declare(strict_types=1);

namespace App\Models;

class CourseBookmark extends Model {
    protected string $table = 'course_bookmarks';
    protected array $fillable = [
        'user_id',
        'course_id',
        'created_at',
        'updated_at',
    ];

    public static function hasBookmarked(int $userId, int $courseId): bool {
        return self::query()
            ->where('user_id', $userId)
            ->where('course_id', $courseId)
            ->exists();
    }

    public static function forUser(int $userId): array {
        $rows = self::query()
            ->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->get();
        return array_map(fn($r) => new self($r), $rows);
    }

    public function course(): ?Course {
        return Course::find((int)$this->course_id);
    }

    public function user(): ?User {
        return User::find((int)$this->user_id);
    }
}
