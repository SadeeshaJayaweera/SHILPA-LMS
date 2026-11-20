<?php
declare(strict_types=1);

namespace App\Models;

class Course extends Model {
    protected string $table = 'course';
    protected string $primaryKey = 'C_ID';
    protected array $fillable = [
        'C_NAME',
        'C_CARD_DESCRIPTION',
        'C_DESCRIPTION',
        'C_CARD_IMAGE',
        'C_DONE_BY',
        'C_LINK',
        'TOP_COURSE',
        'C_ADD_DATE',
    ];

    public static function getLatest(int $limit = 4): array {
        $rows = self::query()
            ->orderBy('C_ADD_DATE', 'DESC')
            ->limit($limit)
            ->get();
        return array_map(fn($r) => new self($r), $rows);
    }

    public static function getTopCourses(int $limit = 4): array {
        $rows = self::query()
            ->where('TOP_COURSE', 'Yes')
            ->orWhere('TOP_COURSE', 'yes')
            ->limit($limit)
            ->get();

        if (empty($rows)) {
            $rows = self::query()->limit($limit)->get();
        }

        return array_map(fn($r) => new self($r), $rows);
    }

    public static function search(string $term): array {
        $rows = self::query()
            ->where('C_NAME', 'LIKE', "%$term%")
            ->orWhere('C_DESCRIPTION', 'LIKE', "%$term%")
            ->get();
        return array_map(fn($r) => new self($r), $rows);
    }
}
