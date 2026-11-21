<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Security\Gate;
use App\Services\CourseManagementService;

class CourseManagementController extends Controller {
    public function index(Request $request): Response {
        if ($auth = $this->checkAuthorization()) {
            return $auth;
        }

        $status = (string)$request->query('status', 'all');
        $search = (string)$request->query('q', '');
        $categoryId = (int)$request->query('category', 0);

        $courses = CourseManagementService::getCourses(null, [
            'status' => $status,
            'search' => $search,
            'category_id' => $categoryId,
        ]);
        $categories = CourseCategory::allOrdered();

        return $this->render('courses/manage/index', [
            'title' => 'Course Curriculum & Management — ' . config('app.name', 'SHILPA LMS'),
            'courses' => $courses,
            'categories' => $categories,
            'currentStatus' => $status,
            'currentSearch' => $search,
            'currentCategory' => $categoryId,
        ]);
    }

    public function create(Request $request): Response {
        if ($auth = $this->checkAuthorization()) {
            return $auth;
        }

        $categories = CourseCategory::allOrdered();
        return $this->render('courses/manage/form', [
            'title' => 'Author New Academic Course — ' . config('app.name', 'SHILPA LMS'),
            'course' => null,
            'categories' => $categories,
            'action' => '/faculty/courses',
        ]);
    }

    public function store(Request $request): Response {
        if ($auth = $this->checkAuthorization()) {
            return $auth;
        }

        $userId = (int)session()->get('user_id', 0);
        $course = CourseManagementService::createCourse($request->all(), $userId);

        session()->flash('success', "Course '{$course->C_NAME}' created successfully.");
        return $this->redirect('/faculty/courses');
    }

    public function edit(Request $request, $id): Response {
        if ($auth = $this->checkAuthorization()) {
            return $auth;
        }

        $course = Course::find((int)$id);
        if (!$course) {
            session()->flash('error', 'Course not found.');
            return $this->redirect('/faculty/courses');
        }

        $categories = CourseCategory::allOrdered();
        return $this->render('courses/manage/form', [
            'title' => "Edit Course: {$course->C_NAME} — " . config('app.name', 'SHILPA LMS'),
            'course' => $course,
            'metadata' => $course->metadata(),
            'categories' => $categories,
            'action' => "/faculty/courses/{$course->C_ID}/edit",
        ]);
    }

    public function update(Request $request, $id): Response {
        if ($auth = $this->checkAuthorization()) {
            return $auth;
        }

        $course = CourseManagementService::updateCourse((int)$id, $request->all());

        session()->flash('success', "Course '{$course->C_NAME}' updated successfully.");
        return $this->redirect('/faculty/courses');
    }

    public function destroy(Request $request, $id): Response {
        if ($auth = $this->checkAuthorization()) {
            return $auth;
        }

        $success = CourseManagementService::deleteCourse((int)$id);
        if ($success) {
            session()->flash('success', 'Course deleted successfully.');
        } else {
            session()->flash('error', 'Could not delete course.');
        }

        return $this->redirect('/faculty/courses');
    }

    public function publish(Request $request, $id): Response {
        if ($auth = $this->checkAuthorization()) {
            return $auth;
        }

        $isPublished = CourseManagementService::togglePublish((int)$id);
        $statusMsg = $isPublished ? 'Course published successfully.' : 'Course set to draft mode.';

        if ($request->isAjax()) {
            return json(['success' => true, 'is_published' => $isPublished, 'message' => $statusMsg]);
        }

        session()->flash('success', $statusMsg);
        return $this->redirect('/faculty/courses');
    }

    private function checkAuthorization(): ?Response {
        if (!session()->has('user')) {
            session()->flash('error', 'Please sign in to access course management.');
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
