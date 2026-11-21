<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Services\QuizAssessmentService;

class QuizController extends Controller {
    public function facultyIndex(Request $request, $id): Response {
        if ($auth = $this->checkFacultyAuthorization()) {
            return $auth;
        }

        $course = Course::find((int)$id);
        if (!$course) {
            session()->flash('error', 'Course not found.');
            return $this->redirect('/faculty/courses');
        }

        $quizzes = Quiz::forCourse((int)$course->C_ID, false);

        return $this->render('courses/quizzes/index', [
            'title' => 'Assessments & Question Banks — ' . htmlspecialchars((string)$course->C_NAME),
            'course' => $course,
            'quizzes' => $quizzes,
        ]);
    }

    public function store(Request $request, $id): Response {
        if ($auth = $this->checkFacultyAuthorization()) {
            return $auth;
        }

        $userId = (int)session()->get('user_id', 0);
        $courseId = (int)$id;

        try {
            $quiz = QuizAssessmentService::createQuiz($courseId, $userId, $request->all());

            if ($request->isAjax()) {
                return json(['success' => true, 'quiz' => $quiz->toArray()]);
            }

            session()->flash('success', "Assessment '{$quiz->title}' created.");
            return $this->redirect("/faculty/courses/{$courseId}/quizzes/{$quiz->id}/builder");
        } catch (\Throwable $e) {
            session()->flash('error', $e->getMessage());
            return $this->redirect("/faculty/courses/{$courseId}/quizzes");
        }
    }

    public function builder(Request $request, $courseId, $id): Response {
        if ($auth = $this->checkFacultyAuthorization()) {
            return $auth;
        }

        $course = Course::find((int)$courseId);
        $quiz = Quiz::find((int)$id);

        if (!$course || !$quiz) {
            session()->flash('error', 'Quiz not found.');
            return $this->redirect("/faculty/courses/{$courseId}/quizzes");
        }

        return $this->render('courses/quizzes/builder', [
            'title' => 'Question Bank Builder: ' . htmlspecialchars((string)$quiz->title),
            'course' => $course,
            'quiz' => $quiz,
            'questions' => $quiz->questions(),
        ]);
    }

    public function storeQuestion(Request $request, $courseId, $quizId): Response {
        if ($auth = $this->checkFacultyAuthorization()) {
            return $auth;
        }

        try {
            $q = QuizAssessmentService::addQuestion((int)$quizId, $request->all());

            if ($request->isAjax()) {
                return json(['success' => true, 'question' => $q->toArray()]);
            }

            session()->flash('success', 'Question added to assessment bank.');
        } catch (\Throwable $e) {
            session()->flash('error', $e->getMessage());
        }

        return $this->redirect("/faculty/courses/{$courseId}/quizzes/{$quizId}/builder");
    }

    public function show(Request $request, $courseId, $id): Response {
        $course = Course::find((int)$courseId);
        $quiz = Quiz::find((int)$id);

        if (!$course || !$quiz) {
            session()->flash('error', 'Assessment not found.');
            return $this->redirect("/courses/{$courseId}/workspace");
        }

        $userId = (int)session()->get('user_id', 0);
        $isEnrolled = $userId > 0 && Enrollment::isEnrolled($userId, (int)$courseId);
        $attempts = $userId > 0 ? $quiz->attemptsForStudent($userId) : [];

        return $this->render('courses/quizzes/show', [
            'title' => htmlspecialchars((string)$quiz->title) . ' — ' . htmlspecialchars((string)$course->C_NAME),
            'course' => $course,
            'quiz' => $quiz,
            'isEnrolled' => $isEnrolled,
            'attempts' => $attempts,
        ]);
    }

    public function start(Request $request, $courseId, $id): Response {
        if (!session()->has('user')) {
            session()->flash('error', 'Please sign in to take this assessment.');
            return $this->redirect('/login');
        }

        $userId = (int)session()->get('user_id', 0);

        try {
            $attempt = QuizAssessmentService::startAttempt((int)$id, $userId);
            return $this->redirect("/courses/{$courseId}/quizzes/{$id}/take/{$attempt->id}");
        } catch (\Throwable $e) {
            session()->flash('error', $e->getMessage());
            return $this->redirect("/courses/{$courseId}/quizzes/{$id}");
        }
    }

    public function take(Request $request, $courseId, $id, $attemptId): Response {
        if (!session()->has('user')) {
            return $this->redirect('/login');
        }

        $course = Course::find((int)$courseId);
        $quiz = Quiz::find((int)$id);
        $attempt = QuizAttempt::find((int)$attemptId);

        if (!$course || !$quiz || !$attempt || (int)$attempt->student_id !== (int)session()->get('user_id', 0)) {
            session()->flash('error', 'Attempt not found or access restricted.');
            return $this->redirect("/courses/{$courseId}/workspace");
        }

        if ($attempt->submitted_at !== null) {
            return $this->redirect("/courses/{$courseId}/quizzes/{$id}/result/{$attemptId}");
        }

        return $this->render('courses/quizzes/take', [
            'title' => 'Exam Session: ' . htmlspecialchars((string)$quiz->title),
            'course' => $course,
            'quiz' => $quiz,
            'attempt' => $attempt,
            'questions' => $quiz->questions(),
        ]);
    }

    public function submit(Request $request, $courseId, $id, $attemptId): Response {
        if (!session()->has('user')) {
            return $this->redirect('/login');
        }

        $answers = (array)$request->input('answers', []);

        try {
            QuizAssessmentService::submitAttempt((int)$attemptId, $answers);
            session()->flash('success', 'Assessment completed and submitted.');
            return $this->redirect("/courses/{$courseId}/quizzes/{$id}/result/{$attemptId}");
        } catch (\Throwable $e) {
            session()->flash('error', $e->getMessage());
            return $this->redirect("/courses/{$courseId}/quizzes/{$id}");
        }
    }

    public function result(Request $request, $courseId, $id, $attemptId): Response {
        if (!session()->has('user')) {
            return $this->redirect('/login');
        }

        $course = Course::find((int)$courseId);
        $quiz = Quiz::find((int)$id);
        $attempt = QuizAttempt::find((int)$attemptId);

        if (!$course || !$quiz || !$attempt) {
            session()->flash('error', 'Result not found.');
            return $this->redirect("/courses/{$courseId}/workspace");
        }

        return $this->render('courses/quizzes/result', [
            'title' => 'Assessment Results — ' . htmlspecialchars((string)$quiz->title),
            'course' => $course,
            'quiz' => $quiz,
            'attempt' => $attempt,
            'questions' => $quiz->questions(),
        ]);
    }

    private function checkFacultyAuthorization(): ?Response {
        if (!session()->has('user')) {
            session()->flash('error', 'Please sign in to access assessment management.');
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
