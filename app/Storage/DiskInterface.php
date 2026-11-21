<?php
declare(strict_types=1);

namespace App\Storage;

interface DiskInterface {
    public function put(string $path, string $contents): bool;
    public function get(string $path): ?string;
    public function exists(string $path): bool;
    public function delete(string $path): bool;
    public function size(string $path): int;
    public function mimeType(string $path): string;
    public function checksum(string $path): ?string;
    public function url(string $path): string;
}
