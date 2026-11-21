<?php
declare(strict_types=1);

namespace App\Models;

class CourseGrade extends Model {
    protected string $table = 'course_grades';
    protected array $fillable = [
        'course_id',
        'student_id',
        'total_score',
        'max_score',
        'percentage',
        'letter_grade',
        'is_published',
        'published_at',
        'created_at',
        'updated_at',
    ];

    public static function forCourse(int $courseId): array {
        $rows = self::query()
            ->where('course_id', $courseId)
            ->get();
        return array_map(fn($r) => new self($r), $rows);
    }

    public static function forStudent(int $studentId, bool $publishedOnly = true): array {
        $q = self::query()->where('student_id', $studentId);
        if ($publishedOnly) {
            $q->where('is_published', 1);
        }
        $rows = $q->get();
        return array_map(fn($r) => new self($r), $rows);
    }

    public static function calculateLetter(int $percentage): string {
        return match (true) {
            $percentage >= 90 => 'A+',
            $percentage >= 85 => 'A',
            $percentage >= 80 => 'A-',
            $percentage >= 75 => 'B+',
            $percentage >= 70 => 'B',
            $percentage >= 65 => 'B-',
            $percentage >= 55 => 'C',
            default => 'F',
        };
    }

    public function course(): ?Course {
        return Course::find((int)$this->course_id);
    }

    public function student(): ?User {
        return User::find((int)$this->student_id);
    }

    public function isPublished(): bool {
        return (int)($this->is_published ?? 0) === 1;
    }
}
