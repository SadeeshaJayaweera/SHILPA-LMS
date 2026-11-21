<?php
declare(strict_types=1);

namespace App\Jobs;

use App\Queue\JobInterface;
use App\Services\AnalyticsService;

class RecalculateCourseAnalyticsJob implements JobInterface {
    public int $courseId;

    public function __construct(int $courseId) {
        $this->courseId = $courseId;
    }

    public function handle(): void {
        AnalyticsService::getCourseEngagementMetrics($this->courseId);
    }
}
