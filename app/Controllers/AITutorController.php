<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Models\AIConversation;
use App\Models\Course;
use App\Models\Lesson;
use App\Services\AITutorService;

class AITutorController extends Controller {
    public function index(Request $request): Response {
        if (!session()->has('user')) {
            session()->flash('error', 'Please sign in to access the Shilpa AI Learning Assistant.');
            return $this->redirect('/login');
        }

        $userId = (int)session()->get('user_id', 0);
        $courseId = $request->query('course_id') ? (int)$request->query('course_id') : null;
        $lessonId = $request->query('lesson_id') ? (int)$request->query('lesson_id') : null;

        $conversations = AITutorService::getUserConversations($userId);

        $activeConversation = null;
        $activeMessages = [];

        if ($request->query('conversation_id')) {
            $convId = (int)$request->query('conversation_id');
            $c = AIConversation::find($convId);
            if ($c && (int)$c->user_id === $userId) {
                $activeConversation = $c;
                $activeMessages = $c->messages();
            }
        }

        if (!$activeConversation && !empty($conversations)) {
            $activeConversation = $conversations[0];
            $activeMessages = $activeConversation->messages();
        }

        $focusedCourse = $courseId ? Course::find($courseId) : ($activeConversation ? $activeConversation->course() : null);
        $focusedLesson = $lessonId ? Lesson::find($lessonId) : ($activeConversation ? $activeConversation->lesson() : null);

        return $this->render('ai/tutor', [
            'title' => 'Shilpa AI Tutor & Autonomous Learning Assistant — SHILPA LMS',
            'conversations' => $conversations,
            'activeConversation' => $activeConversation,
            'activeMessages' => $activeMessages,
            'focusedCourse' => $focusedCourse,
            'focusedLesson' => $focusedLesson,
        ]);
    }

    public function chat(Request $request): Response {
        if (!session()->has('user')) {
            return json(['error' => 'Unauthenticated'], 401);
        }

        $userId = (int)session()->get('user_id', 0);
        $prompt = (string)$request->input('prompt', '');
        $convId = $request->input('conversation_id') ? (int)$request->input('conversation_id') : 0;
        $courseId = $request->input('course_id') ? (int)$request->input('course_id') : null;
        $lessonId = $request->input('lesson_id') ? (int)$request->input('lesson_id') : null;

        try {
            if ($convId <= 0) {
                $conv = AITutorService::startConversation($userId, $courseId, $lessonId, substr($prompt, 0, 40));
                $convId = (int)$conv->id;
            }

            $assistantMsg = AITutorService::askTutor($convId, $userId, $prompt);

            return json([
                'success' => true,
                'conversation_id' => $convId,
                'response' => $assistantMsg->toArray(),
            ]);
        } catch (\Throwable $e) {
            return json(['error' => $e->getMessage()], 422);
        }
    }

    public function conversations(Request $request): Response {
        if (!session()->has('user')) {
            return json(['error' => 'Unauthenticated'], 401);
        }

        $userId = (int)session()->get('user_id', 0);
        $list = AITutorService::getUserConversations($userId);

        return json([
            'conversations' => array_map(fn($c) => $c->toArray(), $list),
        ]);
    }

    public function showConversation(Request $request, $id): Response {
        if (!session()->has('user')) {
            return json(['error' => 'Unauthenticated'], 401);
        }

        $userId = (int)session()->get('user_id', 0);
        $conv = AIConversation::find((int)$id);

        if (!$conv || (int)$conv->user_id !== $userId) {
            return json(['error' => 'Not found'], 404);
        }

        return json([
            'conversation' => $conv->toArray(),
            'messages' => array_map(fn($m) => $m->toArray(), $conv->messages()),
        ]);
    }

    public function summarizeLesson(Request $request): Response {
        if (!session()->has('user')) {
            return json(['error' => 'Unauthenticated'], 401);
        }

        $lessonId = (int)$request->input('lesson_id', 0);
        try {
            $summary = AITutorService::summarizeLesson($lessonId);
            return json(['success' => true, 'summary' => $summary]);
        } catch (\Throwable $e) {
            return json(['error' => $e->getMessage()], 422);
        }
    }

    public function generatePractice(Request $request): Response {
        if (!session()->has('user')) {
            return json(['error' => 'Unauthenticated'], 401);
        }

        $lessonId = (int)$request->input('lesson_id', 0);
        $count = (int)$request->input('count', 3);

        try {
            $questions = AITutorService::generatePracticeQuiz($lessonId, $count);
            return json(['success' => true, 'questions' => $questions]);
        } catch (\Throwable $e) {
            return json(['error' => $e->getMessage()], 422);
        }
    }
}
