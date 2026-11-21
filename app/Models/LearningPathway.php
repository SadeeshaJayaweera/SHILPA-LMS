<?php
declare(strict_types=1);

namespace App\Models;

class LearningPathway extends Model {
    protected string $table = 'learning_pathways';
    protected array $fillable = [
        'title',
        'slug',
        'description',
        'target_role',
        'course_ids',
        'icon',
        'is_published',
        'created_at',
        'updated_at',
    ];

    public static function published(): array {
        $rows = self::query()
            ->where('is_published', 1)
            ->orderBy('title', 'ASC')
            ->get();
        return array_map(fn($r) => new self($r), $rows);
    }

    public static function findBySlug(string $slug): ?self {
        $row = self::query()->where('slug', $slug)->first();
        return $row ? new self($row) : null;
    }

    public function getCourseIds(): array {
        $data = json_decode((string)$this->course_ids, true);
        return is_array($data) ? array_map('intval', $data) : [];
    }

    public function courses(): array {
        $ids = $this->getCourseIds();
        $list = [];
        foreach ($ids as $id) {
            $c = Course::find($id);
            if ($c) {
                $list[] = $c;
            }
        }
        return $list;
    }
}
