<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Services\PerformanceProfiler;

class PerformanceController extends Controller {
    /**
     * Admin Performance & Cache Diagnostics Dashboard
     */
    public function adminMetrics(Request $request): Response {
        $role = (string)session()->get('user_role', 'student');
        if ($role !== 'admin') {
            session()->flash('error', 'Administrator access required.');
            return $this->redirect('/login');
        }

        $metrics = PerformanceProfiler::getMetrics();
        $cacheFiles = glob(dirname(__DIR__, 2) . '/storage/cache/*.cache');
        $cacheCount = is_array($cacheFiles) ? count($cacheFiles) : 0;

        $data = [
            'metrics' => $metrics,
            'cacheCount' => $cacheCount,
            'title' => 'System Performance & Cache Diagnostics — SHILPA LMS',
        ];

        return $this->render('admin/performance/index', array_merge($data, ['data' => $data]));
    }

    /**
     * Clear Cache Storage
     */
    public function flushCache(Request $request): Response {
        $role = (string)session()->get('user_role', 'student');
        if ($role !== 'admin') {
            if ($request->expectsJson()) {
                return $this->json(['error' => 'Unauthorized'], 403);
            }
            session()->flash('error', 'Administrator access required.');
            return $this->redirect('/login');
        }

        cache()->flush();

        if ($request->expectsJson()) {
            return $this->json([
                'status' => 'success',
                'message' => 'Cache storage flushed successfully',
            ]);
        }

        session()->flash('success', 'Cache storage flushed successfully.');
        return $this->redirect('/admin/performance');
    }

    /**
     * JSON Telemetry Monitoring API Endpoint
     */
    public function apiMetrics(Request $request): Response {
        $metrics = PerformanceProfiler::getMetrics();

        return $this->json([
            'status' => 'success',
            'profiler' => $metrics,
        ], 200, [
            'X-Response-Time' => $metrics['execution_time_ms'] . 'ms',
            'X-Memory-Peak' => $metrics['peak_memory_mb'] . 'MB',
        ]);
    }
}
