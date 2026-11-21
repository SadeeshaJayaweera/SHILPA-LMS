<?php
declare(strict_types=1);

namespace App\Models;

class LessonBlock extends Model {
    protected string $table = 'lesson_blocks';
    protected array $fillable = [
        'lesson_id',
        'block_type',
        'content',
        'order_index',
        'created_at',
        'updated_at',
    ];

    public static function forLesson(int $lessonId): array {
        $rows = self::query()
            ->where('lesson_id', $lessonId)
            ->orderBy('order_index', 'ASC')
            ->get();
        return array_map(fn($r) => new self($r), $rows);
    }

    public function payload(): array {
        $raw = (string)($this->content ?? '{}');
        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : ['text' => $raw];
    }

    public function renderHtml(): string {
        $payload = $this->payload();
        $type = (string)$this->block_type;

        return match ($type) {
            'code_snippet' => '<pre class="code-block" style="background:#1e293b; color:#f8fafc; padding:1rem; border-radius:8px; overflow-x:auto;"><code>' . htmlspecialchars($payload['code'] ?? '', ENT_QUOTES) . '</code></pre>',
            'callout' => '<div class="callout-box" style="padding:1rem; border-left:4px solid var(--primary); background:var(--bg-surface); border-radius:4px; margin:1rem 0;">' . htmlspecialchars($payload['text'] ?? '', ENT_QUOTES) . '</div>',
            'video' => '<div style="aspect-ratio:16/9; margin:1rem 0;"><iframe src="' . htmlspecialchars($payload['url'] ?? '', ENT_QUOTES) . '" style="width:100%; height:100%; border:none;" allowfullscreen></iframe></div>',
            default => '<div class="rich-text-block">' . nl2br(htmlspecialchars($payload['text'] ?? '', ENT_QUOTES)) . '</div>',
        };
    }
}
