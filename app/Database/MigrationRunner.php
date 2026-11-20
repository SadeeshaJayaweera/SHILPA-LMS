<?php
declare(strict_types=1);

namespace App\Database;

use PDO;

class MigrationRunner {
    private PDO $pdo;
    private string $migrationsPath;

    public function __construct(?PDO $pdo = null, ?string $migrationsPath = null) {
        $this->pdo = $pdo ?? Connection::get();
        $this->migrationsPath = $migrationsPath ?? dirname(__DIR__, 2) . '/database/migrations';
        $this->initMigrationsTable();
    }

    private function initMigrationsTable(): void {
        $driver = $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
        if ($driver === 'sqlite') {
            $this->pdo->exec("
                CREATE TABLE IF NOT EXISTS \"migrations\" (
                    \"id\" INTEGER PRIMARY KEY AUTOINCREMENT,
                    \"migration\" VARCHAR(255) NOT NULL,
                    \"batch\" INTEGER NOT NULL,
                    \"executed_at\" DATETIME DEFAULT CURRENT_TIMESTAMP
                );
            ");
        } else {
            $this->pdo->exec("
                CREATE TABLE IF NOT EXISTS `migrations` (
                    `id` INT AUTO_INCREMENT PRIMARY KEY,
                    `migration` VARCHAR(255) NOT NULL,
                    `batch` INT NOT NULL,
                    `executed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");
        }
    }

    public function run(): array {
        $applied = $this->getAppliedMigrations();
        $files = glob($this->migrationsPath . '/*.php');
        sort($files);

        $nextBatch = $this->getNextBatch();
        $executed = [];

        foreach ($files as $file) {
            $migrationName = basename($file, '.php');
            if (in_array($migrationName, $applied, true)) {
                continue;
            }

            $migration = require $file;
            if ($migration instanceof Migration) {
                $migration->up($this->pdo);

                $stmt = $this->pdo->prepare("INSERT INTO migrations (migration, batch) VALUES (?, ?)");
                $stmt->execute([$migrationName, $nextBatch]);
                $executed[] = $migrationName;
            }
        }

        return $executed;
    }

    private function getAppliedMigrations(): array {
        $stmt = $this->pdo->query("SELECT migration FROM migrations ORDER BY id ASC");
        return $stmt ? $stmt->fetchAll(PDO::FETCH_COLUMN) : [];
    }

    private function getNextBatch(): int {
        $stmt = $this->pdo->query("SELECT MAX(batch) as max_batch FROM migrations");
        $res = $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : null;
        return ((int)($res['max_batch'] ?? 0)) + 1;
    }
}
