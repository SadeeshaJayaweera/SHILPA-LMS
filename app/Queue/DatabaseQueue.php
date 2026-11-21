<?php
declare(strict_types=1);

namespace App\Queue;

use App\Database\Connection;
use PDO;
use Throwable;

class DatabaseQueue {
    protected PDO $pdo;

    public function __construct(?PDO $pdo = null) {
        $this->pdo = $pdo ?? Connection::get();
    }

    /**
     * Push a new job onto the queue
     */
    public function push(object|string $job, array $data = [], string $queue = 'default'): int {
        $payload = is_object($job)
            ? serialize($job)
            : json_encode(['job' => $job, 'data' => $data]);

        $now = date('Y-m-d H:i:s');
        $stmt = $this->pdo->prepare("
            INSERT INTO jobs (queue, payload, attempts, reserved_at, available_at, created_at, updated_at)
            VALUES (:queue, :payload, 0, NULL, :available_at, :created_at, :updated_at)
        ");
        $stmt->execute([
            ':queue' => $queue,
            ':payload' => $payload,
            ':available_at' => $now,
            ':created_at' => $now,
            ':updated_at' => $now,
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Pop the next available job off the queue and reserve it
     */
    public function pop(string $queue = 'default'): ?array {
        $now = date('Y-m-d H:i:s');

        // Find available unreserved job
        $stmt = $this->pdo->prepare("
            SELECT id, queue, payload, attempts
            FROM jobs
            WHERE queue = :queue
              AND (reserved_at IS NULL OR reserved_at < :timeout)
              AND (available_at IS NULL OR available_at <= :now)
            ORDER BY id ASC
            LIMIT 1
        ");
        $timeout = date('Y-m-d H:i:s', time() - 300); // 5 min reservation timeout
        $stmt->execute([
            ':queue' => $queue,
            ':timeout' => $timeout,
            ':now' => $now,
        ]);

        $job = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$job) {
            return null;
        }

        // Reserve job
        $reserveStmt = $this->pdo->prepare("
            UPDATE jobs
            SET reserved_at = :reserved_at, attempts = attempts + 1, updated_at = :now
            WHERE id = :id
        ");
        $reserveStmt->execute([
            ':reserved_at' => $now,
            ':now' => $now,
            ':id' => $job['id'],
        ]);

        $job['attempts'] = (int)$job['attempts'] + 1;
        return $job;
    }

    /**
     * Delete a completed job
     */
    public function delete(int $jobId): bool {
        $stmt = $this->pdo->prepare("DELETE FROM jobs WHERE id = :id");
        return $stmt->execute([':id' => $jobId]);
    }

    /**
     * Release a reserved job back to the queue with optional delay
     */
    public function release(int $jobId, int $delay = 0): bool {
        $availableAt = date('Y-m-d H:i:s', time() + $delay);
        $stmt = $this->pdo->prepare("
            UPDATE jobs
            SET reserved_at = NULL, available_at = :available_at, updated_at = :now
            WHERE id = :id
        ");
        return $stmt->execute([
            ':available_at' => $availableAt,
            ':now' => date('Y-m-d H:i:s'),
            ':id' => $jobId,
        ]);
    }

    /**
     * Log a failed job and remove it from active queue
     */
    public function fail(int $jobId, Throwable $e): void {
        $stmt = $this->pdo->prepare("SELECT * FROM jobs WHERE id = :id");
        $stmt->execute([':id' => $jobId]);
        $job = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($job) {
            $failStmt = $this->pdo->prepare("
                INSERT INTO failed_jobs (connection, queue, payload, exception, failed_at)
                VALUES ('database', :queue, :payload, :exception, :failed_at)
            ");
            $failStmt->execute([
                ':queue' => $job['queue'],
                ':payload' => $job['payload'],
                ':exception' => (string)$e,
                ':failed_at' => date('Y-m-d H:i:s'),
            ]);

            $this->delete($jobId);
        }
    }

    /**
     * Count remaining active jobs in queue
     */
    public function count(string $queue = 'default'): int {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM jobs WHERE queue = :queue");
        $stmt->execute([':queue' => $queue]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Count failed jobs
     */
    public function failedCount(): int {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM failed_jobs");
        return (int)($stmt ? $stmt->fetchColumn() : 0);
    }
}
