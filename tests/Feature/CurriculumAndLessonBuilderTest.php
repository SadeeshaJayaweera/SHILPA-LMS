<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Lesson;
use App\Models\LessonBlock;
use App\Models\User;
use App\Services\CourseManagementService;
use App\Services\CurriculumBuilderService;
use App\Testing\TestCase;

class CurriculumAndLessonBuilderTest extends TestCase {
    public function testModuleAndLessonBuilderServiceFlow(): void {
        $course = CourseManagementService::createCourse([
            'title' => 'Advanced Distributed Systems and Cloud Computing',
            'status' => 'published',
        ]);
        $cId = (int)$course->C_ID;

        // 1. Create Modules
        $mod1 = CurriculumBuilderService::createModule($cId, [
            'title' => 'Module 1: Consensus Protocols',
            'description' => 'Paxos, Raft, and Byzantine Fault Tolerance',
            'order_index' => 1,
            'is_published' => 1,
        ]);

        $mod2 = CurriculumBuilderService::createModule($cId, [
            'title' => 'Module 2: Distributed Storage Systems',
            'description' => 'Consistent hashing, replication, and vector clocks',
            'order_index' => 2,
            'is_published' => 1,
        ]);

        $modules = CourseModule::forCourse($cId);
        assert(count($modules) === 2, "Expected 2 active course modules");
        assert($modules[0]->title === 'Module 1: Consensus Protocols', "Module 1 title mismatch");

        // 2. Create Lessons
        $lesson1 = CurriculumBuilderService::createLesson((int)$mod1->id, [
            'title' => 'Raft Leader Election and Heartbeats',
            'content_type' => 'video',
            'content_body' => 'Understanding state machine replication and term increments.',
            'duration_minutes' => 35,
            'is_free_preview' => 1,
        ]);

        $lesson2 = CurriculumBuilderService::createLesson((int)$mod1->id, [
            'title' => 'Log Replication & Safety Invariants',
            'content_type' => 'article',
            'content_body' => 'How commit indices and matching terms guarantee correctness.',
            'duration_minutes' => 20,
            'is_free_preview' => 0,
        ]);

        $lessons = $mod1->lessons();
        assert(count($lessons) === 2, "Module 1 must have 2 lessons");
        assert($lessons[0]->title === 'Raft Leader Election and Heartbeats');
        assert($lessons[0]->isFreePreview() === true);

        // 3. Add Content Blocks
        $b1 = CurriculumBuilderService::addLessonBlock((int)$lesson1->id, 'rich_text', [
            'text' => 'In Raft, at any given time each server is either a leader, follower, or candidate.',
        ], 1);

        $b2 = CurriculumBuilderService::addLessonBlock((int)$lesson1->id, 'code_snippet', [
            'code' => "type State int\nconst (\n  Follower State = iota\n  Candidate\n  Leader\n)",
        ], 2);

        $b3 = CurriculumBuilderService::addLessonBlock((int)$lesson1->id, 'callout', [
            'text' => 'Safety Guarantee: Election Safety allows at most one leader per term.',
        ], 3);

        $blocks = LessonBlock::forLesson((int)$lesson1->id);
        assert(count($blocks) === 3, "Lesson 1 must contain 3 content blocks");
        assert(str_contains($blocks[1]->renderHtml(), '<pre class="code-block"'), "Block 2 must render as code snippet");
        assert(str_contains($blocks[2]->renderHtml(), 'callout-box'), "Block 3 must render as callout");

        // 4. Update Lesson
        $updatedLesson = CurriculumBuilderService::updateLesson((int)$lesson1->id, [
            'title' => 'Raft Protocol: Leader Election & Log Replication',
            'duration_minutes' => 45,
        ]);
        assert($updatedLesson->title === 'Raft Protocol: Leader Election & Log Replication');
        assert((int)$updatedLesson->duration_minutes === 45);

        // 5. Reorder Modules & Lessons
        CurriculumBuilderService::reorderModules($cId, [(int)$mod2->id, (int)$mod1->id]);
        $reorderedModules = CourseModule::forCourse($cId);
        assert((int)$reorderedModules[0]->id === (int)$mod2->id, "Module 2 should now be first in sequence");

        // 6. Delete Lesson & Cascade Cleanup
        CurriculumBuilderService::deleteLesson((int)$lesson2->id);
        assert(count($mod1->lessons()) === 1, "Module 1 should have 1 lesson after deletion");

        // 7. Delete Module & Cascade Cleanup
        CurriculumBuilderService::deleteModule((int)$mod2->id);
        assert(count(CourseModule::forCourse($cId)) === 1, "Should have 1 module after deletion");
    }

    public function testCurriculumBuilderControllerAccess(): void {
        $course = CourseManagementService::createCourse([
            'title' => 'SEC601 Cloud Security Engineering',
            'status' => 'published',
        ]);
        $cId = (int)$course->C_ID;

        // Unauthenticated access redirects to login
        session()->destroy();
        $response = $this->get("/faculty/courses/{$cId}/builder");
        $response->assertRedirect('/login');

        // Student access redirects away
        session()->set('user', ['id' => 99, 'name' => 'Alice Student']);
        session()->set('user_role', 'student');
        $studentResponse = $this->get("/faculty/courses/{$cId}/builder");
        $studentResponse->assertRedirect('/workspace');

        // Lecturer / Faculty access succeeds
        session()->set('user_role', 'lecturer');
        $facultyResponse = $this->get("/faculty/courses/{$cId}/builder");
        $facultyResponse->assertOk();
        $facultyResponse->assertSee('Curriculum Studio');
    }
}
