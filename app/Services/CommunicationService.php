<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Announcement;
use App\Models\Course;
use App\Models\DirectMessage;
use App\Models\DiscussionReply;
use App\Models\DiscussionThread;
use App\Models\User;
use App\Security\SecurityAuditLog;
use InvalidArgumentException;
use RuntimeException;

class CommunicationService {
    // ==========================================
    // Course & Campus Announcements
    // ==========================================

    public static function createAnnouncement(?int $courseId, int $authorId, array $data): Announcement {
        $title = trim((string)($data['title'] ?? ''));
        $content = trim((string)($data['content'] ?? ''));

        if ($title === '' || $content === '') {
            throw new InvalidArgumentException('Announcement title and content are required.');
        }

        $now = date('Y-m-d H:i:s');
        $announcement = Announcement::create([
            'course_id' => $courseId,
            'author_id' => $authorId,
            'title' => $title,
            'content' => $content,
            'is_pinned' => !empty($data['is_pinned']) ? 1 : 0,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        SecurityAuditLog::log('communication.announcement.created', 'info', $authorId, null, [
            'announcement_id' => $announcement->id,
            'course_id' => $courseId,
            'title' => $title,
        ]);

        return $announcement;
    }

    public static function getCourseAnnouncements(?int $courseId): array {
        return Announcement::forCourse($courseId);
    }

    public static function deleteAnnouncement(int $announcementId, int $requesterId): bool {
        $ann = Announcement::find($announcementId);
        if (!$ann) {
            return false;
        }

        $ann->delete();

        SecurityAuditLog::log('communication.announcement.deleted', 'info', $requesterId, null, [
            'announcement_id' => $announcementId,
        ]);

        return true;
    }

    // ==========================================
    // Discussion Forums & Threads
    // ==========================================

    public static function createThread(int $courseId, int $userId, array $data): DiscussionThread {
        $title = trim((string)($data['title'] ?? ''));
        $body = trim((string)($data['body'] ?? ''));

        if ($title === '' || $body === '') {
            throw new InvalidArgumentException('Thread title and discussion body are required.');
        }

        $course = Course::find($courseId);
        if (!$course) {
            throw new RuntimeException("Course not found: $courseId");
        }

        $now = date('Y-m-d H:i:s');
        $thread = DiscussionThread::create([
            'course_id' => $courseId,
            'user_id' => $userId,
            'title' => $title,
            'body' => $body,
            'is_pinned' => !empty($data['is_pinned']) ? 1 : 0,
            'is_locked' => 0,
            'replies_count' => 0,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        SecurityAuditLog::log('communication.discussion.thread_created', 'info', $userId, null, [
            'thread_id' => $thread->id,
            'course_id' => $courseId,
            'title' => $title,
        ]);

        return $thread;
    }

    public static function getCourseThreads(int $courseId): array {
        return DiscussionThread::forCourse($courseId);
    }

    public static function getThreadWithReplies(int $threadId): ?array {
        $thread = DiscussionThread::find($threadId);
        if (!$thread) {
            return null;
        }

        return [
            'thread' => $thread,
            'author' => $thread->author(),
            'course' => $thread->course(),
            'replies' => $thread->replies(),
        ];
    }

    public static function replyToThread(int $threadId, int $userId, string $body, ?int $parentReplyId = null): DiscussionReply {
        $thread = DiscussionThread::find($threadId);
        if (!$thread) {
            throw new RuntimeException("Discussion thread not found: $threadId");
        }

        if ($thread->isLocked()) {
            throw new RuntimeException('This discussion thread has been locked by faculty.');
        }

        $cleanBody = trim($body);
        if ($cleanBody === '') {
            throw new InvalidArgumentException('Reply body cannot be empty.');
        }

        $now = date('Y-m-d H:i:s');
        $reply = DiscussionReply::create([
            'thread_id' => $threadId,
            'user_id' => $userId,
            'body' => $cleanBody,
            'is_solution' => 0,
            'parent_reply_id' => $parentReplyId,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Increment replies count and touch timestamp
        $thread->replies_count = (int)$thread->replies_count + 1;
        $thread->updated_at = $now;
        $thread->save();

        SecurityAuditLog::log('communication.discussion.replied', 'info', $userId, null, [
            'thread_id' => $threadId,
            'reply_id' => $reply->id,
        ]);

        return $reply;
    }

    public static function toggleThreadLock(int $threadId, int $requesterId): bool {
        $thread = DiscussionThread::find($threadId);
        if (!$thread) {
            return false;
        }

        $thread->is_locked = $thread->isLocked() ? 0 : 1;
        $thread->updated_at = date('Y-m-d H:i:s');
        $thread->save();

        SecurityAuditLog::log('communication.discussion.thread_locked_toggle', 'info', $requesterId, null, [
            'thread_id' => $threadId,
            'is_locked' => $thread->is_locked,
        ]);

        return true;
    }

    public static function toggleThreadPin(int $threadId, int $requesterId): bool {
        $thread = DiscussionThread::find($threadId);
        if (!$thread) {
            return false;
        }

        $thread->is_pinned = $thread->isPinned() ? 0 : 1;
        $thread->updated_at = date('Y-m-d H:i:s');
        $thread->save();

        SecurityAuditLog::log('communication.discussion.thread_pinned_toggle', 'info', $requesterId, null, [
            'thread_id' => $threadId,
            'is_pinned' => $thread->is_pinned,
        ]);

        return true;
    }

    // ==========================================
    // Direct Messaging & Conversations
    // ==========================================

    public static function sendMessage(int $senderId, int $recipientId, string $message): DirectMessage {
        $cleanMessage = trim($message);
        if ($cleanMessage === '') {
            throw new InvalidArgumentException('Message cannot be empty.');
        }

        if ($senderId === $recipientId) {
            throw new InvalidArgumentException('Cannot send direct message to self.');
        }

        $recipient = User::find($recipientId);
        if (!$recipient) {
            throw new RuntimeException("Recipient not found: $recipientId");
        }

        $now = date('Y-m-d H:i:s');
        $dm = DirectMessage::create([
            'sender_id' => $senderId,
            'recipient_id' => $recipientId,
            'message' => $cleanMessage,
            'is_read' => 0,
            'read_at' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        SecurityAuditLog::log('communication.message.sent', 'info', $senderId, null, [
            'message_id' => $dm->id,
            'recipient_id' => $recipientId,
        ]);

        return $dm;
    }

    public static function getConversationBetween(int $user1Id, int $user2Id, int $limit = 100): array {
        // Mark messages to user1 from user2 as read
        self::markConversationAsRead($user1Id, $user2Id);

        $pdo = \App\Database\Connection::get();
        $stmt = $pdo->prepare('
            SELECT * FROM direct_messages
            WHERE (sender_id = :u1 AND recipient_id = :u2)
               OR (sender_id = :u2 AND recipient_id = :u1)
            ORDER BY created_at ASC
            LIMIT :limit
        ');
        $stmt->bindValue(':u1', $user1Id, \PDO::PARAM_INT);
        $stmt->bindValue(':u2', $user2Id, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return array_map(fn($r) => new DirectMessage($r), $rows);
    }

    public static function getRecentConversations(int $userId): array {
        $pdo = \App\Database\Connection::get();
        $stmt = $pdo->prepare('
            SELECT DISTINCT
                CASE WHEN sender_id = :u THEN recipient_id ELSE sender_id END AS other_user_id
            FROM direct_messages
            WHERE sender_id = :u OR recipient_id = :u
        ');
        $stmt->bindValue(':u', $userId, \PDO::PARAM_INT);
        $stmt->execute();
        $otherUserIds = $stmt->fetchAll(\PDO::FETCH_COLUMN);

        $conversations = [];
        foreach ($otherUserIds as $otherId) {
            $otherUser = User::find((int)$otherId);
            if (!$otherUser) {
                continue;
            }

            // Latest message
            $stmtLatest = $pdo->prepare('
                SELECT * FROM direct_messages
                WHERE (sender_id = :u AND recipient_id = :o)
                   OR (sender_id = :o AND recipient_id = :u)
                ORDER BY created_at DESC
                LIMIT 1
            ');
            $stmtLatest->bindValue(':u', $userId, \PDO::PARAM_INT);
            $stmtLatest->bindValue(':o', (int)$otherId, \PDO::PARAM_INT);
            $stmtLatest->execute();
            $latestRow = $stmtLatest->fetch(\PDO::FETCH_ASSOC);

            // Unread count
            $stmtUnread = $pdo->prepare('
                SELECT COUNT(*) FROM direct_messages
                WHERE recipient_id = :u AND sender_id = :o AND is_read = 0
            ');
            $stmtUnread->bindValue(':u', $userId, \PDO::PARAM_INT);
            $stmtUnread->bindValue(':o', (int)$otherId, \PDO::PARAM_INT);
            $stmtUnread->execute();
            $unreadCount = (int)$stmtUnread->fetchColumn();

            $conversations[] = [
                'user' => $otherUser,
                'latestMessage' => $latestRow ? new DirectMessage($latestRow) : null,
                'unreadCount' => $unreadCount,
            ];
        }

        // Sort by latest message date descending
        usort($conversations, function ($a, $b) {
            $timeA = $a['latestMessage'] ? strtotime((string)$a['latestMessage']->created_at) : 0;
            $timeB = $b['latestMessage'] ? strtotime((string)$b['latestMessage']->created_at) : 0;
            return $timeB <=> $timeA;
        });

        return $conversations;
    }

    public static function markConversationAsRead(int $recipientId, int $senderId): int {
        $pdo = \App\Database\Connection::get();
        $now = date('Y-m-d H:i:s');
        $stmt = $pdo->prepare('
            UPDATE direct_messages
            SET is_read = 1, read_at = :now, updated_at = :now
            WHERE recipient_id = :rec AND sender_id = :snd AND is_read = 0
        ');
        $stmt->execute([
            ':now' => $now,
            ':rec' => $recipientId,
            ':snd' => $senderId,
        ]);
        return $stmt->rowCount();
    }

    public static function getUnreadMessagesCount(int $userId): int {
        $pdo = \App\Database\Connection::get();
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM direct_messages WHERE recipient_id = :u AND is_read = 0');
        $stmt->execute([':u' => $userId]);
        return (int)$stmt->fetchColumn();
    }
}
