<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use App\Services\CourseManagementService;
use App\Services\EnrollmentService;
use App\Testing\TestCase;

class CourseEnrollmentAndAccessTest extends TestCase {
    public function testUnauthenticatedUserCannotEnroll(): void {
        session()->clear();
        $response = $this->post('/courses/1/enroll');
        assert($response->getStatusCode() === 302, "Unauthenticated enrollment must redirect to login");
    }

    public function testStudentEnrollmentInPublishedCourse(): void {
        $student = User::create([
            'Full_Name' => 'Saman Learner',
            'Email' => 'saman.learner@shilpa.lk',
            'Password' => 'HashPass123!#',
            'user_type' => 'student',
        ]);

        $course = CourseManagementService::createCourse([
            'title' => 'CS304 Software Architecture',
            'status' => 'published',
        ]);

        $result = EnrollmentService::enroll((int)$student->id, (int)$course->C_ID);
        assert($result['success'] === true, "Enrollment in published course should succeed");
        assert(Enrollment::isEnrolled((int)$student->id, (int)$course->C_ID) === true, "Enrollment status must be active");

        // Verify duplicate enrollment prevention
        $duplicate = EnrollmentService::enroll((int)$student->id, (int)$course->C_ID);
        assert($duplicate['success'] === false, "Duplicate active enrollment must be rejected");
        assert(str_contains($duplicate['error'], 'already actively enrolled'), "Error message should mention already enrolled");
    }

    public function testEnrollmentFailsForDraftCourse(): void {
        $student = User::create([
            'Full_Name' => 'Draft Tester',
            'Email' => 'draft.tester@shilpa.lk',
            'Password' => 'HashPass123!#',
            'user_type' => 'student',
        ]);

        $course = CourseManagementService::createCourse([
            'title' => 'Under-development Secret Course',
            'status' => 'draft',
        ]);

        $result = EnrollmentService::enroll((int)$student->id, (int)$course->C_ID);
        assert($result['success'] === false, "Enrollment in draft course must fail");
        assert(str_contains($result['error'], 'draft mode'), "Error should mention draft mode");
        assert(Enrollment::isEnrolled((int)$student->id, (int)$course->C_ID) === false, "Student must not be enrolled");
    }

    public function testStudentCanDropCourse(): void {
        $student = User::create([
            'Full_Name' => 'Drop Tester',
            'Email' => 'drop.tester@shilpa.lk',
            'Password' => 'HashPass123!#',
            'user_type' => 'student',
        ]);

        $course = CourseManagementService::createCourse([
            'title' => 'SE202 Database Systems',
            'status' => 'published',
        ]);

        EnrollmentService::enroll((int)$student->id, (int)$course->C_ID);
        assert(Enrollment::isEnrolled((int)$student->id, (int)$course->C_ID) === true, "Student should be enrolled");

        $dropped = EnrollmentService::drop((int)$student->id, (int)$course->C_ID);
        assert($dropped === true, "Drop course should succeed");
        assert(Enrollment::isEnrolled((int)$student->id, (int)$course->C_ID) === false, "Student should no longer be actively enrolled");
    }

    public function testRosterRetrievalAndFacultyAccessControl(): void {
        $course = CourseManagementService::createCourse([
            'title' => 'CS501 Distributed Systems',
            'status' => 'published',
        ]);

        $student1 = User::create(['Full_Name' => 'Alice Student', 'Email' => 'alice@shilpa.lk', 'Password' => 'HashPass123!#', 'user_type' => 'student']);
        $student2 = User::create(['Full_Name' => 'Bob Student', 'Email' => 'bob@shilpa.lk', 'Password' => 'HashPass123!#', 'user_type' => 'student']);

        EnrollmentService::enroll((int)$student1->id, (int)$course->C_ID);
        EnrollmentService::enroll((int)$student2->id, (int)$course->C_ID);

        $roster = EnrollmentService::getRoster((int)$course->C_ID);
        assert(count($roster) === 2, "Roster must contain 2 enrolled students");

        // 1. Student access to faculty roster is forbidden/redirected
        session()->set('user', 'Student User');
        session()->set('user_role', 'student');
        $studentAccess = $this->get("/faculty/courses/{$course->C_ID}/roster");
        assert($studentAccess->getStatusCode() === 302, "Student should be redirected away from roster");

        // 2. Lecturer access to roster succeeds
        session()->set('user', 'Dr. Perera');
        session()->set('user_role', 'lecturer');
        $lecturerAccess = $this->get("/faculty/courses/{$course->C_ID}/roster");
        assert($lecturerAccess->getStatusCode() === 200, "Lecturer should access roster with 200 OK");
        $lecturerAccess->assertSee('Student Enrollment Roster');
        $lecturerAccess->assertSee('Alice Student');
        $lecturerAccess->assertSee('Bob Student');
    }

    public function testFacultyCanUpdateEnrollmentStatus(): void {
        $student = User::create(['Full_Name' => 'Grad Student', 'Email' => 'grad@shilpa.lk', 'Password' => 'HashPass123!#', 'user_type' => 'student']);
        $course = CourseManagementService::createCourse(['title' => 'CS600 Thesis Research', 'status' => 'published']);

        $enrResult = EnrollmentService::enroll((int)$student->id, (int)$course->C_ID);
        $enrId = (int)$enrResult['enrollment']->id;

        $updated = EnrollmentService::updateStatus($enrId, 'completed');
        assert($updated === true, "Updating status to completed should succeed");

        $enr = Enrollment::find($enrId);
        assert($enr->status === 'completed', "Status must be completed");
        assert((int)$enr->progress_percent === 100, "Progress must be 100%");
        assert(!empty($enr->completed_at), "Completed timestamp must be set");
    }
}
