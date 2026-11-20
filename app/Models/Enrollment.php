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
        'progress_percent',
        'enrolled_at',
        'completed_at',
    ];

    public static function isEnrolled(int $userId, int $courseId): bool {
        $res = self::query()
            ->where('user_id', $userId)
            ->where('course_id', $courseId)
            ->first();
        return $res !== null;
    }
}
