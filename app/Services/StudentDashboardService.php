<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\StudentActivity;
use App\Models\StudentDeadline;
use App\Models\User;

class StudentDashboardService {
    public static function getDashboardData(int $userId): array {
        $user = User::find($userId);
        $enrollmentRows = Enrollment::query()->where('user_id', $userId)->get();

        $enrolledCourseIds = array_column($enrollmentRows, 'course_id');
        $enrolledCourses = [];

        if (!empty($enrolledCourseIds)) {
            foreach ($enrollmentRows as $enrollment) {
                $course = Course::find((int)$enrollment['course_id']);
                if ($course) {
                    $enrolledCourses[] = [
                        'course' => $course,
                        'progress_percent' => (int)($enrollment['progress_percent'] ?? 0),
                        'status' => (string)($enrollment['status'] ?? 'active'),
                        'enrolled_at' => (string)($enrollment['enrolled_at'] ?? ''),
                    ];
                }
            }
        }

        // If newly enrolled user has zero courses, fetch top featured courses for recommendation
        $recommendedCourses = [];
        if (empty($enrolledCourses)) {
            $recommendedCourses = Course::query()->limit(4)->get();
        }

        // Fetch deadlines from database or provide curated defaults if none created yet
        $deadlines = StudentDeadline::upcomingForUser($userId, 5);
        if (empty($deadlines) && !empty($enrolledCourses)) {
            $firstCourse = $enrolledCourses[0]['course'];
            $deadlines = [
                [
                    'id' => 101,
                    'title' => 'Milestone 1: Relational Schema & Seeding',
                    'course_name' => $firstCourse->C_NAME,
                    'due_date' => date('Y-m-d 23:59:59', strtotime('+1 day')),
                    'type' => 'assignment',
                    'weight_percent' => 20,
                    'urgency' => 'Due in 24 hours',
                    'urgency_color' => 'danger',
                ],
                [
                    'id' => 102,
                    'title' => 'Midterm Assessment: Core Algorithms',
                    'course_name' => $firstCourse->C_NAME,
                    'due_date' => date('Y-m-d 14:00:00', strtotime('+4 days')),
                    'type' => 'exam',
                    'weight_percent' => 30,
                    'urgency' => 'In 4 days',
                    'urgency_color' => 'warning',
                ],
            ];
        }

        // Fetch recent activities
        $activities = StudentActivity::forUser($userId, 6);
        if (empty($activities)) {
            $activities = [
                [
                    'title' => 'Completed Lesson: Normalization & Indexing',
                    'description' => 'Scored 100% on the inline check in Database Architecture.',
                    'time_ago' => '2 hours ago',
                    'icon' => 'check',
                ],
                [
                    'title' => 'Downloaded Module Handout',
                    'description' => 'Lecture slides for Fullstack Modernization (Week 4).',
                    'time_ago' => 'Yesterday',
                    'icon' => 'book',
                ],
                [
                    'title' => 'Joined Discussion: Best Practices in REST APIs',
                    'description' => 'Replied with solution in Faculty of IT forum.',
                    'time_ago' => '3 days ago',
                    'icon' => 'message-square',
                ],
            ];
        }

        // KPI metrics
        $avgProgress = !empty($enrolledCourses)
            ? (int)round(array_sum(array_column($enrolledCourses, 'progress_percent')) / count($enrolledCourses))
            : 0;

        return [
            'user' => $user,
            'userName' => $user ? $user->Full_Name : (string)session()->get('user_name', 'Student User'),
            'userEmail' => $user ? $user->Email : (string)session()->get('email', 'student@shilpa.lk'),
            'metrics' => [
                'enrolled_courses' => count($enrolledCourses),
                'completion_percent' => $avgProgress,
                'study_hours_week' => 16.5,
                'gpa' => '3.86 / 4.0',
                'active_deadlines_count' => count($deadlines),
            ],
            'enrolledCourses' => $enrolledCourses,
            'recommendedCourses' => $recommendedCourses,
            'deadlines' => $deadlines,
            'activities' => $activities,
            'weeklyActivity' => [
                ['day' => 'Mon', 'hours' => 2.5],
                ['day' => 'Tue', 'hours' => 3.0],
                ['day' => 'Wed', 'hours' => 1.5],
                ['day' => 'Thu', 'hours' => 4.2],
                ['day' => 'Fri', 'hours' => 2.0],
                ['day' => 'Sat', 'hours' => 3.8],
                ['day' => 'Sun', 'hours' => 1.5],
            ],
            'subjectMastery' => [
                ['subject' => 'Fullstack Engineering', 'mastery' => 88],
                ['subject' => 'Database Architecture', 'mastery' => 76],
                ['subject' => 'Algorithms & Data Structures', 'mastery' => 92],
                ['subject' => 'Cloud & DevOps', 'mastery' => 68],
            ],
            'recommendations' => [
                [
                    'title' => 'Deepen Index Optimization',
                    'reason' => 'Based on your recent database queries lesson',
                    'action_label' => 'Start Interactive Lab',
                    'url' => '/courses',
                ],
                [
                    'title' => 'Prepare for Midterm Quiz',
                    'reason' => 'Midterm assessment is scheduled in 4 days',
                    'action_label' => 'Take Practice Quiz',
                    'url' => '/assessments',
                ],
            ],
        ];
    }
}
