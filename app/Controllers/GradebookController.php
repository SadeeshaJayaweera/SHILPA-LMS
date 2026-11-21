<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Models\Course;
use App\Models\CourseGrade;
use App\Services\GradingAndResultsService;

class GradebookController extends Controller {
    public function facultyGradebook(Request $request, $id): Response {
        if ($auth = $this->checkFacultyAuthorization()) {
            return $auth;
        }

        $courseId = (int)$id;
        $course = Course::find($courseId);
        if (!$course) {
            session()->flash('error', 'Course not found.');
            return $this->redirect('/faculty/courses');
        }

        $gradebook = GradingAndResultsService::getCourseGradebook($courseId);

        return $this->render('courses/gradebook/index', [
            'title' => 'Academic Gradebook & Marks Matrix — ' . htmlspecialchars((string)$course->C_NAME),
            'course' => $course,
            'assignments' => $gradebook['assignments'],
            'quizzes' => $gradebook['quizzes'],
            'studentsData' => $gradebook['studentsData'],
        ]);
    }

    public function gradeSubmission(Request $request, $id): Response {
        if ($auth = $this->checkFacultyAuthorization()) {
            return $auth;
        }

        $graderId = (int)session()->get('user_id', 0);
        $score = (int)$request->input('score', 0);
        $feedback = (string)$request->input('feedback', '');

        try {
            $sub = GradingAndResultsService::gradeSubmission((int)$id, $graderId, $score, $feedback);

            if ($request->isAjax()) {
                return json([
                    'success' => true,
                    'message' => 'Submission graded successfully.',
                    'submission' => $sub->toArray(),
                ]);
            }

            session()->flash('success', 'Student submission graded.');
            $asgn = $sub->assignment();
            return $this->redirect("/faculty/courses/{$asgn->course_id}/gradebook");
        } catch (\Throwable $e) {
            session()->flash('error', $e->getMessage());
            return $this->redirect('/faculty/courses');
        }
    }

    public function publishGrades(Request $request, $id): Response {
        if ($auth = $this->checkFacultyAuthorization()) {
            return $auth;
        }

        $courseId = (int)$id;
        GradingAndResultsService::publishCourseGrades($courseId);

        if ($request->isAjax()) {
            return json(['success' => true, 'message' => 'Grades published to official student transcripts.']);
        }

        session()->flash('success', 'Course grades published to official student transcripts.');
        return $this->redirect("/faculty/courses/{$courseId}/gradebook");
    }

    public function studentGrades(Request $request): Response {
        if (!session()->has('user')) {
            session()->flash('error', 'Please sign in to view your grades.');
            return $this->redirect('/login');
        }

        $userId = (int)session()->get('user_id', 0);
        $transcript = GradingAndResultsService::getStudentTranscript($userId);

        return $this->render('student/grades/index', [
            'title' => 'My Academic Performance & Official Transcripts',
            'records' => $transcript['records'],
            'totalCredits' => $transcript['totalCredits'],
            'cumulativeGpa' => $transcript['cumulativeGpa'],
        ]);
    }

    private function checkFacultyAuthorization(): ?Response {
        if (!session()->has('user')) {
            session()->flash('error', 'Please sign in to access grading.');
            return $this->redirect('/login');
        }

        $userRole = (string)session()->get('user_role', '');
        $isFacultyOrAdmin = in_array(strtolower($userRole), ['l', 'lecturer', 'instructor', 'teacher', 'a', 'admin', 'administrator', 'superadmin'], true);

        if (!$isFacultyOrAdmin) {
            session()->flash('error', 'Faculty or administrative privileges required.');
            return $this->redirect('/workspace');
        }

        return null;
    }
}
