<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Services\NotificationService;

class NotificationController extends Controller {
    public function index(Request $request): Response {
        if (!session()->has('user')) {
            session()->flash('error', 'Please sign in to view your notifications.');
            return $this->redirect('/login');
        }

        $userId = (int)session()->get('user_id', 0);
        $filter = (string)$request->query('filter', 'all');

        $allNotifications = NotificationService::getUserNotifications($userId, false, 100);
        $unreadCount = NotificationService::getUnreadCount($userId);

        // Apply tab filtering
        $notifications = match ($filter) {
            'unread' => array_filter($allNotifications, fn($n) => !$n->isRead()),
            'assignments' => array_filter($allNotifications, fn($n) => str_starts_with((string)$n->type, 'assignment')),
            'announcements' => array_filter($allNotifications, fn($n) => str_starts_with((string)$n->type, 'announcement')),
            'grades' => array_filter($allNotifications, fn($n) => str_starts_with((string)$n->type, 'grade') || str_starts_with((string)$n->type, 'quiz')),
            default => $allNotifications,
        };

        return $this->render('notifications/index', [
            'title' => 'Notification Center & Academic Alerts — SHILPA LMS',
            'notifications' => array_values($notifications),
            'unreadCount' => $unreadCount,
            'currentFilter' => $filter,
        ]);
    }

    public function apiIndex(Request $request): Response {
        if (!session()->has('user')) {
            return json(['unread_count' => 0, 'notifications' => []], 401);
        }

        $userId = (int)session()->get('user_id', 0);
        $unreadCount = NotificationService::getUnreadCount($userId);
        $notifications = NotificationService::getUserNotifications($userId, false, 10);

        return json([
            'unread_count' => $unreadCount,
            'notifications' => array_map(fn($n) => $n->toArray(), $notifications),
        ]);
    }

    public function markRead(Request $request, $id): Response {
        if (!session()->has('user')) {
            return $this->redirect('/login');
        }

        $userId = (int)session()->get('user_id', 0);
        NotificationService::markAsRead((int)$id, $userId);

        if ($request->isAjax()) {
            return json(['success' => true]);
        }

        return $this->redirect('/notifications');
    }

    public function markAllRead(Request $request): Response {
        if (!session()->has('user')) {
            return $this->redirect('/login');
        }

        $userId = (int)session()->get('user_id', 0);
        NotificationService::markAllAsRead($userId);

        if ($request->isAjax()) {
            return json(['success' => true]);
        }

        session()->flash('success', 'All notifications marked as read.');
        return $this->redirect('/notifications');
    }

    public function settings(Request $request): Response {
        if (!session()->has('user')) {
            return $this->redirect('/login');
        }

        $userId = (int)session()->get('user_id', 0);
        $preferences = NotificationService::getPreferences($userId);

        return $this->render('notifications/settings', [
            'title' => 'Notification Preferences & Email Alerts — SHILPA LMS',
            'preferences' => $preferences,
        ]);
    }

    public function updateSettings(Request $request): Response {
        if (!session()->has('user')) {
            return $this->redirect('/login');
        }

        $userId = (int)session()->get('user_id', 0);
        NotificationService::updatePreferences($userId, $request->all());

        session()->flash('success', 'Notification preferences saved successfully.');
        return $this->redirect('/notifications/settings');
    }
}
