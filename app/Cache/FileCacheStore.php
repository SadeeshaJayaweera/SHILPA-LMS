<?php
declare(strict_types=1);

namespace App\Cache;

use Closure;

class FileCacheStore implements CacheStoreInterface {
    protected string $directory;

    public function __construct(?string $directory = null) {
        $this->directory = $directory ?? (dirname(__DIR__, 2) . '/storage/cache');
        if (!is_dir($this->directory)) {
            @mkdir($this->directory, 0777, true);
        }
    }

    protected function path(string $key): string {
        $hash = sha1($key);
        return $this->directory . '/' . $hash . '.cache';
    }

    public function get(string $key, mixed $default = null): mixed {
        $file = $this->path($key);
        if (!file_exists($file)) {
            return $default;
        }

        $content = @file_get_contents($file);
        if ($content === false) {
            return $default;
        }

        $data = @unserialize($content);
        if (!is_array($data) || !isset($data['exp'], $data['val'])) {
            @unlink($file);
            return $default;
        }

        if ($data['exp'] !== 0 && $data['exp'] < time()) {
            @unlink($file);
            return $default;
        }

        return $data['val'];
    }

    public function put(string $key, mixed $value, int $ttl = 3600): bool {
        $file = $this->path($key);
        $exp = $ttl > 0 ? time() + $ttl : 0;
        $payload = serialize([
            'key' => $key,
            'exp' => $exp,
            'val' => $value,
            'created' => time(),
        ]);

        return @file_put_contents($file, $payload, LOCK_EX) !== false;
    }

    public function has(string $key): bool {
        return $this->get($key) !== null;
    }

    public function forget(string $key): bool {
        $file = $this->path($key);
        if (file_exists($file)) {
            return @unlink($file);
        }
        return false;
    }

    public function flush(): bool {
        $files = glob($this->directory . '/*.cache');
        if (is_array($files)) {
            foreach ($files as $file) {
                @unlink($file);
            }
        }
        return true;
    }

    public function remember(string $key, int $ttl, Closure $callback): mixed {
        $val = $this->get($key);
        if ($val !== null) {
            return $val;
        }

        $computed = $callback();
        $this->put($key, $computed, $ttl);
        return $computed;
    }

    public function tags(array $tags): TaggedCache {
        return new TaggedCache($this, $tags);
    }
}
