<?php
declare(strict_types=1);

$metrics = $data['metrics'] ?? [];
$cacheCount = $data['cacheCount'] ?? 0;

ob_start();
?>
<div class="performance-admin-container" style="max-width: 1000px; margin: 0 auto; padding-bottom: 4rem;">
    <!-- Page Header -->
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <div style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.25rem 0.75rem; background: var(--primary-50); color: var(--primary-700); border-radius: 9999px; font-size: 0.8125rem; font-weight: 600; margin-bottom: 0.5rem;">
                <?= component('icon', ['name' => 'trending-up', 'size' => 14]) ?>
                <span>Telemetry & Optimization</span>
            </div>
            <h1 style="font-size: 2rem; font-weight: 800; font-family: var(--font-heading); margin: 0 0 0.25rem 0;">
                Performance & Cache Profiler
            </h1>
            <p style="color: var(--text-muted); font-size: 0.9375rem; margin: 0;">
                Real-time runtime memory footprint, cache invalidation, and execution latency.
            </p>
        </div>

        <form action="/admin/cache/flush" method="POST" style="margin: 0;">
            <input type="hidden" name="_csrf" value="<?= session()->csrfToken() ?>">
            <button type="submit" class="btn btn-secondary btn-sm" onclick="return confirm('Flush all cache keys across the platform?')">
                <?= component('icon', ['name' => 'trash-2', 'size' => 14]) ?>
                <span>Flush Cache Storage</span>
            </button>
        </form>
    </div>

    <!-- Stat Metric Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.25rem; margin-bottom: 2rem;">
        <!-- Peak Memory -->
        <div class="card" style="padding: 1.5rem; border-radius: var(--radius-lg); border: 1px solid var(--border-color);">
            <div style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 0.5rem;">
                Peak Memory
            </div>
            <div style="font-size: 1.75rem; font-weight: 800; font-family: var(--font-heading); margin-bottom: 0.25rem;">
                <?= $metrics['peak_memory_mb'] ?? 0 ?> <span style="font-size: 1rem; font-weight: 500; color: var(--text-muted);">MB</span>
            </div>
            <div style="font-size: 0.75rem; color: var(--success-text);">Optimized Heap Allocation</div>
        </div>

        <!-- Latency -->
        <div class="card" style="padding: 1.5rem; border-radius: var(--radius-lg); border: 1px solid var(--border-color);">
            <div style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 0.5rem;">
                Execution Latency
            </div>
            <div style="font-size: 1.75rem; font-weight: 800; font-family: var(--font-heading); margin-bottom: 0.25rem;">
                <?= $metrics['execution_time_ms'] ?? 0 ?> <span style="font-size: 1rem; font-weight: 500; color: var(--text-muted);">ms</span>
            </div>
            <div style="font-size: 0.75rem; color: var(--success-text);">Sub-50ms Benchmark Target</div>
        </div>

        <!-- Cache Entries -->
        <div class="card" style="padding: 1.5rem; border-radius: var(--radius-lg); border: 1px solid var(--border-color);">
            <div style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 0.5rem;">
                Cached Objects
            </div>
            <div style="font-size: 1.75rem; font-weight: 800; font-family: var(--font-heading); margin-bottom: 0.25rem;">
                <?= $cacheCount ?> <span style="font-size: 1rem; font-weight: 500; color: var(--text-muted);">Keys</span>
            </div>
            <div style="font-size: 0.75rem; color: var(--primary);">Tag-Indexed Store</div>
        </div>

        <!-- PHP Runtime -->
        <div class="card" style="padding: 1.5rem; border-radius: var(--radius-lg); border: 1px solid var(--border-color);">
            <div style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 0.5rem;">
                PHP Runtime
            </div>
            <div style="font-size: 1.75rem; font-weight: 800; font-family: var(--font-heading); margin-bottom: 0.25rem;">
                <?= PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION ?>
            </div>
            <div style="font-size: 0.75rem; color: var(--text-muted);"><?= php_sapi_name() ?></div>
        </div>
    </div>

    <!-- Cache Architecture Details -->
    <div class="card" style="padding: 2rem; border-radius: var(--radius-xl); border: 1px solid var(--border-color); margin-bottom: 2rem;">
        <h2 style="font-size: 1.25rem; font-weight: 700; margin: 0 0 1rem 0;">
            Multi-Tier Caching Pipeline
        </h2>
        <div style="display: flex; flex-direction: column; gap: 1rem;">
            <div style="padding: 1rem; background: var(--surface-secondary); border-radius: var(--radius-md); display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <div style="font-weight: 600; font-size: 0.9375rem; margin-bottom: 0.25rem;">Query & Catalog Cache Layer</div>
                    <div style="font-size: 0.8125rem; color: var(--text-muted);">Memoizes high-frequency course listings, top featured courses, and category aggregates with automatic invalidation on updates.</div>
                </div>
                <span class="badge badge-success">Active</span>
            </div>

            <div style="padding: 1rem; background: var(--surface-secondary); border-radius: var(--radius-md); display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <div style="font-weight: 600; font-size: 0.9375rem; margin-bottom: 0.25rem;">Tag-Based Cache Invalidation</div>
                    <div style="font-size: 0.8125rem; color: var(--text-muted);">Enables atomic clearing of specific cache domains (e.g. ['courses', 'catalog']) without flushing unrelated session or analytics state.</div>
                </div>
                <span class="badge badge-primary">Enabled</span>
            </div>

            <div style="padding: 1rem; background: var(--surface-secondary); border-radius: var(--radius-md); display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <div style="font-weight: 600; font-size: 0.9375rem; margin-bottom: 0.25rem;">Telemetry API & Health Check</div>
                    <div style="font-size: 0.8125rem; color: var(--text-muted);">Endpoint at <code>/api/performance/metrics</code> ready for external Prometheus, Grafana, or Datadog scrapers.</div>
                </div>
                <a href="/api/performance/metrics" target="_blank" class="btn btn-ghost btn-sm">Inspect JSON</a>
            </div>
        </div>
    </div>
</div>

<?php
$slot = ob_get_clean();
include dirname(__DIR__, 2) . '/layouts/workspace.php';
