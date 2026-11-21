<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\Response;
use App\Models\Course;
use App\Models\CourseResource;
use App\Models\Enrollment;
use RuntimeException;

class ResourceManagementService {
    // Whitelist of allowed extensions for academic resources
    private const ALLOWED_EXTENSIONS = [
        'pdf', 'zip', 'tar', 'gz', 'doc', 'docx', 'ppt', 'pptx',
        'xls', 'xlsx', 'txt', 'csv', 'png', 'jpg', 'jpeg', 'webp',
        'mp4', 'py', 'java', 'sql', 'json', 'md',
    ];

    // Explicitly dangerous extensions that must never be accepted
    private const FORBIDDEN_EXTENSIONS = [
        'php', 'phtml', 'php3', 'php4', 'php5', 'php7', 'phps',
        'sh', 'bash', 'exe', 'bat', 'cmd', 'bin', 'cgi', 'pl',
        'vbs', 'scr', 'msi', 'jsp', 'asp', 'aspx',
    ];

    private const MAX_FILE_SIZE = 52428800; // 50 MB

    public static function getStorageBaseDirectory(): string {
        $base = dirname(__DIR__, 2) . '/storage/app/resources';
        if (!is_dir($base)) {
            mkdir($base, 0755, true);
        }
        return $base;
    }

    public static function storeUploadedFile(
        int $courseId,
        ?int $lessonId,
        int $uploaderId,
        array $fileData,
        string $title,
        bool $isPublicPreview = false
    ): CourseResource {
        $origName = trim((string)($fileData['name'] ?? ''));
        $tmpPath = (string)($fileData['tmp_name'] ?? '');
        $fileSize = (int)($fileData['size'] ?? 0);
        $error = (int)($fileData['error'] ?? 0);

        if ($error !== UPLOAD_ERR_OK && !file_exists($tmpPath)) {
            throw new RuntimeException("File upload failed with error code $error.");
        }

        if ($fileSize > self::MAX_FILE_SIZE) {
            throw new RuntimeException("File exceeds maximum allowable size of 50MB.");
        }

        $extension = strtolower(pathinfo($origName, PATHINFO_EXTENSION));

        if (in_array($extension, self::FORBIDDEN_EXTENSIONS, true)) {
            throw new RuntimeException("Executable and script files are strictly prohibited.");
        }

        if (!in_array($extension, self::ALLOWED_EXTENSIONS, true)) {
            throw new RuntimeException("File extension '$extension' is not permitted for learning resources.");
        }

        // Determine MIME type securely
        $mimeType = 'application/octet-stream';
        if (function_exists('mime_content_type') && file_exists($tmpPath)) {
            $detected = mime_content_type($tmpPath);
            if ($detected) {
                $mimeType = $detected;
            }
        }

        // Generate isolated storage destination
        $courseDir = self::getStorageBaseDirectory() . '/' . $courseId;
        if (!is_dir($courseDir)) {
            mkdir($courseDir, 0755, true);
        }

        $randomHash = bin2hex(random_bytes(16));
        $storedFilename = $randomHash . '.' . $extension;
        $destPath = $courseDir . '/' . $storedFilename;
        $relativePath = $courseId . '/' . $storedFilename;

        // Move or copy file
        if (is_uploaded_file($tmpPath)) {
            if (!move_uploaded_file($tmpPath, $destPath)) {
                throw new RuntimeException("Failed to persist uploaded resource.");
            }
        } else {
            if (!copy($tmpPath, $destPath)) {
                throw new RuntimeException("Failed to copy resource into storage.");
            }
        }

        $now = date('Y-m-d H:i:s');
        return CourseResource::create([
            'course_id' => $courseId,
            'lesson_id' => $lessonId ?: null,
            'uploader_id' => $uploaderId,
            'title' => $title ?: pathinfo($origName, PATHINFO_FILENAME),
            'original_filename' => $origName,
            'storage_path' => $relativePath,
            'mime_type' => $mimeType,
            'file_size_bytes' => $fileSize,
            'download_count' => 0,
            'is_public_preview' => $isPublicPreview ? 1 : 0,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    public static function canUserAccess(CourseResource $resource, ?int $userId, string $userRole): bool {
        // Public preview resources are accessible by all
        if ($resource->isPublic()) {
            return true;
        }

        // Admins and faculty have access
        $normalizedRole = strtolower($userRole);
        if (in_array($normalizedRole, ['admin', 'administrator', 'superadmin', 'lecturer', 'instructor', 'faculty', 'a', 'l'], true)) {
            return true;
        }

        // Enrolled learners have access
        if ($userId && $userId > 0) {
            $enrollment = Enrollment::query()
                ->where('user_id', $userId)
                ->where('course_id', (int)$resource->course_id)
                ->first();

            if ($enrollment) {
                return true;
            }
        }

        return false;
    }

    public static function streamDownload(CourseResource $resource): Response {
        $fullPath = self::getStorageBaseDirectory() . '/' . $resource->storage_path;

        if (!file_exists($fullPath)) {
            throw new RuntimeException("Requested resource file could not be found in storage.");
        }

        $resource->incrementDownloadCount();

        return Response::download(
            $fullPath,
            (string)$resource->original_filename,
            (string)($resource->mime_type ?: 'application/octet-stream')
        );
    }

    public static function deleteResource(int $resourceId): bool {
        $resource = CourseResource::find($resourceId);
        if (!$resource) {
            return false;
        }

        $fullPath = self::getStorageBaseDirectory() . '/' . $resource->storage_path;
        if (file_exists($fullPath)) {
            @unlink($fullPath);
        }

        return $resource->delete();
    }
}
