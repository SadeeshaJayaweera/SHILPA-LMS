<?php
declare(strict_types=1);

use App\Controllers\AdminDashboardController;
use App\Controllers\AuthController;
use App\Controllers\CourseDiscoveryController;
use App\Controllers\CourseManagementController;
use App\Controllers\CourseWorkspaceController;
use App\Controllers\CurriculumBuilderController;
use App\Controllers\EnrollmentController;
use App\Controllers\HomeController;
use App\Controllers\LecturerDashboardController;
use App\Controllers\ProfileController;
use App\Controllers\ProgressTrackingController;
use App\Controllers\ResourceManagementController;
use App\Controllers\StudentDashboardController;
use App\Controllers\WorkspaceController;
use App\Core\Router;

return function (Router $router): void {
    $router->get('/', [HomeController::class, 'index']);
    $router->get('/courses', [CourseDiscoveryController::class, 'index']);
    $router->get('/courses/{id}', [CourseDiscoveryController::class, 'show']);
    $router->get('/courses/{id}/workspace', [CourseWorkspaceController::class, 'show']);
    $router->get('/courses/{courseId}/learn/{lessonId}', [CourseWorkspaceController::class, 'learn']);
    $router->get('/workspace', [WorkspaceController::class, 'index']);
    $router->get('/student/dashboard', [StudentDashboardController::class, 'index']);
    $router->get('/dashboard', [StudentDashboardController::class, 'index']);
    $router->get('/lecturer/dashboard', [LecturerDashboardController::class, 'index']);
    $router->get('/admin/dashboard', [AdminDashboardController::class, 'index']);
    $router->get('/admin/users', [AdminDashboardController::class, 'users']);
    $router->get('/api/student/analytics', [StudentDashboardController::class, 'analytics']);
    $router->get('/api/admin/metrics', [AdminDashboardController::class, 'systemHealth']);
    $router->get('/admin/security-audit', [AdminDashboardController::class, 'securityAudit']);
    $router->post('/admin/security-audit/verify', [AdminDashboardController::class, 'verifyAuditLogIntegrity']);
    $router->get('/design-system', function () {
        return view('design-system/index', [
            'title' => 'Design System & Component Library — SHILPA LMS',
        ]);
    });
    $router->get('/health', function () {
        return json([
            'status' => 'healthy',
            'timestamp' => date('c'),
            'app' => config('app.name'),
            'env' => config('app.env'),
            'php' => PHP_VERSION,
        ]);
    });

    // Authentication & Onboarding Routes
    $router->get('/login', [AuthController::class, 'showLogin']);
    $router->post('/login', [AuthController::class, 'login']);
    $router->get('/register', [AuthController::class, 'showRegister']);
    $router->post('/register', [AuthController::class, 'register']);
    $router->get('/forgot-password', [AuthController::class, 'showForgotPassword']);
    $router->post('/forgot-password', [AuthController::class, 'sendResetLink']);
    $router->get('/reset-password', [AuthController::class, 'showResetPassword']);
    $router->post('/reset-password', [AuthController::class, 'updatePassword']);
    $router->get('/two-factor', [AuthController::class, 'showTwoFactor']);
    $router->post('/two-factor/verify', [AuthController::class, 'verifyTwoFactor']);
    $router->get('/onboarding', [AuthController::class, 'showOnboarding']);
    $router->post('/onboarding', [AuthController::class, 'completeOnboarding']);
    $router->get('/logout', [AuthController::class, 'logout']);
    $router->post('/logout', [AuthController::class, 'logout']);

    // Profile & Account Management Routes
    $router->get('/profile', [ProfileController::class, 'index']);
    $router->post('/profile', [ProfileController::class, 'update']);
    $router->post('/profile/preferences', [ProfileController::class, 'updatePreferences']);
    $router->post('/profile/password', [ProfileController::class, 'updatePassword']);
    $router->post('/profile/sessions/terminate', [ProfileController::class, 'terminateSession']);
    $router->post('/profile/sessions/terminate-others', [ProfileController::class, 'terminateOtherSessions']);

    // Advanced Course Management Routes (Faculty & Admin)
    $router->get('/faculty/courses', [CourseManagementController::class, 'index']);
    $router->get('/faculty/courses/create', [CourseManagementController::class, 'create']);
    $router->post('/faculty/courses', [CourseManagementController::class, 'store']);
    $router->get('/faculty/courses/{id}/edit', [CourseManagementController::class, 'edit']);
    $router->post('/faculty/courses/{id}/edit', [CourseManagementController::class, 'update']);
    $router->post('/faculty/courses/{id}/delete', [CourseManagementController::class, 'destroy']);
    $router->post('/faculty/courses/{id}/publish', [CourseManagementController::class, 'publish']);

    // Curriculum & Lesson Builder Routes (Faculty & Admin)
    $router->get('/faculty/courses/{id}/builder', [CurriculumBuilderController::class, 'index']);
    $router->post('/faculty/courses/{id}/modules', [CurriculumBuilderController::class, 'storeModule']);
    $router->post('/faculty/courses/{courseId}/modules/{moduleId}', [CurriculumBuilderController::class, 'updateModule']);
    $router->post('/faculty/courses/{courseId}/modules/{moduleId}/delete', [CurriculumBuilderController::class, 'deleteModule']);
    $router->post('/faculty/courses/{id}/modules/reorder', [CurriculumBuilderController::class, 'reorderModules']);
    $router->post('/faculty/courses/{courseId}/modules/{moduleId}/lessons', [CurriculumBuilderController::class, 'storeLesson']);
    $router->post('/faculty/courses/{courseId}/lessons/{lessonId}', [CurriculumBuilderController::class, 'updateLesson']);
    $router->post('/faculty/courses/{courseId}/lessons/{lessonId}/delete', [CurriculumBuilderController::class, 'deleteLesson']);
    $router->post('/faculty/courses/{courseId}/modules/{moduleId}/lessons/reorder', [CurriculumBuilderController::class, 'reorderLessons']);
    $router->post('/faculty/courses/{courseId}/lessons/{lessonId}/blocks', [CurriculumBuilderController::class, 'storeBlock']);

    // Secure Learning Resources and File Management Routes
    $router->get('/faculty/courses/{id}/resources', [ResourceManagementController::class, 'index']);
    $router->post('/faculty/courses/{id}/resources', [ResourceManagementController::class, 'store']);
    $router->post('/faculty/courses/{courseId}/resources/{id}/delete', [ResourceManagementController::class, 'destroy']);
    $router->get('/courses/{courseId}/resources/{id}/download', [ResourceManagementController::class, 'download']);

    // Learning Progress & Completion Tracking Routes
    $router->post('/courses/{courseId}/lessons/{lessonId}/complete', [ProgressTrackingController::class, 'toggle']);
    $router->get('/api/courses/{courseId}/progress', [ProgressTrackingController::class, 'progress']);

    // Course Enrollment and Access Control Routes
    $router->post('/courses/{id}/enroll', [EnrollmentController::class, 'enroll']);
    $router->post('/courses/{id}/drop', [EnrollmentController::class, 'drop']);
    $router->get('/faculty/courses/{id}/roster', [EnrollmentController::class, 'roster']);
    $router->post('/faculty/enrollments/{id}/status', [EnrollmentController::class, 'updateStatus']);
};
