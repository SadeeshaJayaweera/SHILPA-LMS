<?php
declare(strict_types=1);

namespace App\Models;

class Quiz extends Model {
    protected string $table = 'quizzes';
    protected array $fillable = [
        'course_id',
        'created_by',
        'title',
        'description',
        'time_limit_minutes',
        'passing_score_percent',
        'max_attempts',
        'is_published',
        'created_at',
        'updated_at',
    ];

    public static function forCourse(int $courseId, bool $publishedOnly = false): array {
        $q = self::query()->where('course_id', $courseId);
        if ($publishedOnly) {
            $q->where('is_published', 1);
        }
        $rows = $q->orderBy('id', 'ASC')->get();
        return array_map(fn($r) => new self($r), $rows);
    }

    public function questions(): array {
        $rows = QuizQuestion::query()
            ->where('quiz_id', $this->id)
            ->orderBy('order_index', 'ASC')
            ->get();
        return array_map(fn($r) => new QuizQuestion($r), $rows);
    }

    public function totalPoints(): int {
        $questions = $this->questions();
        return array_reduce($questions, fn($carry, $q) => $carry + (int)$q->points, 0);
    }

    public function attemptsForStudent(int $studentId): array {
        $rows = QuizAttempt::query()
            ->where('quiz_id', $this->id)
            ->where('student_id', $studentId)
            ->orderBy('attempt_number', 'DESC')
            ->get();
        return array_map(fn($r) => new QuizAttempt($r), $rows);
    }
}
