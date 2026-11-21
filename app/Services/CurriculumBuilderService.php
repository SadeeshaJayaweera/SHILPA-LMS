<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Lesson;
use App\Models\LessonBlock;

class CurriculumBuilderService {
    public static function createModule(int $courseId, array $data): CourseModule {
        $maxOrder = CourseModule::query()->where('course_id', $courseId)->count();
        $now = date('Y-m-d H:i:s');

        return CourseModule::create([
            'course_id' => $courseId,
            'title' => trim((string)($data['title'] ?? 'New Module')),
            'description' => trim((string)($data['description'] ?? '')),
            'order_index' => (int)($data['order_index'] ?? ($maxOrder + 1)),
            'is_published' => isset($data['is_published']) ? (int)$data['is_published'] : 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    public static function updateModule(int $moduleId, array $data): CourseModule {
        $module = CourseModule::find($moduleId);
        if (!$module) {
            throw new \RuntimeException("Module not found: $moduleId");
        }

        if (isset($data['title'])) {
            $module->title = trim((string)$data['title']);
        }
        if (isset($data['description'])) {
            $module->description = trim((string)$data['description']);
        }
        if (isset($data['order_index'])) {
            $module->order_index = (int)$data['order_index'];
        }
        if (isset($data['is_published'])) {
            $module->is_published = (int)$data['is_published'];
        }
        $module->updated_at = date('Y-m-d H:i:s');
        $module->save();

        return $module;
    }

    public static function deleteModule(int $moduleId): bool {
        $module = CourseModule::find($moduleId);
        if (!$module) {
            return false;
        }

        // Delete cascade lessons and blocks
        $lessons = $module->lessons();
        foreach ($lessons as $l) {
            self::deleteLesson((int)$l->id);
        }

        return $module->delete();
    }

    public static function reorderModules(int $courseId, array $moduleIdsInOrder): bool {
        foreach ($moduleIdsInOrder as $order => $mId) {
            $mod = CourseModule::find((int)$mId);
            if ($mod && (int)$mod->course_id === $courseId) {
                $mod->order_index = $order + 1;
                $mod->save();
            }
        }
        return true;
    }

    public static function createLesson(int $moduleId, array $data): Lesson {
        $module = CourseModule::find($moduleId);
        if (!$module) {
            throw new \RuntimeException("Module not found: $moduleId");
        }

        $maxOrder = Lesson::query()->where('module_id', $moduleId)->count();
        $title = trim((string)($data['title'] ?? 'New Lecture'));
        $slug = preg_replace('/[^a-z0-9]+/i', '-', strtolower($title)) ?: 'lesson-' . time();
        $now = date('Y-m-d H:i:s');

        return Lesson::create([
            'module_id' => $moduleId,
            'title' => $title,
            'slug' => $slug,
            'content_type' => in_array($data['content_type'] ?? '', ['video', 'article', 'interactive', 'quiz'], true) ? (string)$data['content_type'] : 'video',
            'content_body' => trim((string)($data['content_body'] ?? '')),
            'video_url' => trim((string)($data['video_url'] ?? '')),
            'duration_minutes' => (int)($data['duration_minutes'] ?? 15),
            'order_index' => (int)($data['order_index'] ?? ($maxOrder + 1)),
            'is_free_preview' => !empty($data['is_free_preview']) ? 1 : 0,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    public static function updateLesson(int $lessonId, array $data): Lesson {
        $lesson = Lesson::find($lessonId);
        if (!$lesson) {
            throw new \RuntimeException("Lesson not found: $lessonId");
        }

        if (isset($data['title'])) {
            $lesson->title = trim((string)$data['title']);
        }
        if (isset($data['content_type'])) {
            $lesson->content_type = (string)$data['content_type'];
        }
        if (isset($data['content_body'])) {
            $lesson->content_body = trim((string)$data['content_body']);
        }
        if (isset($data['video_url'])) {
            $lesson->video_url = trim((string)$data['video_url']);
        }
        if (isset($data['duration_minutes'])) {
            $lesson->duration_minutes = (int)$data['duration_minutes'];
        }
        if (isset($data['is_free_preview'])) {
            $lesson->is_free_preview = !empty($data['is_free_preview']) ? 1 : 0;
        }
        $lesson->updated_at = date('Y-m-d H:i:s');
        $lesson->save();

        return $lesson;
    }

    public static function deleteLesson(int $lessonId): bool {
        $lesson = Lesson::find($lessonId);
        if (!$lesson) {
            return false;
        }

        // Clean up blocks
        $blocks = LessonBlock::query()->where('lesson_id', $lessonId)->get();
        foreach ($blocks as $b) {
            (new LessonBlock($b))->delete();
        }

        return $lesson->delete();
    }

    public static function reorderLessons(int $moduleId, array $lessonIdsInOrder): bool {
        foreach ($lessonIdsInOrder as $order => $lId) {
            $lesson = Lesson::find((int)$lId);
            if ($lesson && (int)$lesson->module_id === $moduleId) {
                $lesson->order_index = $order + 1;
                $lesson->save();
            }
        }
        return true;
    }

    public static function addLessonBlock(int $lessonId, string $type, array $payload, int $orderIndex = 0): LessonBlock {
        $now = date('Y-m-d H:i:s');
        return LessonBlock::create([
            'lesson_id' => $lessonId,
            'block_type' => $type,
            'content' => json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            'order_index' => $orderIndex,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
