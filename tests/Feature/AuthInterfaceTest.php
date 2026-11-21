<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use App\Testing\TestCase;

class AuthInterfaceTest extends TestCase {
    public function testLoginPageRendersProperly(): void {
        $response = $this->get('/login');
        $response->assertOk()
            ->assertSee('Sign In')
            ->assertSee('action="/login"')
            ->assertSee('name="email"')
            ->assertSee('name="password"')
            ->assertSee('Forgot password?');
    }

    public function testRegisterPageRendersProperly(): void {
        $response = $this->get('/register');
        $response->assertOk()
            ->assertSee('Create your account')
            ->assertSee('action="/register"')
            ->assertSee('name="fullname"')
            ->assertSee('name="email"')
            ->assertSee('name="password"')
            ->assertSee('name="repeat_password"')
            ->assertSee('Terms of Service');
    }

    public function testForgotPasswordPageRendersProperly(): void {
        $response = $this->get('/forgot-password');
        $response->assertOk()
            ->assertSee('Reset your password')
            ->assertSee('action="/forgot-password"')
            ->assertSee('Send Password Reset Link');
    }

    public function testOnboardingRedirectsUnauthenticatedUser(): void {
        session()->destroy();
        $response = $this->get('/onboarding');
        $response->assertRedirect('/login');
    }

    public function testSuccessfulUserRegistrationAndRedirectToOnboarding(): void {
        $response = $this->post('/register', [
            'fullname' => 'Kavindu Perera',
            'email' => 'kavindu@shilpa.lk',
            'role' => 'student',
            'password' => 'SecurePass2026!#',
            'repeat_password' => 'SecurePass2026!#',
            'terms' => 'on',
        ]);

        $response->assertRedirect('/onboarding');

        $user = User::findByEmail('kavindu@shilpa.lk');
        assert($user !== null, "User should be persisted in database");
        assert($user->Full_Name === 'Kavindu Perera', "Full name must match");
        assert($user->verifyPassword('SecurePass2026!#') === true, "Password hash must verify");
    }

    public function testRegistrationFailsWithMismatchedPassword(): void {
        $response = $this->post('/register', [
            'fullname' => 'Test User',
            'email' => 'testmismatch@shilpa.lk',
            'role' => 'student',
            'password' => 'Password1234!',
            'repeat_password' => 'DifferentPassword1234!',
            'terms' => 'on',
        ]);

        $response->assertRedirect('/register');
        $user = User::findByEmail('testmismatch@shilpa.lk');
        assert($user === null, "User should not be created on validation failure");
    }

    public function testSuccessfulLoginRedirectsToWorkspace(): void {
        User::create([
            'Full_Name' => 'Nimal Jayawardena',
            'Email' => 'nimal@shilpa.lk',
            'Password' => password_hash('NimalPass2026!', PASSWORD_DEFAULT),
            'user_type' => 'student',
        ]);

        $response = $this->post('/login', [
            'email' => 'nimal@shilpa.lk',
            'password' => 'NimalPass2026!',
        ]);

        $response->assertRedirect('/welcome.php');
        assert(session()->get('user') === 'yes', "Session user flag should be set");
        assert(session()->get('email') === 'nimal@shilpa.lk', "Session email must be set");
    }

    public function testFailedLoginRedirectsBack(): void {
        $response = $this->post('/login', [
            'email' => 'unknown@shilpa.lk',
            'password' => 'wrongpassword',
        ]);

        $response->assertRedirect('/login');
    }

    public function testLogoutTerminatesSessionAndRedirectsHome(): void {
        session()->set('user', 'yes');
        session()->set('email', 'test@shilpa.lk');

        $response = $this->get('/logout');
        $response->assertRedirect('/');
        assert(!session()->has('user'), "User session must be cleared on logout");
    }
}
