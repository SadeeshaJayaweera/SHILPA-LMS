<?php
declare(strict_types=1);

namespace App\Cache;

use Closure;

class ArrayCacheStore implements CacheStoreInterface {
    protected array $storage = []; // [key] => ['val' => ..., 'exp' => timestamp]

    public function get(string $key, mixed $default = null): mixed {
        if (!isset($this->storage[$key])) {
            return $default;
        }

        $item = $this->storage[$key];
        if ($item['exp'] !== 0 && $item['exp'] < time()) {
            unset($this->storage[$key]);
            return $default;
        }

        return $item['val'];
    }

    public function put(string $key, mixed $value, int $ttl = 3600): bool {
        $exp = $ttl > 0 ? time() + $ttl : 0;
        $this->storage[$key] = [
            'val' => $value,
            'exp' => $exp,
        ];
        return true;
    }

    public function has(string $key): bool {
        return $this->get($key) !== null;
    }

    public function forget(string $key): bool {
        if (isset($this->storage[$key])) {
            unset($this->storage[$key]);
            return true;
        }
        return false;
    }

    public function flush(): bool {
        $this->storage = [];
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
