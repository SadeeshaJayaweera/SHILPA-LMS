<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Models\PasswordReset;
use App\Models\User;
use App\Security\PasswordService;
use App\Security\RateLimiter;
use App\Security\TwoFactorAuth;
use App\Testing\TestCase;

class SecureAuthenticationTest extends TestCase {
    public function testPasswordServiceValidationAndHashing(): void {
        $weakErrors = PasswordService::validateStrength('weak');
        assert(!empty($weakErrors), "Weak password must produce complexity errors");

        $noUpperErrors = PasswordService::validateStrength('lowercase123!');
        assert(!empty($noUpperErrors), "Password missing uppercase must fail");

        $strongErrors = PasswordService::validateStrength('SecurePassword2026!#');
        assert(empty($strongErrors), "Strong password must have zero validation errors");

        $hash = PasswordService::hash('SecurePassword2026!#');
        assert(PasswordService::verify('SecurePassword2026!#', $hash) === true, "Password must verify against hash");
        assert(PasswordService::verify('WrongPassword', $hash) === false, "Incorrect password must not verify");
    }

    public function testBruteForceRateLimiterLockout(): void {
        $key = 'login:127.0.0.1:brutetest@shilpa.lk';
        RateLimiter::clear($key);

        assert(RateLimiter::tooManyAttempts($key, 5) === false, "Initial state should not be throttled");

        for ($i = 1; $i <= 5; $i++) {
            RateLimiter::hit($key, 900, 5);
        }

        assert(RateLimiter::tooManyAttempts($key, 5) === true, "Must be throttled after 5 failed attempts");
        assert(RateLimiter::availableIn($key) > 0, "Lockout seconds must be greater than zero");

        RateLimiter::clear($key);
        assert(RateLimiter::tooManyAttempts($key, 5) === false, "Must not be throttled after clear");
    }

    public function testPasswordResetWorkflow(): void {
        $user = User::create([
            'Full_Name' => 'Reset Test User',
            'Email' => 'resetuser@shilpa.lk',
            'Password' => PasswordService::hash('OldPass2026!#'),
            'user_type' => 'student',
        ]);

        $token = PasswordReset::createToken('resetuser@shilpa.lk');
        assert(strlen($token) === 64, "Generated token must be 64 characters");
        assert(PasswordReset::verifyToken('resetuser@shilpa.lk', $token) === true, "Token must verify successfully");
        assert(PasswordReset::verifyToken('resetuser@shilpa.lk', 'invalidtoken123') === false, "Invalid token must fail");

        // Submit password reset
        $response = $this->post('/reset-password', [
            'email' => 'resetuser@shilpa.lk',
            'token' => $token,
            'password' => 'NewStrongPass2026!#',
            'password_confirmation' => 'NewStrongPass2026!#',
        ]);

        $response->assertRedirect('/login');

        // Verify password was updated in database
        $refreshed = User::findByEmail('resetuser@shilpa.lk');
        assert($refreshed !== null);
        assert(PasswordService::verify('NewStrongPass2026!#', (string)$refreshed->Password) === true, "New password must authenticate");
    }

    public function testTwoFactorTotpGenerationAndVerification(): void {
        $secret = TwoFactorAuth::generateSecret(16);
        assert(strlen($secret) === 16, "Secret length should be 16 Base32 characters");

        $code = TwoFactorAuth::getTotpCode($secret);
        assert(strlen($code) === 6, "TOTP code must be 6 digits");
        assert(ctype_digit($code), "TOTP code must contain only digits");

        assert(TwoFactorAuth::verifyCode($secret, $code) === true, "Generated TOTP code must verify");
        assert(TwoFactorAuth::verifyCode($secret, '999999') === false || TwoFactorAuth::verifyCode($secret, '000000') === false, "Invalid OTP code must fail verification");
    }

    public function testLoginRegeneratesSessionAndUpdatesLastActivity(): void {
        User::create([
            'Full_Name' => 'Session Security User',
            'Email' => 'sessionsec@shilpa.lk',
            'Password' => PasswordService::hash('SessionPass2026!#'),
            'user_type' => 'student',
        ]);

        $response = $this->post('/login', [
            'email' => 'sessionsec@shilpa.lk',
            'password' => 'SessionPass2026!#',
        ]);

        $response->assertRedirect('/welcome.php');
        assert(session()->get('user') === 'yes', "Session user flag must be set");
        assert(session()->has('_last_activity'), "Session last activity timestamp must be recorded");
    }
}
