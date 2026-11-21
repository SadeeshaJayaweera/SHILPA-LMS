<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Models\ActiveSession;
use App\Models\User;
use App\Models\UserProfile;
use App\Security\PasswordService;
use App\Services\ProfileService;
use App\Testing\TestCase;

class UserProfileAndAccountTest extends TestCase {
    public function testProfilePageRequiresAuthentication(): void {
        session()->clear();
        $response = $this->get('/profile');
        assert($response->getStatusCode() === 302, "Unauthenticated profile access must redirect to login");
    }

    public function testProfilePageRendersForAuthenticatedUser(): void {
        $user = User::create([
            'Full_Name' => 'Profile Test User',
            'Email' => 'profileuser@shilpa.lk',
            'Password' => PasswordService::hash('ProfilePass2026!#'),
            'user_type' => 'student',
        ]);

        session()->set('user', $user->Full_Name);
        session()->set('user_id', $user->id);
        session()->set('email', $user->Email);
        session()->set('user_type', 'student');

        $response = $this->get('/profile');
        assert($response->getStatusCode() === 200, "Authenticated user should access profile with 200 OK");
        $response->assertSee('Profile &amp; Account Settings');
        $response->assertSee('profileuser@shilpa.lk');
    }

    public function testGeneralProfileUpdate(): void {
        $user = User::create([
            'Full_Name' => 'Original Name',
            'Email' => 'updateuser@shilpa.lk',
            'Password' => PasswordService::hash('ProfilePass2026!#'),
            'user_type' => 'student',
        ]);

        $result = ProfileService::updateGeneralProfile((int)$user->id, [
            'full_name' => 'Updated Modern Name',
            'headline' => 'Senior Machine Learning Specialist',
            'bio' => 'Focusing on distributed deep learning systems and NLP.',
            'phone' => '+94 77 987 6543',
        ]);

        assert($result['success'] === true, "Profile update should succeed");
        
        $refreshedUser = User::find((int)$user->id);
        assert($refreshedUser->Full_Name === 'Updated Modern Name', "User full name must be updated in database");

        $profile = UserProfile::forUser((int)$user->id);
        assert($profile->headline === 'Senior Machine Learning Specialist', "Profile headline must match updated value");
        assert($profile->phone === '+94 77 987 6543', "Phone number must match updated value");
    }

    public function testPreferencesUpdate(): void {
        $user = User::create([
            'Full_Name' => 'Preferences User',
            'Email' => 'prefuser@shilpa.lk',
            'Password' => PasswordService::hash('ProfilePass2026!#'),
            'user_type' => 'student',
        ]);

        $result = ProfileService::updatePreferences((int)$user->id, [
            'theme_preference' => 'dark',
            'language' => 'si',
            'timezone' => 'Asia/Colombo',
            'email_notifications' => 1,
            'security_alerts' => 1,
        ]);

        assert($result['success'] === true, "Preferences update should succeed");

        $profile = UserProfile::forUser((int)$user->id);
        assert($profile->theme_preference === 'dark', "Theme preference should be dark");
        assert($profile->language === 'si', "Language should be si");
    }

    public function testPasswordChangeVerification(): void {
        $user = User::create([
            'Full_Name' => 'Password User',
            'Email' => 'passuser@shilpa.lk',
            'Password' => PasswordService::hash('OldSecure2026!#'),
            'user_type' => 'student',
        ]);

        // 1. Wrong current password fails
        $wrongCurrent = ProfileService::updatePassword(
            (int)$user->id,
            'IncorrectOldPass',
            'NewSecure2026!#',
            'NewSecure2026!#'
        );
        assert($wrongCurrent['success'] === false, "Incorrect current password must fail");
        assert(str_contains($wrongCurrent['error'], 'current password'), "Error message should mention current password");

        // 2. Mismatched confirmation fails
        $mismatched = ProfileService::updatePassword(
            (int)$user->id,
            'OldSecure2026!#',
            'NewSecure2026!#',
            'DifferentNewSecure2026!#'
        );
        assert($mismatched['success'] === false, "Mismatched passwords must fail");

        // 3. Weak new password fails
        $weak = ProfileService::updatePassword(
            (int)$user->id,
            'OldSecure2026!#',
            'weak',
            'weak'
        );
        assert($weak['success'] === false, "Weak password must fail complexity checks");

        // 4. Valid password change succeeds
        $valid = ProfileService::updatePassword(
            (int)$user->id,
            'OldSecure2026!#',
            'BrandNewSecure2026!#',
            'BrandNewSecure2026!#'
        );
        assert($valid['success'] === true, "Valid password change must succeed");

        $refreshedUser = User::find((int)$user->id);
        assert(PasswordService::verify('BrandNewSecure2026!#', (string)$refreshedUser->Password), "New password must verify successfully");
    }

    public function testActiveSessionTrackingAndTermination(): void {
        $user = User::create([
            'Full_Name' => 'Session User',
            'Email' => 'sessionuser@shilpa.lk',
            'Password' => PasswordService::hash('SessionPass2026!#'),
            'user_type' => 'student',
        ]);

        $userId = (int)$user->id;
        $sess1 = 'sess_device_chrome_101';
        $sess2 = 'sess_device_safari_102';
        $sess3 = 'sess_device_firefox_103';

        ActiveSession::record($userId, $sess1);
        ActiveSession::record($userId, $sess2);
        ActiveSession::record($userId, $sess3);

        $sessions = ActiveSession::forUser($userId);
        assert(count($sessions) === 3, "User should have 3 recorded sessions");

        // Terminate individual session 3
        $terminated = ProfileService::terminateSession($userId, $sess3);
        assert($terminated === true, "Individual session termination should succeed");
        assert(count(ActiveSession::forUser($userId)) === 2, "Session count should now be 2");

        // Terminate all other sessions keeping sess1
        $terminatedOthers = ProfileService::terminateOtherSessions($userId, $sess1);
        assert($terminatedOthers === 1, "Should have terminated 1 remaining other session");
        
        $finalSessions = ActiveSession::forUser($userId);
        assert(count($finalSessions) === 1, "Only current session should remain");
        assert($finalSessions[0]['id'] === $sess1, "Remaining session must be current session");
    }
}
