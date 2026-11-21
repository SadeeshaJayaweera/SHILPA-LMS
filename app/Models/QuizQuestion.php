<?php
declare(strict_types=1);

namespace App\Models;

class QuizQuestion extends Model {
    protected string $table = 'quiz_questions';
    protected array $fillable = [
        'quiz_id',
        'question_type',
        'question_text',
        'options_json',
        'correct_answer_json',
        'points',
        'order_index',
        'created_at',
        'updated_at',
    ];

    public function options(): array {
        $raw = (string)($this->options_json ?? '[]');
        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : [];
    }

    public function correctAnswer(): mixed {
        $raw = (string)($this->correct_answer_json ?? 'null');
        return json_decode($raw, true);
    }

    public function isCorrect(mixed $givenAnswer): bool {
        $correct = $this->correctAnswer();
        $type = (string)$this->question_type;

        if ($type === 'multiple_choice' && is_array($correct) && is_array($givenAnswer)) {
            sort($correct);
            sort($givenAnswer);
            return $correct === $givenAnswer;
        }

        if ($type === 'true_false' || $type === 'single_choice') {
            return (string)$correct === (string)$givenAnswer;
        }

        if ($type === 'short_answer') {
            return strtolower(trim((string)$correct)) === strtolower(trim((string)$givenAnswer));
        }

        return $correct == $givenAnswer;
    }
}
