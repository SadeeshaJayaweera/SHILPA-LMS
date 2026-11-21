<?php
declare(strict_types=1);

namespace App\Models;

class QuizAttempt extends Model {
    protected string $table = 'quiz_attempts';
    protected array $fillable = [
        'quiz_id',
        'student_id',
        'attempt_number',
        'score_achieved',
        'max_score',
        'is_passed',
        'answers_json',
        'started_at',
        'submitted_at',
        'created_at',
        'updated_at',
    ];

    public function quiz(): ?Quiz {
        return Quiz::find((int)$this->quiz_id);
    }

    public function student(): ?User {
        return User::find((int)$this->student_id);
    }

    public function percentage(): int {
        $max = (int)($this->max_score ?? 0);
        if ($max <= 0) {
            return 0;
        }
        return (int)round(((int)$this->score_achieved / $max) * 100);
    }

    public function answers(): array {
        $raw = (string)($this->answers_json ?? '{}');
        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : [];
    }

    public function isPassed(): bool {
        return (int)($this->is_passed ?? 0) === 1;
    }
}
