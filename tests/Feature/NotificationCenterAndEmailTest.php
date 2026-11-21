<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\NotificationPreference;
use App\Models\User;
use App\Services\MailService;
use App\Services\NotificationService;
use App\Testing\TestCase;

class NotificationCenterAndEmailTest extends TestCase {
    public function testNotificationCreationAndEmailDispatch(): void {
        $user = User::create([
            'Full_Name' => 'Emmy Noether',
            'Email' => 'noether.' . uniqid() . '@shilpa.edu',
            'Password' => password_hash('Symmetry123!', PASSWORD_BCRYPT),
            'user_type' => 'student',
        ]);

        MailService::clearSentMails();

        // 1. Create notification with email preference active
        $notif = NotificationService::notify(
            (int)$user->id,
            'assignment.deadline',
            'Abstract Algebra Problem Set Due Tomorrow',
            'Please submit your Ring and Field proofs by 23:59.',
            '/assignments/101',
            'clock'
        );

        assert($notif->title === 'Abstract Algebra Problem Set Due Tomorrow');
        assert(!$notif->isRead());
        assert((int)$notif->user_id === (int)$user->id);

        // 2. Assert transactional email was dispatched
        $sentMails = MailService::getSentMails();
        assert(count($sentMails) === 1);
        assert($sentMails[0]['to_email'] === (string)$user->Email);
        assert(str_contains($sentMails[0]['subject'], 'Abstract Algebra Problem Set Due Tomorrow'));
        assert(str_contains($sentMails[0]['html'], 'Ring and Field proofs'));
    }

    public function testNotificationPreferencesMutingEmail(): void {
        $user = User::create([
            'Full_Name' => 'Kurt Godel',
            'Email' => 'godel.' . uniqid() . '@shilpa.edu',
            'Password' => password_hash('Incomplete123!', PASSWORD_BCRYPT),
            'user_type' => 'student',
        ]);

        // Mute assignment alerts
        NotificationService::updatePreferences((int)$user->id, [
            'email_enabled' => 1,
            'assignment_alerts' => 0,
            'announcement_alerts' => 1,
        ]);

        MailService::clearSentMails();

        // Dispatch assignment notification
        NotificationService::notify(
            (int)$user->id,
            'assignment.released',
            'Incompleteness Theorems Homework',
            'Formal systems logic problem set.',
            '/assignments/202'
        );

        // In-app notification created, but email muted
        $inbox = NotificationService::getUserNotifications((int)$user->id);
        assert(count($inbox) === 1);
        assert(empty(MailService::getSentMails()));
    }

    public function testMarkAsReadAndUnreadCount(): void {
        $user = User::create([
            'Full_Name' => 'Henri Poincare',
            'Email' => 'poincare.' . uniqid() . '@shilpa.edu',
            'Password' => password_hash('Conjecture1!', PASSWORD_BCRYPT),
            'user_type' => 'student',
        ]);

        assert(NotificationService::getUnreadCount((int)$user->id) === 0);

        // Create 3 alerts
        $n1 = NotificationService::notify((int)$user->id, 'grade.published', 'Topology Exam Graded', 'Score: 98/100 (A+)');
        $n2 = NotificationService::notify((int)$user->id, 'announcement.posted', 'Department Colloquium', 'Friday at 2 PM');
        $n3 = NotificationService::notify((int)$user->id, 'discussion.reply', 'New reply to your conjecture', 'Perelman posted an answer');

        assert(NotificationService::getUnreadCount((int)$user->id) === 3);

        // Mark single as read
        $readResult = NotificationService::markAsRead((int)$n1->id, (int)$user->id);
        assert($readResult === true);
        assert(NotificationService::getUnreadCount((int)$user->id) === 2);

        // Mark all as read
        $allRead = NotificationService::markAllAsRead((int)$user->id);
        assert($allRead === 2);
        assert(NotificationService::getUnreadCount((int)$user->id) === 0);
    }

    public function testNotificationControllerHttpEndpoints(): void {
        $user = User::create([
            'Full_Name' => 'Srinivasa Ramanujan',
            'Email' => 'ramanujan.' . uniqid() . '@shilpa.edu',
            'Password' => password_hash('Hardy1729!', PASSWORD_BCRYPT),
            'user_type' => 'student',
        ]);

        $notif = NotificationService::notify(
            (int)$user->id,
            'assignment.reminder',
            'Modular Forms and Mock Theta Functions',
            'Draft manuscript review due next week.',
            '/assignments/1729'
        );

        $this->actingAs($user);

        // 1. View Notification Center
        $indexRes = $this->get('/notifications');
        $indexRes->assertStatus(200);
        $indexRes->assertSee('Notification Center');
        $indexRes->assertSee('Modular Forms and Mock Theta Functions');

        // 2. API endpoint
        $apiRes = $this->get('/api/notifications');
        $apiRes->assertStatus(200);
        $apiRes->assertSee('Modular Forms');

        // 3. Mark single notification as read via POST
        $readRes = $this->post("/notifications/{$notif->id}/read", [
            '_csrf' => session()->csrfToken(),
        ]);
        $readRes->assertStatus(302);

        // 4. View Preferences Settings Page
        $settingsRes = $this->get('/notifications/settings');
        $settingsRes->assertStatus(200);
        $settingsRes->assertSee('Notification Preferences');

        // 5. Update Preferences via POST
        $updateRes = $this->post('/notifications/settings', [
            '_csrf' => session()->csrfToken(),
            'email_enabled' => '1',
            'assignment_alerts' => '1',
            'announcement_alerts' => '0',
            'grade_alerts' => '1',
            'discussion_alerts' => '0',
        ]);
        $updateRes->assertStatus(302);

        $prefs = NotificationService::getPreferences((int)$user->id);
        assert((int)$prefs->announcement_alerts === 0);
        assert((int)$prefs->grade_alerts === 1);
    }
}
