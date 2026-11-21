<?php
declare(strict_types=1);

namespace App\Storage;

class StorageManager {
    protected static array $disks = [];

    public static function disk(string $name = 'local'): DiskInterface {
        if (isset(self::$disks[$name])) {
            return self::$disks[$name];
        }

        $baseDir = dirname(__DIR__, 2) . '/storage/app';

        $disk = match ($name) {
            'public' => new LocalDisk($baseDir . '/public', '/storage'),
            'secure' => new LocalDisk($baseDir . '/secure', '/secure-files'),
            default => new LocalDisk($baseDir . '/local', '/storage/local'),
        };

        self::$disks[$name] = $disk;
        return $disk;
    }

    public static function setDisk(string $name, DiskInterface $disk): void {
        self::$disks[$name] = $disk;
    }

    public static function reset(): void {
        self::$disks = [];
    }
}
