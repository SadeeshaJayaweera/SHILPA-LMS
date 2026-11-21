<?php
declare(strict_types=1);

namespace App\Queue;

class QueueManager {
    protected static ?DatabaseQueue $databaseQueue = null;
    protected static ?SyncQueue $syncQueue = null;

    public static function queue(string $driver = 'database'): DatabaseQueue|SyncQueue {
        if ($driver === 'sync') {
            if (self::$syncQueue === null) {
                self::$syncQueue = new SyncQueue();
            }
            return self::$syncQueue;
        }

        if (self::$databaseQueue === null) {
            self::$databaseQueue = new DatabaseQueue();
        }

        return self::$databaseQueue;
    }

    public static function push(object|string $job, array $data = [], string $queue = 'default'): int {
        return self::queue('database')->push($job, $data, $queue);
    }
}
