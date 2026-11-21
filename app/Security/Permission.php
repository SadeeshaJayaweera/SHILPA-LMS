<?php
declare(strict_types=1);

namespace App\Security;

use App\Models\Model;

class Permission extends Model {
    protected string $table = 'permissions';
    protected array $fillable = [
        'name',
        'description',
    ];

    public static function findByName(string $name): ?self {
        $row = self::query()->where('name', $name)->first();
        return $row ? new self($row) : null;
    }
}
