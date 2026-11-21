<?php
declare(strict_types=1);

namespace App\Models;

class CourseCategory extends Model {
    protected string $table = 'course_categories';
    protected array $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'display_order',
    ];

    public static function allOrdered(): array {
        $rows = self::query()->orderBy('display_order', 'ASC')->get();
        return array_map(fn($r) => new self($r), $rows);
    }

    public static function findBySlug(string $slug): ?self {
        $row = self::query()->where('slug', $slug)->first();
        return $row ? new self($row) : null;
    }
}
