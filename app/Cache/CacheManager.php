<?php
declare(strict_types=1);

namespace App\Cache;

class CacheManager {
    protected static ?CacheStoreInterface $defaultStore = null;

    public static function store(?string $driver = null): CacheStoreInterface {
        if ($driver === 'array' || defined('SHILPA_TEST_MODE')) {
            if (!self::$defaultStore instanceof ArrayCacheStore) {
                self::$defaultStore = new ArrayCacheStore();
            }
            return self::$defaultStore;
        }

        if (self::$defaultStore === null) {
            self::$defaultStore = new FileCacheStore();
        }

        return self::$defaultStore;
    }

    public static function setStore(CacheStoreInterface $store): void {
        self::$defaultStore = $store;
    }

    public static function reset(): void {
        self::$defaultStore = null;
    }
}
