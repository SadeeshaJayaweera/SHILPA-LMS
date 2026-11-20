<?php
declare(strict_types=1);

namespace App\Database;

use PDO;

class TableBlueprint {
    private string $table;
    private PDO $pdo;
    private array $columns = [];
    private array $indexes = [];
    private array $foreignKeys = [];

    public function __construct(string $table, PDO $pdo) {
        $this->table = $table;
        $this->pdo = $pdo;
    }

    public function id(string $name = 'id'): self {
        $driver = $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
        if ($driver === 'sqlite') {
            $this->columns[] = "\"$name\" INTEGER PRIMARY KEY AUTOINCREMENT";
        } else {
            $this->columns[] = "`$name` INT AUTO_INCREMENT PRIMARY KEY";
        }
        return $this;
    }

    public function string(string $name, int $length = 255, bool $nullable = false, ?string $default = null): self {
        $this->addColumn($name, "VARCHAR($length)", $nullable, $default);
        return $this;
    }

    public function text(string $name, bool $nullable = true): self {
        $this->addColumn($name, "TEXT", $nullable);
        return $this;
    }

    public function integer(string $name, bool $nullable = false, ?int $default = null): self {
        $this->addColumn($name, "INT", $nullable, $default !== null ? (string)$default : null);
        return $this;
    }

    public function boolean(string $name, bool $default = false): self {
        $this->addColumn($name, "TINYINT(1)", false, $default ? '1' : '0');
        return $this;
    }

    public function timestamp(string $name, bool $useCurrent = true): self {
        $def = $useCurrent ? "CURRENT_TIMESTAMP" : null;
        $this->addColumn($name, "DATETIME", true, $def);
        return $this;
    }

    public function timestamps(): self {
        $this->timestamp('created_at', true);
        $this->timestamp('updated_at', true);
        return $this;
    }

    public function unique(string|array $columns): self {
        $cols = is_array($columns) ? $columns : [$columns];
        $this->indexes[] = "UNIQUE (" . implode(', ', array_map(fn($c) => "`$c`", $cols)) . ")";
        return $this;
    }

    public function index(string|array $columns): self {
        $cols = is_array($columns) ? $columns : [$columns];
        $this->indexes[] = "INDEX (" . implode(', ', array_map(fn($c) => "`$c`", $cols)) . ")";
        return $this;
    }

    public function foreign(string $column, string $referencedTable, string $referencedColumn = 'id'): self {
        $this->foreignKeys[] = "FOREIGN KEY (`$column`) REFERENCES `$referencedTable`(`$referencedColumn`) ON DELETE CASCADE";
        return $this;
    }

    private function addColumn(string $name, string $type, bool $nullable, ?string $default = null): void {
        $nullStr = $nullable ? "NULL" : "NOT NULL";
        $defaultStr = $default !== null ? "DEFAULT " . ($default === 'CURRENT_TIMESTAMP' ? 'CURRENT_TIMESTAMP' : "'$default'") : "";
        $this->columns[] = trim("`$name` $type $nullStr $defaultStr");
    }

    public function build(): void {
        $driver = $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
        $defs = array_merge($this->columns, $this->indexes, $this->foreignKeys);
        $body = implode(",\n  ", $defs);

        if ($driver === 'sqlite') {
            $filteredDefs = array_filter($defs, fn($d) => !str_starts_with(trim($d), 'INDEX'));
            $body = str_replace('`', '"', implode(",\n  ", $filteredDefs));
            $sql = "CREATE TABLE IF NOT EXISTS \"{$this->table}\" (\n  $body\n);";
        } else {
            $sql = "CREATE TABLE IF NOT EXISTS `{$this->table}` (\n  $body\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        }

        $this->pdo->exec($sql);
    }
}
