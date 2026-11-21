<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Models\Course;
use App\Models\User;
use App\Services\AnalyticsService;

class AnalyticsController extends Controller {
    public function studentAnalytics(Request $request): Response {
        if (!session()->has('user')) {
            session()->flash('error', 'Please sign in to view your learning analytics.');
            return $this->redirect('/login');
        }

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

        $data = AnalyticsService::getStudentLearningVelocity($userId);

        return $this->render('analytics/student', array_merge($data, [
            'title' => 'Personal Learning Velocity & Study Metrics — SHILPA LMS',
            'data' => $data,
        ]));
    }

    public function courseAnalytics(Request $request, $id): Response {
        if ($auth = $this->checkFacultyAuthorization()) {
            return $auth;
        }

        $courseId = (int)$id;
        $course = Course::find($courseId);
        if (!$course) {
            session()->flash('error', 'Course not found.');
            return $this->redirect('/faculty/courses');
        }

        $insights = AnalyticsService::getCourseInsights($courseId);

        return $this->render('analytics/course', [
            'title' => 'Learning Analytics & Retention Funnel — ' . htmlspecialchars((string)$course->C_NAME),
            'course' => $course,
            'insights' => $insights,
        ]);
    }

    public function institutionalAnalytics(Request $request): Response {
        if ($auth = $this->checkAdminAuthorization()) {
            return $auth;
        }

        $data = AnalyticsService::getInstitutionalInsights();

        return $this->render('analytics/institutional', array_merge($data, [
            'title' => 'Institutional Learning Analytics & Platform Metrics — SHILPA LMS',
            'data' => $data,
        ]));
    }

    public function apiStudent(Request $request, $id): Response {
        if (!session()->has('user')) {
            return json(['error' => 'Unauthenticated'], 401);
        }

        $targetUserId = (int)$id;
        $currentUserId = (int)session()->get('user_id', 0);
        $userRole = (string)session()->get('user_role', 'student');

        // Only allow student to view own metrics, unless faculty/admin
        $isStaff = in_array(strtolower($userRole), ['l', 'lecturer', 'instructor', 'a', 'admin', 'superadmin'], true);
        if ($targetUserId !== $currentUserId && !$isStaff) {
            return json(['error' => 'Unauthorized'], 403);
        }

        try {
            $data = AnalyticsService::getStudentLearningVelocity($targetUserId);
            return json(['success' => true, 'data' => $data]);
        } catch (\Throwable $e) {
            return json(['error' => $e->getMessage()], 404);
        }
    }

    public function apiCourse(Request $request, $id): Response {
        if ($auth = $this->checkFacultyAuthorization()) {
            return json(['error' => 'Unauthorized'], 403);
        }

        try {
            $insights = AnalyticsService::getCourseInsights((int)$id);
            return json(['success' => true, 'data' => $insights]);
        } catch (\Throwable $e) {
            return json(['error' => $e->getMessage()], 404);
        }
    }

    private function checkFacultyAuthorization(): ?Response {
        if (!session()->has('user')) {
            session()->flash('error', 'Please sign in to access analytics.');
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

    private function checkAdminAuthorization(): ?Response {
        if (!session()->has('user')) {
            session()->flash('error', 'Please sign in to access platform analytics.');
            return $this->redirect('/login');
        }

        $userRole = (string)session()->get('user_role', '');
        $isAdmin = in_array(strtolower($userRole), ['a', 'admin', 'administrator', 'superadmin'], true);

        if (!$isAdmin) {
            session()->flash('error', 'Administrative privileges required.');
            return $this->redirect('/workspace');
        }

        return null;
    }
}
