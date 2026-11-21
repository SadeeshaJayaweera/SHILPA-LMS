<?php
declare(strict_types=1);

$totalPending = $data['totalPending'] ?? 0;
$pendingQueues = $data['pendingQueues'] ?? [];
$failedJobs = $data['failedJobs'] ?? [];

ob_start();
?>
<div class="queue-monitor-container" style="max-width: 1000px; margin: 0 auto; padding-bottom: 4rem;">
    <!-- Page Header -->
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <div style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.25rem 0.75rem; background: var(--primary-50); color: var(--primary-700); border-radius: 9999px; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.5rem;">
                <?= component('icon', ['name' => 'cpu', 'size' => 14]) ?>
                <span>Worker Subsystem</span>
            </div>
            <h1 style="font-size: 2rem; font-weight: 800; font-family: var(--font-heading); margin: 0 0 0.25rem 0;">
                Background Job Queues
            </h1>
            <p style="color: var(--text-muted); font-size: 0.9375rem; margin: 0;">
                Monitor asynchronous background execution, job dispatchers, and retry dead-letter queues.
            </p>
        </div>

        <?php if (!empty($failedJobs)): ?>
            <form action="/admin/queues/clear-failed" method="POST" style="margin: 0;">
                <input type="hidden" name="_csrf" value="<?= session()->csrfToken() ?>">
                <button type="submit" class="btn btn-secondary btn-sm" onclick="return confirm('Clear all failed jobs permanently?')">
                    <?= component('icon', ['name' => 'trash-2', 'size' => 14]) ?>
                    <span>Clear Dead-Letter Queue</span>
                </button>
            </form>
        <?php endif; ?>
    </div>

    <!-- Stat Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; margin-bottom: 2.5rem;">
        <div class="card" style="padding: 1.5rem; border-radius: var(--radius-lg); border: 1px solid var(--border-color);">
            <div style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 0.5rem;">
                Pending In Queue
            </div>
            <div style="font-size: 2rem; font-weight: 800; font-family: var(--font-heading); margin-bottom: 0.25rem;">
                <?= $totalPending ?>
            </div>
            <div style="font-size: 0.75rem; color: var(--text-muted);">Awaiting worker pickup</div>
        </div>

        <div class="card" style="padding: 1.5rem; border-radius: var(--radius-lg); border: 1px solid var(--border-color);">
            <div style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 0.5rem;">
                Failed / Dead-Letter
            </div>
            <div style="font-size: 2rem; font-weight: 800; font-family: var(--font-heading); margin-bottom: 0.25rem; color: <?= count($failedJobs) > 0 ? 'var(--danger)' : 'var(--text-color)' ?>;">
                <?= count($failedJobs) ?>
            </div>
            <div style="font-size: 0.75rem; color: var(--text-muted);">Exceptions logged</div>
        </div>

        <div class="card" style="padding: 1.5rem; border-radius: var(--radius-lg); border: 1px solid var(--border-color);">
            <div style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 0.5rem;">
                Queue CLI Worker
            </div>
            <div style="font-size: 1.125rem; font-weight: 700; margin-bottom: 0.25rem;">
                <code>php bin/queue-worker</code>
            </div>
            <div style="font-size: 0.75rem; color: var(--success-text);">Daemon & Cron Ready</div>
        </div>
    </div>

    <!-- Failed Jobs Section -->
    <div class="card" style="padding: 2rem; border-radius: var(--radius-xl); border: 1px solid var(--border-color);">
        <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0 0 1rem 0;">
            Failed Jobs Dead-Letter Log
        </h2>

        <?php if (empty($failedJobs)): ?>
            <div style="padding: 2.5rem; text-align: center; color: var(--text-muted);">
                <div style="display: inline-flex; width: 44px; height: 44px; border-radius: 50%; background: var(--success-50); color: var(--success); align-items: center; justify-content: center; margin-bottom: 0.75rem;">
                    <?= component('icon', ['name' => 'check', 'size' => 20]) ?>
                </div>
                <div style="font-weight: 600; font-size: 0.9375rem; color: var(--text-color); margin-bottom: 0.25rem;">No failed jobs</div>
                <div style="font-size: 0.8125rem;">All background pipelines are executing cleanly.</div>
            </div>
        <?php else: ?>
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <?php foreach ($failedJobs as $job): ?>
                    <div style="padding: 1.25rem; background: var(--surface-secondary); border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem; flex-wrap: wrap; gap: 0.5rem;">
                            <div>
                                <span class="badge badge-secondary" style="font-family: monospace; font-size: 0.6875rem;">
                                    Queue: <?= e($job['queue']) ?>
                                </span>
                                <span style="font-size: 0.75rem; color: var(--text-muted); margin-left: 0.5rem;">
                                    Failed at: <?= e($job['failed_at']) ?>
                                </span>
                            </div>
                            <form action="/admin/queues/retry/<?= (int)$job['id'] ?>" method="POST" style="margin: 0;">
                                <input type="hidden" name="_csrf" value="<?= session()->csrfToken() ?>">
                                <button type="submit" class="btn btn-primary btn-sm" style="font-size: 0.75rem; padding: 0.25rem 0.5rem;">
                                    <?= component('icon', ['name' => 'refresh-cw', 'size' => 12]) ?>
                                    <span>Retry Job</span>
                                </button>
                            </form>
                        </div>
                        <pre style="background: var(--surface); padding: 0.75rem; border-radius: 4px; font-size: 0.75rem; color: var(--danger); overflow-x: auto; margin: 0; font-family: monospace; border: 1px solid var(--border-color); max-height: 120px;"><?= e(substr((string)$job['exception'], 0, 400)) ?>...</pre>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$slot = ob_get_clean();
include dirname(__DIR__, 2) . '/layouts/workspace.php';
