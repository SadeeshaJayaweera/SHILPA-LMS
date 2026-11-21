<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseGrade;
use App\Models\Enrollment;
use App\Models\User;
use App\Services\AnalyticsService;
use App\Services\CourseManagementService;
use App\Services\EnrollmentService;
use App\Testing\TestCase;

class LearningAnalyticsAndPerformanceInsightsTest extends TestCase {
    public function testRecordLearningEventAndStudentVelocity(): void {
        $student = User::create([
            'Full_Name' => 'Blaise Pascal',
            'Email' => 'pascal.' . uniqid() . '@shilpa.edu',
            'Password' => password_hash('Probability1654!', PASSWORD_BCRYPT),
            'user_type' => 'student',
        ]);

        $course = CourseManagementService::createCourse([
            'title' => 'MATH880 Combinatorics & Probability',
            'status' => 'published',
        ]);
        $cId = (int)$course->C_ID;

        // 1. Record Learning Events
        AnalyticsService::recordEvent((int)$student->id, $cId, 'lesson.completed', 1800, ['lesson_id' => 1]);
        AnalyticsService::recordEvent((int)$student->id, $cId, 'study.session', 3600, ['topic' => 'Permutations']);

        // 2. Fetch velocity
        $velocity = AnalyticsService::getStudentLearningVelocity((int)$student->id);

        assert($velocity['totalEvents'] === 2);
        assert($velocity['totalMinutes'] === 90);
        assert($velocity['totalHours'] === 1.5);
        assert($velocity['streakDays'] >= 1);
        assert(count($velocity['dailyActivity']) === 14);
    }

    public function testCourseAnalyticsAndAtRiskLearnerDetection(): void {
        $course = CourseManagementService::createCourse([
            'title' => 'CS950 Distributed Database Internals',
            'status' => 'published',
        ]);
        $cId = (int)$course->C_ID;

        $studentHigh = User::create([
            'Full_Name' => 'Edgar Codd',
            'Email' => 'codd.' . uniqid() . '@shilpa.edu',
            'Password' => password_hash('Relational1!', PASSWORD_BCRYPT),
            'user_type' => 'student',
        ]);

        $studentAtRisk = User::create([
            'Full_Name' => 'Struggling Learner',
            'Email' => 'struggling.' . uniqid() . '@shilpa.edu',
            'Password' => password_hash('Learner123!', PASSWORD_BCRYPT),
            'user_type' => 'student',
        ]);

        // Enroll students
        EnrollmentService::enroll((int)$studentHigh->id, $cId);
        EnrollmentService::enroll((int)$studentAtRisk->id, $cId);

        // Update progress
        $enrHigh = Enrollment::query()->where('course_id', $cId)->where('user_id', (int)$studentHigh->id)->first();
        $enrHighModel = new Enrollment($enrHigh);
        $enrHighModel->progress_percent = 90;
        $enrHighModel->save();

        $enrLow = Enrollment::query()->where('course_id', $cId)->where('user_id', (int)$studentAtRisk->id)->first();
        $enrLowModel = new Enrollment($enrLow);
        $enrLowModel->progress_percent = 15; // < 25% threshold
        $enrLowModel->save();

        // Assign grades
        CourseGrade::create([
            'course_id' => $cId,
            'student_id' => (int)$studentHigh->id,
            'total_score' => 95,
            'max_score' => 100,
            'percentage' => 95,
            'letter_grade' => 'A+',
            'is_published' => 1,
        ]);

        CourseGrade::create([
            'course_id' => $cId,
            'student_id' => (int)$studentAtRisk->id,
            'total_score' => 40,
            'max_score' => 100,
            'percentage' => 40,
            'letter_grade' => 'F',
            'is_published' => 1,
        ]);

        $insights = AnalyticsService::getCourseInsights($cId);

        assert($insights['enrolledCount'] === 2);
        assert($insights['atRiskCount'] === 1);
        assert((int)$insights['atRiskLearners'][0]['student']->id === (int)$studentAtRisk->id);
    }

    public function testInstitutionalAnalytics(): void {
        User::create([
            'Full_Name' => 'Institutional Scholar',
            'Email' => 'scholar.' . uniqid() . '@shilpa.edu',
            'Password' => password_hash('Scholar123!', PASSWORD_BCRYPT),
            'user_type' => 'student',
        ]);

        $inst = AnalyticsService::getInstitutionalInsights();

        assert($inst['totalUsers'] >= 1);
        assert($inst['totalStudents'] >= 1);
        assert(isset($inst['totalLearningHours']));
        assert(is_array($inst['topCourses']));
    }

    public function testAnalyticsHttpWorkflows(): void {
        $admin = User::create([
            'Full_Name' => 'Dean of Computing',
            'Email' => 'dean.' . uniqid() . '@shilpa.edu',
            'Password' => password_hash('AdminPass1!', PASSWORD_BCRYPT),
            'user_type' => 'admin',
        ]);

        $faculty = User::create([
            'Full_Name' => 'Prof. Andrew Tanenbaum',
            'Email' => 'tanenbaum.' . uniqid() . '@shilpa.edu',
            'Password' => password_hash('MinixPass1!', PASSWORD_BCRYPT),
            'user_type' => 'lecturer',
        ]);

        $student = User::create([
            'Full_Name' => 'Linus Torvalds',
            'Email' => 'linus.' . uniqid() . '@shilpa.edu',
            'Password' => password_hash('LinuxKernel1!', PASSWORD_BCRYPT),
            'user_type' => 'student',
        ]);

        $course = CourseManagementService::createCourse([
            'title' => 'CS900 Monolithic vs Microkernels',
            'status' => 'published',
            'instructor' => 'Prof. Andrew Tanenbaum',
        ]);
        $cId = (int)$course->C_ID;

        // 1. Student views personal analytics
        $this->actingAs($student);
        $studentRes = $this->get('/analytics');
        $studentRes->assertStatus(200);
        $studentRes->assertSee('My Learning Velocity');

        // 2. Student calls personal API
        $apiStudentRes = $this->get("/api/analytics/student/{$student->id}");
        $apiStudentRes->assertStatus(200);
        $apiStudentRes->assertSee('streakDays');

        // 3. Faculty views course analytics
        $this->actingAs($faculty);
        $courseRes = $this->get("/faculty/courses/{$cId}/analytics");
        $courseRes->assertStatus(200);
        $courseRes->assertSee('Retention Funnel');

        // 4. Admin views institutional analytics
        $this->actingAs($admin);
        $adminRes = $this->get('/admin/analytics');
        $adminRes->assertStatus(200);
        $adminRes->assertSee('Institutional Learning Analytics');

        // 5. Unauthorized access check: student cannot view admin analytics
        $this->actingAs($student);
        $unauthRes = $this->get('/admin/analytics');
        $unauthRes->assertStatus(302);
    }
}
