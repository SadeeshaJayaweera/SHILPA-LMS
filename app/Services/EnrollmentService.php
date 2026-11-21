<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Course;
use App\Models\CourseWaitlist;
use App\Models\Enrollment;
use App\Models\User;
use App\Security\SecurityAuditLog;

class EnrollmentService {
    public static function enroll(int $userId, int $courseId, string $role = 'student'): array {
        $course = Course::find($courseId);
        if (!$course) {
            return ['success' => false, 'error' => 'Course module not found.'];
        }

        // Must be published unless admin/instructor
        if (!$course->isPublished()) {
            return ['success' => false, 'error' => 'This course is currently in draft mode and not accepting enrollments.'];
        }

        $user = User::find($userId);
        if (!$user) {
            return ['success' => false, 'error' => 'User account not found.'];
        }

        // Check for existing enrollment
        $existing = Enrollment::query()
            ->where('course_id', $courseId)
            ->where('user_id', $userId)
            ->first();

        if ($existing) {
            if ($existing['status'] === 'active') {
                return ['success' => false, 'error' => 'You are already actively enrolled in this course.'];
            }

            // Reactivate previously cancelled enrollment
            $enrollment = new Enrollment($existing);
            $enrollment->status = 'active';
            $enrollment->enrolled_at = date('Y-m-d H:i:s');
            $enrollment->save();

            SecurityAuditLog::log('course.enroll.reactivated', 'info', $userId, $user->Email, [
                'course_id' => $courseId,
                'course_name' => $course->C_NAME,
            ]);

            return [
                'success' => true,
                'message' => 'Your course enrollment has been reactivated.',
                'enrollment' => $enrollment,
            ];
        }

        $now = date('Y-m-d H:i:s');
        $enrollment = Enrollment::create([
            'course_id' => $courseId,
            'user_id' => $userId,
            'status' => 'active',
            'role' => $role,
            'progress_percent' => 0,
            'enrolled_at' => $now,
        ]);

        SecurityAuditLog::log('course.enroll.success', 'info', $userId, $user->Email, [
            'course_id' => $courseId,
            'course_name' => $course->C_NAME,
            'role' => $role,
        ]);

        return [
            'success' => true,
            'message' => 'Successfully enrolled in course!',
            'enrollment' => $enrollment,
        ];
    }

    public static function drop(int $userId, int $courseId): bool {
        $row = Enrollment::query()
            ->where('course_id', $courseId)
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->first();

        if (!$row) {
            return false;
        }

        $enrollment = new Enrollment($row);
        $enrollment->status = 'cancelled';
        $enrollment->save();

        SecurityAuditLog::log('course.enroll.dropped', 'info', $userId, null, [
            'course_id' => $courseId,
        ]);

        return true;
    }

    public static function getRoster(int $courseId): array {
        $enrollments = Enrollment::rosterForCourse($courseId);
        $roster = [];

        foreach ($enrollments as $enr) {
            $user = User::find((int)$enr->user_id);
            $roster[] = [
                'enrollment' => $enr,
                'user' => $user,
                'name' => $user ? $user->Full_Name : 'Unknown Student',
                'email' => $user ? $user->Email : 'unknown',
                'status' => $enr->status,
                'progress' => (int)$enr->progress_percent,
                'enrolled_at' => $enr->enrolled_at,
            ];
        }

        return $roster;
    }

    public static function updateStatus(int $enrollmentId, string $status): bool {
        $validStatuses = ['active', 'completed', 'cancelled', 'expired'];
        if (!in_array($status, $validStatuses, true)) {
            return false;
        }

        $enr = Enrollment::find($enrollmentId);
        if (!$enr) {
            return false;
        }

        $enr->status = $status;
        if ($status === 'completed') {
            $enr->completed_at = date('Y-m-d H:i:s');
            $enr->progress_percent = 100;
        }
        return $enr->save();
    }
}
