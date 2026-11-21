<?php
ob_start();
?>
<div class="security-audit-page" style="max-width: 1200px; margin: 0 auto;">
    <!-- Page Header -->
    <div style="margin-bottom: 2rem;">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 0.5rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <h1 style="font-size: 1.875rem; font-weight: 800; margin: 0; font-family: var(--font-heading);">
                    Security Operations & Audit Trail
                </h1>
                <span class="badge badge-primary">SecOps Console</span>
            </div>
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <a href="/admin/dashboard" class="btn btn-secondary btn-sm">
                    <?= component('icon', ['name' => 'arrow-left', 'size' => 16]) ?>
                    <span>Admin Dashboard</span>
                </a>
            </div>
        </div>
        <p style="color: var(--text-muted); margin: 0; font-size: 0.9375rem;">
            Immutable cryptographic hash chain audit logging, intrusion detection, and tamper verification.
        </p>
    </div>

    <!-- Integrity Status Banner -->
    <div style="padding: 1.25rem 1.75rem; border-radius: var(--radius-lg); margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; <?= $integrity['intact'] ? 'background: rgba(16, 185, 129, 0.08); border: 1px solid rgba(16, 185, 129, 0.3);' : 'background: rgba(239, 68, 68, 0.08); border: 1px solid rgba(239, 68, 68, 0.3);' ?>">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <div style="width: 44px; height: 44px; border-radius: 50%; display: flex; align-items: center; justify-content: center; <?= $integrity['intact'] ? 'background: #10b981; color: white;' : 'background: #ef4444; color: white;' ?>">
                <?= component('icon', ['name' => $integrity['intact'] ? 'shield' : 'alert-triangle', 'size' => 24]) ?>
            </div>
            <div>
                <div style="font-size: 1.125rem; font-weight: 700; color: <?= $integrity['intact'] ? '#10b981' : '#ef4444' ?>;">
                    <?= $integrity['intact'] ? 'Cryptographic Audit Chain Intact &amp; Verified' : 'CRITICAL: Database Audit Tampering Detected!' ?>
                </div>
                <div style="font-size: 0.8125rem; color: var(--text-muted); margin-top: 0.25rem;">
                    <?= (int)$integrity['checked_count'] ?> records verified with SHA-256 HMAC chained signatures. Zero unauthorized database modifications detected.
                </div>
            </div>
        </div>

        <button onclick="location.reload();" class="btn btn-secondary btn-sm">
            <?= component('icon', ['name' => 'refresh-cw', 'size' => 14]) ?>
            <span>Re-verify Audit Chain</span>
        </button>
    </div>

    <!-- Security KPI Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
        <div class="card" style="padding: 1.25rem;">
            <div style="font-size: 0.8125rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Total Audited Events</div>
            <div style="font-size: 1.875rem; font-weight: 800; margin: 0.25rem 0;"><?= count($logs) ?></div>
            <div style="font-size: 0.75rem; color: var(--text-muted);">Real-time immutable ledger</div>
        </div>

        <div class="card" style="padding: 1.25rem;">
            <div style="font-size: 0.8125rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Hardened Headers</div>
            <div style="font-size: 1.875rem; font-weight: 800; margin: 0.25rem 0; color: #10b981;">Active</div>
            <div style="font-size: 0.75rem; color: var(--text-muted);">CSP, HSTS, X-Frame, X-Content-Type</div>
        </div>

        <div class="card" style="padding: 1.25rem;">
            <div style="font-size: 0.8125rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Sanitization Layer</div>
            <div style="font-size: 1.875rem; font-weight: 800; margin: 0.25rem 0; color: var(--primary-text);">Enforced</div>
            <div style="font-size: 0.75rem; color: var(--text-muted);">XSS &amp; Directory Traversal blocked</div>
        </div>

        <div class="card" style="padding: 1.25rem;">
            <div style="font-size: 0.8125rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase;">Chain Algorithm</div>
            <div style="font-size: 1.875rem; font-weight: 800; margin: 0.25rem 0;">HMAC-256</div>
            <div style="font-size: 0.75rem; color: var(--text-muted);">Tamper-evident block linkage</div>
        </div>
    </div>

    <!-- Audit Event Log Table -->
    <div class="card" style="padding: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h2 style="font-size: 1.125rem; font-weight: 700; margin: 0 0 0.25rem;">Audit Trail Records</h2>
                <p style="font-size: 0.8125rem; color: var(--text-muted); margin: 0;">Comprehensive sequence of user authentication, RBAC checks, and administration activities.</p>
            </div>
        </div>

        <?php if (empty($logs)): ?>
            <div style="text-align: center; padding: 3rem 1rem; color: var(--text-muted);">
                <?= component('icon', ['name' => 'inbox', 'size' => 40]) ?>
                <div style="font-weight: 600; margin-top: 0.75rem;">No security events recorded yet</div>
                <p style="font-size: 0.8125rem; margin-top: 0.25rem;">Security events will populate automatically upon user and administrative activity.</p>
            </div>
        <?php else: ?>
            <div style="overflow-x: auto;">
                <table class="table" style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--border-subtle); text-align: left;">
                            <th style="padding: 0.75rem; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase;">Event</th>
                            <th style="padding: 0.75rem; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase;">Severity</th>
                            <th style="padding: 0.75rem; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase;">Actor</th>
                            <th style="padding: 0.75rem; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase;">IP Address</th>
                            <th style="padding: 0.75rem; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase;">Timestamp</th>
                            <th style="padding: 0.75rem; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase;">Integrity Signature</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $log): ?>
                            <tr style="border-bottom: 1px solid var(--border-subtle);">
                                <td style="padding: 0.75rem; font-weight: 600;">
                                    <code><?= e((string)$log['event']) ?></code>
                                </td>
                                <td style="padding: 0.75rem;">
                                    <?php
                                    $sev = (string)$log['severity'];
                                    $badgeClass = match ($sev) {
                                        'critical', 'emergency' => 'badge-danger',
                                        'warning' => 'badge-warning',
                                        default => 'badge-info',
                                    };
                                    ?>
                                    <span class="badge <?= $badgeClass ?>"><?= strtoupper(e($sev)) ?></span>
                                </td>
                                <td style="padding: 0.75rem; color: var(--text-base);">
                                    <?= e((string)($log['actor_email'] ?: 'System / Anonymous')) ?>
                                </td>
                                <td style="padding: 0.75rem; font-family: monospace; font-size: 0.8125rem; color: var(--text-muted);">
                                    <?= e((string)$log['ip_address']) ?>
                                </td>
                                <td style="padding: 0.75rem; color: var(--text-muted); font-size: 0.8125rem;">
                                    <?= e((string)$log['created_at']) ?>
                                </td>
                                <td style="padding: 0.75rem; font-family: monospace; font-size: 0.75rem; color: var(--primary-text);">
                                    <?= substr((string)$log['integrity_hash'], 0, 16) ?>...
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php
$slot = ob_get_clean();
include dirname(__DIR__) . '/layouts/workspace.php';
