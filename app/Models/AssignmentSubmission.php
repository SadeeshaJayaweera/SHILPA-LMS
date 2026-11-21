<?php
declare(strict_types=1);

namespace App\Models;

class AssignmentSubmission extends Model {
    protected string $table = 'assignment_submissions';
    protected array $fillable = [
        'assignment_id',
        'student_id',
        'submission_text',
        'attachment_path',
        'score',
        'feedback',
        'status',
        'submitted_at',
        'created_at',
        'updated_at',
    ];

    public function assignment(): ?Assignment {
        return Assignment::find((int)$this->assignment_id);
    }

    public function student(): ?User {
        return User::find((int)$this->student_id);
    }

    public function isGraded(): bool {
        return $this->status === 'graded';
    }

    public function isLate(): bool {
        return $this->status === 'late';
    }
}
