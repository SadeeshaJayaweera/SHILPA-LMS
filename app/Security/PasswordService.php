<?php
declare(strict_types=1);

namespace App\Security;

class PasswordService {
    /**
     * Hash the given plain password with the strongest supported algorithm.
     */
    public static function hash(string $plainPassword): string {
        if (defined('PASSWORD_ARGON2ID')) {
            return password_hash($plainPassword, PASSWORD_ARGON2ID, [
                'memory_cost' => 65536,
                'time_cost' => 4,
                'threads' => 1,
            ]);
        }

        return password_hash($plainPassword, PASSWORD_BCRYPT, [
            'cost' => 12,
        ]);
    }

    /**
     * Verify a plain password against an existing hash.
     */
    public static function verify(string $plainPassword, string $hash): bool {
        if ($hash === '' || $plainPassword === '') {
            return false;
        }
        return password_verify($plainPassword, $hash);
    }

    /**
     * Determine if the hash needs to be upgraded to modern cost/algorithm.
     */
    public static function needsRehash(string $hash): bool {
        if (defined('PASSWORD_ARGON2ID')) {
            return password_needs_rehash($hash, PASSWORD_ARGON2ID, [
                'memory_cost' => 65536,
                'time_cost' => 4,
                'threads' => 1,
            ]);
        }

        return password_needs_rehash($hash, PASSWORD_BCRYPT, [
            'cost' => 12,
        ]);
    }

    /**
     * Validate password complexity according to NIST guidelines.
     */
    public static function validateStrength(string $password): array {
        $errors = [];

        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters long.';
        }
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password must contain at least one uppercase letter.';
        }
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Password must contain at least one lowercase letter.';
        }
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password must contain at least one number.';
        }
        if (!preg_match('/[^A-Za-z0-9]/', $password)) {
            $errors[] = 'Password must contain at least one special character.';
        }

        return $errors;
    }
}
