<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Course;
use App\Models\User;
use App\Testing\TestCase;

class LecturerAndAdminDashboardTest extends TestCase {
    public function testLecturerDashboardRequiresAuthentication(): void {
        session()->destroy();
        $response = $this->get('/lecturer/dashboard');
        $response->assertRedirect('/login');
    }

    public function testLecturerDashboardRestrictedFromStudent(): void {
        $student = User::create([
            'Full_Name' => 'Regular Student',
            'Email' => 'student@shilpa.lk',
            'Password' => password_hash('Pass12345!', PASSWORD_DEFAULT),
            'user_type' => 'student',
        ]);

        $this->actingAs($student);
        $response = $this->get('/lecturer/dashboard');
        $response->assertRedirect('/student/dashboard');
    }

    public function testLecturerDashboardRendersForFaculty(): void {
        $lecturer = User::create([
            'Full_Name' => 'Dr. Jayaweera',
            'Email' => 'lecturer@shilpa.lk',
            'Password' => password_hash('LecturerPass2026!', PASSWORD_DEFAULT),
            'user_type' => 'lecturer',
        ]);

        Course::create([
            'C_NAME' => 'Compiler Engineering & Automata',
            'C_CARD_DESCRIPTION' => 'Theory and practice of compiler construction',
            'C_DESCRIPTION' => 'Deep dive into LLVM and syntax trees',
            'C_CARD_IMAGE' => 'Images/allCourses/kiddo.png',
            'C_DONE_BY' => 'Dr. Jayaweera',
            'C_LINK' => 'https://shilpalms.com',
            'TOP_COURSE' => 'Yes',
        ]);

        $this->actingAs($lecturer);
        $response = $this->get('/lecturer/dashboard');

        $response->assertOk()
            ->assertSee('Faculty Workspace')
            ->assertSee('Dr. Jayaweera')
            ->assertSee('Assigned Courses')
            ->assertSee('Pending Grading Queue')
            ->assertSee('Curriculum Management')
            ->assertSee('Cohort Grade Distribution');
    }

    public function testAdminDashboardRequiresAuthentication(): void {
        session()->destroy();
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/login');
    }

    public function testAdminDashboardRestrictedFromStudent(): void {
        $student = User::create([
            'Full_Name' => 'Student Accessing Admin',
            'Email' => 'studentadmin@shilpa.lk',
            'Password' => password_hash('Pass12345!', PASSWORD_DEFAULT),
            'user_type' => 'student',
        ]);

        $this->actingAs($student);
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/workspace');
    }

    public function testAdminDashboardRendersForAdmin(): void {
        $admin = User::create([
            'Full_Name' => 'Platform Administrator',
            'Email' => 'admin@shilpa.lk',
            'Password' => password_hash('AdminPass2026!', PASSWORD_DEFAULT),
            'user_type' => 'admin',
        ]);

        $this->actingAs($admin);
        $response = $this->get('/admin/dashboard');

        $response->assertOk()
            ->assertSee('Platform Governance Console')
            ->assertSee('Total Platform Users')
            ->assertSee('Active Courses')
            ->assertSee('Enrollment Growth Trajectory')
            ->assertSee('Security & Audit Logs')
            ->assertSee('System Diagnostics');
    }

    public function testAdminUserDirectoryFiltering(): void {
        $admin = User::create([
            'Full_Name' => 'Admin User Manager',
            'Email' => 'usermanager@shilpa.lk',
            'Password' => password_hash('AdminPass2026!', PASSWORD_DEFAULT),
            'user_type' => 'admin',
        ]);

        $this->actingAs($admin);
        $response = $this->get('/admin/users');

        $response->assertOk()
            ->assertSee('User Directory Management')
            ->assertSee('All Users')
            ->assertSee('Students')
            ->assertSee('Faculty')
            ->assertSee('Admins');

        // Test filtering by role
        $studentFilter = $this->get('/admin/users?role=student');
        $studentFilter->assertOk();
    }

    public function testAdminSystemHealthApi(): void {
        $admin = User::create([
            'Full_Name' => 'Health Auditor',
            'Email' => 'auditor@shilpa.lk',
            'Password' => password_hash('AdminPass2026!', PASSWORD_DEFAULT),
            'user_type' => 'admin',
        ]);

        $this->actingAs($admin);
        $response = $this->get('/api/admin/metrics');

        $response->assertOk();
        $json = $response->json();
        assert($json['status'] === 'healthy', "Status must be healthy");
        assert(isset($json['systemHealth']), "systemHealth key must be present");
        assert(isset($json['metrics']), "metrics key must be present");
    }
}
