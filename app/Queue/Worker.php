<?php
declare(strict_types=1);

namespace App\Queue;

use Throwable;

class Worker {
    protected DatabaseQueue $queue;
    protected int $maxAttempts;

    public function __construct(?DatabaseQueue $queue = null, int $maxAttempts = 3) {
        $this->queue = $queue ?? new DatabaseQueue();
        $this->maxAttempts = $maxAttempts;
    }

    /**
     * Run the next pending job from the queue
     *
     * @return bool True if a job was processed, false if queue is empty
     */
    public function runNextJob(string $queueName = 'default'): bool {
        $jobData = $this->queue->pop($queueName);
        if (!$jobData) {
            return false;
        }

        $jobId = (int)$jobData['id'];
        $attempts = (int)$jobData['attempts'];
        $payload = $jobData['payload'];

        try {
            $instance = @unserialize($payload);

            if (!is_object($instance) || !method_exists($instance, 'handle')) {
                // Try JSON format
                $json = json_decode($payload, true);
                if (is_array($json) && isset($json['job']) && class_exists($json['job'])) {
                    $class = $json['job'];
                    $data = $json['data'] ?? [];
                    $instance = new $class(...$data);
                }
            }

            if (!is_object($instance) || !method_exists($instance, 'handle')) {
                throw new \RuntimeException("Invalid job payload: cannot instantiate handler");
            }

            // Execute job
            $instance->handle();

            // Mark completed
            $this->queue->delete($jobId);
            return true;
        } catch (Throwable $e) {
            if ($attempts >= $this->maxAttempts) {
                // Failed permanently
                $this->queue->fail($jobId, $e);
            } else {
                // Release back with exponential backoff (attempts * 15 seconds)
                $this->queue->release($jobId, $attempts * 15);
            }
            return true;
        }
    }

    /**
     * Process multiple jobs until queue empty or limit reached
     */
    public function work(int $maxJobs = 0, string $queueName = 'default'): int {
        $processed = 0;
        while ($maxJobs === 0 || $processed < $maxJobs) {
            $ran = $this->runNextJob($queueName);
            if (!$ran) {
                break;
            }
            $processed++;
        }
        return $processed;
    }
}
