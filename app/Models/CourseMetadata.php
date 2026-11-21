<?php
declare(strict_types=1);

namespace App\Models;

class CourseMetadata extends Model {
    protected string $table = 'course_metadata';
    protected array $fillable = [
        'course_id',
        'category_id',
        'status',
        'level',
        'prerequisites',
        'tags',
        'syllabus',
        'learning_outcomes',
        'instructor_id',
        'featured',
        'created_at',
        'updated_at',
    ];

    public static function forCourse(int $courseId): self {
        $row = self::query()->where('course_id', $courseId)->first();
        if ($row) {
            return new self($row);
        }

        $now = date('Y-m-d H:i:s');
        return self::create([
            'course_id' => $courseId,
            'category_id' => 1,
            'status' => 'published',
            'level' => 'intermediate',
            'prerequisites' => '[]',
            'tags' => '["Software", "Engineering"]',
            'syllabus' => 'Foundations and advanced application architecture.',
            'learning_outcomes' => '["Build production systems", "Master clean design patterns"]',
            'instructor_id' => null,
            'featured' => 0,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
