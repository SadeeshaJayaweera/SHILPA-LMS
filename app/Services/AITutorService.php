<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\AIConversation;
use App\Models\AIMessage;
use App\Models\Course;
use App\Models\Lesson;
use App\Security\SecurityAuditLog;
use App\Services\AI\AIManager;
use InvalidArgumentException;
use RuntimeException;

class AITutorService {
    public static function startConversation(
        int $userId,
        ?int $courseId = null,
        ?int $lessonId = null,
        ?string $title = null
    ): AIConversation {
        $now = date('Y-m-d H:i:s');
        $defaultTitle = 'Academic Consultation';
        if ($courseId) {
            $course = Course::find($courseId);
            if ($course) {
                $defaultTitle = "Tutor: " . (string)$course->C_NAME;
            }
        }

        $conv = AIConversation::create([
            'user_id' => $userId,
            'course_id' => $courseId,
            'lesson_id' => $lessonId,
            'title' => $title ? trim($title) : $defaultTitle,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        SecurityAuditLog::log('ai.conversation.started', 'info', $userId, null, [
            'conversation_id' => $conv->id,
            'course_id' => $courseId,
            'lesson_id' => $lessonId,
        ]);

        return $conv;
    }

    public static function askTutor(int $conversationId, int $userId, string $prompt): AIMessage {
        $cleanPrompt = trim($prompt);
        if ($cleanPrompt === '') {
            throw new InvalidArgumentException('Prompt cannot be empty.');
        }

        $conv = AIConversation::find($conversationId);
        if (!$conv || (int)$conv->user_id !== $userId) {
            throw new RuntimeException("Conversation not found or unauthorized.");
        }

        $now = date('Y-m-d H:i:s');

        // 1. Store User Message
        AIMessage::create([
            'conversation_id' => $conversationId,
            'role' => 'user',
            'content' => $cleanPrompt,
            'tokens_used' => (int)ceil(strlen($cleanPrompt) / 4),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 2. Build message context history
        $rawMessages = $conv->messages();
        $formattedMessages = [];
        foreach ($rawMessages as $m) {
            $formattedMessages[] = [
                'role' => (string)$m->role,
                'content' => (string)$m->content,
            ];
        }

        // 3. Assemble academic context
        $options = [];
        if ($conv->course()) {
            $options['course_title'] = (string)$conv->course()->C_NAME;
        }
        if ($conv->lesson()) {
            $options['lesson_title'] = (string)$conv->lesson()->title;
        }

        // 4. Generate AI completion
        $provider = AIManager::provider();
        $response = $provider->chat($formattedMessages, $options);

        // 5. Store Assistant Message
        $assistantMsg = AIMessage::create([
            'conversation_id' => $conversationId,
            'role' => 'assistant',
            'content' => $response['content'],
            'tokens_used' => $response['tokens_used'],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // Touch conversation updated_at
        $conv->updated_at = date('Y-m-d H:i:s');
        $conv->save();

        SecurityAuditLog::log('ai.tutor.interaction', 'info', $userId, null, [
            'conversation_id' => $conversationId,
            'tokens_used' => $response['tokens_used'],
        ]);

        return $assistantMsg;
    }

    public static function summarizeLesson(int $lessonId): string {
        $lesson = Lesson::find($lessonId);
        if (!$lesson) {
            throw new RuntimeException("Lesson not found: $lessonId");
        }

        // Collect lesson content and block text
        $content = (string)$lesson->summary;
        $blocks = $lesson->blocks();
        foreach ($blocks as $b) {
            $content .= "\n\n" . (string)$b->content;
        }

        if (trim($content) === '') {
            $content = "Lesson " . (string)$lesson->title . " covers fundamental architectural and programming principles.";
        }

        $provider = AIManager::provider();
        return $provider->summarize($content, [
            'lesson_title' => (string)$lesson->title,
        ]);
    }

    public static function generatePracticeQuiz(int $lessonId, int $count = 3): array {
        $lesson = Lesson::find($lessonId);
        if (!$lesson) {
            throw new RuntimeException("Lesson not found: $lessonId");
        }

        $content = (string)$lesson->title . "\n" . (string)$lesson->summary;
        $provider = AIManager::provider();
        return $provider->generatePracticeQuestions($content, $count);
    }

    public static function getUserConversations(int $userId, int $limit = 20): array {
        $rows = AIConversation::query()
            ->where('user_id', $userId)
            ->orderBy('updated_at', 'DESC')
            ->limit($limit)
            ->get();
        return array_map(fn($r) => new AIConversation($r), $rows);
    }
}
