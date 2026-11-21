<?php
declare(strict_types=1);

namespace App\Services;

use App\Database\Connection;
use App\Models\Course;
use App\Models\CourseGrade;
use App\Models\Enrollment;
use App\Models\LearningEvent;
use App\Models\Lesson;
use App\Models\LessonCompletion;
use App\Models\User;
use RuntimeException;

class AnalyticsService {
    public static function recordEvent(
        int $userId,
        ?int $courseId,
        string $eventType,
        int $durationSeconds = 0,
        array $metadata = []
    ): LearningEvent {
        $now = date('Y-m-d H:i:s');
        return LearningEvent::create([
            'user_id' => $userId,
            'course_id' => $courseId,
            'event_type' => $eventType,
            'duration_seconds' => max(0, $durationSeconds),
            'metadata' => !empty($metadata) ? json_encode($metadata) : null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    public static function getStudentLearningVelocity(int $userId): array {
        $user = User::find($userId);
        if (!$user) {
            throw new RuntimeException("User not found: $userId");
        }

        $pdo = Connection::get();

        // 1. Total study duration & events
        $stmt = $pdo->prepare('
            SELECT COUNT(*) AS total_events, COALESCE(SUM(duration_seconds), 0) AS total_seconds
            FROM learning_events
            WHERE user_id = :u
        ');
        $stmt->execute([':u' => $userId]);
        $stats = $stmt->fetch(\PDO::FETCH_ASSOC);

        $totalEvents = (int)($stats['total_events'] ?? 0);
        $totalMinutes = (int)round(((int)($stats['total_seconds'] ?? 0)) / 60);
        $totalHours = round($totalMinutes / 60, 1);

        // 2. Daily activity for the past 14 days
        $dailyActivity = [];
        for ($i = 13; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $dailyActivity[$date] = [
                'date' => $date,
                'label' => date('M d', strtotime($date)),
                'events' => 0,
                'minutes' => 0,
            ];
        }

        $stmtDaily = $pdo->prepare('
            SELECT DATE(created_at) AS event_date, COUNT(*) AS count, COALESCE(SUM(duration_seconds), 0) AS secs
            FROM learning_events
            WHERE user_id = :u AND created_at >= :since
            GROUP BY DATE(created_at)
        ');
        $stmtDaily->execute([
            ':u' => $userId,
            ':since' => date('Y-m-d 00:00:00', strtotime('-14 days')),
        ]);
        while ($row = $stmtDaily->fetch(\PDO::FETCH_ASSOC)) {
            $d = (string)$row['event_date'];
            if (isset($dailyActivity[$d])) {
                $dailyActivity[$d]['events'] = (int)$row['count'];
                $dailyActivity[$d]['minutes'] = (int)round(((int)$row['secs']) / 60);
            }
        }

        // 3. Consecutive study streak
        $streak = 0;
        $checkDate = date('Y-m-d');
        // If no events today, check from yesterday to preserve yesterday's active streak
        $todayCountStmt = $pdo->prepare('SELECT COUNT(*) FROM learning_events WHERE user_id = :u AND DATE(created_at) = :d');
        $todayCountStmt->execute([':u' => $userId, ':d' => $checkDate]);
        if ((int)$todayCountStmt->fetchColumn() === 0) {
            $checkDate = date('Y-m-d', strtotime('-1 day'));
        }

        while (true) {
            $stmtStreak = $pdo->prepare('SELECT COUNT(*) FROM learning_events WHERE user_id = :u AND DATE(created_at) = :d');
            $stmtStreak->execute([':u' => $userId, ':d' => $checkDate]);
            if ((int)$stmtStreak->fetchColumn() > 0) {
                $streak++;
                $checkDate = date('Y-m-d', strtotime("$checkDate -1 day"));
            } else {
                break;
            }
        }

        // 4. Enrolled courses progress
        $enrollments = Enrollment::query()->where('user_id', $userId)->get();
        $courseBreakdown = [];
        foreach ($enrollments as $enr) {
            $course = Course::find((int)$enr['course_id']);
            if (!$course) continue;

            $courseBreakdown[] = [
                'course' => $course,
                'progress' => (int)($enr['progress_percent'] ?? 0),
                'enrolled_at' => $enr['enrolled_at'] ?? $enr['created_at'],
            ];
        }

        return [
            'user' => $user,
            'totalEvents' => $totalEvents,
            'totalMinutes' => $totalMinutes,
            'totalHours' => $totalHours,
            'streakDays' => $streak,
            'dailyActivity' => array_values($dailyActivity),
            'courseBreakdown' => $courseBreakdown,
        ];
    }

    public static function getCourseInsights(int $courseId): array {
        $course = Course::find($courseId);
        if (!$course) {
            throw new RuntimeException("Course not found: $courseId");
        }

        $pdo = Connection::get();

        // 1. Enrollment and Active Learners
        $enrolledRows = Enrollment::query()->where('course_id', $courseId)->get();
        $enrolledCount = count($enrolledRows);

        $activeThreshold = date('Y-m-d H:i:s', strtotime('-14 days'));
        $stmtActive = $pdo->prepare('
            SELECT COUNT(DISTINCT user_id)
            FROM learning_events
            WHERE course_id = :c AND created_at >= :since
        ');
        $stmtActive->execute([':c' => $courseId, ':since' => $activeThreshold]);
        $activeLearnersCount = (int)$stmtActive->fetchColumn();

        // 2. Average Progress & Grades
        $totalProgress = 0;
        $atRiskLearners = [];

        foreach ($enrolledRows as $enr) {
            $prog = (int)($enr['progress_percent'] ?? 0);
            $totalProgress += $prog;

            $student = User::find((int)$enr['user_id']);
            if (!$student) continue;

            $grade = CourseGrade::query()
                ->where('course_id', $courseId)
                ->where('student_id', (int)$student->id)
                ->first();

            $gradePct = $grade ? (int)$grade['percentage'] : 0;

            // At risk if progress < 25% or grade < 50%
            if ($prog < 25 || ($grade && $gradePct < 50)) {
                $atRiskLearners[] = [
                    'student' => $student,
                    'progress' => $prog,
                    'gradePercent' => $gradePct,
                    'letterGrade' => $grade['letter_grade'] ?? 'F',
                ];
            }
        }

        $avgCompletionPercent = $enrolledCount > 0 ? (int)round($totalProgress / $enrolledCount) : 0;

        // 3. Module Completion Funnel
        $modules = \App\Models\CourseModule::allForCourse($courseId);
        $moduleFunnel = [];
        foreach ($modules as $m) {
            $lessons = $m->lessons();
            $lessonCount = count($lessons);
            $completedCount = 0;

            foreach ($lessons as $les) {
                $stmtComps = $pdo->prepare('SELECT COUNT(*) FROM lesson_completions WHERE lesson_id = :l AND is_completed = 1');
                $stmtComps->execute([':l' => (int)$les->id]);
                $completedCount += (int)$stmtComps->fetchColumn();
            }

            $maxPossible = $enrolledCount * max(1, $lessonCount);
            $rate = $maxPossible > 0 ? (int)round(($completedCount / $maxPossible) * 100) : 0;

            $moduleFunnel[] = [
                'module_id' => (int)$m->id,
                'title' => (string)$m->title,
                'lesson_count' => $lessonCount,
                'completion_rate' => $rate,
            ];
        }

        return [
            'course' => $course,
            'enrolledCount' => $enrolledCount,
            'activeLearnersCount' => $activeLearnersCount,
            'avgCompletionPercent' => $avgCompletionPercent,
            'atRiskCount' => count($atRiskLearners),
            'atRiskLearners' => $atRiskLearners,
            'moduleFunnel' => $moduleFunnel,
        ];
    }

    public static function getInstitutionalInsights(): array {
        $pdo = Connection::get();

        $totalUsers = (int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
        $totalStudents = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE user_type = 'student'")->fetchColumn();
        $totalFaculty = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE user_type IN ('lecturer', 'instructor', 'l')")->fetchColumn();
        $totalCourses = (int)$pdo->query('SELECT COUNT(*) FROM course')->fetchColumn();
        $totalEnrollments = (int)$pdo->query('SELECT COUNT(*) FROM enrollments')->fetchColumn();

        $totalSeconds = (int)$pdo->query('SELECT COALESCE(SUM(duration_seconds), 0) FROM learning_events')->fetchColumn();
        $totalLearningHours = round($totalSeconds / 3600, 1);

        // Top courses by enrollment
        $stmtTop = $pdo->query('
            SELECT course_id, COUNT(*) AS count
            FROM enrollments
            GROUP BY course_id
            ORDER BY count DESC
            LIMIT 5
        ');
        $topCourses = [];
        while ($row = $stmtTop->fetch(\PDO::FETCH_ASSOC)) {
            $c = Course::find((int)$row['course_id']);
            if ($c) {
                $topCourses[] = [
                    'course' => $c,
                    'enrollments' => (int)$row['count'],
                ];
            }
        }

        return [
            'totalUsers' => $totalUsers,
            'totalStudents' => $totalStudents,
            'totalFaculty' => $totalFaculty,
            'totalCourses' => $totalCourses,
            'totalEnrollments' => $totalEnrollments,
            'totalLearningHours' => $totalLearningHours,
            'topCourses' => $topCourses,
        ];
    }
}
