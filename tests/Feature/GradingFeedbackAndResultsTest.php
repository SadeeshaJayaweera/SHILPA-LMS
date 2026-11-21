<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use App\Models\CourseGrade;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use App\Services\AssignmentManagementService;
use App\Services\CourseManagementService;
use App\Services\EnrollmentService;
use App\Services\GradingAndResultsService;
use App\Services\QuizAssessmentService;
use App\Testing\TestCase;

class GradingFeedbackAndResultsTest extends TestCase {
    public function testSubmissionGradingAndRecalculation(): void {
        $faculty = User::create([
            'Full_Name' => 'Dr. Robert Oppenheimer',
            'Email' => 'robert.oppenheimer.' . uniqid() . '@shilpa.edu',
            'Password' => password_hash('Pass1234!', PASSWORD_BCRYPT),
            'user_type' => 'lecturer',
        ]);

        $student = User::create([
            'Full_Name' => 'Alice Quantum',
            'Email' => 'alice.quantum.' . uniqid() . '@shilpa.edu',
            'Password' => password_hash('Pass1234!', PASSWORD_BCRYPT),
            'user_type' => 'student',
        ]);

        $course = CourseManagementService::createCourse([
            'title' => 'PHYS901 Quantum Field Theory',
            'status' => 'published',
            'instructor' => 'Dr. Robert Oppenheimer',
        ]);
        $cId = (int)$course->C_ID;

        EnrollmentService::enroll((int)$student->id, $cId);

        $assignment = AssignmentManagementService::createAssignment($cId, (int)$faculty->id, [
            'title' => 'Path Integrals in Scalar Field Theory',
            'max_score' => 100,
            'is_published' => 1,
        ]);

        $submission = AssignmentManagementService::submitAssignment((int)$assignment->id, (int)$student->id, [
            'submission_type' => 'text',
            'content' => 'Feynman diagrams and path integral derivations.',
        ]);

        // Evaluate and grade submission
        $graded = GradingAndResultsService::gradeSubmission(
            (int)$submission->id,
            (int)$faculty->id,
            95,
            'Outstanding rigor and elegant Feynman diagram derivation.'
        );

        assert($graded->isGraded());
        assert((int)$graded->score === 95);
        assert($graded->feedback === 'Outstanding rigor and elegant Feynman diagram derivation.');

        // Verify CourseGrade recalculation
        $cg = CourseGrade::query()
            ->where('course_id', $cId)
            ->where('student_id', (int)$student->id)
            ->first();

        assert($cg !== null);
        assert((int)$cg['total_score'] === 95);
        assert((int)$cg['max_score'] === 100);
        assert((int)$cg['percentage'] === 95);
        assert($cg['letter_grade'] === 'A+');
    }

    public function testCourseGradebookMatrixRetrieval(): void {
        $course = CourseManagementService::createCourse([
            'title' => 'MATH702 Advanced Differential Geometry',
            'status' => 'published',
        ]);
        $cId = (int)$course->C_ID;

        $student1 = User::create([
            'Full_Name' => 'Bernhard Riemann',
            'Email' => 'riemann.' . uniqid() . '@shilpa.edu',
            'Password' => password_hash('Pass1234!', PASSWORD_BCRYPT),
            'user_type' => 'student',
        ]);

        EnrollmentService::enroll((int)$student1->id, $cId);

        $asgn = AssignmentManagementService::createAssignment($cId, 1, [
            'title' => 'Riemannian Curvature Tensor Problem Set',
            'max_score' => 50,
            'is_published' => 1,
        ]);

        $sub = AssignmentManagementService::submitAssignment((int)$asgn->id, (int)$student1->id, [
            'submission_type' => 'text',
            'content' => 'Bianchi identities verified.',
        ]);

        GradingAndResultsService::gradeSubmission((int)$sub->id, 1, 45, 'Flawless tensor proof.');

        $gradebook = GradingAndResultsService::getCourseGradebook($cId);

        assert(count($gradebook['assignments']) === 1);
        assert(count($gradebook['studentsData']) >= 1);

        $studentEntry = null;
        foreach ($gradebook['studentsData'] as $entry) {
            if ((int)$entry['student']->id === (int)$student1->id) {
                $studentEntry = $entry;
                break;
            }
        }

        assert($studentEntry !== null);
        assert((int)$studentEntry['grade']->percentage === 90);
        assert($studentEntry['grade']->letter_grade === 'A+');
        assert($studentEntry['assignmentScores'][(int)$asgn->id]['score'] === 45);
    }

    public function testPublishCourseGradesAndTranscript(): void {
        $course = CourseManagementService::createCourse([
            'title' => 'BIO601 Molecular Biophysics',
            'status' => 'published',
        ]);
        $cId = (int)$course->C_ID;

        $student = User::create([
            'Full_Name' => 'Rosalind Franklin',
            'Email' => 'rosalind.' . uniqid() . '@shilpa.edu',
            'Password' => password_hash('Pass1234!', PASSWORD_BCRYPT),
            'user_type' => 'student',
        ]);

        EnrollmentService::enroll((int)$student->id, $cId);

        $asgn = AssignmentManagementService::createAssignment($cId, 1, [
            'title' => 'X-ray Crystallography of DNA',
            'max_score' => 100,
            'is_published' => 1,
        ]);

        $sub = AssignmentManagementService::submitAssignment((int)$asgn->id, (int)$student->id, [
            'submission_type' => 'text',
            'content' => 'Photo 51 diffraction analysis.',
        ]);

        GradingAndResultsService::gradeSubmission((int)$sub->id, 1, 88, 'Historic helical diffraction clarity.');

        // Before publishing, transcript should not include unpublished grades
        $transcriptBefore = GradingAndResultsService::getStudentTranscript((int)$student->id);
        $found = false;
        foreach ($transcriptBefore['records'] as $r) {
            if ((int)$r['course']->C_ID === $cId) {
                $found = true;
            }
        }
        assert(!$found);

        // Publish grades
        GradingAndResultsService::publishCourseGrades($cId);

        // After publishing, transcript contains record with GPA
        $transcriptAfter = GradingAndResultsService::getStudentTranscript((int)$student->id);
        $publishedRecord = null;
        foreach ($transcriptAfter['records'] as $r) {
            if ((int)$r['course']->C_ID === $cId) {
                $publishedRecord = $r;
                break;
            }
        }

        assert($publishedRecord !== null);
        assert($publishedRecord['grade']->letter_grade === 'A');
        assert($transcriptAfter['totalCredits'] >= 3);
        assert($transcriptAfter['cumulativeGpa'] > 0.0);
    }

    public function testFacultyAndStudentHttpWorkflows(): void {
        $faculty = User::create([
            'Full_Name' => 'Prof. Richard Feynman',
            'Email' => 'feynman.' . uniqid() . '@shilpa.edu',
            'Password' => password_hash('Physics123!', PASSWORD_BCRYPT),
            'user_type' => 'lecturer',
        ]);

        $student = User::create([
            'Full_Name' => 'Murray Gell-Mann',
            'Email' => 'gellmann.' . uniqid() . '@shilpa.edu',
            'Password' => password_hash('Quarks123!', PASSWORD_BCRYPT),
            'user_type' => 'student',
        ]);

        $course = CourseManagementService::createCourse([
            'title' => 'PHYS805 Quantum Electrodynamics',
            'status' => 'published',
            'instructor' => 'Prof. Richard Feynman',
        ]);
        $cId = (int)$course->C_ID;

        EnrollmentService::enroll((int)$student->id, $cId);

        $asgn = AssignmentManagementService::createAssignment($cId, (int)$faculty->id, [
            'title' => 'Anomalous Magnetic Moment of the Electron',
            'max_score' => 100,
            'is_published' => 1,
        ]);

        $sub = AssignmentManagementService::submitAssignment((int)$asgn->id, (int)$student->id, [
            'submission_type' => 'text',
            'content' => 'Two-loop radiative correction calculation.',
        ]);

        // 1. Faculty views Gradebook
        $this->actingAs($faculty);
        $res = $this->get("/faculty/courses/{$cId}/gradebook");
        $res->assertStatus(200);
        $res->assertSee('Academic Gradebook &amp; Marks Matrix');
        $res->assertSee('Murray Gell-Mann');

        // 2. Faculty grades submission via POST
        $gradeRes = $this->post("/faculty/submissions/{$sub->id}/grade", [
            '_csrf' => session()->csrfToken(),
            'score' => 92,
            'feedback' => 'Exceptional precision in higher-order loop integration.',
        ]);
        $gradeRes->assertStatus(302);

        // 3. Faculty publishes grades
        $publishRes = $this->post("/faculty/courses/{$cId}/gradebook/publish", [
            '_csrf' => session()->csrfToken(),
        ]);
        $publishRes->assertStatus(302);

        // 4. Student views their official grades and transcript
        $this->actingAs($student);
        $studentRes = $this->get('/student/grades');
        $studentRes->assertStatus(200);
        $studentRes->assertSee('My Academic Performance &amp; Official Transcripts');
        $studentRes->assertSee('PHYS805 Quantum Electrodynamics');
        $studentRes->assertSee('A+');
    }
}
