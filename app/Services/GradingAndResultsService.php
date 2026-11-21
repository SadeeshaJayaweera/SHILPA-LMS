<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use App\Models\CourseGrade;
use App\Models\Enrollment;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use App\Security\SecurityAuditLog;
use RuntimeException;

class GradingAndResultsService {
    public static function gradeSubmission(int $submissionId, int $graderId, int $score, ?string $feedback): AssignmentSubmission {
        $submission = AssignmentSubmission::find($submissionId);
        if (!$submission) {
            throw new RuntimeException("Submission not found: $submissionId");
        }

        $assignment = $submission->assignment();
        if (!$assignment) {
            throw new RuntimeException("Assignment not found.");
        }

        $now = date('Y-m-d H:i:s');
        $submission->score = min((int)$assignment->max_score, max(0, $score));
        $submission->feedback = trim((string)$feedback);
        $submission->status = 'graded';
        $submission->updated_at = $now;
        $submission->save();

        self::recalculateStudentCourseGrade((int)$assignment->course_id, (int)$submission->student_id);

        SecurityAuditLog::log('grading.submission.evaluated', 'info', $graderId, null, [
            'submission_id' => $submissionId,
            'student_id' => $submission->student_id,
            'score' => $submission->score,
        ]);

        return $submission;
    }

    public static function recalculateStudentCourseGrade(int $courseId, int $studentId): CourseGrade {
        $assignments = Assignment::forCourse($courseId, true);
        $quizzes = Quiz::forCourse($courseId, true);

        $totalEarned = 0;
        $totalMax = 0;

        // Assignments
        foreach ($assignments as $asgn) {
            $totalMax += (int)$asgn->max_score;
            $sub = $asgn->submissionForStudent($studentId);
            if ($sub && $sub->isGraded()) {
                $totalEarned += (int)$sub->score;
            }
        }

        // Quizzes
        foreach ($quizzes as $quiz) {
            $totalMax += $quiz->totalPoints();
            $attempts = $quiz->attemptsForStudent($studentId);
            $bestScore = 0;
            foreach ($attempts as $att) {
                if ($att->submitted_at !== null && (int)$att->score_achieved > $bestScore) {
                    $bestScore = (int)$att->score_achieved;
                }
            }
            $totalEarned += $bestScore;
        }

        $percentage = $totalMax > 0 ? (int)round(($totalEarned / $totalMax) * 100) : 0;
        $letter = CourseGrade::calculateLetter($percentage);

        $now = date('Y-m-d H:i:s');
        $existing = CourseGrade::query()
            ->where('course_id', $courseId)
            ->where('student_id', $studentId)
            ->first();

        if ($existing) {
            $grade = new CourseGrade($existing);
            $grade->total_score = $totalEarned;
            $grade->max_score = $totalMax;
            $grade->percentage = $percentage;
            $grade->letter_grade = $letter;
            $grade->updated_at = $now;
            $grade->save();
            return $grade;
        }

        return CourseGrade::create([
            'course_id' => $courseId,
            'student_id' => $studentId,
            'total_score' => $totalEarned,
            'max_score' => $totalMax,
            'percentage' => $percentage,
            'letter_grade' => $letter,
            'is_published' => 0,
            'published_at' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    public static function getCourseGradebook(int $courseId): array {
        $course = Course::find($courseId);
        if (!$course) {
            throw new RuntimeException("Course not found: $courseId");
        }

        $assignments = Assignment::forCourse($courseId, true);
        $quizzes = Quiz::forCourse($courseId, true);

        $enrollments = Enrollment::query()
            ->where('course_id', $courseId)
            ->get();

        $studentsData = [];
        foreach ($enrollments as $enr) {
            $studentId = (int)$enr['user_id'];
            $student = User::find($studentId);
            if (!$student) {
                continue;
            }

            $grade = self::recalculateStudentCourseGrade($courseId, $studentId);

            $assignmentScores = [];
            foreach ($assignments as $a) {
                $sub = $a->submissionForStudent($studentId);
                $assignmentScores[(int)$a->id] = $sub ? [
                    'submission_id' => (int)$sub->id,
                    'score' => $sub->score,
                    'max_score' => (int)$a->max_score,
                    'status' => $sub->status,
                    'is_graded' => $sub->isGraded(),
                    'feedback' => $sub->feedback,
                ] : null;
            }

            $quizScores = [];
            foreach ($quizzes as $q) {
                $attempts = $q->attemptsForStudent($studentId);
                $bestScore = 0;
                $attemptCount = count($attempts);
                foreach ($attempts as $att) {
                    if ($att->submitted_at !== null && (int)$att->score_achieved > $bestScore) {
                        $bestScore = (int)$att->score_achieved;
                    }
                }
                $quizScores[(int)$q->id] = [
                    'best_score' => $bestScore,
                    'max_score' => $q->totalPoints(),
                    'attempts' => $attemptCount,
                ];
            }

            $studentsData[] = [
                'student' => $student,
                'grade' => $grade,
                'assignmentScores' => $assignmentScores,
                'quizScores' => $quizScores,
            ];
        }

        return [
            'course' => $course,
            'assignments' => $assignments,
            'quizzes' => $quizzes,
            'studentsData' => $studentsData,
        ];
    }

    public static function publishCourseGrades(int $courseId): bool {
        $now = date('Y-m-d H:i:s');
        $rows = CourseGrade::query()->where('course_id', $courseId)->get();
        foreach ($rows as $r) {
            $cg = new CourseGrade($r);
            $cg->is_published = 1;
            $cg->published_at = $now;
            $cg->save();
        }
        return true;
    }

    public static function getStudentTranscript(int $studentId): array {
        $grades = CourseGrade::forStudent($studentId, true);
        $totalQualityPoints = 0.0;
        $totalCredits = 0;

        $gpaMap = [
            'A+' => 4.0, 'A' => 4.0, 'A-' => 3.7,
            'B+' => 3.3, 'B' => 3.0, 'B-' => 2.7,
            'C' => 2.0, 'F' => 0.0,
        ];

        $records = [];
        foreach ($grades as $g) {
            $course = $g->course();
            if (!$course) {
                continue;
            }
            $credits = 3; // Default academic credit unit
            $totalCredits += $credits;
            $qp = ($gpaMap[(string)$g->letter_grade] ?? 0.0) * $credits;
            $totalQualityPoints += $qp;

            $records[] = [
                'course' => $course,
                'grade' => $g,
                'credits' => $credits,
            ];
        }

        $cumulativeGpa = $totalCredits > 0 ? round($totalQualityPoints / $totalCredits, 2) : 0.0;

        return [
            'records' => $records,
            'totalCredits' => $totalCredits,
            'cumulativeGpa' => $cumulativeGpa,
        ];
    }
}
