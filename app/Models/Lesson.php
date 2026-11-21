<?php
declare(strict_types=1);

namespace App\Models;

class Lesson extends Model {
    protected string $table = 'lessons';
    protected array $fillable = [
        'module_id',
        'title',
        'slug',
        'content_type',
        'content_body',
        'video_url',
        'duration_minutes',
        'order_index',
        'is_free_preview',
        'created_at',
        'updated_at',
    ];

    public function module(): ?CourseModule {
        return CourseModule::find((int)$this->module_id);
    }

    public function isFreePreview(): bool {
        return (int)($this->is_free_preview ?? 0) === 1;
    }

    public function blocks(): array {
        return LessonBlock::forLesson((int)$this->id);
    }
}
