<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizQuestion;
use App\Models\User;
use App\Services\CourseManagementService;
use App\Services\EnrollmentService;
use App\Services\QuizAssessmentService;
use App\Testing\TestCase;

class QuizAndQuestionBankSystemTest extends TestCase {
    public function testQuizCreationAndQuestionBankManagement(): void {
        $course = CourseManagementService::createCourse([
            'title' => 'CS801 Advanced Algorithms & Complexity',
            'status' => 'published',
        ]);
        $cId = (int)$course->C_ID;

        // 1. Create Quiz
        $quiz = QuizAssessmentService::createQuiz($cId, 1, [
            'title' => 'Midterm Assessment: Graph Algorithms & P vs NP',
            'description' => 'Timed theoretical examination.',
            'time_limit_minutes' => 45,
            'passing_score_percent' => 75,
            'max_attempts' => 2,
        ]);

        assert($quiz->title === 'Midterm Assessment: Graph Algorithms & P vs NP');
        assert((int)$quiz->time_limit_minutes === 45);
        assert((int)$quiz->passing_score_percent === 75);

        // 2. Add Questions
        $q1 = QuizAssessmentService::addQuestion((int)$quiz->id, [
            'question_type' => 'single_choice',
            'question_text' => 'What is the runtime of Dijkstra algorithm with a Fibonacci heap?',
            'options' => ['A' => 'O(V^2)', 'B' => 'O(E + V log V)', 'C' => 'O(E log V)'],
            'correct_answer' => 'B',
            'points' => 20,
        ]);

        $q2 = QuizAssessmentService::addQuestion((int)$quiz->id, [
            'question_type' => 'true_false',
            'question_text' => 'Every problem in NP can be solved in polynomial time on a deterministic Turing machine.',
            'options' => [],
            'correct_answer' => 'False',
            'points' => 10,
        ]);

        $q3 = QuizAssessmentService::addQuestion((int)$quiz->id, [
            'question_type' => 'short_answer',
            'question_text' => 'Which algorithm finds strongly connected components in linear time?',
            'options' => [],
            'correct_answer' => 'Tarjan',
            'points' => 20,
        ]);

        $questions = $quiz->questions();
        assert(count($questions) === 3, "Expected 3 quiz questions");
        assert($quiz->totalPoints() === 50, "Total points must equal 20 + 10 + 20 = 50");
    }

    public function testStudentQuizAttemptAndAutomaticScoring(): void {
        $course = CourseManagementService::createCourse([
            'title' => 'CS802 Distributed Consensus and Replication',
            'status' => 'published',
        ]);
        $cId = (int)$course->C_ID;

        $quiz = QuizAssessmentService::createQuiz($cId, 1, [
            'title' => 'Raft Protocol Mastery Quiz',
            'passing_score_percent' => 70,
            'max_attempts' => 2,
        ]);

        $q1 = QuizAssessmentService::addQuestion((int)$quiz->id, [
            'question_type' => 'single_choice',
            'question_text' => 'How many servers must respond to form a quorum in a cluster of 5 nodes?',
            'options' => ['A' => '2', 'B' => '3', 'C' => '4'],
            'correct_answer' => 'B',
            'points' => 10,
        ]);

        $q2 = QuizAssessmentService::addQuestion((int)$quiz->id, [
            'question_type' => 'true_false',
            'question_text' => 'Followers reject AppendEntries RPCs if the term is smaller than their current term.',
            'options' => [],
            'correct_answer' => 'True',
            'points' => 10,
        ]);

        $student = User::create([
            'Full_Name' => 'Alan Turing',
            'Email' => 'alan.turing.' . time() . '@example.com',
            'Password' => password_hash('Pass123!', PASSWORD_BCRYPT),
            'user_type' => 'student',
        ]);
        $sId = (int)$student->UserId;

        // Unenrolled student attempt throws exception
        $threwUnenrolled = false;
        try {
            QuizAssessmentService::startAttempt((int)$quiz->id, $sId);
        } catch (\Throwable $e) {
            $threwUnenrolled = true;
            assert(str_contains($e->getMessage(), 'enrolled'));
        }
        assert($threwUnenrolled, "Unenrolled student cannot take assessment");

        EnrollmentService::enroll($sId, $cId);

        // 1. Attempt 1: 100% Score -> Pass
        $att1 = QuizAssessmentService::startAttempt((int)$quiz->id, $sId);
        assert((int)$att1->attempt_number === 1);
        assert($att1->submitted_at === null);

        $graded1 = QuizAssessmentService::submitAttempt((int)$att1->id, [
            (int)$q1->id => 'B',
            (int)$q2->id => 'True',
        ]);

        assert((int)$graded1->score_achieved === 20, "Must achieve 20/20 points");
        assert($graded1->percentage() === 100);
        assert($graded1->isPassed() === true);
        assert($graded1->submitted_at !== null);

        // 2. Attempt 2: 50% Score -> Fail (below 70%)
        $att2 = QuizAssessmentService::startAttempt((int)$quiz->id, $sId);
        assert((int)$att2->attempt_number === 2);

        $graded2 = QuizAssessmentService::submitAttempt((int)$att2->id, [
            (int)$q1->id => 'A', // Wrong
            (int)$q2->id => 'True', // Correct
        ]);

        assert((int)$graded2->score_achieved === 10);
        assert($graded2->percentage() === 50);
        assert($graded2->isPassed() === false);

        // 3. Attempt 3: Exceeds max_attempts (2) -> Throws exception
        $threwMaxAttempts = false;
        try {
            QuizAssessmentService::startAttempt((int)$quiz->id, $sId);
        } catch (\Throwable $e) {
            $threwMaxAttempts = true;
            assert(str_contains($e->getMessage(), 'Maximum'));
        }
        assert($threwMaxAttempts, "Must block attempt beyond max attempts allowed");
    }

    public function testQuizControllerEndpoints(): void {
        $course = CourseManagementService::createCourse([
            'title' => 'CS803 Software Architecture Assessment',
            'status' => 'published',
        ]);
        $cId = (int)$course->C_ID;

        $quiz = QuizAssessmentService::createQuiz($cId, 1, [
            'title' => 'Architecture Patterns Quiz',
            'passing_score_percent' => 60,
            'max_attempts' => 3,
        ]);

        $q = QuizAssessmentService::addQuestion((int)$quiz->id, [
            'question_type' => 'true_false',
            'question_text' => 'CQRS separates read and update operations for a data store.',
            'correct_answer' => 'True',
            'points' => 10,
        ]);

        $student = User::create([
            'Full_Name' => 'Katherine Johnson',
            'Email' => 'katherine.j.' . time() . '@example.com',
            'Password' => password_hash('Pass123!', PASSWORD_BCRYPT),
            'user_type' => 'student',
        ]);
        $sId = (int)$student->UserId;
        EnrollmentService::enroll($sId, $cId);

        session()->set('user', ['id' => $sId, 'name' => 'Katherine Johnson']);
        session()->set('user_id', $sId);
        session()->set('user_role', 'student');

        // View quiz overview
        $showResp = $this->get("/courses/{$cId}/quizzes/{$quiz->id}");
        $showResp->assertOk();
        $showResp->assertSee('Architecture Patterns Quiz');

        // Start attempt
        $startResp = $this->post("/courses/{$cId}/quizzes/{$quiz->id}/start");
        $startResp->assertRedirect(); // redirects to take session
        $location = $startResp->getHeader('Location');
        assert(str_contains((string)$location, '/take/'));

        // Take view
        $takeResp = $this->get((string)$location);
        $takeResp->assertOk();
        $takeResp->assertSee('CQRS separates read and update');

        // Extract attempt ID from location
        preg_match('#/take/(\d+)$#', (string)$location, $m);
        $attemptId = (int)$m[1];

        // Submit attempt
        $submitResp = $this->post("/courses/{$cId}/quizzes/{$quiz->id}/take/{$attemptId}", [
            'answers' => [(int)$q->id => 'True'],
        ]);
        $submitResp->assertRedirect("/courses/{$cId}/quizzes/{$quiz->id}/result/{$attemptId}");

        // Result view
        $resultResp = $this->get("/courses/{$cId}/quizzes/{$quiz->id}/result/{$attemptId}");
        $resultResp->assertOk();
        $resultResp->assertSee('Assessment Passed!');
    }
}
