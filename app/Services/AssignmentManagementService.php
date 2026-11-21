<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use App\Models\Enrollment;
use App\Security\SecurityAuditLog;
use RuntimeException;

class AssignmentManagementService {
    private const ALLOWED_SUBMISSION_EXTENSIONS = [
        'pdf', 'zip', 'tar', 'gz', 'doc', 'docx', 'txt', 'py', 'java', 'sql', 'cpp', 'c', 'cs', 'json', 'md'
    ];

    public static function createAssignment(int $courseId, int $authorId, array $data): Assignment {
        $course = Course::find($courseId);
        if (!$course) {
            throw new RuntimeException("Course not found: $courseId");
        }

        $now = date('Y-m-d H:i:s');
        $dueAt = !empty($data['due_at']) ? date('Y-m-d H:i:s', strtotime((string)$data['due_at'])) : null;

        return Assignment::create([
            'course_id' => $courseId,
            'created_by' => $authorId,
            'title' => trim((string)($data['title'] ?? 'New Assignment')),
            'description' => trim((string)($data['description'] ?? '')),
            'max_score' => (int)($data['max_score'] ?? 100),
            'due_at' => $dueAt,
            'is_published' => isset($data['is_published']) ? (int)$data['is_published'] : 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    public static function updateAssignment(int $assignmentId, array $data): Assignment {
        $assignment = Assignment::find($assignmentId);
        if (!$assignment) {
            throw new RuntimeException("Assignment not found: $assignmentId");
        }

        if (isset($data['title'])) {
            $assignment->title = trim((string)$data['title']);
        }
        if (isset($data['description'])) {
            $assignment->description = trim((string)$data['description']);
        }
        if (isset($data['max_score'])) {
            $assignment->max_score = (int)$data['max_score'];
        }
        if (isset($data['due_at'])) {
            $assignment->due_at = !empty($data['due_at']) ? date('Y-m-d H:i:s', strtotime((string)$data['due_at'])) : null;
        }
        if (isset($data['is_published'])) {
            $assignment->is_published = (int)$data['is_published'];
        }
        $assignment->updated_at = date('Y-m-d H:i:s');
        $assignment->save();

        return $assignment;
    }

    public static function deleteAssignment(int $assignmentId): bool {
        $assignment = Assignment::find($assignmentId);
        if (!$assignment) {
            return false;
        }

        // Delete submissions
        $submissions = $assignment->submissions();
        foreach ($submissions as $sub) {
            $sub->delete();
        }

        return $assignment->delete();
    }

    public static function submitAssignment(int $assignmentId, int $studentId, array $data, ?array $fileData = null): AssignmentSubmission {
        $assignment = Assignment::find($assignmentId);
        if (!$assignment) {
            throw new RuntimeException("Assignment not found: $assignmentId");
        }

        // Enforce enrollment
        $isEnrolled = Enrollment::query()
            ->where('user_id', $studentId)
            ->where('course_id', (int)$assignment->course_id)
            ->exists();

        if (!$isEnrolled) {
            throw new RuntimeException("You must be enrolled in this course to submit assignments.");
        }

        $isLate = $assignment->isOverdue();
        $status = $isLate ? 'late' : 'submitted';

        $attachmentPath = null;
        if ($fileData && !empty($fileData['name']) && empty($fileData['error'])) {
            $origName = (string)$fileData['name'];
            $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));

            if (!in_array($ext, self::ALLOWED_SUBMISSION_EXTENSIONS, true)) {
                throw new RuntimeException("File extension '.$ext' is not permitted for submissions.");
            }

            $storageBase = dirname(__DIR__, 2) . '/storage/app/submissions/' . $assignmentId;
            if (!is_dir($storageBase)) {
                mkdir($storageBase, 0755, true);
            }

            $destName = 'sub_' . $studentId . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
            $fullDest = $storageBase . '/' . $destName;

            if (is_uploaded_file($fileData['tmp_name'])) {
                move_uploaded_file($fileData['tmp_name'], $fullDest);
            } else {
                copy($fileData['tmp_name'], $fullDest);
            }

            $attachmentPath = $assignmentId . '/' . $destName;
        }

        $now = date('Y-m-d H:i:s');
        $existing = $assignment->submissionForStudent($studentId);

        if ($existing) {
            $existing->submission_text = trim((string)($data['submission_text'] ?? $existing->submission_text));
            if ($attachmentPath) {
                $existing->attachment_path = $attachmentPath;
            }
            $existing->status = $status;
            $existing->submitted_at = $now;
            $existing->updated_at = $now;
            $existing->save();

            SecurityAuditLog::log('assignment.resubmitted', 'info', $studentId, null, [
                'assignment_id' => $assignmentId,
                'is_late' => $isLate,
            ]);

            return $existing;
        }

        $submission = AssignmentSubmission::create([
            'assignment_id' => $assignmentId,
            'student_id' => $studentId,
            'submission_text' => trim((string)($data['submission_text'] ?? '')),
            'attachment_path' => $attachmentPath,
            'score' => null,
            'feedback' => null,
            'status' => $status,
            'submitted_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        SecurityAuditLog::log('assignment.submitted', 'info', $studentId, null, [
            'assignment_id' => $assignmentId,
            'is_late' => $isLate,
        ]);

        return $submission;
    }

    public static function gradeSubmission(int $submissionId, int $graderId, int $score, ?string $feedback): AssignmentSubmission {
        $submission = AssignmentSubmission::find($submissionId);
        if (!$submission) {
            throw new RuntimeException("Submission not found: $submissionId");
        }

        $submission->score = $score;
        $submission->feedback = trim((string)$feedback);
        $submission->status = 'graded';
        $submission->updated_at = date('Y-m-d H:i:s');
        $submission->save();

        SecurityAuditLog::log('assignment.graded', 'info', $graderId, null, [
            'submission_id' => $submissionId,
            'score' => $score,
        ]);

        return $submission;
    }
}
