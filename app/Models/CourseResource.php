<?php
declare(strict_types=1);

namespace App\Models;

class CourseResource extends Model {
    protected string $table = 'course_resources';
    protected array $fillable = [
        'course_id',
        'lesson_id',
        'uploader_id',
        'title',
        'original_filename',
        'storage_path',
        'mime_type',
        'file_size_bytes',
        'download_count',
        'is_public_preview',
        'created_at',
        'updated_at',
    ];

    public static function forCourse(int $courseId): array {
        $rows = self::query()
            ->where('course_id', $courseId)
            ->orderBy('id', 'DESC')
            ->get();
        return array_map(fn($r) => new self($r), $rows);
    }

    public static function forLesson(int $lessonId): array {
        $rows = self::query()
            ->where('lesson_id', $lessonId)
            ->orderBy('id', 'DESC')
            ->get();
        return array_map(fn($r) => new self($r), $rows);
    }

    public function formattedSize(): string {
        $bytes = (int)($this->file_size_bytes ?? 0);
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 1) . ' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 1) . ' KB';
        }
        return $bytes . ' B';
    }

    public function icon(): string {
        $mime = strtolower((string)($this->mime_type ?? ''));
        $ext = strtolower(pathinfo((string)$this->original_filename, PATHINFO_EXTENSION));

        if (str_contains($mime, 'pdf') || $ext === 'pdf') {
            return 'file-text';
        }
        if (str_contains($mime, 'image') || in_array($ext, ['png', 'jpg', 'jpeg', 'webp', 'svg'], true)) {
            return 'image';
        }
        if (str_contains($mime, 'video') || in_array($ext, ['mp4', 'webm', 'mov'], true)) {
            return 'video';
        }
        if (in_array($ext, ['zip', 'tar', 'gz', 'rar', '7z'], true)) {
            return 'archive';
        }
        if (in_array($ext, ['py', 'php', 'js', 'ts', 'sql', 'java', 'cpp', 'c', 'cs', 'go', 'html', 'json'], true)) {
            return 'code';
        }
        return 'file';
    }

    public function isPublic(): bool {
        return (int)($this->is_public_preview ?? 0) === 1;
    }

    public function incrementDownloadCount(): void {
        $this->download_count = ((int)($this->download_count ?? 0)) + 1;
        $this->save();
    }
}
