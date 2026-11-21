<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use App\Models\User;
use App\Services\AssignmentManagementService;
use App\Services\CourseManagementService;
use App\Services\EnrollmentService;
use App\Testing\TestCase;

class AssignmentAndSubmissionManagementTest extends TestCase {
    public function testAssignmentCreationAndManagement(): void {
        $course = CourseManagementService::createCourse([
            'title' => 'CS701 Distributed Storage Engine',
            'status' => 'published',
        ]);
        $cId = (int)$course->C_ID;

        // Faculty creates assignment
        $dueDate = date('Y-m-d H:i:s', time() + 86400 * 7); // 7 days from now
        $assignment = AssignmentManagementService::createAssignment($cId, 1, [
            'title' => 'Project 1: Raft Consensus Engine',
            'description' => 'Implement leader election and log replication.',
            'max_score' => 100,
            'due_at' => $dueDate,
            'is_published' => 1,
        ]);

        assert($assignment->title === 'Project 1: Raft Consensus Engine');
        assert((int)$assignment->max_score === 100);
        assert($assignment->isOverdue() === false);

        $courseAssignments = Assignment::forCourse($cId);
        assert(count($courseAssignments) === 1);

        // Update assignment
        $updated = AssignmentManagementService::updateAssignment((int)$assignment->id, [
            'max_score' => 120,
        ]);
        assert((int)$updated->max_score === 120);
    }

    public function testStudentAssignmentSubmissionAndDeadlineEnforcement(): void {
        $course = CourseManagementService::createCourse([
            'title' => 'CS702 High Performance Compilers',
            'status' => 'published',
        ]);
        $cId = (int)$course->C_ID;

        $student = User::create([
            'Full_Name' => 'Grace Hopper',
            'Email' => 'grace.hopper.' . time() . '@example.com',
            'Password' => password_hash('Pass123!', PASSWORD_BCRYPT),
            'user_type' => 'student',
        ]);
        $sId = (int)$student->UserId;

        // 1. On-time assignment
        $onTimeAsgn = AssignmentManagementService::createAssignment($cId, 1, [
            'title' => 'Lab 1: Lexical Analyzer in Lex/Yacc',
            'due_at' => date('Y-m-d H:i:s', time() + 86400 * 3),
            'max_score' => 50,
        ]);

        // Unenrolled student attempt throws exception
        $threwUnenrolled = false;
        try {
            AssignmentManagementService::submitAssignment((int)$onTimeAsgn->id, $sId, [
                'submission_text' => 'My solution repository: https://github.com/example/lex'
            ]);
        } catch (\Throwable $e) {
            $threwUnenrolled = true;
            assert(str_contains($e->getMessage(), 'enrolled'));
        }
        assert($threwUnenrolled, "Unenrolled student must not be allowed to submit");

        // Enroll student
        EnrollmentService::enroll($sId, $cId);

        // Successful on-time submission
        $sub1 = AssignmentManagementService::submitAssignment((int)$onTimeAsgn->id, $sId, [
            'submission_text' => 'My solution repository: https://github.com/example/lex'
        ]);
        assert($sub1->status === 'submitted', "On-time submission must have status 'submitted'");
        assert($sub1->isLate() === false);

        // 2. Overdue assignment -> marks submission as 'late'
        $overdueAsgn = AssignmentManagementService::createAssignment($cId, 1, [
            'title' => 'Lab 0: Environment Setup (Past Deadline)',
            'due_at' => date('Y-m-d H:i:s', time() - 3600), // 1 hour ago
            'max_score' => 20,
        ]);
        assert($overdueAsgn->isOverdue() === true, "Assignment should be overdue");

        $sub2 = AssignmentManagementService::submitAssignment((int)$overdueAsgn->id, $sId, [
            'submission_text' => 'Late submission of environment checklist.'
        ]);
        assert($sub2->status === 'late', "Past-deadline submission must be marked 'late'");
        assert($sub2->isLate() === true);

        // 3. Faculty grading
        $gradedSub = AssignmentManagementService::gradeSubmission((int)$sub1->id, 1, 48, 'Excellent lexical state machine implementation.');
        assert($gradedSub->status === 'graded');
        assert((int)$gradedSub->score === 48);
        assert(str_contains($gradedSub->feedback, 'Excellent'));
    }

    public function testControllerEndpointsAndAccess(): void {
        $course = CourseManagementService::createCourse([
            'title' => 'CS703 Distributed Cloud Infrastructure',
            'status' => 'published',
        ]);
        $cId = (int)$course->C_ID;

        $assignment = AssignmentManagementService::createAssignment($cId, 1, [
            'title' => 'Milestone 1: Multi-Region Cluster',
            'max_score' => 100,
        ]);

        // Unauthenticated access to faculty assignment management redirects
        session()->destroy();
        $resp = $this->get("/faculty/courses/{$cId}/assignments");
        $resp->assertRedirect('/login');

        // Student viewing assignment
        $student = User::create([
            'Full_Name' => 'Ada Lovelace',
            'Email' => 'ada.lovelace.' . time() . '@example.com',
            'Password' => password_hash('Pass123!', PASSWORD_BCRYPT),
            'user_type' => 'student',
        ]);
        $sId = (int)$student->UserId;
        EnrollmentService::enroll($sId, $cId);

        session()->set('user', ['id' => $sId, 'name' => 'Ada Lovelace']);
        session()->set('user_id', $sId);
        session()->set('user_role', 'student');

        $viewResp = $this->get("/courses/{$cId}/assignments/{$assignment->id}");
        $viewResp->assertOk();
        $viewResp->assertSee('Milestone 1: Multi-Region Cluster');

        // Student submits via POST route
        $submitResp = $this->post("/courses/{$cId}/assignments/{$assignment->id}/submit", [
            'submission_text' => 'Cluster setup scripts verified across us-east and eu-west.',
        ]);
        $submitResp->assertRedirect("/courses/{$cId}/assignments/{$assignment->id}");

        $submission = $assignment->submissionForStudent($sId);
        assert($submission !== null, "Submission must exist in database");
        assert(str_contains($submission->submission_text, 'Cluster setup scripts'));
    }
}
