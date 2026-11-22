<?php
declare(strict_types=1);

namespace App\Database;

use PDO;
use PDOException;
use RuntimeException;

class Connection {
    private static array $instances = [];

    public static function get(?string $name = null): PDO {
        $name = $name ?? (string)config('database.default', 'mysql');

        if (isset(self::$instances[$name])) {
            return self::$instances[$name];
        }

        $config = config("database.connections.$name");
        if (!$config || !is_array($config)) {
            throw new RuntimeException("Database connection [$name] is not configured.");
        }

        $driver = $config['driver'] ?? 'mysql';

        try {
            if ($driver === 'sqlite') {
                $dbPath = $config['database'] ?? ':memory:';
                $pdo = new PDO("sqlite:$dbPath", null, null, $config['options'] ?? []);
                $pdo->exec('PRAGMA foreign_keys = ON;');
            } elseif ($driver === 'mysql') {
                $host = $config['host'] ?? '127.0.0.1';
                $port = $config['port'] ?? 3306;
                $dbname = $config['database'] ?? 'shilpa';
                $charset = $config['charset'] ?? 'utf8mb4';
                $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=$charset";
                $pdo = new PDO(
                    $dsn,
                    (string)($config['username'] ?? 'root'),
                    (string)($config['password'] ?? ''),
                    $config['options'] ?? []
                );
            } else {
                throw new RuntimeException("Unsupported database driver [$driver].");
            }

            self::$instances[$name] = $pdo;
            return $pdo;
        } catch (PDOException $e) {
            // In CLI / test / local dev server environments, fallback to SQLite if MySQL is unavailable
            if ($driver === 'mysql' && (defined('SHILPA_TEST_MODE') || php_sapi_name() === 'cli' || php_sapi_name() === 'cli-server')) {
                $dbFile = dirname(__DIR__, 2) . '/storage/database.sqlite';
                $dsn = file_exists($dbFile) ? "sqlite:$dbFile" : 'sqlite::memory:';
                $fallbackPdo = new PDO($dsn, null, null, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);
                $fallbackPdo->exec('PRAGMA foreign_keys = ON;');
                self::$instances[$name] = $fallbackPdo;
                return $fallbackPdo;
            }
            throw new RuntimeException("Database connection error: " . $e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    public static function set(string $name, PDO $pdo): void {
        self::$instances[$name] = $pdo;
    }

    public static function reset(): void {
        self::$instances = [];
    }

    public static function transaction(callable $callback, ?string $connectionName = null): mixed {
        $pdo = self::get($connectionName);
        $pdo->beginTransaction();
        try {
            $result = $callback($pdo);
            $pdo->commit();
            return $result;
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }
}
