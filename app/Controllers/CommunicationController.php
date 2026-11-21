<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Models\Course;
use App\Models\DiscussionThread;
use App\Models\Enrollment;
use App\Models\User;
use App\Services\CommunicationService;

class CommunicationController extends Controller {
    // ==========================================
    // Course & Global Announcements
    // ==========================================

    public function indexAnnouncements(Request $request, $id): Response {
        if (!session()->has('user')) {
            session()->flash('error', 'Please sign in to view course announcements.');
            return $this->redirect('/login');
        }

        $courseId = (int)$id;
        $course = Course::find($courseId);
        if (!$course) {
            session()->flash('error', 'Course not found.');
            return $this->redirect('/courses');
        }

        $announcements = CommunicationService::getCourseAnnouncements($courseId);
        $userRole = (string)session()->get('user_role', 'student');
        $isFacultyOrAdmin = in_array(strtolower($userRole), ['l', 'lecturer', 'instructor', 'teacher', 'a', 'admin', 'administrator', 'superadmin'], true);

        return $this->render('communication/announcements/index', [
            'title' => 'Course Announcements — ' . htmlspecialchars((string)$course->C_NAME),
            'course' => $course,
            'announcements' => $announcements,
            'isFacultyOrAdmin' => $isFacultyOrAdmin,
        ]);
    }

    public function globalAnnouncements(Request $request): Response {
        if (!session()->has('user')) {
            session()->flash('error', 'Please sign in to view announcements.');
            return $this->redirect('/login');
        }

        $announcements = CommunicationService::getCourseAnnouncements(null);
        $userRole = (string)session()->get('user_role', 'student');
        $isFacultyOrAdmin = in_array(strtolower($userRole), ['l', 'lecturer', 'instructor', 'teacher', 'a', 'admin', 'administrator', 'superadmin'], true);

        return $this->render('communication/announcements/index', [
            'title' => 'Campus & Course Announcements — SHILPA LMS',
            'course' => null,
            'announcements' => $announcements,
            'isFacultyOrAdmin' => $isFacultyOrAdmin,
        ]);
    }

    public function storeAnnouncement(Request $request, $id): Response {
        if ($auth = $this->checkFacultyAuthorization()) {
            return $auth;
        }

        $courseId = (int)$id;
        $authorId = (int)session()->get('user_id', 0);

        try {
            CommunicationService::createAnnouncement($courseId, $authorId, [
                'title' => (string)$request->input('title', ''),
                'content' => (string)$request->input('content', ''),
                'is_pinned' => $request->input('is_pinned') ? 1 : 0,
            ]);

            session()->flash('success', 'Announcement published to course feed.');
            return $this->redirect("/courses/{$courseId}/announcements");
        } catch (\Throwable $e) {
            session()->flash('error', $e->getMessage());
            return $this->redirect("/courses/{$courseId}/announcements");
        }
    }

    public function deleteAnnouncement(Request $request, $id): Response {
        if ($auth = $this->checkFacultyAuthorization()) {
            return $auth;
        }

        $userId = (int)session()->get('user_id', 0);
        CommunicationService::deleteAnnouncement((int)$id, $userId);

        session()->flash('success', 'Announcement deleted.');
        return $this->redirect($_SERVER['HTTP_REFERER'] ?? '/announcements');
    }

    // ==========================================
    // Discussion Forums & Threads
    // ==========================================

    public function indexDiscussions(Request $request, $id): Response {
        if (!session()->has('user')) {
            session()->flash('error', 'Please sign in to participate in course discussions.');
            return $this->redirect('/login');
        }

        $courseId = (int)$id;
        $course = Course::find($courseId);
        if (!$course) {
            session()->flash('error', 'Course not found.');
            return $this->redirect('/courses');
        }

        $threads = CommunicationService::getCourseThreads($courseId);

        return $this->render('communication/discussions/index', [
            'title' => 'Discussion Forum & Peer Q&A — ' . htmlspecialchars((string)$course->C_NAME),
            'course' => $course,
            'threads' => $threads,
        ]);
    }

    public function globalDiscussions(Request $request): Response {
        if (!session()->has('user')) {
            session()->flash('error', 'Please sign in to access discussions.');
            return $this->redirect('/login');
        }

        $userId = (int)session()->get('user_id', 0);
        $enrolled = Enrollment::query()->where('user_id', $userId)->first();
        if ($enrolled && !empty($enrolled['course_id'])) {
            return $this->redirect('/courses/' . (int)$enrolled['course_id'] . '/discussions');
        }

        $firstCourse = Course::query()->first();
        if ($firstCourse && !empty($firstCourse['C_ID'])) {
            return $this->redirect('/courses/' . (int)$firstCourse['C_ID'] . '/discussions');
        }

        return $this->redirect('/courses');
    }

    public function showDiscussion(Request $request, $courseId, $threadId): Response {
        if (!session()->has('user')) {
            session()->flash('error', 'Please sign in to view discussion threads.');
            return $this->redirect('/login');
        }

        $data = CommunicationService::getThreadWithReplies((int)$threadId);
        if (!$data) {
            session()->flash('error', 'Discussion thread not found.');
            return $this->redirect("/courses/{$courseId}/discussions");
        }

        return $this->render('communication/discussions/show', [
            'title' => htmlspecialchars((string)$data['thread']->title) . ' — Forum Thread',
            'course' => $data['course'],
            'thread' => $data['thread'],
            'author' => $data['author'],
            'replies' => $data['replies'],
        ]);
    }

    public function storeDiscussion(Request $request, $id): Response {
        if (!session()->has('user')) {
            session()->flash('error', 'Please sign in to post in discussions.');
            return $this->redirect('/login');
        }

        $courseId = (int)$id;
        $userId = (int)session()->get('user_id', 0);

        try {
            $thread = CommunicationService::createThread((int)$courseId, $userId, [
                'title' => (string)$request->input('title', ''),
                'body' => (string)$request->input('body', ''),
                'is_pinned' => $request->input('is_pinned') ? 1 : 0,
            ]);

            session()->flash('success', 'Discussion thread posted.');
            return $this->redirect("/courses/{$courseId}/discussions/{$thread->id}");
        } catch (\Throwable $e) {
            session()->flash('error', $e->getMessage());
            return $this->redirect("/courses/{$courseId}/discussions");
        }
    }

    public function replyDiscussion(Request $request, $courseId, $threadId): Response {
        if (!session()->has('user')) {
            session()->flash('error', 'Please sign in to reply to discussions.');
            return $this->redirect('/login');
        }

        $userId = (int)session()->get('user_id', 0);
        $body = (string)$request->input('body', '');
        $parentId = $request->input('parent_reply_id') ? (int)$request->input('parent_reply_id') : null;

        try {
            CommunicationService::replyToThread((int)$threadId, $userId, $body, $parentId);
            session()->flash('success', 'Reply posted.');
            return $this->redirect("/courses/{$courseId}/discussions/{$threadId}");
        } catch (\Throwable $e) {
            session()->flash('error', $e->getMessage());
            return $this->redirect("/courses/{$courseId}/discussions/{$threadId}");
        }
    }

    public function togglePinDiscussion(Request $request, $id): Response {
        if ($auth = $this->checkFacultyAuthorization()) {
            return $auth;
        }

        $userId = (int)session()->get('user_id', 0);
        CommunicationService::toggleThreadPin((int)$id, $userId);

        session()->flash('success', 'Thread pin status updated.');
        return $this->redirect($_SERVER['HTTP_REFERER'] ?? '/discussions');
    }

    public function toggleLockDiscussion(Request $request, $id): Response {
        if ($auth = $this->checkFacultyAuthorization()) {
            return $auth;
        }

        $userId = (int)session()->get('user_id', 0);
        CommunicationService::toggleThreadLock((int)$id, $userId);

        session()->flash('success', 'Thread lock status updated.');
        return $this->redirect($_SERVER['HTTP_REFERER'] ?? '/discussions');
    }

    // ==========================================
    // Direct Messaging & Conversations
    // ==========================================

    public function indexMessages(Request $request): Response {
        if (!session()->has('user')) {
            session()->flash('error', 'Please sign in to access direct messages.');
            return $this->redirect('/login');
        }

        $userId = (int)session()->get('user_id', 0);
        $conversations = CommunicationService::getRecentConversations($userId);

        $activeUser = null;
        $messages = [];
        if (!empty($conversations)) {
            $activeUser = $conversations[0]['user'];
            $messages = CommunicationService::getConversationBetween($userId, (int)$activeUser->id);
        }

        return $this->render('communication/messages/index', [
            'title' => 'Direct Messages & Academic Inquiries — SHILPA LMS',
            'conversations' => $conversations,
            'activeUser' => $activeUser,
            'messages' => $messages,
        ]);
    }

    public function showConversation(Request $request, $userId): Response {
        if (!session()->has('user')) {
            session()->flash('error', 'Please sign in to access direct messages.');
            return $this->redirect('/login');
        }

        $currentUserId = (int)session()->get('user_id', 0);
        $targetUserId = (int)$userId;
        $activeUser = User::find($targetUserId);

        if (!$activeUser) {
            session()->flash('error', 'User not found.');
            return $this->redirect('/messages');
        }

        $conversations = CommunicationService::getRecentConversations($currentUserId);
        $messages = CommunicationService::getConversationBetween($currentUserId, $targetUserId);

        return $this->render('communication/messages/index', [
            'title' => 'Chat with ' . htmlspecialchars((string)$activeUser->Full_Name) . ' — Messages',
            'conversations' => $conversations,
            'activeUser' => $activeUser,
            'messages' => $messages,
        ]);
    }

    public function sendMessage(Request $request): Response {
        if (!session()->has('user')) {
            if ($request->isAjax()) {
                return json(['success' => false, 'error' => 'Unauthenticated'], 401);
            }
            return $this->redirect('/login');
        }

        $senderId = (int)session()->get('user_id', 0);
        $recipientId = (int)$request->input('recipient_id', 0);
        $message = (string)$request->input('message', '');

        try {
            $dm = CommunicationService::sendMessage($senderId, $recipientId, $message);

            if ($request->isAjax()) {
                return json([
                    'success' => true,
                    'message' => $dm->toArray(),
                ]);
            }

            return $this->redirect("/messages/{$recipientId}");
        } catch (\Throwable $e) {
            if ($request->isAjax()) {
                return json(['success' => false, 'error' => $e->getMessage()], 422);
            }
            session()->flash('error', $e->getMessage());
            return $this->redirect('/messages');
        }
    }

    public function unreadCount(Request $request): Response {
        if (!session()->has('user')) {
            return json(['unread' => 0]);
        }

        $userId = (int)session()->get('user_id', 0);
        $count = CommunicationService::getUnreadMessagesCount($userId);

        return json(['unread' => $count]);
    }

    private function checkFacultyAuthorization(): ?Response {
        if (!session()->has('user')) {
            session()->flash('error', 'Please sign in to perform this action.');
            return $this->redirect('/login');
        }

        $userRole = (string)session()->get('user_role', '');
        $isFacultyOrAdmin = in_array(strtolower($userRole), ['l', 'lecturer', 'instructor', 'teacher', 'a', 'admin', 'administrator', 'superadmin'], true);

        if (!$isFacultyOrAdmin) {
            session()->flash('error', 'Faculty or administrative privileges required.');
            return $this->redirect('/workspace');
        }

        return null;
    }
}
