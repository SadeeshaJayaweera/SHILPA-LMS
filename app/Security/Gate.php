<?php
declare(strict_types=1);

namespace App\Security;

use App\Models\User;

class Gate {
    private static array $abilities = [];

    /**
     * Define a custom authorization gate callback.
     */
    public static function define(string $ability, callable $callback): void {
        self::$abilities[$ability] = $callback;
    }

    /**
     * Determine if the given ability is allowed for a user.
     */
    public static function allows(string $ability, ?User $user = null, mixed ...$args): bool {
        $user = $user ?? self::resolveCurrentUser();
        if (!$user) {
            return false;
        }

        // Administrators and Superadmins have universal bypass permissions
        if ($user->isAdmin()) {
            return true;
        }

        // Custom gate callback if explicitly registered
        if (isset(self::$abilities[$ability])) {
            return (bool)call_user_func(self::$abilities[$ability], $user, ...$args);
        }

        // Standard RBAC matrix mapping
        return self::checkDefaultMatrix($user, $ability, ...$args);
    }

    /**
     * Determine if the given ability is denied for a user.
     */
    public static function denies(string $ability, ?User $user = null, mixed ...$args): bool {
        return !self::allows($ability, $user, ...$args);
    }

    /**
     * Authorize a given ability, throwing an exception on failure.
     */
    public static function authorize(string $ability, ?User $user = null, mixed ...$args): void {
        if (self::denies($ability, $user, ...$args)) {
            throw new AuthorizationException("Unauthorized access: missing permission '{$ability}'.");
        }
    }

    /**
     * Standard fine-grained permission matrix.
     */
    private static function checkDefaultMatrix(User $user, string $ability, mixed ...$args): bool {
        $role = strtolower((string)($user->user_type ?? 'student'));

        if (in_array($role, ['l', 'lecturer', 'instructor', 'teacher'], true)) {
            $lecturerPermissions = [
                'course.view',
                'course.create',
                'course.update',
                'course.edit',
                'course.delete',
                'module.create',
                'module.edit',
                'assignment.create',
                'grade.view',
                'grade.submit',
                'analytics.view',
                'announcement.create',
            ];
            return in_array($ability, $lecturerPermissions, true);
        }

        // Default student permissions
        $studentPermissions = [
            'course.view',
            'course.enroll',
            'lesson.view',
            'assignment.submit',
            'grade.view_own',
            'assessment.take',
            'discussion.post',
        ];

        return in_array($ability, $studentPermissions, true);
    }

    private static function resolveCurrentUser(): ?User {
        $userId = (int)session()->get('user_id', 0);
        if ($userId > 0) {
            return User::find($userId);
        }

        $email = (string)session()->get('email', '');
        if ($email !== '') {
            return User::findByEmail($email);
        }

        return null;
    }
}
