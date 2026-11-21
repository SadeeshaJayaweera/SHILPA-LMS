<?php
declare(strict_types=1);

namespace App\Models;

class AIMessage extends Model {
    protected string $table = 'ai_messages';
    protected array $fillable = [
        'conversation_id',
        'role',
        'content',
        'tokens_used',
        'created_at',
        'updated_at',
    ];

    public function conversation(): ?AIConversation {
        return AIConversation::find((int)$this->conversation_id);
    }

    public function isUser(): bool {
        return $this->role === 'user';
    }

    public function isAssistant(): bool {
        return $this->role === 'assistant';
    }
}
