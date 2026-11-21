<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Models\User;

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

        if ($email === '' || $password === '') {
            session()->flash('error', 'Please provide both email and password.');
            session()->flash('old', ['email' => $email]);
            return $this->redirect('/login');
        }

        $user = User::findByEmail($email);
        if (!$user || !$user->verifyPassword($password)) {
            session()->flash('error', 'Invalid email credentials or password.');
            session()->flash('old', ['email' => $email]);
            return $this->redirect('/login');
        }

        // Set secure session
        session()->set('user', 'yes');
        session()->set('email', $user->Email);
        session()->set('user_id', $user->UserId);
        session()->set('user_name', $user->Full_Name);
        session()->set('user_role', $user->user_type ?? 'student');
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
        if (strlen($password) < 8) $errors['password'] = 'Password must be at least 8 characters long.';
        if ($password !== $repeatPassword) $errors['repeat_password'] = 'Passwords do not match.';

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
            'Password' => password_hash($password, PASSWORD_DEFAULT),
            'user_type' => $roleType,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        session()->set('user', 'yes');
        session()->set('email', $user->Email);
        session()->set('user_id', $user->UserId);
        session()->set('user_name', $user->Full_Name);
        session()->set('user_role', $user->user_type);
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
        session()->flash('status', 'If an account exists for ' . e($email) . ', password reset instructions have been generated.');
        return $this->redirect('/forgot-password');
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
