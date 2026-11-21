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
        return cache()->tags(['courses', 'catalog'])->remember("top_courses_{$limit}", 3600, function () use ($limit) {
            $rows = self::query()
                ->where('TOP_COURSE', 'Yes')
                ->orWhere('TOP_COURSE', 'yes')
                ->limit($limit)
                ->get();

            if (empty($rows)) {
                $rows = self::query()->limit($limit)->get();
            }

            return array_map(fn($r) => new self($r), $rows);
        });
    }

    public static function search(string $term): array {
        $rows = self::query()
            ->where('C_NAME', 'LIKE', "%$term%")
            ->orWhere('C_DESCRIPTION', 'LIKE', "%$term%")
            ->get();
        return array_map(fn($r) => new self($r), $rows);
    }

    public function metadata(): CourseMetadata {
        return CourseMetadata::forCourse((int)$this->C_ID);
    }

    public function category(): ?CourseCategory {
        $meta = $this->metadata();
        if ($meta->category_id) {
            $cat = CourseCategory::find((int)$meta->category_id);
            return $cat;
        }
        return null;
    }

    public function status(): string {
        return (string)($this->metadata()->status ?? 'published');
    }

    public function level(): string {
        return (string)($this->metadata()->level ?? 'intermediate');
    }

    public function isPublished(): bool {
        return $this->status() === 'published';
    }

    public function isDraft(): bool {
        return $this->status() === 'draft';
    }

    public function publish(): void {
        $meta = $this->metadata();
        $meta->status = 'published';
        $meta->updated_at = date('Y-m-d H:i:s');
        $meta->save();
    }

    public function unpublish(): void {
        $meta = $this->metadata();
        $meta->status = 'draft';
        $meta->updated_at = date('Y-m-d H:i:s');
        $meta->save();
    }

    public function archive(): void {
        $meta = $this->metadata();
        $meta->status = 'archived';
        $meta->updated_at = date('Y-m-d H:i:s');
        $meta->save();
    }
}
