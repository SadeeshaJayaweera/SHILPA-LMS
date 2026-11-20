<?php
declare(strict_types=1);

namespace App\Core;

class Config {
    private static array $items = [];

    public static function load(string $configDir): void {
        if (!is_dir($configDir)) {
            return;
        }

        $files = scandir($configDir);
        if ($files === false) {
            return;
        }

        foreach ($files as $file) {
            if ($file === '.' || $file === '..' || !str_ends_with($file, '.php')) {
                continue;
            }

            $key = basename($file, '.php');
            $data = require "$configDir/$file";
            if (is_array($data)) {
                self::$items[$key] = $data;
            }
        }
    }

    public static function get(string $key, mixed $default = null): mixed {
        $segments = explode('.', $key);
        $current = self::$items;

        foreach ($segments as $segment) {
            if (!is_array($current) || !array_key_exists($segment, $current)) {
                return $default;
            }
            $current = $current[$segment];
        }

        return $current;
    }

    public static function set(string $key, mixed $value): void {
        $segments = explode('.', $key);
        $current = &self::$items;

        foreach ($segments as $segment) {
            if (!isset($current[$segment]) || !is_array($current[$segment])) {
                $current[$segment] = [];
            }
            $current = &$current[$segment];
        }

        $current = $value;
    }

    public static function all(): array {
        return self::$items;
    }
}
