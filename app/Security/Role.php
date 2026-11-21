<?php
declare(strict_types=1);

namespace App\Security;

use App\Models\Model;

class Role extends Model {
    protected string $table = 'roles';
    protected array $fillable = [
        'name',
        'display_name',
        'description',
    ];

    public static function findByName(string $name): ?self {
        $row = self::query()->where('name', $name)->first();
        return $row ? new self($row) : null;
    }
}
