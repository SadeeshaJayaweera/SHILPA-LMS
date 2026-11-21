<?php
declare(strict_types=1);

namespace App\Services;

use App\Database\Connection;
use PDO;

class SearchService {
    /**
     * Search across all LMS entities (courses, lessons, assignments, discussions, pathways, commands)
     *
     * @return array{
     *   query: string,
     *   total: int,
     *   courses: array,
     *   lessons: array,
     *   assignments: array,
     *   discussions: array,
     *   pathways: array,
     *   commands: array
     * }
     */
    public static function search(string $query, ?string $type = null, int $limit = 20, ?string $userRole = null): array {
        $trimmed = trim($query);
        $results = [
            'query' => $trimmed,
            'total' => 0,
            'courses' => [],
            'lessons' => [],
            'assignments' => [],
            'discussions' => [],
            'pathways' => [],
            'commands' => [],
        ];

        if ($trimmed === '') {
            $results['commands'] = array_slice(self::getCommands($userRole), 0, 8);
            $results['total'] = count($results['commands']);
            return $results;
        }

        $pdo = Connection::get();
        $searchTerm = '%' . strtolower($trimmed) . '%';

        // 1. Search Courses
        if ($type === null || $type === 'courses') {
            try {
                $stmt = $pdo->prepare("
                    SELECT C_ID as id, C_NAME as title, C_CARD_DESCRIPTION as description, 
                           C_DONE_BY as instructor, C_CARD_IMAGE as image
                    FROM course
                    WHERE LOWER(C_NAME) LIKE :term OR LOWER(C_DESCRIPTION) LIKE :term OR LOWER(C_CARD_DESCRIPTION) LIKE :term
                    ORDER BY C_ID DESC
                    LIMIT :lim
                ");
                $stmt->bindValue(':term', $searchTerm, PDO::PARAM_STR);
                $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
                $stmt->execute();
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($rows as $r) {
                    $results['courses'][] = [
                        'id' => (int)$r['id'],
                        'title' => (string)$r['title'],
                        'snippet' => self::generateSnippet((string)$r['description'], $trimmed),
                        'url' => '/courses/' . $r['id'],
                        'instructor' => (string)($r['instructor'] ?? 'Instructor'),
                        'image' => (string)($r['image'] ?? '/img/courses/default.jpg'),
                        'type' => 'course',
                    ];
                }
            } catch (\Throwable $e) {}
        }

        // 2. Search Lessons
        if ($type === null || $type === 'lessons') {
            try {
                $stmt = $pdo->prepare("
                    SELECT l.id, l.title, l.content_body, l.module_id, m.course_id, c.C_NAME as course_title
                    FROM lessons l
                    JOIN modules m ON l.module_id = m.id
                    LEFT JOIN course c ON m.course_id = c.C_ID
                    WHERE LOWER(l.title) LIKE :term OR LOWER(l.content_body) LIKE :term
                    ORDER BY l.id DESC
                    LIMIT :lim
                ");
                $stmt->bindValue(':term', $searchTerm, PDO::PARAM_STR);
                $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
                $stmt->execute();
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($rows as $r) {
                    $results['lessons'][] = [
                        'id' => (int)$r['id'],
                        'title' => (string)$r['title'],
                        'snippet' => self::generateSnippet((string)$r['content_body'], $trimmed),
                        'url' => '/courses/' . $r['course_id'] . '/learn/' . $r['id'],
                        'course_id' => (int)$r['course_id'],
                        'course_title' => (string)($r['course_title'] ?? 'Course'),
                        'type' => 'lesson',
                    ];
                }
            } catch (\Throwable $e) {}
        }

        // 3. Search Assignments
        if ($type === null || $type === 'assignments') {
            try {
                $stmt = $pdo->prepare("
                    SELECT a.id, a.course_id, a.title, a.description, a.due_at, c.C_NAME as course_title
                    FROM assignments a
                    LEFT JOIN course c ON a.course_id = c.C_ID
                    WHERE LOWER(a.title) LIKE :term OR LOWER(a.description) LIKE :term
                    ORDER BY a.id DESC
                    LIMIT :lim
                ");
                $stmt->bindValue(':term', $searchTerm, PDO::PARAM_STR);
                $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
                $stmt->execute();
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($rows as $r) {
                    $results['assignments'][] = [
                        'id' => (int)$r['id'],
                        'title' => (string)$r['title'],
                        'snippet' => self::generateSnippet((string)$r['description'], $trimmed),
                        'url' => '/courses/' . $r['course_id'] . '/assignments/' . $r['id'],
                        'course_title' => (string)($r['course_title'] ?? 'Course'),
                        'due_at' => (string)($r['due_at'] ?? ''),
                        'type' => 'assignment',
                    ];
                }
            } catch (\Throwable $e) {}
        }

        // 4. Search Discussions
        if ($type === null || $type === 'discussions') {
            try {
                $stmt = $pdo->prepare("
                    SELECT d.id, d.course_id, d.title, d.body, d.replies_count, c.C_NAME as course_title
                    FROM discussion_threads d
                    LEFT JOIN course c ON d.course_id = c.C_ID
                    WHERE LOWER(d.title) LIKE :term OR LOWER(d.body) LIKE :term
                    ORDER BY d.id DESC
                    LIMIT :lim
                ");
                $stmt->bindValue(':term', $searchTerm, PDO::PARAM_STR);
                $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
                $stmt->execute();
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($rows as $r) {
                    $results['discussions'][] = [
                        'id' => (int)$r['id'],
                        'title' => (string)$r['title'],
                        'snippet' => self::generateSnippet((string)$r['body'], $trimmed),
                        'url' => '/courses/' . $r['course_id'] . '/discussions/' . $r['id'],
                        'course_title' => (string)($r['course_title'] ?? 'Course'),
                        'replies_count' => (int)$r['replies_count'],
                        'type' => 'discussion',
                    ];
                }
            } catch (\Throwable $e) {}
        }

        // 5. Search Pathways
        if ($type === null || $type === 'pathways') {
            try {
                $stmt = $pdo->prepare("
                    SELECT id, title, slug, description, target_role, icon
                    FROM learning_pathways
                    WHERE LOWER(title) LIKE :term OR LOWER(description) LIKE :term OR LOWER(target_role) LIKE :term
                    LIMIT :lim
                ");
                $stmt->bindValue(':term', $searchTerm, PDO::PARAM_STR);
                $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
                $stmt->execute();
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($rows as $r) {
                    $results['pathways'][] = [
                        'id' => (int)$r['id'],
                        'title' => (string)$r['title'],
                        'snippet' => self::generateSnippet((string)$r['description'], $trimmed),
                        'url' => '/pathways/' . $r['slug'],
                        'target_role' => (string)$r['target_role'],
                        'icon' => (string)($r['icon'] ?? 'compass'),
                        'type' => 'pathway',
                    ];
                }
            } catch (\Throwable $e) {}
        }

        // 6. Match Commands
        if ($type === null || $type === 'commands') {
            $allCommands = self::getCommands($userRole);
            foreach ($allCommands as $cmd) {
                if (
                    str_contains(strtolower($cmd['title']), strtolower($trimmed)) ||
                    str_contains(strtolower($cmd['description'] ?? ''), strtolower($trimmed)) ||
                    str_contains(strtolower($cmd['keywords'] ?? ''), strtolower($trimmed))
                ) {
                    $results['commands'][] = $cmd;
                }
            }
        }

        $results['total'] = count($results['courses']) +
                            count($results['lessons']) +
                            count($results['assignments']) +
                            count($results['discussions']) +
                            count($results['pathways']) +
                            count($results['commands']);

        return $results;
    }

    /**
     * Fast autocomplete quick search for Cmd+K command palette
     *
     * @return array<array{title: string, subtitle: string, url: string, icon: string, category: string}>
     */
    public static function quickSearch(string $query, int $limit = 8, ?string $userRole = null): array {
        $search = self::search($query, null, 4, $userRole);
        $quick = [];

        // Add top matching commands first
        foreach ($search['commands'] as $cmd) {
            $quick[] = [
                'title' => $cmd['title'],
                'subtitle' => $cmd['description'] ?? 'Command action',
                'url' => $cmd['url'],
                'icon' => $cmd['icon'] ?? 'command',
                'category' => 'Commands & Actions',
            ];
            if (count($quick) >= $limit) return $quick;
        }

        // Add top matching courses
        foreach ($search['courses'] as $c) {
            $quick[] = [
                'title' => $c['title'],
                'subtitle' => 'Course by ' . $c['instructor'],
                'url' => $c['url'],
                'icon' => 'book-open',
                'category' => 'Courses',
            ];
            if (count($quick) >= $limit) return $quick;
        }

        // Add top matching lessons
        foreach ($search['lessons'] as $l) {
            $quick[] = [
                'title' => $l['title'],
                'subtitle' => 'Lesson in ' . $l['course_title'],
                'url' => $l['url'],
                'icon' => 'play-circle',
                'category' => 'Lessons',
            ];
            if (count($quick) >= $limit) return $quick;
        }

        // Add top matching pathways
        foreach ($search['pathways'] as $pw) {
            $quick[] = [
                'title' => $pw['title'],
                'subtitle' => 'Pathway for ' . $pw['target_role'],
                'url' => $pw['url'],
                'icon' => 'compass',
                'category' => 'Career Tracks',
            ];
            if (count($quick) >= $limit) return $quick;
        }

        // Add assignments
        foreach ($search['assignments'] as $a) {
            $quick[] = [
                'title' => $a['title'],
                'subtitle' => 'Assignment in ' . $a['course_title'],
                'url' => $a['url'],
                'icon' => 'clipboard',
                'category' => 'Assignments',
            ];
            if (count($quick) >= $limit) return $quick;
        }

        return $quick;
    }

    /**
     * Get platform commands and quick navigation actions
     */
    public static function getCommands(?string $userRole = null): array {
        $commands = [
            [
                'title' => 'Go to Dashboard',
                'description' => 'Navigate to your primary learning workspace',
                'url' => '/dashboard',
                'icon' => 'home',
                'keywords' => 'home main dashboard workspace',
                'type' => 'command',
            ],
            [
                'title' => 'Browse Course Catalog',
                'description' => 'Explore all available courses and subjects',
                'url' => '/courses',
                'icon' => 'book',
                'keywords' => 'catalog browse courses study find',
                'type' => 'command',
            ],
            [
                'title' => 'Personalized Recommendations',
                'description' => 'Intelligent course recommendations tailored to your profile',
                'url' => '/courses/recommended',
                'icon' => 'sparkles',
                'keywords' => 'recommend discovery suggested ai intelligent',
                'type' => 'command',
            ],
            [
                'title' => 'Curated Career Pathways',
                'description' => 'Structured milestones to achieve target industry roles',
                'url' => '/pathways',
                'icon' => 'compass',
                'keywords' => 'pathways track career roadmaps engineer',
                'type' => 'command',
            ],
            [
                'title' => 'My Saved Bookmarks',
                'description' => 'View your bookmarked courses wishlist',
                'url' => '/my-bookmarks',
                'icon' => 'bookmark',
                'keywords' => 'saved bookmarks favorites wishlist',
                'type' => 'command',
            ],
            [
                'title' => 'AI Learning Assistant (Tutor)',
                'description' => 'Interactive AI chat tutor for course conceptual doubts',
                'url' => '/ai-tutor',
                'icon' => 'cpu',
                'keywords' => 'ai tutor assistant chat robot question practice',
                'type' => 'command',
            ],
            [
                'title' => 'Personal Learning Velocity & Analytics',
                'description' => 'Study time, weekly consistency, and mastery completion',
                'url' => '/analytics',
                'icon' => 'bar-chart-2',
                'keywords' => 'analytics metrics performance stats velocity time',
                'type' => 'command',
            ],
            [
                'title' => 'Academic Grades & Transcripts',
                'description' => 'Review GPA, course results, and assignment marks',
                'url' => '/student/grades',
                'icon' => 'award',
                'keywords' => 'grades transcript gpa results score marks',
                'type' => 'command',
            ],
            [
                'title' => 'Notification Center',
                'description' => 'System alerts, grade releases, and announcements',
                'url' => '/notifications',
                'icon' => 'bell',
                'keywords' => 'notifications alerts inbox updates',
                'type' => 'command',
            ],
            [
                'title' => 'Direct Messages & Chat',
                'description' => 'Private conversations with instructors and peer learners',
                'url' => '/messages',
                'icon' => 'mail',
                'keywords' => 'messages chat direct dm inbox peer',
                'type' => 'command',
            ],
            [
                'title' => 'Discussion Forums',
                'description' => 'Course community questions and answers',
                'url' => '/discussions',
                'icon' => 'message-square',
                'keywords' => 'discussions forum q&a community questions',
                'type' => 'command',
            ],
            [
                'title' => 'My Account Profile & Security',
                'description' => 'Manage name, password, two-factor auth, and sessions',
                'url' => '/profile',
                'icon' => 'user',
                'keywords' => 'profile account user password security 2fa',
                'type' => 'command',
            ],
        ];

        // Faculty & Lecturer Commands
        if ($userRole === 'lecturer' || $userRole === 'faculty' || $userRole === 'admin') {
            $commands[] = [
                'title' => 'Faculty Teaching Dashboard',
                'description' => 'Manage courses, student submissions, and grading workloads',
                'url' => '/lecturer/dashboard',
                'icon' => 'layout',
                'keywords' => 'faculty teaching instructor courses grading',
                'type' => 'command',
            ];
            $commands[] = [
                'title' => 'Create New Course',
                'description' => 'Author a new course curriculum and syllabus',
                'url' => '/faculty/courses/create',
                'icon' => 'plus-circle',
                'keywords' => 'create course add author new curriculum',
                'type' => 'command',
            ];
            $commands[] = [
                'title' => 'Course Management Directory',
                'description' => 'View all authored courses and publish states',
                'url' => '/faculty/courses',
                'icon' => 'book-open',
                'keywords' => 'manage courses faculty list directory',
                'type' => 'command',
            ];
        }

        // Admin Commands
        if ($userRole === 'admin') {
            $commands[] = [
                'title' => 'Administrator Command Console',
                'description' => 'Platform overview, server health, and system operations',
                'url' => '/admin/dashboard',
                'icon' => 'shield',
                'keywords' => 'admin system operations platform server',
                'type' => 'command',
            ];
            $commands[] = [
                'title' => 'User Directory Management',
                'description' => 'Search and manage all learner, lecturer, and admin accounts',
                'url' => '/admin/users',
                'icon' => 'users',
                'keywords' => 'admin users directory accounts roles ban',
                'type' => 'command',
            ];
            $commands[] = [
                'title' => 'Institutional Analytics & Telemetry',
                'description' => 'Enterprise enrollment stats, course pass rates, and usage',
                'url' => '/admin/analytics',
                'icon' => 'trending-up',
                'keywords' => 'admin institutional analytics pass rate metrics',
                'type' => 'command',
            ];
            $commands[] = [
                'title' => 'Security Audit Log Viewer',
                'description' => 'Inspect cryptographic hash-chained audit trails and events',
                'url' => '/admin/security-audit',
                'icon' => 'lock',
                'keywords' => 'admin security audit logs tamper chain cryptographic',
                'type' => 'command',
            ];
        }

        return $commands;
    }

    /**
     * Helper to generate a contextual snippet around matching query terms
     */
    protected static function generateSnippet(string $text, string $query): string {
        $clean = strip_tags($text);
        if ($clean === '') {
            return '';
        }

        if ($query === '') {
            return mb_substr($clean, 0, 140) . (mb_strlen($clean) > 140 ? '...' : '');
        }

        $pos = mb_stripos($clean, $query);
        if ($pos === false) {
            return mb_substr($clean, 0, 140) . (mb_strlen($clean) > 140 ? '...' : '');
        }

        $start = max(0, $pos - 50);
        $length = mb_strlen($query) + 100;
        $snippet = mb_substr($clean, $start, $length);

        if ($start > 0) {
            $snippet = '...' . $snippet;
        }
        if ($start + $length < mb_strlen($clean)) {
            $snippet = $snippet . '...';
        }

        return $snippet;
    }
}
