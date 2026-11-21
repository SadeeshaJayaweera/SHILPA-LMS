<?php
declare(strict_types=1);

namespace App\Models;

class Assignment extends Model {
    protected string $table = 'assignments';
    protected array $fillable = [
        'course_id',
        'created_by',
        'title',
        'description',
        'max_score',
        'due_at',
        'is_published',
        'created_at',
        'updated_at',
    ];

    public static function forCourse(int $courseId, bool $publishedOnly = false): array {
        $q = self::query()->where('course_id', $courseId);
        if ($publishedOnly) {
            $q->where('is_published', 1);
        }
        $rows = $q->orderBy('due_at', 'ASC')->get();
        return array_map(fn($r) => new self($r), $rows);
    }

    public function submissions(): array {
        $rows = AssignmentSubmission::query()
            ->where('assignment_id', $this->id)
            ->orderBy('id', 'DESC')
            ->get();
        return array_map(fn($r) => new AssignmentSubmission($r), $rows);
    }

    public function submissionForStudent(int $studentId): ?AssignmentSubmission {
        $row = AssignmentSubmission::query()
            ->where('assignment_id', $this->id)
            ->where('student_id', $studentId)
            ->first();
        return $row ? new AssignmentSubmission($row) : null;
    }

    public function isOverdue(): bool {
        if (empty($this->due_at)) {
            return false;
        }
        return strtotime((string)$this->due_at) < time();
    }
}
