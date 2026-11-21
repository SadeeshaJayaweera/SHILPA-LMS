<?php
declare(strict_types=1);

namespace App\Security;

use App\Core\Request;
use App\Models\Model;

class SecurityAuditLog extends Model {
    protected string $table = 'security_audit_logs';
    protected array $fillable = [
        'event',
        'severity',
        'user_id',
        'actor_email',
        'ip_address',
        'user_agent',
        'payload',
        'previous_hash',
        'integrity_hash',
        'created_at',
    ];

    public static function log(
        string $event,
        string $severity = 'info',
        ?int $userId = null,
        ?string $actorEmail = null,
        array $payload = [],
        ?Request $request = null
    ): self {
        $ip = $request ? $request->ip() : ($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1');
        $ua = $request ? (string)$request->server('HTTP_USER_AGENT', 'System') : ($_SERVER['HTTP_USER_AGENT'] ?? 'System');
        $jsonPayload = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: '{}';
        $now = date('Y-m-d H:i:s');

        // Retrieve last log hash for cryptographic hash chain
        $lastLog = self::query()->orderBy('id', 'DESC')->first();
        $previousHash = $lastLog ? (string)($lastLog['integrity_hash'] ?? 'GENESIS_BLOCK_SHILPA_2026') : 'GENESIS_BLOCK_SHILPA_2026';

        $secretKey = config('app.key', 'shilpa-enterprise-security-secret-key-2026');
        $signString = implode('|', [
            $previousHash,
            $event,
            $severity,
            $userId ?? '0',
            $actorEmail ?? '',
            $ip,
            $jsonPayload,
            $now,
        ]);
        $integrityHash = hash_hmac('sha256', $signString, $secretKey);

        return self::create([
            'event' => $event,
            'severity' => $severity,
            'user_id' => $userId,
            'actor_email' => $actorEmail,
            'ip_address' => $ip,
            'user_agent' => substr($ua, 0, 255),
            'payload' => $jsonPayload,
            'previous_hash' => $previousHash,
            'integrity_hash' => $integrityHash,
            'created_at' => $now,
        ]);
    }

    /**
     * Cryptographically audits the hash chain to detect database tampering.
     */
    public static function verifyChainIntegrity(int $limit = 500): array {
        $logs = self::query()->orderBy('id', 'ASC')->limit($limit)->get();
        if (empty($logs)) {
            return [
                'intact' => true,
                'checked_count' => 0,
                'compromised_at' => null,
            ];
        }

        $secretKey = config('app.key', 'shilpa-enterprise-security-secret-key-2026');
        $expectedPrevHash = 'GENESIS_BLOCK_SHILPA_2026';

        foreach ($logs as $log) {
            $prevHashInDb = (string)($log['previous_hash'] ?? '');
            if ($prevHashInDb !== $expectedPrevHash) {
                return [
                    'intact' => false,
                    'checked_count' => count($logs),
                    'compromised_at' => (int)$log['id'],
                    'reason' => 'Hash chain broken between records.',
                ];
            }

            $signString = implode('|', [
                $prevHashInDb,
                (string)$log['event'],
                (string)$log['severity'],
                $log['user_id'] !== null ? (string)$log['user_id'] : '0',
                (string)($log['actor_email'] ?? ''),
                (string)$log['ip_address'],
                (string)$log['payload'],
                (string)$log['created_at'],
            ]);

            $recalculated = hash_hmac('sha256', $signString, $secretKey);
            if (!hash_equals($recalculated, (string)$log['integrity_hash'])) {
                return [
                    'intact' => false,
                    'checked_count' => count($logs),
                    'compromised_at' => (int)$log['id'],
                    'reason' => 'Payload or cryptographic signature mismatch detected.',
                ];
            }

            $expectedPrevHash = (string)$log['integrity_hash'];
        }

        return [
            'intact' => true,
            'checked_count' => count($logs),
            'compromised_at' => null,
        ];
    }
}
