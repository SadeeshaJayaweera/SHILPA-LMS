<?php
declare(strict_types=1);

// Built-in PHP CLI development server router
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/');

if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    return false;
}

// Development preview session helper
if (isset($_GET['preview_as'])) {
    $sessPath = dirname(__DIR__) . '/storage/framework/sessions';
    if (!is_dir($sessPath)) {
        @mkdir($sessPath, 0775, true);
    }
    @ini_set('session.save_path', $sessPath);
    if (session_status() === PHP_SESSION_NONE) {
        @session_start();
    }
    if ($_GET['preview_as'] === 'admin') {
        $_SESSION['user'] = 'yes';
        $_SESSION['email'] = 'admin@shilpa.edu';
        $_SESSION['user_id'] = 1;
        $_SESSION['user_name'] = 'Dr. Samantha Silva';
        $_SESSION['user_role'] = 'admin';
        $_SESSION['_last_activity'] = time();
    } elseif ($_GET['preview_as'] === 'student') {
        $_SESSION['user'] = 'yes';
        $_SESSION['email'] = 'student@shilpa.edu';
        $_SESSION['user_id'] = 2;
        $_SESSION['user_name'] = 'Ashani Fernando';
        $_SESSION['user_role'] = 'student';
        $_SESSION['_last_activity'] = time();
    }
}

require_once __DIR__ . '/index.php';
