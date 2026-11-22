<?php
declare(strict_types=1);

namespace App\Core;

use RuntimeException;

class Component {
    public static function render(string $componentName, array $props = []): string {
        $componentPath = dirname(__DIR__, 2) . '/resources/views/components/' . str_replace('.', '/', $componentName) . '.php';

        if (!file_exists($componentPath)) {
            throw new RuntimeException("Component [$componentName] not found at [$componentPath].");
        }

        extract($props, EXTR_OVERWRITE);

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
