<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Models\PasswordReset;
use App\Models\User;
use App\Security\PasswordService;
use App\Security\RateLimiter;
use App\Security\TwoFactorAuth;

class AuthController extends Controller {
    public function showLogin(Request $request): Response {
        if (session()->has('user')) {
            return $this->redirectBasedOnRole();
        }

        return $this->render('auth/login', [
            'title' => 'Sign In — ' . config('app.name', 'SHILPA LMS'),
        ]);
    }

    public function login(Request $request): Response {
        $email = trim((string)$request->post('email', ''));
        $password = (string)$request->post('password', '');
        $ip = (string)$request->server('REMOTE_ADDR', '127.0.0.1');
        $throttleKey = 'login:' . $ip . ':' . strtolower($email);

        if ($email === '' || $password === '') {
            session()->flash('error', 'Please provide both email and password.');
            session()->flash('old', ['email' => $email]);
            return $this->redirect('/login');
        }

        // Brute-force rate limiting check
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            session()->flash('error', "Too many failed login attempts. Locked out for {$seconds} seconds.");
            session()->flash('old', ['email' => $email]);
            return $this->redirect('/login');
        }

        $user = User::findByEmail($email);
        if (!$user || !PasswordService::verify($password, (string)($user->Password ?? $user->password ?? ''))) {
            RateLimiter::hit($throttleKey, 900, 5);
            session()->flash('error', 'Invalid email credentials or password.');
            session()->flash('old', ['email' => $email]);
            return $this->redirect('/login');
        }

        // Credentials are valid; clear throttle counter
        RateLimiter::clear($throttleKey);

        // Check if password needs automatic rehashing to upgraded security cost
        if (PasswordService::needsRehash((string)$user->Password)) {
            $user->Password = PasswordService::hash($password);
            $user->save();
        }

        // Set secure session with fixation protection
        session()->set('user', 'yes');
        session()->set('email', $user->Email);
        session()->set('user_id', $user->UserId);
        session()->set('user_name', $user->Full_Name);
        session()->set('user_role', $user->user_type ?? 'student');
        session()->set('_last_activity', time());
        session()->regenerate(true);

        return $this->redirectBasedOnRole($user);
    }

    public function showRegister(Request $request): Response {
        if (session()->has('user')) {
            return $this->redirectBasedOnRole();
        }

        return $this->render('auth/register', [
            'title' => 'Create Account — ' . config('app.name', 'SHILPA LMS'),
        ]);
    }

    public function register(Request $request): Response {
        $fullName = trim((string)$request->post('fullname', ''));
        $email = trim((string)$request->post('email', ''));
        $role = trim((string)$request->post('role', 'student'));
        $password = (string)$request->post('password', '');
        $repeatPassword = (string)$request->post('repeat_password', '');

        $errors = [];
        if ($fullName === '') $errors['fullname'] = 'Full name is required.';
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'A valid email is required.';
        if ($password !== $repeatPassword) $errors['repeat_password'] = 'Passwords do not match.';

        // Enforce NIST password complexity
        $strengthErrors = PasswordService::validateStrength($password);
        if (!empty($strengthErrors)) {
            $errors['password'] = implode(' ', $strengthErrors);
        }

        if (empty($errors)) {
            $existing = User::findByEmail($email);
            if ($existing) {
                $errors['email'] = 'An account with this email already exists.';
            }
        }

        if (!empty($errors)) {
            session()->flash('errors', $errors);
            session()->flash('old', ['fullname' => $fullName, 'email' => $email, 'role' => $role]);
            return $this->redirect('/register');
        }

        $roleType = ($role === 'lecturer') ? 'l' : 'student';

        $user = User::create([
            'Full_Name' => $fullName,
            'Email' => $email,
            'Password' => PasswordService::hash($password),
            'user_type' => $roleType,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        session()->set('user', 'yes');
        session()->set('email', $user->Email);
        session()->set('user_id', $user->UserId);
        session()->set('user_name', $user->Full_Name);
        session()->set('user_role', $user->user_type);
        session()->set('_last_activity', time());
        session()->regenerate(true);

        return $this->redirect('/onboarding');
    }

    public function showForgotPassword(Request $request): Response {
        return $this->render('auth/forgot-password', [
            'title' => 'Reset Password — ' . config('app.name', 'SHILPA LMS'),
        ]);
    }

    public function sendResetLink(Request $request): Response {
        $email = trim((string)$request->post('email', ''));
        $user = User::findByEmail($email);

        if ($user) {
            $token = PasswordReset::createToken($email);
            session()->flash('status', 'Password reset instructions have been generated. Use reset token: ' . $token);
            session()->flash('reset_token', $token);
        } else {
            session()->flash('status', 'If an account exists for ' . e($email) . ', password reset instructions have been generated.');
        }

        return $this->redirect('/forgot-password');
    }

    public function showResetPassword(Request $request): Response {
        $token = (string)$request->query('token', (string)session()->get('reset_token', ''));
        $email = (string)$request->query('email', '');

        return $this->render('auth/reset-password', [
            'title' => 'Set New Password — ' . config('app.name', 'SHILPA LMS'),
            'token' => $token,
            'email' => $email,
        ]);
    }

    public function updatePassword(Request $request): Response {
        $email = trim((string)$request->post('email', ''));
        $token = trim((string)$request->post('token', ''));
        $password = (string)$request->post('password', '');
        $passwordConfirmation = (string)$request->post('password_confirmation', '');

        $errors = [];
        if ($password !== $passwordConfirmation) {
            $errors[] = 'Password confirmation does not match.';
        }

        $strengthErrors = PasswordService::validateStrength($password);
        if (!empty($strengthErrors)) {
            $errors = array_merge($errors, $strengthErrors);
        }

        if (!PasswordReset::verifyToken($email, $token, 60)) {
            $errors[] = 'The password reset token is invalid or has expired.';
        }

        if (!empty($errors)) {
            session()->flash('errors', $errors);
            return $this->redirect('/reset-password?token=' . urlencode($token) . '&email=' . urlencode($email));
        }

        $user = User::findByEmail($email);
        if ($user) {
            $user->Password = PasswordService::hash($password);
            $user->save();
            PasswordReset::deleteToken($email);
        }

        session()->flash('success', 'Your password has been securely updated. Please sign in with your new credentials.');
        return $this->redirect('/login');
    }

    public function showTwoFactor(Request $request): Response {
        return $this->render('auth/two-factor', [
            'title' => 'Two-Factor Authentication — ' . config('app.name', 'SHILPA LMS'),
        ]);
    }

    public function verifyTwoFactor(Request $request): Response {
        $code = trim((string)$request->post('code', ''));
        $secret = (string)session()->get('2fa_secret', '');

        if ($secret !== '' && TwoFactorAuth::verifyCode($secret, $code)) {
            session()->set('2fa_verified', true);
            return $this->redirectBasedOnRole();
        }

        session()->flash('error', 'Invalid two-factor authentication code.');
        return $this->redirect('/two-factor');
    }

    public function showOnboarding(Request $request): Response {
        if (!session()->has('user')) {
            return $this->redirect('/login');
        }

        return $this->render('auth/onboarding', [
            'title' => 'Personalize Your Workspace — ' . config('app.name', 'SHILPA LMS'),
        ]);
    }

    public function completeOnboarding(Request $request): Response {
        return $this->redirect('/welcome.php');
    }

    public function logout(Request $request): Response {
        session()->destroy();
        return $this->redirect('/');
    }

    private function redirectBasedOnRole(?User $user = null): Response {
        $role = $user ? $user->user_type : session()->get('user_role');
        if (in_array($role, ['a', 'admin', 'administrator', 'superadmin'], true)) {
            return $this->redirect('/admin.php');
        }
        return $this->redirect('/welcome.php');
    }
}
