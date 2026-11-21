<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Models\Course;
use App\Models\CourseResource;
use App\Services\ResourceManagementService;

class ResourceManagementController extends Controller {
    public function index(Request $request, $id): Response {
        if ($auth = $this->checkFacultyAuthorization()) {
            return $auth;
        }

        $course = Course::find((int)$id);
        if (!$course) {
            session()->flash('error', 'Course not found.');
            return $this->redirect('/faculty/courses');
        }

        $resources = CourseResource::forCourse((int)$course->C_ID);
        $totalBytes = array_reduce($resources, fn($carry, $r) => $carry + (int)$r->file_size_bytes, 0);

        return $this->render('courses/resources/index', [
            'title' => 'Learning Resources & Asset Library — ' . htmlspecialchars((string)$course->C_NAME),
            'course' => $course,
            'resources' => $resources,
            'totalBytes' => $totalBytes,
        ]);
    }

    public function store(Request $request, $id): Response {
        if ($auth = $this->checkFacultyAuthorization()) {
            return $auth;
        }

        $courseId = (int)$id;
        $course = Course::find($courseId);
        if (!$course) {
            session()->flash('error', 'Course not found.');
            return $this->redirect('/faculty/courses');
        }

        $fileData = $request->file('file') ?? ($_FILES['file'] ?? null);
        if (!$fileData || empty($fileData['name'])) {
            session()->flash('error', 'Please select a valid resource file to upload.');
            return $this->redirect("/faculty/courses/{$courseId}/resources");
        }

        $title = trim((string)$request->input('title', ''));
        $lessonId = (int)$request->input('lesson_id', 0) ?: null;
        $isPublic = !empty($request->input('is_public_preview'));
        $userId = (int)session()->get('user_id', 0);

        try {
            $resource = ResourceManagementService::storeUploadedFile(
                $courseId,
                $lessonId,
                $userId,
                $fileData,
                $title,
                $isPublic
            );

            if ($request->isAjax()) {
                return json([
                    'success' => true,
                    'message' => 'Resource uploaded securely.',
                    'resource' => $resource->toArray(),
                ]);
            }

            session()->flash('success', "Resource '{$resource->title}' uploaded successfully.");
        } catch (\Throwable $e) {
            if ($request->isAjax()) {
                return json(['success' => false, 'error' => $e->getMessage()], 400);
            }
            session()->flash('error', $e->getMessage());
        }

        return $this->redirect("/faculty/courses/{$courseId}/resources");
    }

    public function download(Request $request, $courseId, $id): Response {
        $resource = CourseResource::find((int)$id);
        if (!$resource || (int)$resource->course_id !== (int)$courseId) {
            session()->flash('error', 'Requested resource was not found.');
            return $this->redirect("/courses/{$courseId}/workspace");
        }

        $userId = (int)session()->get('user_id', 0);
        $userRole = (string)session()->get('user_role', 'guest');

        if (!ResourceManagementService::canUserAccess($resource, $userId, $userRole)) {
            session()->flash('error', 'Access restricted. You must be enrolled in this course to download materials.');
            return $this->redirect("/courses/{$courseId}/workspace");
        }

        try {
            return ResourceManagementService::streamDownload($resource);
        } catch (\Throwable $e) {
            session()->flash('error', $e->getMessage());
            return $this->redirect("/courses/{$courseId}/workspace");
        }
    }

    public function destroy(Request $request, $courseId, $id): Response {
        if ($auth = $this->checkFacultyAuthorization()) {
            return $auth;
        }

        $deleted = ResourceManagementService::deleteResource((int)$id);

        if ($request->isAjax()) {
            return json([
                'success' => $deleted,
                'message' => $deleted ? 'Resource removed.' : 'Failed to delete resource.',
            ]);
        }

        session()->flash('success', 'Resource file and database records removed.');
        return $this->redirect("/faculty/courses/{$courseId}/resources");
    }

    private function checkFacultyAuthorization(): ?Response {
        if (!session()->has('user')) {
            session()->flash('error', 'Please sign in to access resource management.');
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
