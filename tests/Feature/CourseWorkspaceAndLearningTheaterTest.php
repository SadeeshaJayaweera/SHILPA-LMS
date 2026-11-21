<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\User;
use App\Services\CourseManagementService;
use App\Services\EnrollmentService;
use App\Testing\TestCase;

class CourseWorkspaceAndLearningTheaterTest extends TestCase {
    public function testCourseWorkspaceCurriculumStructure(): void {
        $course = CourseManagementService::createCourse([
            'title' => 'CS405 Advanced Software Architecture',
            'status' => 'published',
        ]);
        $cId = (int)$course->C_ID;

        $mod1 = CourseModule::create([
            'course_id' => $cId,
            'title' => 'Microservices and Event-Driven Systems',
            'order_index' => 1,
            'is_published' => 1,
        ]);

        $l1 = Lesson::create([
            'module_id' => (int)$mod1->id,
            'title' => 'Event Sourcing and CQRS Foundations',
            'slug' => 'event-sourcing-cqrs',
            'content_type' => 'video',
            'content_body' => 'Deep dive into event streams, projection stores, and snapshotting.',
            'duration_minutes' => 30,
            'order_index' => 1,
            'is_free_preview' => 1,
        ]);

        $l2 = Lesson::create([
            'module_id' => (int)$mod1->id,
            'title' => 'Saga Pattern for Distributed Transactions',
            'slug' => 'saga-pattern',
            'content_type' => 'article',
            'content_body' => 'Orchestration versus choreography in distributed sagas.',
            'duration_minutes' => 25,
            'order_index' => 2,
            'is_free_preview' => 0,
        ]);

        $modules = CourseModule::forCourse($cId);
        assert(count($modules) === 1, "Course should have 1 module");
        assert($modules[0]->title === 'Microservices and Event-Driven Systems', "Module title must match");

        $lessons = $modules[0]->lessons();
        assert(count($lessons) === 2, "Module must contain 2 lessons");
        assert($lessons[0]->title === 'Event Sourcing and CQRS Foundations', "Lesson 1 title must match");
        assert($lessons[0]->isFreePreview() === true, "Lesson 1 must be free preview");
        assert($lessons[1]->isFreePreview() === false, "Lesson 2 must not be free preview");
    }

    public function testWorkspaceOverviewPageRenders(): void {
        $course = CourseManagementService::createCourse([
            'title' => 'CS405 Advanced Software Architecture',
            'status' => 'published',
        ]);
        $cId = (int)$course->C_ID;

        $response = $this->get("/courses/{$cId}/workspace");
        assert($response->getStatusCode() === 200, "Course workspace overview must render with 200 OK");
        $response->assertSee('CS405 Advanced Software Architecture');
        $response->assertSee('Curriculum Syllabus');
        $response->assertSee('Open Enrollment');
    }

    public function testFreePreviewLessonAccessibleWithoutEnrollment(): void {
        session()->clear();

        $course = CourseManagementService::createCourse(['title' => 'CS406 Cloud Native', 'status' => 'published']);
        $mod = CourseModule::create(['course_id' => (int)$course->C_ID, 'title' => 'Intro Module', 'order_index' => 1]);
        $lesson = Lesson::create([
            'module_id' => (int)$mod->id,
            'title' => 'Preview Lecture: Welcome to Cloud Native',
            'slug' => 'preview-lecture',
            'content_type' => 'video',
            'duration_minutes' => 10,
            'is_free_preview' => 1,
        ]);

        $response = $this->get("/courses/{$course->C_ID}/learn/{$lesson->id}");
        assert($response->getStatusCode() === 200, "Free preview lesson must be accessible with 200 OK without enrollment");
        $response->assertSee('Preview Lecture: Welcome to Cloud Native');
        $response->assertSee('Course Curriculum');
    }

    public function testPrivateLessonRedirectsUnenrolledUserToWorkspace(): void {
        session()->clear();

        $course = CourseManagementService::createCourse(['title' => 'CS407 Cryptography', 'status' => 'published']);
        $mod = CourseModule::create(['course_id' => (int)$course->C_ID, 'title' => 'Advanced Cryptography', 'order_index' => 1]);
        $privateLesson = Lesson::create([
            'module_id' => (int)$mod->id,
            'title' => 'Zero-Knowledge Proofs in Practice',
            'slug' => 'zk-proofs',
            'content_type' => 'video',
            'is_free_preview' => 0,
        ]);

        $response = $this->get("/courses/{$course->C_ID}/learn/{$privateLesson->id}");
        assert($response->getStatusCode() === 302, "Unenrolled access to private lesson must redirect");
    }

    public function testEnrolledStudentAccessesPrivateLessonInTheater(): void {
        $student = User::create([
            'Full_Name' => 'Theater Student',
            'Email' => 'theater.student@shilpa.lk',
            'Password' => 'TheaterPass123!#',
            'user_type' => 'student',
        ]);

        $course = CourseManagementService::createCourse(['title' => 'CS408 Systems Optimization', 'status' => 'published']);
        $mod = CourseModule::create(['course_id' => (int)$course->C_ID, 'title' => 'Profiling & Benchmarking', 'order_index' => 1]);
        $lesson1 = Lesson::create([
            'module_id' => (int)$mod->id,
            'title' => 'CPU Cache Locality and Prefetching',
            'slug' => 'cpu-cache',
            'content_type' => 'article',
            'content_body' => 'Understanding L1/L2/L3 cache line boundaries and false sharing.',
            'duration_minutes' => 30,
            'order_index' => 1,
            'is_free_preview' => 0,
        ]);
        $lesson2 = Lesson::create([
            'module_id' => (int)$mod->id,
            'title' => 'Zero-Copy IO with epoll and io_uring',
            'slug' => 'io-uring',
            'content_type' => 'video',
            'duration_minutes' => 45,
            'order_index' => 2,
            'is_free_preview' => 0,
        ]);

        EnrollmentService::enroll((int)$student->id, (int)$course->C_ID);

        // Authenticate student session
        session()->set('user', $student->Full_Name);
        session()->set('user_id', $student->id);
        session()->set('email', $student->Email);
        session()->set('user_role', 'student');

        $response = $this->get("/courses/{$course->C_ID}/learn/{$lesson1->id}");
        assert($response->getStatusCode() === 200, "Enrolled student must access private lesson with 200 OK");
        $response->assertSee('CPU Cache Locality and Prefetching');
        $response->assertSee('Next Lecture');

        // Check overview reflects enrolled state
        $overview = $this->get("/courses/{$course->C_ID}/workspace");
        assert($overview->getStatusCode() === 200, "Workspace overview should return 200 OK");
        $overview->assertSee('Enrolled Learner');
        $overview->assertSee('Resume Learning');
    }
}
