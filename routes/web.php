<?php
declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\CourseDiscoveryController;
use App\Controllers\HomeController;
use App\Controllers\WorkspaceController;
use App\Core\Router;

return function (Router $router): void {
    $router->get('/', [HomeController::class, 'index']);
    $router->get('/courses', [CourseDiscoveryController::class, 'index']);
    $router->get('/courses/{id}', [CourseDiscoveryController::class, 'show']);
    $router->get('/workspace', [WorkspaceController::class, 'index']);
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
    $router->get('/onboarding', [AuthController::class, 'showOnboarding']);
    $router->post('/onboarding', [AuthController::class, 'completeOnboarding']);
    $router->get('/logout', [AuthController::class, 'logout']);
    $router->post('/logout', [AuthController::class, 'logout']);
};
