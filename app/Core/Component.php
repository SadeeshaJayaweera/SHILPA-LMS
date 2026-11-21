<?php
declare(strict_types=1);

namespace App\Core;

use RuntimeException;

class Component {
    public static function render(string $name, array $props = []): string {
        $componentPath = dirname(__DIR__, 2) . '/resources/views/components/' . str_replace('.', '/', $name) . '.php';

        if (!file_exists($componentPath)) {
            throw new RuntimeException("Component [$name] not found at [$componentPath].");
        }

        extract($props, EXTR_SKIP);

        ob_start();
        try {
            include $componentPath;
            return ob_get_clean() ?: '';
        } catch (\Throwable $e) {
            ob_end_clean();
            throw $e;
        }
    }
}
