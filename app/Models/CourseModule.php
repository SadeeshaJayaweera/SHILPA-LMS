<?php
declare(strict_types=1);

namespace App\Models;

class CourseModule extends Model {
    protected string $table = 'modules';
    protected array $fillable = [
        'course_id',
        'title',
        'description',
        'order_index',
        'is_published',
        'created_at',
        'updated_at',
    ];

    public static function forCourse(int $courseId): array {
        $rows = self::query()
            ->where('course_id', $courseId)
            ->where('is_published', 1)
            ->orderBy('order_index', 'ASC')
            ->get();
        return array_map(fn($r) => new self($r), $rows);
    }

    public function lessons(): array {
        $rows = Lesson::query()
            ->where('module_id', $this->id)
            ->orderBy('order_index', 'ASC')
            ->get();
        return array_map(fn($r) => new Lesson($r), $rows);
    }

    public function totalDurationMinutes(): int {
        $lessons = $this->lessons();
        $total = 0;
        foreach ($lessons as $lesson) {
            $total += (int)($lesson->duration_minutes ?? 15);
        }
        return $total;
    }
}
