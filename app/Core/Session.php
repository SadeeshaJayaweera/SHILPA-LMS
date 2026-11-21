<?php
declare(strict_types=1);

namespace App\Core;

class Session {
    private bool $started = false;

    public function __construct() {
        $this->start();
    }

    public function start(): void {
        if ($this->started || session_status() === PHP_SESSION_ACTIVE) {
            $this->started = true;
            return;
        }

        if (headers_sent()) {
            $this->started = true;
            return;
        }

        $lifetime = (int)config('session.lifetime', 120) * 60;
        $secure = (bool)config('session.secure', false);
        $httpOnly = true;
        $sameSite = (string)config('session.same_site', 'Lax');

        session_set_cookie_params([
            'lifetime' => $lifetime,
            'path' => '/',
            'domain' => '',
            'secure' => $secure,
            'httponly' => $httpOnly,
            'samesite' => $sameSite,
        ]);

        session_start();
        $this->started = true;

        if (!isset($_SESSION['_flash_next'])) {
            $_SESSION['_flash_next'] = [];
        }
        if (!isset($_SESSION['_flash_current'])) {
            $_SESSION['_flash_current'] = [];
        }

        // Age flash data
        $_SESSION['_flash_current'] = $_SESSION['_flash_next'] ?? [];
        $_SESSION['_flash_next'] = [];
    }

    public function get(string $key, mixed $default = null): mixed {
        return $_SESSION[$key] ?? $_SESSION['_flash_current'][$key] ?? $default;
    }

    public function set(string $key, mixed $value): void {
        $_SESSION[$key] = $value;
    }

    public function has(string $key): bool {
        return isset($_SESSION[$key]) || isset($_SESSION['_flash_current'][$key]);
    }

    public function remove(string $key): void {
        unset($_SESSION[$key], $_SESSION['_flash_current'][$key], $_SESSION['_flash_next'][$key]);
    }

    public function flash(string $key, mixed $value): void {
        $_SESSION['_flash_next'][$key] = $value;
    }

    public function regenerate(bool $deleteOldSession = true): void {
        if ($this->started && session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id($deleteOldSession);
        }
    }

    public function destroy(): void {
        $_SESSION = [];
        if ($this->started && session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
            $this->started = false;
        }
    }

    public function csrfToken(): string {
        if (empty($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf_token'];
    }

    public function validateCsrfToken(?string $token): bool {
        if ($token === null || empty($_SESSION['_csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['_csrf_token'], $token);
    }
}
