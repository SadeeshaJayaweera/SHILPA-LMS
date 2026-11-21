<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Request;

class ActiveSession extends Model {
    protected string $table = 'active_sessions';
    protected string $primaryKey = 'id';
    protected array $fillable = [
        'id',
        'user_id',
        'ip_address',
        'user_agent',
        'device_type',
        'location',
        'last_activity',
        'created_at',
        'updated_at',
    ];

    public static function record(int $userId, string $sessionId, ?Request $request = null): self {
        $ip = $request ? $request->ip() : ($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1');
        $ua = $request ? (string)$request->server('HTTP_USER_AGENT', 'Unknown Browser') : ($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown');
        $deviceType = self::detectDeviceType($ua);
        $now = date('Y-m-d H:i:s');

        $existing = self::query()->where('id', $sessionId)->first();
        if ($existing) {
            $session = new self($existing);
            $session->last_activity = $now;
            $session->ip_address = $ip;
            $session->user_agent = substr($ua, 0, 255);
            $session->save();
            return $session;
        }

        return self::create([
            'id' => $sessionId,
            'user_id' => $userId,
            'ip_address' => $ip,
            'user_agent' => substr($ua, 0, 255),
            'device_type' => $deviceType,
            'location' => 'Colombo, Sri Lanka',
            'last_activity' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    public static function forUser(int $userId): array {
        return self::query()
            ->where('user_id', $userId)
            ->orderBy('last_activity', 'DESC')
            ->get();
    }

    public static function terminate(int $userId, string $sessionId): bool {
        $row = self::query()
            ->where('id', $sessionId)
            ->where('user_id', $userId)
            ->first();

        if ($row) {
            $session = new self($row);
            $session->delete();
            return true;
        }
        return false;
    }

    public static function terminateOthers(int $userId, string $currentSessionId): int {
        $sessions = self::query()
            ->where('user_id', $userId)
            ->get();

        $count = 0;
        foreach ($sessions as $s) {
            if ($s['id'] !== $currentSessionId) {
                $item = new self($s);
                $item->delete();
                $count++;
            }
        }
        return $count;
    }

    private static function detectDeviceType(string $userAgent): string {
        $ua = strtolower($userAgent);
        if (str_contains($ua, 'mobile') || str_contains($ua, 'iphone') || str_contains($ua, 'android')) {
            return 'Mobile Device';
        }
        if (str_contains($ua, 'ipad') || str_contains($ua, 'tablet')) {
            return 'Tablet Device';
        }
        return 'Desktop Browser';
    }
}
