<?php
declare(strict_types=1);

namespace App\Security;

class TwoFactorAuth {
    private const BASE32_ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    /**
     * Generate a cryptographically secure Base32 16-character secret key.
     */
    public static function generateSecret(int $length = 16): string {
        $secret = '';
        $max = strlen(self::BASE32_ALPHABET) - 1;
        for ($i = 0; $i < $length; $i++) {
            $secret .= self::BASE32_ALPHABET[random_int(0, $max)];
        }
        return $secret;
    }

    /**
     * Calculate the 6-digit TOTP code for a given timestamp.
     */
    public static function getTotpCode(string $secret, ?int $timestamp = null, int $period = 30): string {
        $timestamp = $timestamp ?? time();
        $timeCounter = (int)floor($timestamp / $period);

        $secretBytes = self::base32Decode($secret);
        $timePacked = pack('N*', 0) . pack('N*', $timeCounter);

        $hash = hash_hmac('sha1', $timePacked, $secretBytes, true);
        $offset = ord($hash[strlen($hash) - 1]) & 0x0F;

        $binary = (
            ((ord($hash[$offset]) & 0x7F) << 24) |
            ((ord($hash[$offset + 1]) & 0xFF) << 16) |
            ((ord($hash[$offset + 2]) & 0xFF) << 8) |
            (ord($hash[$offset + 3]) & 0xFF)
        );

        $otp = $binary % 1000000;
        return str_pad((string)$otp, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Verify an OTP code allowing for clock drift.
     */
    public static function verifyCode(string $secret, string $code, int $discrepancy = 1, int $period = 30): bool {
        $code = trim($code);
        if (strlen($code) !== 6 || !ctype_digit($code)) {
            return false;
        }

        $currentTime = time();
        for ($i = -$discrepancy; $i <= $discrepancy; $i++) {
            $calculated = self::getTotpCode($secret, $currentTime + ($i * $period), $period);
            if (hash_equals($calculated, $code)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get otpauth:// URI for QR code generators.
     */
    public static function getProvisioningUri(string $account, string $secret, string $issuer = 'SHILPA LMS'): string {
        return sprintf(
            'otpauth://totp/%s:%s?secret=%s&issuer=%s&algorithm=SHA1&digits=6&period=30',
            rawurlencode($issuer),
            rawurlencode($account),
            $secret,
            rawurlencode($issuer)
        );
    }

    private static function base32Decode(string $b32): string {
        $b32 = strtoupper(rtrim($b32, '='));
        $binaryString = '';

        for ($i = 0; $i < strlen($b32); $i++) {
            $char = $b32[$i];
            $val = strpos(self::BASE32_ALPHABET, $char);
            if ($val === false) {
                continue;
            }
            $binaryString .= sprintf('%05b', $val);
        }

        $bytes = '';
        $length = strlen($binaryString);
        for ($i = 0; $i + 8 <= $length; $i += 8) {
            $bytes .= chr((int)bindec(substr($binaryString, $i, 8)));
        }

        return $bytes;
    }
}
