<?php
declare(strict_types=1);

namespace App\Models;

class LessonCompletion extends Model {
    protected string $table = 'lesson_completions';
    protected array $fillable = [
        'user_id',
        'course_id',
        'lesson_id',
        'time_spent_seconds',
        'completed_at',
        'created_at',
        'updated_at',
    ];

    public static function isCompleted(int $userId, int $lessonId): bool {
        return self::query()
            ->where('user_id', $userId)
            ->where('lesson_id', $lessonId)
            ->exists();
    }

    public static function completedLessonIdsForCourse(int $userId, int $courseId): array {
        $rows = self::query()
            ->where('user_id', $userId)
            ->where('course_id', $courseId)
            ->get();
        return array_map(fn($r) => (int)$r['lesson_id'], $rows);
    }
}
