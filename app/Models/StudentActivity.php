<?php
declare(strict_types=1);

namespace App\Models;

class StudentActivity extends Model {
    protected string $table = 'student_activities';
    protected string $primaryKey = 'id';
    protected array $fillable = [
        'user_id',
        'activity_type',
        'title',
        'description',
        'icon',
        'created_at',
    ];

    public static function forUser(int $userId, int $limit = 6): array {
        return self::query()
            ->where('user_id', $userId)
            ->limit($limit)
            ->get();
    }
}
