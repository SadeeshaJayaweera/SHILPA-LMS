<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Cache\ArrayCacheStore;
use App\Cache\FileCacheStore;
use App\Models\User;
use App\Services\PerformanceProfiler;
use App\Testing\TestCase;

class AdvancedPerformanceAndCachingTest extends TestCase {
    public function testArrayCacheStoreBasicOperations(): void {
        $store = new ArrayCacheStore();

        // 1. Put and Get
        $store->put('benchmark_test', 'optimized_payload', 60);
        assert($store->has('benchmark_test') === true, 'Cache should contain item');
        assert($store->get('benchmark_test') === 'optimized_payload', 'Cache should retrieve exact payload');

        // 2. Remember
        $runCount = 0;
        $val1 = $store->remember('expensive_calc', 60, function () use (&$runCount) {
            $runCount++;
            return 42 * 2;
        });
        assert($val1 === 84);
        assert($runCount === 1);

        // Second call should return cached value without running closure
        $val2 = $store->remember('expensive_calc', 60, function () use (&$runCount) {
            $runCount++;
            return 999;
        });
        assert($val2 === 84, 'Cached value should be returned');
        assert($runCount === 1, 'Closure should not run again on cache hit');

        // 3. Forget
        $store->forget('benchmark_test');
        assert($store->has('benchmark_test') === false, 'Item should be removed after forget');

        // 4. Flush
        $store->put('k1', 'v1');
        $store->put('k2', 'v2');
        $store->flush();
        assert($store->has('k1') === false);
        assert($store->has('k2') === false);
    }

    public function testTaggedCacheInvalidation(): void {
        $store = new ArrayCacheStore();
        $tagged = $store->tags(['courses', 'catalog']);

        $tagged->put('featured_list', ['Course 1', 'Course 2'], 3600);
        assert($tagged->has('featured_list') === true);
        assert(count($tagged->get('featured_list')) === 2);

        // Invalidate via tag
        $store->tags(['courses'])->flush();
        assert($tagged->has('featured_list') === false, 'Tagged key should be flushed when parent tag is flushed');
    }

    public function testFileCacheStoreSerializationAndTtl(): void {
        $dir = dirname(__DIR__, 2) . '/storage/test_cache';
        $store = new FileCacheStore($dir);

        $data = [
            'id' => 101,
            'title' => 'Quantum Algorithms',
            'modules' => [1, 2, 3],
            'timestamp' => microtime(true),
        ];

        $store->put('test_obj', $data, 30);
        assert($store->has('test_obj') === true);

        $retrieved = $store->get('test_obj');
        assert(is_array($retrieved));
        assert($retrieved['id'] === 101);
        assert($retrieved['title'] === 'Quantum Algorithms');

        $store->forget('test_obj');
        assert($store->has('test_obj') === false);

        $store->flush();
        // Cleanup test directory
        @rmdir($dir);
    }

    public function testPerformanceProfilerMetrics(): void {
        PerformanceProfiler::start();
        PerformanceProfiler::incrementQueryCount();
        PerformanceProfiler::incrementQueryCount();

        $metrics = PerformanceProfiler::getMetrics();
        assert(isset($metrics['execution_time_ms']));
        assert(isset($metrics['peak_memory_mb']));
        assert($metrics['query_count'] === 2);
        assert($metrics['php_version'] === PHP_VERSION);
    }

    public function testPerformanceHttpEndpoints(): void {
        $admin = User::create([
            'Full_Name' => 'SysAdmin Performance',
            'Email' => 'sysadmin.' . uniqid() . '@shilpa.edu',
            'Password' => password_hash('Pass123!', PASSWORD_BCRYPT),
            'user_type' => 'admin',
        ]);

        $this->actingAs($admin);

        // 1. Admin Performance View
        $res = $this->get('/admin/performance');
        assert($res->getStatusCode() === 200, 'Admin performance page should return 200');
        assert(str_contains($res->getContent(), 'Performance & Cache Profiler'));
        assert(str_contains($res->getContent(), 'Peak Memory'));

        // 2. Flush Cache POST
        $resFlush = $this->post('/admin/cache/flush', [
            '_csrf' => session()->csrfToken(),
        ]);
        assert($resFlush->getStatusCode() === 302, 'Should redirect after cache flush');

        // 3. API Monitoring Metrics
        $resApi = $this->get('/api/performance/metrics');
        assert($resApi->getStatusCode() === 200, 'Metrics API should return 200');
        $json = json_decode($resApi->getContent(), true);
        assert($json['status'] === 'success');
        assert(isset($json['profiler']['execution_time_ms']));
        assert(isset($json['profiler']['peak_memory_mb']));
    }
}
