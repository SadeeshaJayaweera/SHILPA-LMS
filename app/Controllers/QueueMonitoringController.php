<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Database\Connection;
use PDO;

class QueueMonitoringController extends Controller {
    protected function checkAdmin(): ?Response {
        $role = (string)session()->get('user_role', 'student');
        if ($role !== 'admin') {
            session()->flash('error', 'Administrator access required.');
            return $this->redirect('/login');
        }
        return null;
    }

    /**
     * Show Queue & Background Jobs Status
     */
    public function index(Request $request): Response {
        if ($auth = $this->checkAdmin()) return $auth;

        $pdo = Connection::get();

        // Count pending jobs
        $pendingStmt = $pdo->query("SELECT queue, COUNT(*) as count FROM jobs GROUP BY queue");
        $pendingQueues = $pendingStmt ? $pendingStmt->fetchAll(PDO::FETCH_ASSOC) : [];
        $totalPending = array_sum(array_column($pendingQueues, 'count'));

        // Fetch recent failed jobs
        $failedStmt = $pdo->query("SELECT * FROM failed_jobs ORDER BY id DESC LIMIT 20");
        $failedJobs = $failedStmt ? $failedStmt->fetchAll(PDO::FETCH_ASSOC) : [];

        $data = [
            'totalPending' => $totalPending,
            'pendingQueues' => $pendingQueues,
            'failedJobs' => $failedJobs,
            'title' => 'Asynchronous Queue & Job Monitor — SHILPA LMS',
        ];

        return $this->render('admin/queues/index', array_merge($data, ['data' => $data]));
    }

    /**
     * Retry a failed job
     */
    public function retry(Request $request, $id): Response {
        if ($auth = $this->checkAdmin()) return $auth;

        $jobId = (int)$id;
        $pdo = Connection::get();

        $stmt = $pdo->prepare("SELECT * FROM failed_jobs WHERE id = :id");
        $stmt->execute([':id' => $jobId]);
        $failed = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($failed) {
            $now = date('Y-m-d H:i:s');
            $pushStmt = $pdo->prepare("
                INSERT INTO jobs (queue, payload, attempts, reserved_at, available_at, created_at, updated_at)
                VALUES (:queue, :payload, 0, NULL, :now, :now, :now)
            ");
            $pushStmt->execute([
                ':queue' => $failed['queue'],
                ':payload' => $failed['payload'],
                ':now' => $now,
            ]);

            $delStmt = $pdo->prepare("DELETE FROM failed_jobs WHERE id = :id");
            $delStmt->execute([':id' => $jobId]);

            session()->flash('success', "Failed job #{$jobId} re-queued successfully.");
        } else {
            session()->flash('error', 'Failed job not found.');
        }

        return $this->redirect('/admin/queues');
    }

    /**
     * Clear all failed jobs
     */
    public function clearFailed(Request $request): Response {
        if ($auth = $this->checkAdmin()) return $auth;

        $pdo = Connection::get();
        $pdo->exec("DELETE FROM failed_jobs");

        session()->flash('success', 'All failed jobs have been cleared.');
        return $this->redirect('/admin/queues');
    }
}
