<?php
declare(strict_types=1);

namespace App\Models;

class LearningEvent extends Model {
    protected string $table = 'learning_events';
    protected array $fillable = [
        'user_id',
        'course_id',
        'event_type',
        'duration_seconds',
        'metadata',
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

    public function parsedMetadata(): array {
        if (empty($this->metadata)) {
            return [];
        }
        $data = json_decode((string)$this->metadata, true);
        return is_array($data) ? $data : [];
    }
}
