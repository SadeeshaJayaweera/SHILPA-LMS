<?php
declare(strict_types=1);

namespace App\Services;

use App\Database\Connection;
use App\Models\Course;
use App\Models\CourseBookmark;
use App\Models\CourseCategory;
use App\Models\Enrollment;
use App\Models\LearningPathway;
use PDO;

class RecommendationService {
    /**
     * Get personalized course recommendations for a user.
     * Blends collaborative filtering, category-based affinity, bookmarks, and trending courses.
     *
     * @return array<array{course: Course, reason: string, score: float, is_bookmarked: bool}>
     */
    public static function getRecommendedCoursesForUser(int $userId, int $limit = 6): array {
        $pdo = Connection::get();
        self::seedDefaultPathwaysIfEmpty();

        // 1. Identify courses the user is already actively enrolled in
        $enrolledCourseIds = [];
        if ($userId > 0) {
            $stmt = $pdo->prepare("SELECT course_id FROM enrollments WHERE user_id = :uid AND status = 'active'");
            $stmt->execute([':uid' => $userId]);
            $enrolledCourseIds = array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
        }

        // 2. Identify user's bookmarked courses
        $bookmarkedCourseIds = [];
        if ($userId > 0) {
            $stmt = $pdo->prepare("SELECT course_id FROM course_bookmarks WHERE user_id = :uid");
            $stmt->execute([':uid' => $userId]);
            $bookmarkedCourseIds = array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
        }

        $recommendations = []; // course_id => ['course' => Course, 'reason' => string, 'score' => float]

        // 3. Collaborative Filtering (Peer Cohort Recommendations)
        if ($userId > 0 && !empty($enrolledCourseIds)) {
            $inPlaceholders = implode(',', array_fill(0, count($enrolledCourseIds), '?'));
            $collabSql = "
                SELECT e2.course_id, COUNT(DISTINCT e2.user_id) as peer_count
                FROM enrollments e1
                JOIN enrollments e2 ON e1.user_id != e2.user_id AND e1.course_id != e2.course_id
                WHERE e1.course_id IN ($inPlaceholders)
                  AND e1.user_id = ?
                  AND e2.status = 'active'
                  AND e2.course_id NOT IN ($inPlaceholders)
                GROUP BY e2.course_id
                ORDER BY peer_count DESC
                LIMIT 8
            ";
            $collabParams = array_merge($enrolledCourseIds, [$userId], $enrolledCourseIds);
            try {
                $stmt = $pdo->prepare($collabSql);
                $stmt->execute($collabParams);
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($rows as $row) {
                    $cid = (int)$row['course_id'];
                    $c = Course::find($cid);
                    if ($c) {
                        $recommendations[$cid] = [
                            'course' => $c,
                            'reason' => 'Popular among students taking your courses',
                            'score' => 90.0 + min((float)$row['peer_count'] * 2.0, 9.0),
                            'is_bookmarked' => in_array($cid, $bookmarkedCourseIds, true),
                        ];
                    }
                }
            } catch (\Throwable $e) {
                // Fallback gracefully on query variance
            }
        }

        // 4. Content / Category Affinity Recommendations
        if ($userId > 0 && !empty($enrolledCourseIds)) {
            try {
                $catSql = "
                    SELECT DISTINCT cm.category_id, cat.name as category_name
                    FROM course_metadata cm
                    LEFT JOIN course_categories cat ON cm.category_id = cat.id
                    WHERE cm.course_id IN (" . implode(',', $enrolledCourseIds) . ")
                      AND cm.category_id IS NOT NULL AND cm.category_id > 0
                ";
                $catStmt = $pdo->query($catSql);
                $categories = $catStmt ? $catStmt->fetchAll(PDO::FETCH_ASSOC) : [];

                foreach ($categories as $cat) {
                    $catId = (int)$cat['category_id'];
                    $catName = $cat['category_name'] ?? 'your field of study';

                    $matchSql = "
                        SELECT c.C_ID
                        FROM course c
                        JOIN course_metadata cm ON c.C_ID = cm.course_id
                        WHERE cm.category_id = :catId
                    ";
                    if (!empty($enrolledCourseIds)) {
                        $matchSql .= " AND c.C_ID NOT IN (" . implode(',', $enrolledCourseIds) . ")";
                    }
                    $matchSql .= " LIMIT 4";
                    $mStmt = $pdo->prepare($matchSql);
                    $mStmt->execute([':catId' => $catId]);
                    $matches = $mStmt->fetchAll(PDO::FETCH_COLUMN);

                    foreach ($matches as $cid) {
                        $cid = (int)$cid;
                        if (!isset($recommendations[$cid])) {
                            $c = Course::find($cid);
                            if ($c) {
                                $recommendations[$cid] = [
                                    'course' => $c,
                                    'reason' => "Recommended based on your interest in {$catName}",
                                    'score' => 85.0,
                                    'is_bookmarked' => in_array($cid, $bookmarkedCourseIds, true),
                                ];
                            }
                        }
                    }
                }
            } catch (\Throwable $e) {
                // Fallback gracefully
            }
        }

        // 5. User Bookmarks (Prioritize un-enrolled saved courses)
        foreach ($bookmarkedCourseIds as $bCid) {
            if (!in_array($bCid, $enrolledCourseIds, true) && !isset($recommendations[$bCid])) {
                $c = Course::find($bCid);
                if ($c) {
                    $recommendations[$bCid] = [
                        'course' => $c,
                        'reason' => 'Saved in your bookmarks wishlist',
                        'score' => 95.0,
                        'is_bookmarked' => true,
                    ];
                }
            }
        }

        // 6. Trending / Top Courses Fallback (Fill remaining slots up to limit)
        if (count($recommendations) < $limit) {
            try {
                $excludeIds = array_unique(array_merge($enrolledCourseIds, array_keys($recommendations)));
                $trendSql = "
                    SELECT c.C_ID, COUNT(e.id) as enroll_count
                    FROM course c
                    LEFT JOIN enrollments e ON c.C_ID = e.course_id
                ";
                if (!empty($excludeIds)) {
                    $trendSql .= " WHERE c.C_ID NOT IN (" . implode(',', $excludeIds) . ")";
                }
                $trendSql .= " GROUP BY c.C_ID ORDER BY c.TOP_COURSE DESC, enroll_count DESC, c.C_ID DESC LIMIT " . ($limit - count($recommendations));
                $tStmt = $pdo->query($trendSql);
                $trendRows = $tStmt ? $tStmt->fetchAll(PDO::FETCH_ASSOC) : [];

                foreach ($trendRows as $tr) {
                    $cid = (int)$tr['C_ID'];
                    $c = Course::find($cid);
                    if ($c) {
                        $recommendations[$cid] = [
                            'course' => $c,
                            'reason' => 'Trending & Top Rated on SHILPA LMS',
                            'score' => 75.0 + min((float)$tr['enroll_count'], 10.0),
                            'is_bookmarked' => in_array($cid, $bookmarkedCourseIds, true),
                        ];
                    }
                }
            } catch (\Throwable $e) {
                // Fallback
            }
        }

        // Sort by score descending and slice to limit
        uasort($recommendations, fn($a, $b) => $b['score'] <=> $a['score']);
        return array_values(array_slice($recommendations, 0, $limit));
    }

    /**
     * Toggle bookmark state for a course and user.
     */
    public static function toggleBookmark(int $userId, int $courseId): bool {
        if ($userId <= 0 || $courseId <= 0) {
            return false;
        }

        $existing = CourseBookmark::query()
            ->where('user_id', $userId)
            ->where('course_id', $courseId)
            ->first();

        if ($existing) {
            $bm = new CourseBookmark($existing);
            $bm->delete();
            return false; // Removed
        }

        CourseBookmark::create([
            'user_id' => $userId,
            'course_id' => $courseId,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return true; // Added
    }

    /**
     * Check if a course is bookmarked by a user.
     */
    public static function isBookmarked(int $userId, int $courseId): bool {
        if ($userId <= 0 || $courseId <= 0) {
            return false;
        }
        return CourseBookmark::hasBookmarked($userId, $courseId);
    }

    /**
     * Get all bookmarked courses for a user.
     *
     * @return array<array{bookmark: CourseBookmark, course: Course}>
     */
    public static function getUserBookmarks(int $userId): array {
        if ($userId <= 0) {
            return [];
        }

        $bookmarks = CourseBookmark::forUser($userId);
        $results = [];

        foreach ($bookmarks as $bm) {
            $course = $bm->course();
            if ($course) {
                $results[] = [
                    'bookmark' => $bm,
                    'course' => $course,
                ];
            }
        }

        return $results;
    }

    /**
     * Get all published learning pathways.
     *
     * @return array<LearningPathway>
     */
    public static function getPathways(): array {
        self::seedDefaultPathwaysIfEmpty();
        return LearningPathway::published();
    }

    /**
     * Find a pathway by slug.
     */
    public static function getPathway(string $slug): ?LearningPathway {
        self::seedDefaultPathwaysIfEmpty();
        return LearningPathway::findBySlug($slug);
    }

    /**
     * Seed default curated pathways if none exist.
     */
    public static function seedDefaultPathwaysIfEmpty(): void {
        try {
            $count = LearningPathway::count();
            if ($count > 0) {
                return;
            }

            // Gather all available course IDs
            $courses = Course::query()->limit(10)->get();
            $allIds = array_map(fn($c) => (int)$c['C_ID'], $courses);

            if (empty($allIds)) {
                $allIds = [1, 2, 3];
            }

            $pathwaysData = [
                [
                    'title' => 'Full-Stack Modern Web Engineering',
                    'slug' => 'full-stack-web-architect',
                    'description' => 'A comprehensive, industry-aligned pathway mastering frontend engineering, robust backend architectures, RESTful APIs, and secure cloud deployments.',
                    'target_role' => 'Full-Stack Software Engineer',
                    'course_ids' => json_encode(array_slice($allIds, 0, 3)),
                    'icon' => 'layers',
                    'is_published' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'title' => 'Data Science & Artificial Intelligence',
                    'slug' => 'data-science-ai-specialist',
                    'description' => 'Master Python data pipelines, statistical modeling, machine learning algorithms, and real-world LLM & neural network deployment.',
                    'target_role' => 'AI & Machine Learning Specialist',
                    'course_ids' => json_encode(array_slice($allIds, 1, 3)),
                    'icon' => 'cpu',
                    'is_published' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'title' => 'Cloud Architecture & DevOps Systems',
                    'slug' => 'cloud-devops-engineer',
                    'description' => 'Design resilient cloud native infrastructures, automate continuous deployment with GitHub Actions, and master container orchestration.',
                    'target_role' => 'Cloud & DevOps Architect',
                    'course_ids' => json_encode(array_slice($allIds, 0, 2)),
                    'icon' => 'cloud',
                    'is_published' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
            ];

            foreach ($pathwaysData as $data) {
                LearningPathway::create($data);
            }
        } catch (\Throwable $e) {
            // Silently continue if table does not exist yet before migration
        }
    }
}
