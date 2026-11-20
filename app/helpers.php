<?php
declare(strict_types=1);

use App\Core\Application;
use App\Core\Config;
use App\Core\Env;
use App\Core\Response;
use App\Core\Session;
use App\Core\View;

if (!function_exists('app')) {
    function app(?string $make = null): mixed {
        $instance = Application::getInstance();
        if ($make === null) {
            return $instance;
        }
        return $instance->make($make);
    }
}

if (!function_exists('env')) {
    function env(string $key, mixed $default = null): mixed {
        return Env::get($key, $default);
    }
}

if (!function_exists('config')) {
    function config(string $key, mixed $default = null): mixed {
        return Config::get($key, $default);
    }
}

if (!function_exists('e')) {
    function e(?string $value): string {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('view')) {
    function view(string $template, array $data = []): Response {
        $content = View::render($template, $data);
        return new Response($content, 200, ['Content-Type' => 'text/html; charset=UTF-8']);
    }
}

if (!function_exists('json')) {
    function json(mixed $data, int $status = 200, array $headers = []): Response {
        return Response::json($data, $status, $headers);
    }
}

if (!function_exists('redirect')) {
    function redirect(string $url, int $status = 302): Response {
        return Response::redirect($url, $status);
    }
}

if (!function_exists('session')) {
    function session(): Session {
        return app(Session::class);
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string {
        return session()->csrfToken();
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string {
        return '<input type="hidden" name="_token" value="' . e(csrf_token()) . '">';
    }
}

if (!function_exists('url')) {
    function url(string $path = ''): string {
        $baseUrl = rtrim((string)config('app.url', 'http://localhost:8000'), '/');
        $path = ltrim($path, '/');
        return $path === '' ? $baseUrl : "$baseUrl/$path";
    }
}
