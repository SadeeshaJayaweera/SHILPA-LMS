<?php
declare(strict_types=1);

namespace Tests\Unit;

use App\Models\User;
use App\Testing\TestCase;

class UserTest extends TestCase {
    public function testUserCreationAndAttributes(): void {
        $user = User::create([
            'Full_Name' => 'Ashani Fernando',
            'Email' => 'ashani@shilpa.lk',
            'Password' => password_hash('SecretPass123!', PASSWORD_DEFAULT),
            'user_type' => 'student',
        ]);

        assert($user->UserId > 0, "User ID should be assigned");
        assert($user->Full_Name === 'Ashani Fernando', "User name should match");
        assert($user->isStudent() === true, "User should be student");
        assert($user->isAdmin() === false, "User should not be admin");
    }

    public function testPasswordVerification(): void {
        $user = new User();
        $user->setPassword('MySecurePassword2026!');
        assert($user->verifyPassword('MySecurePassword2026!') === true, "Password should verify");
        assert($user->verifyPassword('WrongPassword') === false, "Wrong password should fail");
    }

    public function testAdminRoleCheck(): void {
        $admin = new User(['user_type' => 'a']);
        assert($admin->isAdmin() === true, "User with type 'a' should be admin");
        assert($admin->isStudent() === false, "Admin is not student");
    }
}
