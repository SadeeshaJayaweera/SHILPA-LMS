<?php
declare(strict_types=1);

namespace App\Models;

class CourseWaitlist extends Model {
    protected string $table = 'course_waitlists';
    protected array $fillable = [
        'course_id',
        'user_id',
        'position',
        'status',
        'created_at',
        'updated_at',
    ];

    public static function nextPosition(int $courseId): int {
        $last = self::query()
            ->where('course_id', $courseId)
            ->orderBy('position', 'DESC')
            ->first();
        return $last ? ((int)$last['position'] + 1) : 1;
    }
}
