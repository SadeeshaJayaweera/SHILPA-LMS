<?php
declare(strict_types=1);

namespace App\Models;

class CourseAnalyticsCache extends Model {
    protected string $table = 'course_analytics_cache';
    protected array $fillable = [
        'course_id',
        'enrolled_count',
        'active_learners_count',
        'completion_rate_percent',
        'avg_grade_percent',
        'at_risk_count',
        'metrics_data',
        'created_at',
        'updated_at',
    ];

    public function course(): ?Course {
        return Course::find((int)$this->course_id);
    }

    public function metrics(): array {
        if (empty($this->metrics_data)) {
            return [];
        }
        $data = json_decode((string)$this->metrics_data, true);
        return is_array($data) ? $data : [];
    }
}
