<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\Course;
use App\Models\DirectMessage;
use App\Models\DiscussionReply;
use App\Models\DiscussionThread;
use App\Models\User;
use App\Services\CommunicationService;
use App\Services\CourseManagementService;
use App\Services\EnrollmentService;
use App\Testing\TestCase;

class AnnouncementsDiscussionsAndMessagingTest extends TestCase {
    public function testAnnouncementCreationAndAccessControl(): void {
        $faculty = User::create([
            'Full_Name' => 'Prof. Ada Lovelace',
            'Email' => 'ada.lovelace.' . uniqid() . '@shilpa.edu',
            'Password' => password_hash('AnalyticalEngine1!', PASSWORD_BCRYPT),
            'user_type' => 'lecturer',
        ]);

        $course = CourseManagementService::createCourse([
            'title' => 'CS901 Advanced Compiler Architecture',
            'status' => 'published',
            'instructor' => 'Prof. Ada Lovelace',
        ]);
        $cId = (int)$course->C_ID;

        // 1. Create Pinned Announcement
        $ann1 = CommunicationService::createAnnouncement($cId, (int)$faculty->id, [
            'title' => 'Midterm Project Specifications Released',
            'content' => 'Review the LLVM IR optimization rubric before Friday.',
            'is_pinned' => 1,
        ]);

        assert($ann1->isPinned());
        assert($ann1->title === 'Midterm Project Specifications Released');

        // 2. Create Regular Announcement
        $ann2 = CommunicationService::createAnnouncement($cId, (int)$faculty->id, [
            'title' => 'Guest Lecture by Bjarne Stroustrup',
            'content' => 'Join the virtual theater on Monday at 10 AM.',
            'is_pinned' => 0,
        ]);

        assert(!$ann2->isPinned());

        // 3. Retrieve announcements list (pinned first)
        $list = CommunicationService::getCourseAnnouncements($cId);
        assert(count($list) === 2);
        assert((int)$list[0]->id === (int)$ann1->id); // Pinned appears first

        // 4. Delete announcement
        $deleted = CommunicationService::deleteAnnouncement((int)$ann2->id, (int)$faculty->id);
        assert($deleted === true);
        assert(count(CommunicationService::getCourseAnnouncements($cId)) === 1);
    }

    public function testDiscussionThreadCreationAndReplies(): void {
        $course = CourseManagementService::createCourse([
            'title' => 'CS850 Distributed Consensus & Raft',
            'status' => 'published',
        ]);
        $cId = (int)$course->C_ID;

        $student1 = User::create([
            'Full_Name' => 'Leslie Lamport',
            'Email' => 'lamport.' . uniqid() . '@shilpa.edu',
            'Password' => password_hash('PaxosLeader1!', PASSWORD_BCRYPT),
            'user_type' => 'student',
        ]);

        $student2 = User::create([
            'Full_Name' => 'Barbara Liskov',
            'Email' => 'liskov.' . uniqid() . '@shilpa.edu',
            'Password' => password_hash('Substitutability1!', PASSWORD_BCRYPT),
            'user_type' => 'student',
        ]);

        // 1. Student creates discussion thread
        $thread = CommunicationService::createThread($cId, (int)$student1->id, [
            'title' => 'Handling Network Partitions during Leader Election',
            'body' => 'How does Raft maintain safety when split-brain occurs in a 5-node cluster?',
        ]);

        assert($thread->title === 'Handling Network Partitions during Leader Election');
        assert((int)$thread->replies_count === 0);
        assert(!$thread->isLocked());

        // 2. Peer replies to thread
        $reply = CommunicationService::replyToThread(
            (int)$thread->id,
            (int)$student2->id,
            'Quorum-based majorities prevent the partitioned minority from committing log entries.'
        );

        assert($reply->author() !== null);
        assert((int)$reply->author()->id === (int)$student2->id);

        $threadDetails = CommunicationService::getThreadWithReplies((int)$thread->id);
        assert(count($threadDetails['replies']) === 1);
        assert((int)$threadDetails['thread']->replies_count === 1);

        // 3. Faculty locks thread
        CommunicationService::toggleThreadLock((int)$thread->id, 1);
        $updatedThread = DiscussionThread::find((int)$thread->id);
        assert($updatedThread->isLocked());

        // 4. Replying to locked thread must throw RuntimeException
        $exceptionThrown = false;
        try {
            CommunicationService::replyToThread((int)$thread->id, (int)$student1->id, 'Further inquiry...');
        } catch (\RuntimeException $e) {
            $exceptionThrown = true;
        }
        assert($exceptionThrown);
    }

    public function testDirectMessagingFlowAndUnreadCounters(): void {
        $studentA = User::create([
            'Full_Name' => 'Claude Shannon',
            'Email' => 'shannon.' . uniqid() . '@shilpa.edu',
            'Password' => password_hash('Entropy123!', PASSWORD_BCRYPT),
            'user_type' => 'student',
        ]);

        $studentB = User::create([
            'Full_Name' => 'Alan Turing',
            'Email' => 'turing.' . uniqid() . '@shilpa.edu',
            'Password' => password_hash('Enigma123!', PASSWORD_BCRYPT),
            'user_type' => 'student',
        ]);

        // 1. Initial unread count should be 0
        assert(CommunicationService::getUnreadMessagesCount((int)$studentB->id) === 0);

        // 2. Student A sends direct message to Student B
        $msg1 = CommunicationService::sendMessage(
            (int)$studentA->id,
            (int)$studentB->id,
            'Hello Alan, have you reviewed the information theory problem set?'
        );

        assert(!$msg1->isRead());
        assert(CommunicationService::getUnreadMessagesCount((int)$studentB->id) === 1);

        // 3. Student B opens conversation
        $conversation = CommunicationService::getConversationBetween((int)$studentB->id, (int)$studentA->id);
        assert(count($conversation) === 1);

        // Messages should now be marked as read
        assert(CommunicationService::getUnreadMessagesCount((int)$studentB->id) === 0);

        // 4. Student B replies
        CommunicationService::sendMessage(
            (int)$studentB->id,
            (int)$studentA->id,
            'Yes Claude, the proof on channel capacity is remarkably sound.'
        );

        // Recent conversations for student A should show student B
        $recentsA = CommunicationService::getRecentConversations((int)$studentA->id);
        assert(count($recentsA) >= 1);
        assert((int)$recentsA[0]['user']->id === (int)$studentB->id);
    }

    public function testCommunicationControllerHttpEndpoints(): void {
        $faculty = User::create([
            'Full_Name' => 'Prof. Grace Hopper',
            'Email' => 'hopper.' . uniqid() . '@shilpa.edu',
            'Password' => password_hash('Nanoseconds1!', PASSWORD_BCRYPT),
            'user_type' => 'lecturer',
        ]);

        $student = User::create([
            'Full_Name' => 'Margaret Hamilton',
            'Email' => 'hamilton.' . uniqid() . '@shilpa.edu',
            'Password' => password_hash('ApolloFlight1!', PASSWORD_BCRYPT),
            'user_type' => 'student',
        ]);

        $course = CourseManagementService::createCourse([
            'title' => 'CS880 Asynchronous Systems & Telemetry',
            'status' => 'published',
            'instructor' => 'Prof. Grace Hopper',
        ]);
        $cId = (int)$course->C_ID;

        EnrollmentService::enroll((int)$student->id, $cId);

        // 1. Faculty posts announcement via HTTP
        $this->actingAs($faculty);
        $postAnnRes = $this->post("/faculty/courses/{$cId}/announcements", [
            '_csrf' => session()->csrfToken(),
            'title' => 'Lab Practical 3 Submission Instructions',
            'content' => 'Ensure all distributed trace logs are included in your archive.',
            'is_pinned' => '1',
        ]);
        $postAnnRes->assertStatus(302);

        // 2. Student views announcements page
        $this->actingAs($student);
        $getAnnRes = $this->get("/courses/{$cId}/announcements");
        $getAnnRes->assertStatus(200);
        $getAnnRes->assertSee('Lab Practical 3 Submission Instructions');

        // 3. Student posts discussion thread via HTTP
        $postThreadRes = $this->post("/courses/{$cId}/discussions", [
            '_csrf' => session()->csrfToken(),
            'title' => 'Optimizing Event Loop Throughput in High-Load Services',
            'body' => 'What buffer sizing strategies are recommended for reactive streams?',
        ]);
        $postThreadRes->assertStatus(302);

        // 4. Student views discussion index
        $getThreadsRes = $this->get("/courses/{$cId}/discussions");
        $getThreadsRes->assertStatus(200);
        $getThreadsRes->assertSee('Optimizing Event Loop Throughput');

        // 5. Send direct message via HTTP
        $postMsgRes = $this->post('/messages/send', [
            '_csrf' => session()->csrfToken(),
            'recipient_id' => (int)$faculty->id,
            'message' => 'Professor Hopper, thank you for the telemetry guidance.',
        ]);
        $postMsgRes->assertStatus(302);

        // 6. Direct messages index view
        $getMsgRes = $this->get('/messages');
        $getMsgRes->assertStatus(200);
        $getMsgRes->assertSee('Messages');

        // 7. Unread count API
        $this->actingAs($faculty);
        $unreadApiRes = $this->get('/api/messages/unread-count');
        $unreadApiRes->assertStatus(200);
        $unreadApiRes->assertSee('1');
    }
}
