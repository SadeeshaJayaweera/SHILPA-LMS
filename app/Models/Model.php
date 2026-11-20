<?php
declare(strict_types=1);

namespace App\Models;

use App\Database\Connection;
use App\Database\QueryBuilder;
use PDO;

abstract class Model {
    protected string $table;
    protected string $primaryKey = 'id';
    protected array $fillable = [];
    protected array $attributes = [];
    protected array $original = [];

    public function __construct(array $attributes = []) {
        $this->fill($attributes);
    }

    public static function query(): QueryBuilder {
        $instance = new static();
        return QueryBuilder::table($instance->table);
    }

    public static function all(): array {
        $rows = static::query()->get();
        return array_map(fn($row) => new static($row), $rows);
    }

    public static function find(mixed $id): ?static {
        $instance = new static();
        $row = static::query()->where($instance->primaryKey, $id)->first();
        return $row ? new static($row) : null;
    }

    public static function create(array $attributes): static {
        $instance = new static();
        $filtered = $instance->filterFillable($attributes);
        $id = static::query()->insert($filtered);
        $attributes[$instance->primaryKey] = $id;
        return new static($attributes);
    }

    public function fill(array $attributes): self {
        foreach ($attributes as $key => $value) {
            $this->attributes[$key] = $value;
        }
        return $this;
    }

    public function save(): bool {
        $id = $this->attributes[$this->primaryKey] ?? null;
        if ($id !== null) {
            $filtered = $this->filterFillable($this->attributes);
            return static::query()->where($this->primaryKey, $id)->update($filtered) >= 0;
        } else {
            $filtered = $this->filterFillable($this->attributes);
            $newId = static::query()->insert($filtered);
            $this->attributes[$this->primaryKey] = $newId;
            return true;
        }
    }

    public function delete(): bool {
        $id = $this->attributes[$this->primaryKey] ?? null;
        if ($id === null) {
            return false;
        }
        return static::query()->where($this->primaryKey, $id)->delete() > 0;
    }

    public function __get(string $name): mixed {
        return $this->attributes[$name] ?? null;
    }

    public function __set(string $name, mixed $value): void {
        $this->attributes[$name] = $value;
    }

    public function toArray(): array {
        return $this->attributes;
    }

    protected function filterFillable(array $attributes): array {
        if (empty($this->fillable)) {
            return $attributes;
        }
        return array_intersect_key($attributes, array_flip($this->fillable));
    }
}
