<?php
declare(strict_types=1);

namespace App\Cache;

use Closure;

interface CacheStoreInterface {
    public function get(string $key, mixed $default = null): mixed;
    public function put(string $key, mixed $value, int $ttl = 3600): bool;
    public function has(string $key): bool;
    public function forget(string $key): bool;
    public function flush(): bool;
    public function remember(string $key, int $ttl, Closure $callback): mixed;
    public function tags(array $tags): TaggedCache;
}
