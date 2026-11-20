<?php
declare(strict_types=1);

namespace App\Models;

class User extends Model {
    protected string $table = 'users';
    protected string $primaryKey = 'UserId';
    protected array $fillable = [
        'Full_Name',
        'Email',
        'Password',
        'user_type',
        'created_at',
    ];

    public static function findByEmail(string $email): ?self {
        $row = self::query()->where('Email', $email)->first();
        if (!$row) {
            // Check lowercase column variant if legacy database
            $row = self::query()->where('email', $email)->first();
        }
        return $row ? new self($row) : null;
    }

    public function verifyPassword(string $plainPassword): bool {
        $hash = (string)($this->Password ?? $this->password ?? '');
        return password_verify($plainPassword, $hash);
    }

    public function setPassword(string $plainPassword): void {
        $this->Password = password_hash($plainPassword, PASSWORD_DEFAULT);
    }

    public function isAdmin(): bool {
        $type = (string)($this->user_type ?? '');
        return in_array($type, ['a', 'admin', 'administrator', 'superadmin'], true);
    }

    public function isLecturer(): bool {
        $type = (string)($this->user_type ?? '');
        return in_array($type, ['l', 'lecturer', 'instructor', 'teacher'], true);
    }

    public function isStudent(): bool {
        return !$this->isAdmin() && !$this->isLecturer();
    }
}
