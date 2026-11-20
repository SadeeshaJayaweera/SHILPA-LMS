<?php
declare(strict_types=1);

namespace App\Core;

use RuntimeException;

class View {
    private static string $viewsPath;

    public static function init(string $path): void {
        self::$viewsPath = rtrim($path, '/');
    }

    public static function render(string $view, array $data = []): string {
        if (!isset(self::$viewsPath)) {
            self::$viewsPath = dirname(__DIR__, 2) . '/resources/views';
        }

        $viewFile = self::$viewsPath . '/' . str_replace('.', '/', $view) . '.php';

        if (!file_exists($viewFile)) {
            // Provide a graceful fallback view for 404 or missing templates
            if ($view === 'errors/404') {
                return self::default404();
            }
            throw new RuntimeException("View [$view] not found at [$viewFile].");
        }

        extract($data, EXTR_SKIP);

        ob_start();
        try {
            include $viewFile;
            return ob_get_clean() ?: '';
        } catch (\Throwable $e) {
            ob_end_clean();
            throw $e;
        }
    }

    private static function default404(): string {
        return '<!DOCTYPE html><html><head><meta charset="utf-8"><title>404 Not Found - SHILPA LMS</title>' .
            '<meta name="viewport" content="width=device-width, initial-scale=1">' .
            '<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">' .
            '<style>body{font-family:Inter,sans-serif;display:flex;align-items:center;justify-content:center;height:100vh;margin:0;background:#0f172a;color:#f8fafc;text-align:center;}' .
            'h1{font-size:4rem;margin:0;color:#38bdf8;}p{font-size:1.25rem;color:#94a3b8;margin:1rem 0 2rem;}' .
            'a{display:inline-block;padding:0.75rem 1.5rem;background:#0284c7;color:#fff;text-decoration:none;border-radius:0.5rem;font-weight:600;transition:background 0.2s;}' .
            'a:hover{background:#0369a1;}</style></head>' .
            '<body><div><h1>404</h1><p>The learning resource or page you requested could not be found.</p>' .
            '<a href="/">Return to Shilpa LMS</a></div></body></html>';
    }
}
