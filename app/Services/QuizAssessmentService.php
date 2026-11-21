<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizQuestion;
use App\Security\SecurityAuditLog;
use RuntimeException;

class QuizAssessmentService {
    public static function createQuiz(int $courseId, int $authorId, array $data): Quiz {
        $course = Course::find($courseId);
        if (!$course) {
            throw new RuntimeException("Course not found: $courseId");
        }

        $now = date('Y-m-d H:i:s');
        return Quiz::create([
            'course_id' => $courseId,
            'created_by' => $authorId,
            'title' => trim((string)($data['title'] ?? 'New Assessment')),
            'description' => trim((string)($data['description'] ?? '')),
            'time_limit_minutes' => (int)($data['time_limit_minutes'] ?? 30),
            'passing_score_percent' => (int)($data['passing_score_percent'] ?? 70),
            'max_attempts' => (int)($data['max_attempts'] ?? 3),
            'is_published' => isset($data['is_published']) ? (int)$data['is_published'] : 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    public static function addQuestion(int $quizId, array $data): QuizQuestion {
        $quiz = Quiz::find($quizId);
        if (!$quiz) {
            throw new RuntimeException("Quiz not found: $quizId");
        }

        $now = date('Y-m-d H:i:s');
        $options = $data['options'] ?? [];
        $correct = $data['correct_answer'] ?? '';

        return QuizQuestion::create([
            'quiz_id' => $quizId,
            'question_type' => (string)($data['question_type'] ?? 'single_choice'),
            'question_text' => trim((string)($data['question_text'] ?? '')),
            'options_json' => is_array($options) ? json_encode($options, JSON_UNESCAPED_UNICODE) : (string)$options,
            'correct_answer_json' => json_encode($correct, JSON_UNESCAPED_UNICODE),
            'points' => (int)($data['points'] ?? 10),
            'order_index' => (int)($data['order_index'] ?? count($quiz->questions()) + 1),
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    public static function startAttempt(int $quizId, int $studentId): QuizAttempt {
        $quiz = Quiz::find($quizId);
        if (!$quiz) {
            throw new RuntimeException("Quiz not found: $quizId");
        }

        // Must be enrolled
        $isEnrolled = Enrollment::query()
            ->where('user_id', $studentId)
            ->where('course_id', (int)$quiz->course_id)
            ->exists();

        if (!$isEnrolled) {
            throw new RuntimeException("You must be enrolled in this course to take this assessment.");
        }

        // Check attempts count
        $existingAttempts = $quiz->attemptsForStudent($studentId);
        $attemptsCount = count($existingAttempts);

        if ($attemptsCount >= (int)$quiz->max_attempts) {
            throw new RuntimeException("Maximum assessment attempts reached ({$quiz->max_attempts}).");
        }

        $now = date('Y-m-d H:i:s');
        return QuizAttempt::create([
            'quiz_id' => $quizId,
            'student_id' => $studentId,
            'attempt_number' => $attemptsCount + 1,
            'score_achieved' => 0,
            'max_score' => $quiz->totalPoints(),
            'is_passed' => 0,
            'answers_json' => json_encode([]),
            'started_at' => $now,
            'submitted_at' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    public static function submitAttempt(int $attemptId, array $submittedAnswers): QuizAttempt {
        $attempt = QuizAttempt::find($attemptId);
        if (!$attempt) {
            throw new RuntimeException("Quiz attempt not found: $attemptId");
        }

        if ($attempt->submitted_at !== null) {
            throw new RuntimeException("This assessment attempt has already been submitted and finalized.");
        }

        $quiz = $attempt->quiz();
        if (!$quiz) {
            throw new RuntimeException("Associated quiz not found.");
        }

        $questions = $quiz->questions();
        $totalAchieved = 0;
        $maxPoints = 0;

        foreach ($questions as $q) {
            $maxPoints += (int)$q->points;
            $qId = (int)$q->id;
            $given = $submittedAnswers[$qId] ?? null;

            if ($q->isCorrect($given)) {
                $totalAchieved += (int)$q->points;
            }
        }

        $percentage = $maxPoints > 0 ? (int)round(($totalAchieved / $maxPoints) * 100) : 0;
        $isPassed = $percentage >= (int)$quiz->passing_score_percent ? 1 : 0;

        $now = date('Y-m-d H:i:s');
        $attempt->score_achieved = $totalAchieved;
        $attempt->max_score = $maxPoints;
        $attempt->is_passed = $isPassed;
        $attempt->answers_json = json_encode($submittedAnswers, JSON_UNESCAPED_UNICODE);
        $attempt->submitted_at = $now;
        $attempt->updated_at = $now;
        $attempt->save();

        SecurityAuditLog::log('quiz.attempt.submitted', 'info', (int)$attempt->student_id, null, [
            'quiz_id' => $quiz->id,
            'score' => $totalAchieved,
            'percentage' => $percentage,
            'is_passed' => (bool)$isPassed,
        ]);

        return $attempt;
    }
}
