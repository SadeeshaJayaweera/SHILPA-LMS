<?php
declare(strict_types=1);

namespace App\Services;

class PerformanceProfiler {
    protected static float $startTime = 0.0;
    protected static int $startMemory = 0;
    protected static int $queryCount = 0;
    protected static array $events = [];

    public static function start(): void {
        self::$startTime = microtime(true);
        self::$startMemory = memory_get_usage(true);
        self::$queryCount = 0;
        self::$events = [];
    }

    public static function incrementQueryCount(): void {
        self::$queryCount++;
    }

    public static function mark(string $name): void {
        self::$events[$name] = microtime(true) - self::getStartTime();
    }

    public static function getStartTime(): float {
        if (self::$startTime <= 0.0) {
            self::$startTime = defined('SHILPA_START') ? (float)SHILPA_START : microtime(true);
        }
        return self::$startTime;
    }

    public static function getExecutionTimeMs(): float {
        return round((microtime(true) - self::getStartTime()) * 1000, 2);
    }

    public static function getPeakMemoryMb(): float {
        return round(memory_get_peak_usage(true) / 1024 / 1024, 2);
    }

    public static function getQueryCount(): int {
        return self::$queryCount;
    }

    public static function getMetrics(): array {
        return [
            'execution_time_ms' => self::getExecutionTimeMs(),
            'peak_memory_mb' => self::getPeakMemoryMb(),
            'current_memory_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
            'query_count' => self::getQueryCount(),
            'php_version' => PHP_VERSION,
            'opcache_enabled' => function_exists('opcache_get_status') && !empty(opcache_get_status()['opcache_enabled']),
            'timestamp' => date('Y-m-d H:i:s'),
        ];
    }
}
