<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseMetadata;

class CourseManagementService {
    public static function getCourses(?int $instructorId = null, array $filters = []): array {
        $query = Course::query();

        if (!empty($filters['search'])) {
            $s = trim((string)$filters['search']);
            $query->where('C_NAME', 'LIKE', "%$s%")
                  ->orWhere('C_DESCRIPTION', 'LIKE', "%$s%");
        }

        $allCourses = array_map(fn($r) => new Course($r), $query->get());

        // Apply metadata filters (status, category)
        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $status = (string)$filters['status'];
            $allCourses = array_filter($allCourses, fn(Course $c) => $c->status() === $status);
        }

        if (!empty($filters['category_id']) && (int)$filters['category_id'] > 0) {
            $catId = (int)$filters['category_id'];
            $allCourses = array_filter($allCourses, fn(Course $c) => (int)$c->metadata()->category_id === $catId);
        }

        return array_values($allCourses);
    }

    public static function createCourse(array $data, ?int $instructorId = null): Course {
        $now = date('Y-m-d H:i:s');
        $course = Course::create([
            'C_NAME' => trim((string)($data['title'] ?? $data['C_NAME'] ?? 'Untitled Course')),
            'C_CARD_DESCRIPTION' => trim((string)($data['short_description'] ?? $data['C_CARD_DESCRIPTION'] ?? '')),
            'C_DESCRIPTION' => trim((string)($data['description'] ?? $data['C_DESCRIPTION'] ?? '')),
            'C_CARD_IMAGE' => trim((string)($data['image_url'] ?? $data['C_CARD_IMAGE'] ?? '/images/courses/default.jpg')),
            'C_DONE_BY' => trim((string)($data['instructor_name'] ?? $data['C_DONE_BY'] ?? 'Faculty Instructor')),
            'C_LINK' => trim((string)($data['course_link'] ?? $data['C_LINK'] ?? '')),
            'TOP_COURSE' => !empty($data['featured']) ? 'Yes' : 'No',
            'C_ADD_DATE' => $now,
        ]);

        $tags = isset($data['tags']) && is_array($data['tags'])
            ? json_encode($data['tags'])
            : (isset($data['tags']) ? json_encode(array_filter(array_map('trim', explode(',', (string)$data['tags'])))) : '[]');

        $prereqs = isset($data['prerequisites']) && is_array($data['prerequisites'])
            ? json_encode($data['prerequisites'])
            : (isset($data['prerequisites']) ? json_encode(array_filter(array_map('trim', explode("\n", (string)$data['prerequisites'])))) : '[]');

        CourseMetadata::create([
            'course_id' => (int)$course->C_ID,
            'category_id' => isset($data['category_id']) ? (int)$data['category_id'] : 1,
            'status' => in_array($data['status'] ?? '', ['draft', 'published', 'archived'], true) ? (string)$data['status'] : 'draft',
            'level' => in_array($data['level'] ?? '', ['beginner', 'intermediate', 'advanced'], true) ? (string)$data['level'] : 'beginner',
            'prerequisites' => $prereqs ?: '[]',
            'tags' => $tags ?: '[]',
            'syllabus' => trim((string)($data['syllabus'] ?? '')),
            'learning_outcomes' => json_encode(array_filter(array_map('trim', explode("\n", (string)($data['learning_outcomes'] ?? ''))))),
            'instructor_id' => $instructorId,
            'featured' => !empty($data['featured']) ? 1 : 0,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return $course;
    }

    public static function updateCourse(int $courseId, array $data): Course {
        $course = Course::find($courseId);
        if (!$course) {
            throw new \RuntimeException("Course not found: $courseId");
        }

        if (isset($data['title']) || isset($data['C_NAME'])) {
            $course->C_NAME = trim((string)($data['title'] ?? $data['C_NAME']));
        }
        if (isset($data['short_description']) || isset($data['C_CARD_DESCRIPTION'])) {
            $course->C_CARD_DESCRIPTION = trim((string)($data['short_description'] ?? $data['C_CARD_DESCRIPTION']));
        }
        if (isset($data['description']) || isset($data['C_DESCRIPTION'])) {
            $course->C_DESCRIPTION = trim((string)($data['description'] ?? $data['C_DESCRIPTION']));
        }
        if (isset($data['instructor_name']) || isset($data['C_DONE_BY'])) {
            $course->C_DONE_BY = trim((string)($data['instructor_name'] ?? $data['C_DONE_BY']));
        }
        if (isset($data['featured'])) {
            $course->TOP_COURSE = !empty($data['featured']) ? 'Yes' : 'No';
        }
        $course->save();

        $meta = $course->metadata();
        if (isset($data['category_id'])) {
            $meta->category_id = (int)$data['category_id'];
        }
        if (isset($data['status']) && in_array($data['status'], ['draft', 'published', 'archived'], true)) {
            $meta->status = (string)$data['status'];
        }
        if (isset($data['level']) && in_array($data['level'], ['beginner', 'intermediate', 'advanced'], true)) {
            $meta->level = (string)$data['level'];
        }
        if (isset($data['tags'])) {
            $meta->tags = is_array($data['tags'])
                ? json_encode($data['tags'])
                : json_encode(array_filter(array_map('trim', explode(',', (string)$data['tags']))));
        }
        if (isset($data['syllabus'])) {
            $meta->syllabus = trim((string)$data['syllabus']);
        }
        if (isset($data['prerequisites'])) {
            $meta->prerequisites = is_array($data['prerequisites'])
                ? json_encode($data['prerequisites'])
                : json_encode(array_filter(array_map('trim', explode("\n", (string)$data['prerequisites']))));
        }
        if (isset($data['featured'])) {
            $meta->featured = !empty($data['featured']) ? 1 : 0;
        }
        $meta->updated_at = date('Y-m-d H:i:s');
        $meta->save();

        return $course;
    }

    public static function deleteCourse(int $courseId): bool {
        $course = Course::find($courseId);
        if (!$course) {
            return false;
        }

        $meta = $course->metadata();
        if ($meta) {
            $meta->delete();
        }

        return $course->delete();
    }

    public static function togglePublish(int $courseId): bool {
        $course = Course::find($courseId);
        if (!$course) {
            return false;
        }

        if ($course->isPublished()) {
            $course->unpublish();
            return false;
        } else {
            $course->publish();
            return true;
        }
    }
}
