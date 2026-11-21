<?php
declare(strict_types=1);

namespace App\Security;

use App\Database\Connection;
use PDO;

class RateLimiter {
    private static array $memoryStore = [];

    /**
     * Determine if the given key has exceeded maximum attempts.
     */
    public static function tooManyAttempts(string $key, int $maxAttempts = 5): bool {
        $now = time();

        // Check memory store (fastest, useful for tests & CLI)
        if (isset(self::$memoryStore[$key])) {
            $data = self::$memoryStore[$key];
            if ($data['locked_until'] > $now) {
                return true;
            }
            if ($data['attempts'] >= $maxAttempts) {
                return true;
            }
        }

        // Check persistent store if connection exists
        try {
            $pdo = Connection::get();
            $stmt = $pdo->prepare("SELECT attempts, locked_until FROM login_throttles WHERE `key` = ?");
            $stmt->execute([$key]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                if ((int)$row['locked_until'] > $now) {
                    return true;
                }
                if ((int)$row['attempts'] >= $maxAttempts) {
                    return true;
                }
            }
        } catch (\Throwable $e) {
            // Fall back to memory store if table not yet initialized
        }

        return false;
    }

    /**
     * Increment the counter for a given key.
     */
    public static function hit(string $key, int $decaySeconds = 900, int $maxAttempts = 5): int {
        $now = time();

        // Memory store update
        $currentAttempts = (self::$memoryStore[$key]['attempts'] ?? 0) + 1;
        $lockedUntil = $currentAttempts >= $maxAttempts ? ($now + $decaySeconds) : 0;

        self::$memoryStore[$key] = [
            'attempts' => $currentAttempts,
            'last_attempt_at' => $now,
            'locked_until' => $lockedUntil,
        ];

        // Database store update
        try {
            $pdo = Connection::get();
            $stmt = $pdo->prepare("
                INSERT INTO login_throttles (`key`, attempts, last_attempt_at, locked_until)
                VALUES (?, 1, ?, 0)
                ON DUPLICATE KEY UPDATE 
                    attempts = attempts + 1,
                    last_attempt_at = VALUES(last_attempt_at),
                    locked_until = IF(attempts + 1 >= ?, ? + ?, locked_until)
            ");
            // If sqlite, use INSERT OR REPLACE
            if ($pdo->getAttribute(PDO::ATTR_DRIVER_NAME) === 'sqlite') {
                $stmt = $pdo->prepare("
                    INSERT INTO login_throttles (`key`, attempts, last_attempt_at, locked_until)
                    VALUES (?, ?, ?, ?)
                    ON CONFLICT(`key`) DO UPDATE SET
                        attempts = attempts + 1,
                        last_attempt_at = excluded.last_attempt_at,
                        locked_until = CASE WHEN attempts + 1 >= ? THEN ? + ? ELSE locked_until END
                ");
                $stmt->execute([$key, 1, $now, $lockedUntil, $maxAttempts, $now, $decaySeconds]);
            } else {
                $stmt->execute([$key, $now, $maxAttempts, $now, $decaySeconds]);
            }
        } catch (\Throwable $e) {
            // Ignore DB error, memory store is active
        }

        return $currentAttempts;
    }

    /**
     * Get the number of seconds until the "key" is accessible again.
     */
    public static function availableIn(string $key): int {
        $now = time();
        $lockedUntil = self::$memoryStore[$key]['locked_until'] ?? 0;

        if ($lockedUntil > $now) {
            return $lockedUntil - $now;
        }

        try {
            $pdo = Connection::get();
            $stmt = $pdo->prepare("SELECT locked_until FROM login_throttles WHERE `key` = ?");
            $stmt->execute([$key]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row && (int)$row['locked_until'] > $now) {
                return (int)$row['locked_until'] - $now;
            }
        } catch (\Throwable $e) {
        }

        return 0;
    }

    /**
     * Clear the hit counter and lockout for the key.
     */
    public static function clear(string $key): void {
        unset(self::$memoryStore[$key]);

        try {
            $pdo = Connection::get();
            $stmt = $pdo->prepare("DELETE FROM login_throttles WHERE `key` = ?");
            $stmt->execute([$key]);
        } catch (\Throwable $e) {
        }
    }
}
