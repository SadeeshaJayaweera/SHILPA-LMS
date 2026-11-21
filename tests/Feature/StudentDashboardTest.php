<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use App\Testing\TestCase;

class StudentDashboardTest extends TestCase {
    public function testStudentDashboardRedirectsUnauthenticatedUser(): void {
        session()->destroy();
        $response = $this->get('/student/dashboard');
        $response->assertRedirect('/login');
    }

    public function testStudentDashboardRendersForAuthenticatedStudent(): void {
        $student = User::create([
            'Full_Name' => 'Dinuka Chamara',
            'Email' => 'dinuka@shilpa.lk',
            'Password' => password_hash('DinukaPass2026!', PASSWORD_DEFAULT),
            'user_type' => 'student',
        ]);

        $this->actingAs($student);
        $response = $this->get('/student/dashboard');

        $response->assertOk()
            ->assertSee('Dinuka Chamara')
            ->assertSee('Enrolled Courses')
            ->assertSee('Curriculum Completion')
            ->assertSee('Study Time This Week')
            ->assertSee('Weekly Study Velocity')
            ->assertSee('Subject Mastery Index')
            ->assertSee('Study Recommendations')
            ->assertSee('Recent Activities');
    }

    public function testStudentDashboardDisplaysEnrolledCourses(): void {
        $student = User::create([
            'Full_Name' => 'Sanjaya Silva',
            'Email' => 'sanjaya@shilpa.lk',
            'Password' => password_hash('Pass12345!', PASSWORD_DEFAULT),
            'user_type' => 'student',
        ]);

        $course = Course::create([
            'C_NAME' => 'Distributed Microservices & Cloud Architecture',
            'C_CARD_DESCRIPTION' => 'Enterprise microservice designs with event streaming',
            'C_DESCRIPTION' => 'Full architectural curriculum',
            'C_CARD_IMAGE' => 'Images/allCourses/kiddo.png',
            'C_DONE_BY' => 'Faculty of Computing',
            'C_LINK' => 'https://shilpalms.com',
            'TOP_COURSE' => 'Yes',
        ]);

        Enrollment::create([
            'user_id' => $student->UserId,
            'course_id' => $course->C_ID,
            'status' => 'active',
            'progress_percent' => 45,
            'enrolled_at' => date('Y-m-d H:i:s'),
        ]);

        $this->actingAs($student);
        $response = $this->get('/student/dashboard');

        $response->assertOk()
            ->assertSee('Distributed Microservices')
            ->assertSee('Faculty of Computing')
            ->assertSee('45% Complete')
            ->assertSee('Resume Learning');
    }

    public function testStudentDashboardEmptyStateWhenNoEnrollments(): void {
        $student = User::create([
            'Full_Name' => 'Brand New Student',
            'Email' => 'newstudent@shilpa.lk',
            'Password' => password_hash('Pass12345!', PASSWORD_DEFAULT),
            'user_type' => 'student',
        ]);

        $this->actingAs($student);
        $response = $this->get('/student/dashboard');

        $response->assertOk()
            ->assertSee('No Enrolled Courses Yet')
            ->assertSee('Browse Open Course Catalog');
    }

    public function testStudentAnalyticsApiEndpoint(): void {
        $student = User::create([
            'Full_Name' => 'Analytics Tester',
            'Email' => 'analyticstester@shilpa.lk',
            'Password' => password_hash('Pass12345!', PASSWORD_DEFAULT),
            'user_type' => 'student',
        ]);

        $this->actingAs($student);
        $response = $this->get('/api/student/analytics');

        $response->assertOk();
        $json = $response->json();
        assert(isset($json['weeklyActivity']), "API must return weeklyActivity");
        assert(isset($json['subjectMastery']), "API must return subjectMastery");
        assert(isset($json['metrics']), "API must return metrics");
        assert(count($json['weeklyActivity']) === 7, "Weekly activity should cover 7 days");
    }

    public function testStudentAnalyticsApiEndpointUnauthorized(): void {
        session()->destroy();
        $response = $this->get('/api/student/analytics');
        $response->assertStatus(401);
    }
}
