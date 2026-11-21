<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Lesson;
use App\Services\CurriculumBuilderService;

class CurriculumBuilderController extends Controller {
    public function index(Request $request, $id): Response {
        if ($auth = $this->checkAuthorization()) {
            return $auth;
        }

        $course = Course::find((int)$id);
        if (!$course) {
            session()->flash('error', 'Course not found.');
            return $this->redirect('/faculty/courses');
        }

        $modules = CourseModule::allForCourse((int)$course->C_ID);
        $totalLessons = 0;
        $totalMinutes = 0;

        foreach ($modules as $mod) {
            $lessons = $mod->lessons();
            $totalLessons += count($lessons);
            $totalMinutes += $mod->totalDurationMinutes();
        }

        return $this->render('courses/builder/index', [
            'title' => 'Curriculum & Lesson Builder — ' . htmlspecialchars((string)$course->C_NAME),
            'course' => $course,
            'modules' => $modules,
            'totalLessons' => $totalLessons,
            'totalMinutes' => $totalMinutes,
        ]);
    }

    public function storeModule(Request $request, $id): Response {
        if ($auth = $this->checkAuthorization()) {
            return $auth;
        }

        $module = CurriculumBuilderService::createModule((int)$id, $request->all());

        if ($request->isAjax()) {
            return json([
                'success' => true,
                'message' => 'Module created successfully.',
                'module' => $module->toArray(),
            ]);
        }

        session()->flash('success', "Module '{$module->title}' added to curriculum.");
        return $this->redirect("/faculty/courses/{$id}/builder");
    }

    public function updateModule(Request $request, $courseId, $moduleId): Response {
        if ($auth = $this->checkAuthorization()) {
            return $auth;
        }

        $module = CurriculumBuilderService::updateModule((int)$moduleId, $request->all());

        if ($request->isAjax()) {
            return json([
                'success' => true,
                'message' => 'Module updated successfully.',
                'module' => $module->toArray(),
            ]);
        }

        session()->flash('success', "Module '{$module->title}' updated.");
        return $this->redirect("/faculty/courses/{$courseId}/builder");
    }

    public function deleteModule(Request $request, $courseId, $moduleId): Response {
        if ($auth = $this->checkAuthorization()) {
            return $auth;
        }

        $deleted = CurriculumBuilderService::deleteModule((int)$moduleId);

        if ($request->isAjax()) {
            return json([
                'success' => $deleted,
                'message' => $deleted ? 'Module removed.' : 'Failed to remove module.',
            ]);
        }

        session()->flash('success', 'Module and nested lessons removed successfully.');
        return $this->redirect("/faculty/courses/{$courseId}/builder");
    }

    public function reorderModules(Request $request, $id): Response {
        if ($auth = $this->checkAuthorization()) {
            return $auth;
        }

        $order = (array)$request->input('order', []);
        CurriculumBuilderService::reorderModules((int)$id, $order);

        return json(['success' => true, 'message' => 'Module order updated.']);
    }

    public function storeLesson(Request $request, $courseId, $moduleId): Response {
        if ($auth = $this->checkAuthorization()) {
            return $auth;
        }

        $lesson = CurriculumBuilderService::createLesson((int)$moduleId, $request->all());

        if ($request->isAjax()) {
            return json([
                'success' => true,
                'message' => 'Lesson created successfully.',
                'lesson' => $lesson->toArray(),
            ]);
        }

        session()->flash('success', "Lesson '{$lesson->title}' added.");
        return $this->redirect("/faculty/courses/{$courseId}/builder");
    }

    public function updateLesson(Request $request, $courseId, $lessonId): Response {
        if ($auth = $this->checkAuthorization()) {
            return $auth;
        }

        $lesson = CurriculumBuilderService::updateLesson((int)$lessonId, $request->all());

        if ($request->isAjax()) {
            return json([
                'success' => true,
                'message' => 'Lesson updated successfully.',
                'lesson' => $lesson->toArray(),
            ]);
        }

        session()->flash('success', "Lesson '{$lesson->title}' updated.");
        return $this->redirect("/faculty/courses/{$courseId}/builder");
    }

    public function deleteLesson(Request $request, $courseId, $lessonId): Response {
        if ($auth = $this->checkAuthorization()) {
            return $auth;
        }

        $deleted = CurriculumBuilderService::deleteLesson((int)$lessonId);

        if ($request->isAjax()) {
            return json([
                'success' => $deleted,
                'message' => $deleted ? 'Lesson deleted.' : 'Failed to delete lesson.',
            ]);
        }

        session()->flash('success', 'Lesson deleted successfully.');
        return $this->redirect("/faculty/courses/{$courseId}/builder");
    }

    public function reorderLessons(Request $request, $courseId, $moduleId): Response {
        if ($auth = $this->checkAuthorization()) {
            return $auth;
        }

        $order = (array)$request->input('order', []);
        CurriculumBuilderService::reorderLessons((int)$moduleId, $order);

        return json(['success' => true, 'message' => 'Lesson order updated.']);
    }

    public function storeBlock(Request $request, $courseId, $lessonId): Response {
        if ($auth = $this->checkAuthorization()) {
            return $auth;
        }

        $type = (string)$request->input('block_type', 'rich_text');
        $text = (string)$request->input('content', '');
        $code = (string)$request->input('code', '');
        $url = (string)$request->input('url', '');

        $payload = match ($type) {
            'code_snippet' => ['code' => $code, 'text' => $text],
            'video' => ['url' => $url, 'caption' => $text],
            default => ['text' => $text],
        };

        $block = CurriculumBuilderService::addLessonBlock((int)$lessonId, $type, $payload, (int)$request->input('order_index', 0));

        if ($request->isAjax()) {
            return json([
                'success' => true,
                'message' => 'Block added successfully.',
                'block' => $block->toArray(),
            ]);
        }

        session()->flash('success', 'Content block appended to lesson.');
        return $this->redirect("/faculty/courses/{$courseId}/builder");
    }

    private function checkAuthorization(): ?Response {
        if (!session()->has('user')) {
            session()->flash('error', 'Please sign in to access curriculum authoring.');
            return $this->redirect('/login');
        }

        $userRole = (string)session()->get('user_role', '');
        $isFacultyOrAdmin = in_array(strtolower($userRole), ['l', 'lecturer', 'instructor', 'teacher', 'a', 'admin', 'administrator', 'superadmin'], true);

        if (!$isFacultyOrAdmin) {
            session()->flash('error', 'Faculty or administrative privileges required.');
            return $this->redirect('/workspace');
        }

        return null;
    }
}
