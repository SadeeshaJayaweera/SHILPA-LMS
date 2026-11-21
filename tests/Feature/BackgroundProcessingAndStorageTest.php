<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Database\Connection;
use App\Models\User;
use App\Queue\DatabaseQueue;
use App\Queue\JobInterface;
use App\Queue\Worker;
use App\Storage\LocalDisk;
use App\Storage\StorageManager;
use App\Testing\TestCase;

class TestSampleJob implements JobInterface {
    public static bool $executed = false;
    public string $payload;

    public function __construct(string $payload = 'sample') {
        $this->payload = $payload;
    }

    public function handle(): void {
        self::$executed = true;
    }
}

class TestFailingJob implements JobInterface {
    public function handle(): void {
        throw new \RuntimeException("Simulated background job failure");
    }
}

class BackgroundProcessingAndStorageTest extends TestCase {
    public function testQueueJobDispatchAndWorkerExecution(): void {
        TestSampleJob::$executed = false;

        $queue = new DatabaseQueue($this->pdo);
        assert($queue->count() === 0);

        // 1. Dispatch Job
        $job = new TestSampleJob('analytics_recalc');
        $id = $queue->push($job);
        assert($id > 0);
        assert($queue->count() === 1);

        // 2. Process with Worker
        $worker = new Worker($queue, 3);
        $ran = $worker->runNextJob();

        assert($ran === true, 'Worker should process available job');
        assert(TestSampleJob::$executed === true, 'Job handle method must execute');
        assert($queue->count() === 0, 'Completed job must be deleted from active queue');
    }

    public function testJobMaxAttemptsAndDeadLetterQueue(): void {
        $queue = new DatabaseQueue($this->pdo);

        // 1. Push failing job
        $failing = new TestFailingJob();
        $id = $queue->push($failing);

        // 2. Run worker with maxAttempts = 1
        $worker = new Worker($queue, 1);
        $worker->runNextJob();

        assert($queue->count() === 0, 'Failed job should be cleared from active jobs');
        assert($queue->failedCount() >= 1, 'Job should be recorded in failed_jobs table');

        // Check exception in failed_jobs table
        $stmt = $this->pdo->query("SELECT * FROM failed_jobs ORDER BY id DESC LIMIT 1");
        $failed = $stmt->fetch();
        assert(str_contains((string)$failed['exception'], 'Simulated background job failure'));
    }

    public function testStorageManagerAndLocalDisk(): void {
        $tempDir = dirname(__DIR__, 2) . '/storage/test_storage';
        $disk = new LocalDisk($tempDir, '/test-media');

        // 1. Write file
        $content = "SHILPA LMS Reliable Storage System Payload";
        $ok = $disk->put('documents/guide.txt', $content);
        assert($ok === true);

        // 2. Exists and Get
        assert($disk->exists('documents/guide.txt') === true);
        assert($disk->get('documents/guide.txt') === $content);

        // 3. Size and MimeType
        assert($disk->size('documents/guide.txt') === strlen($content));
        assert($disk->mimeType('documents/guide.txt') === 'text/plain');

        // 4. SHA-256 Checksum Verification
        $expectedChecksum = hash('sha256', $content);
        assert($disk->checksum('documents/guide.txt') === $expectedChecksum);

        // 5. Delete
        assert($disk->delete('documents/guide.txt') === true);
        assert($disk->exists('documents/guide.txt') === false);

        // Cleanup
        @unlink($tempDir . '/documents/guide.txt');
        @rmdir($tempDir . '/documents');
        @rmdir($tempDir);
    }

    public function testAdminQueueMonitoringEndpoints(): void {
        $admin = User::create([
            'Full_Name' => 'SysAdmin Queues',
            'Email' => 'sysadmin.queue.' . uniqid() . '@shilpa.edu',
            'Password' => password_hash('Pass123!', PASSWORD_BCRYPT),
            'user_type' => 'admin',
        ]);

        $this->actingAs($admin);

        // 1. Queue Dashboard View
        $res = $this->get('/admin/queues');
        assert($res->getStatusCode() === 200, 'Queue monitor page should return 200');
        assert(str_contains($res->getContent(), 'Background Job Queues'));
        assert(str_contains($res->getContent(), 'Pending In Queue'));

        // 2. Clear Failed Jobs
        $resClear = $this->post('/admin/queues/clear-failed', [
            '_csrf' => session()->csrfToken(),
        ]);
        assert($resClear->getStatusCode() === 302, 'Should redirect after clearing failed jobs');
    }
}
