<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use App\Services\EnrollmentService;

class EnrollmentController extends Controller {
    public function enroll(Request $request, $id): Response {
        if (!session()->has('user')) {
            if ($request->isAjax()) {
                return json(['success' => false, 'error' => 'Please sign in to enroll.'], 401);
            }
            session()->flash('error', 'Please sign in to enroll in courses.');
            return $this->redirect('/login');
        }

        $userId = $this->resolveUserId();
        $result = EnrollmentService::enroll($userId, (int)$id);

        if (!$result['success']) {
            if ($request->isAjax()) {
                return json(['success' => false, 'error' => $result['error']], 422);
            }
            session()->flash('error', $result['error']);
            return $this->redirect("/courses/{$id}");
        }

        if ($request->isAjax()) {
            return json(['success' => true, 'message' => $result['message']]);
        }

        session()->flash('success', $result['message']);
        return $this->redirect("/courses/{$id}");
    }

    public function drop(Request $request, $id): Response {
        if (!session()->has('user')) {
            return $this->redirect('/login');
        }

        $userId = $this->resolveUserId();
        $dropped = EnrollmentService::drop($userId, (int)$id);

        if ($dropped) {
            session()->flash('success', 'You have dropped this course enrollment.');
        } else {
            session()->flash('error', 'Could not drop enrollment.');
        }

        return $this->redirect('/student/dashboard');
    }

    public function roster(Request $request, $id): Response {
        if ($auth = $this->checkFacultyOrAdmin()) {
            return $auth;
        }

        $course = Course::find((int)$id);
        if (!$course) {
            session()->flash('error', 'Course not found.');
            return $this->redirect('/faculty/courses');
        }

        $roster = EnrollmentService::getRoster((int)$id);

        return $this->render('courses/manage/roster', [
            'title' => "Student Roster: {$course->C_NAME} — " . config('app.name', 'SHILPA LMS'),
            'course' => $course,
            'roster' => $roster,
        ]);
    }

    public function updateStatus(Request $request, $id): Response {
        if ($auth = $this->checkFacultyOrAdmin()) {
            return $auth;
        }

        $status = (string)$request->input('status', 'active');
        $updated = EnrollmentService::updateStatus((int)$id, $status);

        if ($request->isAjax()) {
            return json(['success' => $updated]);
        }

        if ($updated) {
            session()->flash('success', 'Enrollment status updated.');
        } else {
            session()->flash('error', 'Invalid status update.');
        }

        return $this->redirect($_SERVER['HTTP_REFERER'] ?? '/faculty/courses');
    }

    private function resolveUserId(): int {
        $userId = (int)session()->get('user_id', 0);
        if ($userId <= 0) {
            $email = (string)session()->get('email', '');
            if ($email !== '') {
                $user = User::findByEmail($email);
                if ($user) {
                    $userId = (int)$user->UserId;
                    session()->set('user_id', $userId);
                }
            }
        }
        return $userId;
    }

    private function checkFacultyOrAdmin(): ?Response {
        if (!session()->has('user')) {
            session()->flash('error', 'Please sign in to proceed.');
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
