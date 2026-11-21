<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonCompletion;
use App\Models\User;
use App\Services\CourseManagementService;
use App\Services\CurriculumBuilderService;
use App\Services\EnrollmentService;
use App\Services\ProgressTrackingService;
use App\Testing\TestCase;

class LearningProgressAndCompletionTest extends TestCase {
    public function testLessonCompletionAndRecalculationFlow(): void {
        $course = CourseManagementService::createCourse([
            'title' => 'CS601 Distributed Consensus & Paxos',
            'status' => 'published',
        ]);
        $cId = (int)$course->C_ID;

        $mod = CurriculumBuilderService::createModule($cId, [
            'title' => 'Module 1: Foundations',
            'order_index' => 1,
            'is_published' => 1,
        ]);

        $l1 = CurriculumBuilderService::createLesson((int)$mod->id, [
            'title' => 'Lecture 1: Paxos Made Simple',
            'content_type' => 'video',
            'duration_minutes' => 30,
        ]);

        $l2 = CurriculumBuilderService::createLesson((int)$mod->id, [
            'title' => 'Lecture 2: Raft Consensus',
            'content_type' => 'video',
            'duration_minutes' => 30,
        ]);

        $student = User::create([
            'Full_Name' => 'Sarah Connor',
            'Email' => 'sarah.connor.' . time() . '@example.com',
            'Password' => password_hash('Pass123!', PASSWORD_BCRYPT),
            'user_type' => 'student',
        ]);
        $sId = (int)$student->UserId;

        EnrollmentService::enroll($sId, $cId);
        $enrollment = Enrollment::query()->where('user_id', $sId)->where('course_id', $cId)->first();
        assert((int)($enrollment['progress_percent'] ?? 0) === 0, "Initial progress must be 0%");
        assert(($enrollment['status'] ?? '') === 'active', "Initial status must be active");

        // 1. Mark Lesson 1 Complete -> 50%
        $r1 = ProgressTrackingService::markLessonComplete($sId, $cId, (int)$l1->id);
        assert($r1['progress_percent'] === 50, "1 of 2 lessons completed must equal 50%");
        assert($r1['completed_lessons'] === 1);
        assert($r1['is_course_completed'] === false);

        $enr1 = Enrollment::query()->where('user_id', $sId)->where('course_id', $cId)->first();
        assert((int)$enr1['progress_percent'] === 50);
        assert($enr1['status'] === 'active');

        // 2. Mark Lesson 2 Complete -> 100% and Course Completed
        $r2 = ProgressTrackingService::markLessonComplete($sId, $cId, (int)$l2->id);
        assert($r2['progress_percent'] === 100, "2 of 2 lessons completed must equal 100%");
        assert($r2['is_course_completed'] === true);

        $enr2 = Enrollment::query()->where('user_id', $sId)->where('course_id', $cId)->first();
        assert((int)$enr2['progress_percent'] === 100);
        assert($enr2['status'] === 'completed', "Enrollment status must update to completed upon reaching 100%");
        assert(!empty($enr2['completed_at']), "completed_at timestamp must be populated");

        // 3. Mark Lesson 1 Incomplete -> Reverts to 50% and Status Reverts to Active
        $r3 = ProgressTrackingService::markLessonIncomplete($sId, $cId, (int)$l1->id);
        assert($r3['progress_percent'] === 50);
        assert($r3['is_course_completed'] === false);

        $enr3 = Enrollment::query()->where('user_id', $sId)->where('course_id', $cId)->first();
        assert((int)$enr3['progress_percent'] === 50);
        assert($enr3['status'] === 'active', "Status must revert to active when incomplete");
    }

    public function testControllerEndpointAndAccessControl(): void {
        $course = CourseManagementService::createCourse([
            'title' => 'CS602 Advanced Microservices Testing',
            'status' => 'published',
        ]);
        $cId = (int)$course->C_ID;

        $mod = CurriculumBuilderService::createModule($cId, ['title' => 'Unit 1']);
        $l1 = CurriculumBuilderService::createLesson((int)$mod->id, ['title' => 'Intro']);

        // Unauthenticated access redirects
        session()->destroy();
        $unauthResp = $this->post("/courses/{$cId}/lessons/{$l1->id}/complete");
        $unauthResp->assertRedirect('/login');

        // Authenticated student can toggle completion
        $student = User::create([
            'Full_Name' => 'David Miller',
            'Email' => 'david.m.' . time() . '@example.com',
            'Password' => password_hash('Pass123!', PASSWORD_BCRYPT),
            'user_type' => 'student',
        ]);
        $sId = (int)$student->UserId;

        session()->set('user', ['id' => $sId, 'name' => 'David Miller']);
        session()->set('user_id', $sId);
        session()->set('user_role', 'student');

        $completeResp = $this->post("/courses/{$cId}/lessons/{$l1->id}/complete");
        $completeResp->assertRedirect("/courses/{$cId}/learn/{$l1->id}");

        assert(LessonCompletion::isCompleted($sId, (int)$l1->id) === true, "Lesson must be marked complete");

        // Check progress API
        $apiResp = $this->get("/api/courses/{$cId}/progress");
        $apiResp->assertOk();
        $apiResp->assertJson(['success' => true]);
    }
}
