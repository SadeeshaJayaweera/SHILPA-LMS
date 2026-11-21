<?php
declare(strict_types=1);

namespace App\Cache;

use Closure;

class TaggedCache {
    protected CacheStoreInterface $store;
    protected array $tags;

    public function __construct(CacheStoreInterface $store, array $tags) {
        $this->store = $store;
        $this->tags = array_unique(array_filter($tags));
    }

    protected function taggedKey(string $key): string {
        $prefix = implode(':', $this->tags);
        return "tag:{$prefix}:{$key}";
    }

    public function get(string $key, mixed $default = null): mixed {
        return $this->store->get($this->taggedKey($key), $default);
    }

    public function put(string $key, mixed $value, int $ttl = 3600): bool {
        // Track key under each tag for invalidation
        foreach ($this->tags as $tag) {
            $tagIndexKey = "tag_index:{$tag}";
            $keys = $this->store->get($tagIndexKey, []);
            if (!in_array($this->taggedKey($key), $keys, true)) {
                $keys[] = $this->taggedKey($key);
                $this->store->put($tagIndexKey, $keys, 86400 * 30);
            }
        }
        return $this->store->put($this->taggedKey($key), $value, $ttl);
    }

    public function has(string $key): bool {
        return $this->store->has($this->taggedKey($key));
    }

    public function forget(string $key): bool {
        return $this->store->forget($this->taggedKey($key));
    }

    public function remember(string $key, int $ttl, Closure $callback): mixed {
        $cached = $this->get($key);
        if ($cached !== null) {
            return $cached;
        }

        $val = $callback();
        $this->put($key, $val, $ttl);
        return $val;
    }

    /**
     * Invalidate all keys matching any of the tags
     */
    public function flush(): bool {
        foreach ($this->tags as $tag) {
            $tagIndexKey = "tag_index:{$tag}";
            $keys = $this->store->get($tagIndexKey, []);
            foreach ($keys as $k) {
                $this->store->forget($k);
            }
            $this->store->forget($tagIndexKey);
        }
        return true;
    }
}
