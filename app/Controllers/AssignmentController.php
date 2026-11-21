<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use App\Models\Enrollment;
use App\Services\AssignmentManagementService;

class AssignmentController extends Controller {
    public function facultyIndex(Request $request, $id): Response {
        if ($auth = $this->checkFacultyAuthorization()) {
            return $auth;
        }

        $course = Course::find((int)$id);
        if (!$course) {
            session()->flash('error', 'Course not found.');
            return $this->redirect('/faculty/courses');
        }

        $assignments = Assignment::forCourse((int)$course->C_ID, false);

        return $this->render('courses/assignments/index', [
            'title' => 'Assignments & Projects — ' . htmlspecialchars((string)$course->C_NAME),
            'course' => $course,
            'assignments' => $assignments,
        ]);
    }

    public function create(Request $request, $id): Response {
        if ($auth = $this->checkFacultyAuthorization()) {
            return $auth;
        }

        $course = Course::find((int)$id);
        if (!$course) {
            session()->flash('error', 'Course not found.');
            return $this->redirect('/faculty/courses');
        }

        return $this->render('courses/assignments/create', [
            'title' => 'Create Course Assignment — ' . htmlspecialchars((string)$course->C_NAME),
            'course' => $course,
        ]);
    }

    public function store(Request $request, $id): Response {
        if ($auth = $this->checkFacultyAuthorization()) {
            return $auth;
        }

        $userId = (int)session()->get('user_id', 0);
        $courseId = (int)$id;

        try {
            $assignment = AssignmentManagementService::createAssignment($courseId, $userId, $request->all());

            if ($request->isAjax()) {
                return json([
                    'success' => true,
                    'message' => 'Assignment created successfully.',
                    'assignment' => $assignment->toArray(),
                ]);
            }

            session()->flash('success', "Assignment '{$assignment->title}' published.");
            return $this->redirect("/faculty/courses/{$courseId}/assignments");
        } catch (\Throwable $e) {
            session()->flash('error', $e->getMessage());
            return $this->redirect("/faculty/courses/{$courseId}/assignments/create");
        }
    }

    public function show(Request $request, $courseId, $id): Response {
        $course = Course::find((int)$courseId);
        $assignment = Assignment::find((int)$id);

        if (!$course || !$assignment || (int)$assignment->course_id !== (int)$courseId) {
            session()->flash('error', 'Assignment not found.');
            return $this->redirect("/courses/{$courseId}/workspace");
        }

        $userId = (int)session()->get('user_id', 0);
        $submission = $userId > 0 ? $assignment->submissionForStudent($userId) : null;
        $isEnrolled = $userId > 0 && Enrollment::isEnrolled($userId, (int)$courseId);

        return $this->render('courses/assignments/show', [
            'title' => htmlspecialchars((string)$assignment->title) . ' — ' . htmlspecialchars((string)$course->C_NAME),
            'course' => $course,
            'assignment' => $assignment,
            'submission' => $submission,
            'isEnrolled' => $isEnrolled,
        ]);
    }

    public function submit(Request $request, $courseId, $id): Response {
        if (!session()->has('user')) {
            session()->flash('error', 'Please sign in to submit assignments.');
            return $this->redirect('/login');
        }

        $userId = (int)session()->get('user_id', 0);
        $file = $request->file('attachment') ?? ($_FILES['attachment'] ?? null);

        try {
            $sub = AssignmentManagementService::submitAssignment((int)$id, $userId, $request->all(), $file);

            if ($request->isAjax()) {
                return json(['success' => true, 'message' => 'Assignment submitted successfully.']);
            }

            $msg = $sub->isLate() ? 'Assignment submitted (marked as late).' : 'Assignment submitted successfully.';
            session()->flash('success', $msg);
        } catch (\Throwable $e) {
            session()->flash('error', $e->getMessage());
        }

        return $this->redirect("/courses/{$courseId}/assignments/{$id}");
    }

    private function checkFacultyAuthorization(): ?Response {
        if (!session()->has('user')) {
            session()->flash('error', 'Please sign in to access assignment management.');
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
