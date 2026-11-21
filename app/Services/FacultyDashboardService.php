<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;

class FacultyDashboardService {
    public static function getDashboardData(int $lecturerId): array {
        $lecturer = User::find($lecturerId);
        $courses = Course::all();
        $enrollments = Enrollment::all();

        $totalStudents = count($enrollments);
        $totalCourses = count($courses);

        // Pending submissions to grade
        $gradingQueue = [
            [
                'id' => 201,
                'student_name' => 'Kavindu Perera',
                'student_avatar' => 'K',
                'course_name' => $courses[0]->C_NAME ?? 'Fullstack Web Development',
                'assignment_title' => 'Laboratory 3: RESTful API Implementation',
                'submitted_at' => date('Y-m-d H:i', strtotime('-2 hours')),
                'time_ago' => '2 hours ago',
                'status' => 'pending_grading',
            ],
            [
                'id' => 202,
                'student_name' => 'Dinuka Chamara',
                'student_avatar' => 'D',
                'course_name' => $courses[0]->C_NAME ?? 'Fullstack Web Development',
                'assignment_title' => 'Laboratory 3: RESTful API Implementation',
                'submitted_at' => date('Y-m-d H:i', strtotime('-4 hours')),
                'time_ago' => '4 hours ago',
                'status' => 'pending_grading',
            ],
            [
                'id' => 203,
                'student_name' => 'Sanjaya Silva',
                'student_avatar' => 'S',
                'course_name' => $courses[1]->C_NAME ?? 'Database Systems Architecture',
                'assignment_title' => 'Milestone 1: Normalized Database Schema',
                'submitted_at' => date('Y-m-d H:i', strtotime('-1 day')),
                'time_ago' => 'Yesterday',
                'status' => 'pending_grading',
            ],
            [
                'id' => 204,
                'student_name' => 'Nimal Jayawardena',
                'student_avatar' => 'N',
                'course_name' => $courses[1]->C_NAME ?? 'Database Systems Architecture',
                'assignment_title' => 'Milestone 1: Normalized Database Schema',
                'submitted_at' => date('Y-m-d H:i', strtotime('-1 day')),
                'time_ago' => 'Yesterday',
                'status' => 'pending_grading',
            ],
        ];

        // Upcoming lecture classes
        $upcomingClasses = [
            [
                'title' => 'Advanced Query Optimization & Execution Plans',
                'course_code' => 'CS304',
                'room' => 'Computing Lab 4 / Virtual Hall A',
                'scheduled_time' => 'Today at 02:00 PM',
                'attendees' => 48,
                'is_live_today' => true,
            ],
            [
                'title' => 'Modern PHP 8.2 Architecture & Service Containers',
                'course_code' => 'SE201',
                'room' => 'Auditorium 2 • NSBM Campus',
                'scheduled_time' => 'Tomorrow at 10:00 AM',
                'attendees' => 62,
                'is_live_today' => false,
            ],
        ];

        // Grade distribution breakdown
        $gradeDistribution = [
            ['grade' => 'A (90-100%)', 'count' => 42, 'percentage' => 38, 'color' => 'var(--success-text)'],
            ['grade' => 'B (80-89%)', 'count' => 46, 'percentage' => 42, 'color' => 'var(--primary-text)'],
            ['grade' => 'C (70-79%)', 'count' => 17, 'percentage' => 15, 'color' => 'var(--warning-text)'],
            ['grade' => 'D/F (<70%)', 'count' => 6, 'percentage' => 5, 'color' => 'var(--danger-text)'],
        ];

        return [
            'lecturer' => $lecturer,
            'lecturerName' => $lecturer ? $lecturer->Full_Name : 'Faculty Instructor',
            'metrics' => [
                'courses_managed' => $totalCourses,
                'total_enrolled_students' => $totalStudents,
                'pending_grading_count' => count($gradingQueue),
                'average_score' => '84.6%',
            ],
            'courses' => $courses,
            'gradingQueue' => $gradingQueue,
            'upcomingClasses' => $upcomingClasses,
            'gradeDistribution' => $gradeDistribution,
        ];
    }
}
