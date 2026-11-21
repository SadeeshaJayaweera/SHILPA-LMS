<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Enrollment;
use App\Models\Lesson;

class CourseWorkspaceService {
    public static function getWorkspaceOverview(int $courseId, ?int $userId = null): array {
        $course = Course::find($courseId);
        if (!$course) {
            throw new \RuntimeException("Course not found: $courseId");
        }

        $modules = CourseModule::forCourse($courseId);
        $totalLessons = 0;
        $totalMinutes = 0;

        foreach ($modules as $mod) {
            $lessons = $mod->lessons();
            $totalLessons += count($lessons);
            $totalMinutes += $mod->totalDurationMinutes();
        }

        $isEnrolled = false;
        $enrollment = null;
        if ($userId !== null && $userId > 0) {
            $isEnrolled = Enrollment::isEnrolled($userId, $courseId);
            $enrollment = Enrollment::query()
                ->where('user_id', $userId)
                ->where('course_id', $courseId)
                ->where('status', 'active')
                ->first();
        }

        return [
            'course' => $course,
            'metadata' => $course->metadata(),
            'modules' => $modules,
            'totalLessons' => $totalLessons,
            'totalMinutes' => $totalMinutes,
            'isEnrolled' => $isEnrolled,
            'enrollment' => $enrollment ? new Enrollment($enrollment) : null,
        ];
    }

    public static function getLearningTheater(int $courseId, int $lessonId, ?int $userId = null): array {
        $course = Course::find($courseId);
        if (!$course) {
            throw new \RuntimeException("Course not found: $courseId");
        }

        $lesson = Lesson::find($lessonId);
        if (!$lesson) {
            throw new \RuntimeException("Lesson not found: $lessonId");
        }

        $modules = CourseModule::forCourse($courseId);

        // Build flat ordered list of all lessons in course to find prev/next
        $allLessons = [];
        foreach ($modules as $mod) {
            foreach ($mod->lessons() as $l) {
                $allLessons[] = $l;
            }
        }

        $prevLesson = null;
        $nextLesson = null;
        $currentIndex = -1;

        foreach ($allLessons as $idx => $l) {
            if ((int)$l->id === $lessonId) {
                $currentIndex = $idx;
                break;
            }
        }

        if ($currentIndex > 0) {
            $prevLesson = $allLessons[$currentIndex - 1];
        }
        if ($currentIndex >= 0 && $currentIndex < count($allLessons) - 1) {
            $nextLesson = $allLessons[$currentIndex + 1];
        }

        $isEnrolled = ($userId !== null && $userId > 0) ? Enrollment::isEnrolled($userId, $courseId) : false;

        return [
            'course' => $course,
            'lesson' => $lesson,
            'currentModule' => $lesson->module(),
            'modules' => $modules,
            'allLessons' => $allLessons,
            'prevLesson' => $prevLesson,
            'nextLesson' => $nextLesson,
            'isEnrolled' => $isEnrolled,
        ];
    }
}
