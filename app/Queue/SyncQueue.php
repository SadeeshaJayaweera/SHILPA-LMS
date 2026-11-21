<?php
declare(strict_types=1);

namespace App\Queue;

class SyncQueue {
    /**
     * Executes the job immediately in-process
     */
    public function push(object|string $job, array $data = [], string $queue = 'default'): int {
        if (is_object($job) && method_exists($job, 'handle')) {
            $job->handle();
            return 1;
        }

        if (is_string($job) && class_exists($job)) {
            $instance = new $job(...$data);
            if (method_exists($instance, 'handle')) {
                $instance->handle();
                return 1;
            }
        }

        return 0;
    }

    public function count(string $queue = 'default'): int {
        return 0;
    }
}
