<?php
declare(strict_types=1);

namespace App\Storage;

class LocalDisk implements DiskInterface {
    protected string $root;
    protected string $baseUrl;

    public function __construct(string $root, string $baseUrl = '/storage') {
        $this->root = rtrim($root, '/');
        $this->baseUrl = rtrim($baseUrl, '/');
        if (!is_dir($this->root)) {
            @mkdir($this->root, 0777, true);
        }
    }

    protected function fullPath(string $path): string {
        return $this->root . '/' . ltrim($path, '/');
    }

    public function put(string $path, string $contents): bool {
        $full = $this->fullPath($path);
        $dir = dirname($full);
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }
        return @file_put_contents($full, $contents, LOCK_EX) !== false;
    }

    public function get(string $path): ?string {
        $full = $this->fullPath($path);
        if (!file_exists($full)) {
            return null;
        }
        $contents = @file_get_contents($full);
        return $contents !== false ? $contents : null;
    }

    public function exists(string $path): bool {
        return file_exists($this->fullPath($path));
    }

    public function delete(string $path): bool {
        $full = $this->fullPath($path);
        if (file_exists($full)) {
            return @unlink($full);
        }
        return false;
    }

    public function size(string $path): int {
        $full = $this->fullPath($path);
        return file_exists($full) ? (int)filesize($full) : 0;
    }

    public function mimeType(string $path): string {
        $full = $this->fullPath($path);
        if (!file_exists($full)) {
            return 'application/octet-stream';
        }
        $ext = strtolower(pathinfo($full, PATHINFO_EXTENSION));
        return match ($ext) {
            'pdf' => 'application/pdf',
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'txt' => 'text/plain',
            'json' => 'application/json',
            'zip' => 'application/zip',
            'mp4' => 'video/mp4',
            default => 'application/octet-stream',
        };
    }

    public function checksum(string $path): ?string {
        $full = $this->fullPath($path);
        if (!file_exists($full)) {
            return null;
        }
        return hash_file('sha256', $full) ?: null;
    }

    public function url(string $path): string {
        return $this->baseUrl . '/' . ltrim($path, '/');
    }
}
