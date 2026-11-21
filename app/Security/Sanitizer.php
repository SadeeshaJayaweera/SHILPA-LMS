<?php
declare(strict_types=1);

namespace App\Security;

class Sanitizer {
    /**
     * Alias for cleanString for convenience
     */
    public static function clean(string $input): string {
        return self::cleanString($input);
    }

    /**
     * Clean and strip potential XSS vectors from HTML/text.
     */
    public static function cleanString(string $input): string {
        $stripped = strip_tags($input);
        return htmlspecialchars($stripped, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * Recursively sanitize array or string parameters.
     */
    public static function cleanInput(mixed $data): mixed {
        if (is_array($data)) {
            $cleaned = [];
            foreach ($data as $key => $value) {
                $cleaned[self::cleanString((string)$key)] = self::cleanInput($value);
            }
            return $cleaned;
        }

        if (is_string($data)) {
            return trim(self::cleanString($data));
        }

        return $data;
    }

    /**
     * Sanitize upload file names to prevent directory traversal or script execution.
     */
    public static function sanitizeFilename(string $filename): string {
        $basename = basename($filename);
        $clean = preg_replace('/[^a-zA-Z0-9_\-\.]/', '', $basename) ?: 'file_' . time();
        return strtolower($clean);
    }
}
