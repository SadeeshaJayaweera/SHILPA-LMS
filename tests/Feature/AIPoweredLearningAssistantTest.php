<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Models\AIConversation;
use App\Models\AIMessage;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use App\Services\AITutorService;
use App\Services\CourseManagementService;
use App\Services\CurriculumBuilderService;
use App\Testing\TestCase;

class AIPoweredLearningAssistantTest extends TestCase {
    public function testConversationCreationAndContextInjection(): void {
        $student = User::create([
            'Full_Name' => 'Donald Knuth',
            'Email' => 'knuth.' . uniqid() . '@shilpa.edu',
            'Password' => password_hash('TeX1978!', PASSWORD_BCRYPT),
            'user_type' => 'student',
        ]);

        $course = CourseManagementService::createCourse([
            'title' => 'CS990 The Art of Computer Programming',
            'status' => 'published',
        ]);
        $cId = (int)$course->C_ID;

        // 1. Start conversation with course focus
        $conv = AITutorService::startConversation((int)$student->id, $cId, null, 'Algorithmic Analysis');
        assert($conv->title === 'Algorithmic Analysis');
        assert((int)$conv->course_id === $cId);

        // 2. Ask question
        $assistantMsg = AITutorService::askTutor(
            (int)$conv->id,
            (int)$student->id,
            'Explain time complexity of binary search.'
        );

        assert($assistantMsg->isAssistant());
        assert(str_contains($assistantMsg->content, 'Complexity'));
        assert($assistantMsg->tokens_used > 0);

        // 3. Verify conversation message history
        $messages = $conv->messages();
        assert(count($messages) === 2);
        assert($messages[0]->isUser());
        assert($messages[1]->isAssistant());
    }

    public function testLessonSummarizationAndPracticeGeneration(): void {
        $course = CourseManagementService::createCourse([
            'title' => 'CS801 Modern Distributed Systems',
            'status' => 'published',
        ]);
        $cId = (int)$course->C_ID;

        $mod = CurriculumBuilderService::createModule($cId, [
            'title' => 'Module 1: Replication & Consensus',
        ]);

        $lesson = CurriculumBuilderService::createLesson((int)$mod->id, [
            'title' => 'Raft Protocol & State Machine Safety',
            'summary' => 'Formal leader election and uncommitted log entry truncations.',
        ]);
        $lId = (int)$lesson->id;

        // 1. Generate Instant Lesson Summary
        $summary = AITutorService::summarizeLesson($lId);
        assert(str_contains($summary, 'Executive Academic Summary'));
        assert(str_contains($summary, 'Key Takeaways'));

        // 2. Generate Practice Quiz
        $quizQuestions = AITutorService::generatePracticeQuiz($lId, 2);
        assert(count($quizQuestions) === 2);
        assert(isset($quizQuestions[0]['question']));
        assert(isset($quizQuestions[0]['options']));
        assert(isset($quizQuestions[0]['answer']));
        assert(isset($quizQuestions[0]['explanation']));
    }

    public function testAITutorControllerHttpWorkflows(): void {
        $student = User::create([
            'Full_Name' => 'John von Neumann',
            'Email' => 'neumann.' . uniqid() . '@shilpa.edu',
            'Password' => password_hash('GameTheory1!', PASSWORD_BCRYPT),
            'user_type' => 'student',
        ]);

        $course = CourseManagementService::createCourse([
            'title' => 'CS888 Quantum Cellular Automata',
            'status' => 'published',
        ]);
        $cId = (int)$course->C_ID;

        $mod = CurriculumBuilderService::createModule($cId, ['title' => 'Cellular Spaces']);
        $lesson = CurriculumBuilderService::createLesson((int)$mod->id, ['title' => 'Universal Constructor']);
        $lId = (int)$lesson->id;

        $this->actingAs($student);

        // 1. Render AI Tutor main workspace
        $indexRes = $this->get('/ai-tutor');
        $indexRes->assertStatus(200);
        $indexRes->assertSee('Shilpa AI Tutor');

        // 2. Chat API endpoint
        $chatRes = $this->post('/api/ai/chat', [
            '_csrf' => session()->csrfToken(),
            'course_id' => $cId,
            'prompt' => 'Give me a quiz on relational databases.',
        ]);
        $chatRes->assertStatus(200);
        $chatRes->assertSee('success');

        // 3. User conversations API
        $convsRes = $this->get('/api/ai/conversations');
        $convsRes->assertStatus(200);
        $convsRes->assertSee('conversations');

        // 4. Summarize lesson API
        $sumRes = $this->post('/api/ai/summarize-lesson', [
            '_csrf' => session()->csrfToken(),
            'lesson_id' => $lId,
        ]);
        $sumRes->assertStatus(200);
        $sumRes->assertSee('Executive Academic Summary');

        // 5. Generate practice API
        $practiceRes = $this->post('/api/ai/generate-practice', [
            '_csrf' => session()->csrfToken(),
            'lesson_id' => $lId,
            'count' => 2,
        ]);
        $practiceRes->assertStatus(200);
        $practiceRes->assertSee('questions');
    }
}
