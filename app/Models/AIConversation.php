<?php
declare(strict_types=1);

namespace App\Models;

class AIConversation extends Model {
    protected string $table = 'ai_conversations';
    protected array $fillable = [
        'user_id',
        'course_id',
        'lesson_id',
        'title',
        'created_at',
        'updated_at',
    ];

    public function user(): ?User {
        return User::find((int)$this->user_id);
    }

    public function course(): ?Course {
        if (!$this->course_id) {
            return null;
        }
        return Course::find((int)$this->course_id);
    }

    public function lesson(): ?Lesson {
        if (!$this->lesson_id) {
            return null;
        }
        return Lesson::find((int)$this->lesson_id);
    }

    public function messages(): array {
        $rows = AIMessage::query()
            ->where('conversation_id', (int)$this->id)
            ->orderBy('id', 'ASC')
            ->get();
        return array_map(fn($r) => new AIMessage($r), $rows);
    }
}
