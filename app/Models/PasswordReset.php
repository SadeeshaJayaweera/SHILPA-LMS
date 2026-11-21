<?php
declare(strict_types=1);

namespace App\Models;

use App\Database\Connection;
use PDO;

class PasswordReset extends Model {
    protected string $table = 'password_resets';
    protected array $fillable = [
        'email',
        'token',
        'created_at',
    ];

    /**
     * Create a secure reset token and persist its hash.
     */
    public static function createToken(string $email): string {
        $plainToken = bin2hex(random_bytes(32));
        $hashedToken = hash('sha256', $plainToken);

        // Delete any existing tokens for this email
        self::deleteToken($email);

        self::query()->insert([
            'email' => $email,
            'token' => $hashedToken,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return $plainToken;
    }

    /**
     * Verify whether a token is valid and unexpired.
     */
    public static function verifyToken(string $email, string $token, int $expireMinutes = 60): bool {
        $hashed = hash('sha256', $token);
        $row = self::query()
            ->where('email', $email)
            ->where('token', $hashed)
            ->first();

        if (!$row) {
            return false;
        }

        $createdAt = strtotime((string)$row['created_at']);
        $expiresAt = $createdAt + ($expireMinutes * 60);

        return time() <= $expiresAt;
    }

    /**
     * Purge all tokens associated with an email.
     */
    public static function deleteToken(string $email): void {
        try {
            $pdo = Connection::get();
            $stmt = $pdo->prepare("DELETE FROM password_resets WHERE email = ?");
            $stmt->execute([$email]);
        } catch (\Throwable $e) {
        }
    }
}
