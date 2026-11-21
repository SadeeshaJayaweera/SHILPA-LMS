<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonCompletion;
use App\Security\SecurityAuditLog;
use RuntimeException;

class ProgressTrackingService {
    public static function toggleLessonCompletion(int $userId, int $courseId, int $lessonId, int $timeSpent = 0): array {
        $existing = LessonCompletion::query()
            ->where('user_id', $userId)
            ->where('lesson_id', $lessonId)
            ->first();

        if ($existing) {
            return self::markLessonIncomplete($userId, $courseId, $lessonId);
        }

        return self::markLessonComplete($userId, $courseId, $lessonId, $timeSpent);
    }

    public static function markLessonComplete(int $userId, int $courseId, int $lessonId, int $timeSpent = 0): array {
        $lesson = Lesson::find($lessonId);
        if (!$lesson) {
            throw new RuntimeException("Lesson not found: $lessonId");
        }

        $now = date('Y-m-d H:i:s');
        $existing = LessonCompletion::query()
            ->where('user_id', $userId)
            ->where('lesson_id', $lessonId)
            ->first();

        if (!$existing) {
            LessonCompletion::create([
                'user_id' => $userId,
                'course_id' => $courseId,
                'lesson_id' => $lessonId,
                'time_spent_seconds' => $timeSpent > 0 ? $timeSpent : (int)($lesson->duration_minutes ?? 15) * 60,
                'completed_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $progress = self::recalculateCourseProgress($userId, $courseId);

        SecurityAuditLog::log('learning.lesson.completed', 'info', $userId, null, [
            'course_id' => $courseId,
            'lesson_id' => $lessonId,
            'progress_percent' => $progress['progress_percent'],
        ]);

        return array_merge($progress, ['is_completed' => true]);
    }

    public static function markLessonIncomplete(int $userId, int $courseId, int $lessonId): array {
        LessonCompletion::query()
            ->where('user_id', $userId)
            ->where('lesson_id', $lessonId)
            ->delete();

        $progress = self::recalculateCourseProgress($userId, $courseId);

        return array_merge($progress, ['is_completed' => false]);
    }

    public static function recalculateCourseProgress(int $userId, int $courseId): array {
        $modules = CourseModule::forCourse($courseId);
        $totalLessons = 0;
        foreach ($modules as $mod) {
            $totalLessons += count($mod->lessons());
        }

        $completedIds = LessonCompletion::completedLessonIdsForCourse($userId, $courseId);
        $completedCount = count($completedIds);

        $percent = $totalLessons > 0 ? (int)round(($completedCount / $totalLessons) * 100) : 0;
        $percent = min(100, max(0, $percent));

        // Update enrollment progress
        $enrollmentRow = Enrollment::query()
            ->where('user_id', $userId)
            ->where('course_id', $courseId)
            ->first();

        if ($enrollmentRow) {
            $enrollment = new Enrollment($enrollmentRow);
            $enrollment->progress_percent = $percent;
            if ($percent >= 100) {
                $enrollment->status = 'completed';
                $enrollment->completed_at = date('Y-m-d H:i:s');
            } elseif ($enrollment->status === 'completed') {
                $enrollment->status = 'active';
            }
            $enrollment->save();
        }

        return [
            'total_lessons' => $totalLessons,
            'completed_lessons' => $completedCount,
            'progress_percent' => $percent,
            'completed_lesson_ids' => $completedIds,
            'is_course_completed' => $percent >= 100,
        ];
    }

    public static function getCourseProgress(int $userId, int $courseId): array {
        return self::recalculateCourseProgress($userId, $courseId);
    }
}
