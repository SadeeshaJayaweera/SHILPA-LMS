<?php
declare(strict_types=1);

namespace App\Models;

class Enrollment extends Model {
    protected string $table = 'enrollments';
    protected string $primaryKey = 'id';
    protected array $fillable = [
        'user_id',
        'course_id',
        'status',
        'role',
        'progress_percent',
        'enrolled_at',
        'expires_at',
        'completed_at',
    ];

    public function course(): ?Course {
        return Course::find((int)$this->course_id);
    }

    public function user(): ?User {
        return User::find((int)$this->user_id);
    }

    public function isActive(): bool {
        if ($this->status !== 'active') {
            return false;
        }
        if (!empty($this->expires_at) && strtotime((string)$this->expires_at) < time()) {
            return false;
        }
        return true;
    }

    public function isExpired(): bool {
        return !empty($this->expires_at) && strtotime((string)$this->expires_at) < time();
    }

    public static function isEnrolled(int $userId, int $courseId): bool {
        $res = self::query()
            ->where('user_id', $userId)
            ->where('course_id', $courseId)
            ->where('status', 'active')
            ->first();
        return $res !== null;
    }

    public static function forUser(int $userId): array {
        $rows = self::query()
            ->where('user_id', $userId)
            ->orderBy('enrolled_at', 'DESC')
            ->get();
        return array_map(fn($r) => new self($r), $rows);
    }

    public static function rosterForCourse(int $courseId): array {
        $rows = self::query()
            ->where('course_id', $courseId)
            ->orderBy('enrolled_at', 'DESC')
            ->get();
        return array_map(fn($r) => new self($r), $rows);
    }
}
