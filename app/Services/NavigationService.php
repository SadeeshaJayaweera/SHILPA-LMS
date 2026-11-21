<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\User;

class NavigationService {
    /**
     * Get structured navigation sections for the given or current user.
     */
    public static function getNavigationForUser(?User $user = null): array {
        $role = $user ? $user->user_type : (string)session()->get('user_role', 'student');

        if (in_array($role, ['a', 'm', 'admin', 'administrator', 'superadmin'], true)) {
            return self::getAdminNavigation();
        }

        if (in_array($role, ['l', 'lecturer', 'instructor', 'teacher'], true)) {
            return self::getLecturerNavigation();
        }

        return self::getStudentNavigation();
    }

    private static function getStudentNavigation(): array {
        return [
            'Learning Hub' => [
                [
                    'label' => 'Student Overview',
                    'url' => '/workspace',
                    'icon' => 'home',
                    'badge' => null,
                ],
                [
                    'label' => 'Course Catalog',
                    'url' => '/courses',
                    'icon' => 'book',
                    'badge' => 'Browse',
                ],
                [
                    'label' => 'Enrolled Courses',
                    'url' => '/my-courses',
                    'icon' => 'graduation-cap',
                    'badge' => 'Active',
                ],
            ],
            'Assessments & Progress' => [
                [
                    'label' => 'Assignments',
                    'url' => '/assignments',
                    'icon' => 'clipboard',
                    'badge' => '2 Due',
                ],
                [
                    'label' => 'Quizzes & Tests',
                    'url' => '/assessments',
                    'icon' => 'help-circle',
                    'badge' => null,
                ],
                [
                    'label' => 'Grades & Feedback',
                    'url' => '/grades',
                    'icon' => 'award',
                    'badge' => null,
                ],
                [
                    'label' => 'Academic Calendar',
                    'url' => '/calendar',
                    'icon' => 'calendar',
                    'badge' => null,
                ],
            ],
            'Collaboration' => [
                [
                    'label' => 'Discussion Forum',
                    'url' => '/discussions',
                    'icon' => 'message-square',
                    'badge' => null,
                ],
                [
                    'label' => 'Resources Library',
                    'url' => '/resources',
                    'icon' => 'folder',
                    'badge' => null,
                ],
            ],
        ];
    }

    private static function getLecturerNavigation(): array {
        return [
            'Faculty Workspace' => [
                [
                    'label' => 'Lecturer Overview',
                    'url' => '/workspace',
                    'icon' => 'home',
                    'badge' => null,
                ],
                [
                    'label' => 'Course Management',
                    'url' => '/courses/manage',
                    'icon' => 'book',
                    'badge' => null,
                ],
                [
                    'label' => 'Module Builder',
                    'url' => '/modules/builder',
                    'icon' => 'layers',
                    'badge' => 'New',
                ],
            ],
            'Instruction & Grading' => [
                [
                    'label' => 'Student Submissions',
                    'url' => '/grading',
                    'icon' => 'clipboard',
                    'badge' => '5 Pending',
                ],
                [
                    'label' => 'Assessment Bank',
                    'url' => '/assessments/manage',
                    'icon' => 'help-circle',
                    'badge' => null,
                ],
                [
                    'label' => 'Class Analytics',
                    'url' => '/analytics',
                    'icon' => 'bar-chart',
                    'badge' => null,
                ],
            ],
            'Communication' => [
                [
                    'label' => 'Announcements',
                    'url' => '/announcements',
                    'icon' => 'bell',
                    'badge' => null,
                ],
                [
                    'label' => 'Student Inquiries',
                    'url' => '/discussions',
                    'icon' => 'message-square',
                    'badge' => null,
                ],
            ],
        ];
    }

    private static function getAdminNavigation(): array {
        return [
            'System Administration' => [
                [
                    'label' => 'Admin Console',
                    'url' => '/admin.php',
                    'icon' => 'home',
                    'badge' => null,
                ],
                [
                    'label' => 'User Management',
                    'url' => '/admin/users',
                    'icon' => 'user',
                    'badge' => 'Directory',
                ],
                [
                    'label' => 'Course Governance',
                    'url' => '/admin/courses',
                    'icon' => 'book',
                    'badge' => null,
                ],
            ],
            'Platform & Health' => [
                [
                    'label' => 'System Health',
                    'url' => '/health',
                    'icon' => 'shield',
                    'badge' => 'Live',
                ],
                [
                    'label' => 'Design System Tokens',
                    'url' => '/design-system',
                    'icon' => 'layers',
                    'badge' => null,
                ],
                [
                    'label' => 'Platform Settings',
                    'url' => '/admin/settings',
                    'icon' => 'settings',
                    'badge' => null,
                ],
            ],
        ];
    }

    /**
     * Generate breadcrumb trails dynamically for any URL.
     */
    public static function getBreadcrumbs(string $uri, array $custom = []): array {
        if (!empty($custom)) {
            return $custom;
        }

        $crumbs = [
            ['label' => 'Home', 'url' => '/'],
        ];

        $path = parse_url($uri, PHP_URL_PATH) ?? '/';
        $segments = array_filter(explode('/', trim($path, '/')));

        $built = '';
        foreach ($segments as $segment) {
            $built .= '/' . $segment;
            $crumbs[] = [
                'label' => ucwords(str_replace(['-', '_', '.php'], ' ', $segment)),
                'url' => $built,
            ];
        }

        return $crumbs;
    }

    /**
     * Get real-time notifications for the header bell dropdown.
     */
    public static function getQuickNotifications(): array {
        return [
            [
                'id' => 1,
                'title' => 'Upcoming Assignment Deadline',
                'description' => 'Fullstack Web App Milestone 1 submission is due tomorrow at 11:59 PM.',
                'time' => '10m ago',
                'type' => 'warning',
                'unread' => true,
            ],
            [
                'id' => 2,
                'title' => 'New Module Published',
                'description' => 'Dr. Jayaweera published "Advanced Relational Data Architecture" in Database Systems.',
                'time' => '2h ago',
                'type' => 'info',
                'unread' => true,
            ],
            [
                'id' => 3,
                'title' => 'Assessment Graded',
                'description' => 'Your score for "PHP & Design Systems Quiz" has been published: 96/100 (A+).',
                'time' => '1d ago',
                'type' => 'success',
                'unread' => false,
            ],
        ];
    }
}
