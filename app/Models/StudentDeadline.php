<?php
declare(strict_types=1);

namespace App\Models;

class StudentDeadline extends Model {
    protected string $table = 'student_deadlines';
    protected string $primaryKey = 'id';
    protected array $fillable = [
        'user_id',
        'course_id',
        'title',
        'due_date',
        'type',
        'status',
        'weight_percent',
        'created_at',
    ];

    public static function upcomingForUser(int $userId, int $limit = 5): array {
        return self::query()
            ->where('user_id', $userId)
            ->where('status', 'pending')
            ->limit($limit)
            ->get();
    }
}
