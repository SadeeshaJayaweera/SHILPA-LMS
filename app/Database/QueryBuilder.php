<?php
declare(strict_types=1);

namespace App\Database;

use PDO;
use PDOStatement;
use RuntimeException;

class QueryBuilder {
    private PDO $pdo;
    private string $table = '';
    private array $columns = ['*'];
    private array $wheres = [];
    private array $joins = [];
    private array $bindings = [];
    private array $orders = [];
    private ?int $limit = null;
    private ?int $offset = null;

    public function __construct(?PDO $pdo = null) {
        $this->pdo = $pdo ?? Connection::get();
    }

    public static function table(string $table, ?PDO $pdo = null): self {
        $builder = new self($pdo);
        $builder->table = $table;
        return $builder;
    }

    public function select(array $columns = ['*']): self {
        $this->columns = $columns;
        return $this;
    }

    public function where(string $column, mixed $operator = null, mixed $value = null): self {
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }

        $this->wheres[] = [
            'type' => 'AND',
            'column' => $column,
            'operator' => (string)$operator,
            'value' => $value,
        ];
        $this->bindings[] = $value;
        return $this;
    }

    public function orWhere(string $column, mixed $operator = null, mixed $value = null): self {
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }

        $this->wheres[] = [
            'type' => 'OR',
            'column' => $column,
            'operator' => (string)$operator,
            'value' => $value,
        ];
        $this->bindings[] = $value;
        return $this;
    }

    public function join(string $table, string $first, string $operator, string $second, string $type = 'INNER'): self {
        $this->joins[] = "$type JOIN $table ON $first $operator $second";
        return $this;
    }

    public function leftJoin(string $table, string $first, string $operator, string $second): self {
        return $this->join($table, $first, $operator, $second, 'LEFT');
    }

    public function orderBy(string $column, string $direction = 'ASC'): self {
        $direction = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';
        $this->orders[] = "$column $direction";
        return $this;
    }

    public function limit(int $limit): self {
        $this->limit = $limit;
        return $this;
    }

    public function offset(int $offset): self {
        $this->offset = $offset;
        return $this;
    }

    public function get(): array {
        $sql = $this->toSelectSql();
        $stmt = $this->execute($sql, $this->bindings);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function first(): ?array {
        $this->limit(1);
        $results = $this->get();
        return $results[0] ?? null;
    }

    public function find(mixed $id, string $primaryKey = 'id'): ?array {
        return $this->where($primaryKey, $id)->first();
    }

    public function count(): int {
        $prevColumns = $this->columns;
        $this->columns = ['COUNT(*) as total'];
        $sql = $this->toSelectSql();
        $this->columns = $prevColumns;

        $stmt = $this->execute($sql, $this->bindings);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($res['total'] ?? 0);
    }

    public function insert(array $values): int|string {
        $columns = array_keys($values);
        $placeholders = array_fill(0, count($columns), '?');

        $colList = implode(', ', array_map(fn($col) => "`$col`", $columns));
        $placeList = implode(', ', $placeholders);

        $sql = "INSERT INTO `{$this->table}` ($colList) VALUES ($placeList)";
        $this->execute($sql, array_values($values));

        return $this->pdo->lastInsertId();
    }

    public function update(array $values): int {
        if (empty($values)) {
            return 0;
        }

        $sets = [];
        $bindings = [];
        foreach ($values as $col => $val) {
            $sets[] = "`$col` = ?";
            $bindings[] = $val;
        }

        $setSql = implode(', ', $sets);
        $whereSql = $this->compileWheres();
        $bindings = array_merge($bindings, $this->bindings);

        $sql = "UPDATE `{$this->table}` SET $setSql $whereSql";
        $stmt = $this->execute($sql, $bindings);
        return $stmt->rowCount();
    }

    public function delete(): int {
        $whereSql = $this->compileWheres();
        $sql = "DELETE FROM `{$this->table}` $whereSql";
        $stmt = $this->execute($sql, $this->bindings);
        return $stmt->rowCount();
    }

    private function toSelectSql(): string {
        $cols = implode(', ', $this->columns);
        $sql = "SELECT $cols FROM `{$this->table}`";

        if (!empty($this->joins)) {
            $sql .= ' ' . implode(' ', $this->joins);
        }

        $sql .= $this->compileWheres();

        if (!empty($this->orders)) {
            $sql .= ' ORDER BY ' . implode(', ', $this->orders);
        }

        if ($this->limit !== null) {
            $sql .= " LIMIT {$this->limit}";
        }

        if ($this->offset !== null) {
            $sql .= " OFFSET {$this->offset}";
        }

        return $sql;
    }

    private function compileWheres(): string {
        if (empty($this->wheres)) {
            return '';
        }

        $parts = [];
        foreach ($this->wheres as $i => $where) {
            $prefix = $i === 0 ? '' : $where['type'] . ' ';
            $parts[] = "{$prefix}`{$where['column']}` {$where['operator']} ?";
        }

        return ' WHERE ' . implode(' ', $parts);
    }

    private function execute(string $sql, array $bindings): PDOStatement {
        // Normalize backticks for SQLite compatibility
        $driver = $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
        if ($driver === 'sqlite') {
            $sql = str_replace('`', '"', $sql);
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($bindings);
        return $stmt;
    }
}
