<?php
declare(strict_types=1);

use App\Controllers\AdminDashboardController;
use App\Controllers\AuthController;
use App\Controllers\CourseDiscoveryController;
use App\Controllers\HomeController;
use App\Controllers\LecturerDashboardController;
use App\Controllers\ProfileController;
use App\Controllers\StudentDashboardController;
use App\Controllers\WorkspaceController;
use App\Core\Router;

return function (Router $router): void {
    $router->get('/', [HomeController::class, 'index']);
    $router->get('/courses', [CourseDiscoveryController::class, 'index']);
    $router->get('/courses/{id}', [CourseDiscoveryController::class, 'show']);
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
};
