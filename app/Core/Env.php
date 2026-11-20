<?php
declare(strict_types=1);

namespace App\Core;

class Env {
    private static bool $loaded = false;
    private static array $variables = [];

    public static function load(string $path): void {
        if (!file_exists($path)) {
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            return;
        }

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            if (!str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            // Strip surrounding quotes
            if (
                (str_starts_with($value, '"') && str_ends_with($value, '"')) ||
                (str_starts_with($value, "'") && str_ends_with($value, "'"))
            ) {
                $value = substr($value, 1, -1);
            }

            self::$variables[$key] = $value;
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }

        self::$loaded = true;
    }

    public static function get(string $key, mixed $default = null): mixed {
        if (!self::$loaded && file_exists(dirname(__DIR__, 2) . '/.env')) {
            self::load(dirname(__DIR__, 2) . '/.env');
        }

        if (array_key_exists($key, self::$variables)) {
            return self::cast(self::$variables[$key]);
        }

        $env = getenv($key);
        if ($env !== false) {
            return self::cast($env);
        }

        if (isset($_ENV[$key])) {
            return self::cast($_ENV[$key]);
        }

        return $default;
    }

    private static function cast(mixed $value): mixed {
        if (!is_string($value)) {
            return $value;
        }

        return match (strtolower($value)) {
            'true', '(true)' => true,
            'false', '(false)' => false,
            'null', '(null)' => null,
            'empty', '(empty)' => '',
            default => $value,
        };
    }
}
