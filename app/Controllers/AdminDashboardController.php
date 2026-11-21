<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Models\User;
use App\Services\AdminDashboardService;

class AdminDashboardController extends Controller {
    public function index(Request $request): Response {
        if (!session()->has('user')) {
            session()->flash('error', 'Please sign in to access the Administration Console.');
            return $this->redirect('/login');
        }

        $userRole = (string)session()->get('user_role', '');
        if (!in_array($userRole, ['a', 'm', 'admin', 'administrator', 'superadmin'], true)) {
            session()->flash('error', 'Administrative privileges required.');
            return $this->redirect('/workspace');
        }

        $data = AdminDashboardService::getDashboardData();
        $data['title'] = 'Platform Administration Console — ' . config('app.name', 'SHILPA LMS');

        return $this->render('admin/dashboard', $data);
    }

    public function users(Request $request): Response {
        if (!session()->has('user')) {
            return $this->redirect('/login');
        }

        $userRole = (string)session()->get('user_role', '');
        if (!in_array($userRole, ['a', 'm', 'admin', 'administrator', 'superadmin'], true)) {
            return $this->redirect('/workspace');
        }

        $roleFilter = (string)$request->query('role', 'all');
        $search = trim((string)$request->query('q', ''));

        $query = User::query();
        if ($search !== '') {
            $query->where('Full_Name', 'LIKE', "%$search%")
                  ->orWhere('Email', 'LIKE', "%$search%");
        }

        $users = array_map(fn($r) => new User($r), $query->get());

        if ($roleFilter === 'student') {
            $users = array_filter($users, fn($u) => $u->isStudent());
        } elseif ($roleFilter === 'lecturer') {
            $users = array_filter($users, fn($u) => $u->isLecturer());
        } elseif ($roleFilter === 'admin') {
            $users = array_filter($users, fn($u) => $u->isAdmin());
        }

        return $this->render('admin/users', [
            'title' => 'User Directory Management — ' . config('app.name', 'SHILPA LMS'),
            'users' => $users,
            'roleFilter' => $roleFilter,
            'search' => $search,
        ]);
    }

    public function systemHealth(Request $request): Response {
        if (!session()->has('user')) {
            return json(['error' => 'Unauthorized'], 401);
        }

        $data = AdminDashboardService::getDashboardData();
        return json([
            'status' => 'healthy',
            'systemHealth' => $data['systemHealth'],
            'metrics' => $data['metrics'],
        ]);
    }

    public function securityAudit(Request $request): Response {
        if (!session()->has('user')) {
            return $this->redirect('/login');
        }

        $userRole = (string)session()->get('user_role', '');
        if (!in_array($userRole, ['a', 'm', 'admin', 'administrator', 'superadmin'], true)) {
            return $this->redirect('/workspace');
        }

        $logs = \App\Security\SecurityAuditLog::query()->orderBy('id', 'DESC')->limit(100)->get();
        $integrity = \App\Security\SecurityAuditLog::verifyChainIntegrity(100);

        return $this->render('admin/security-audit', [
            'title' => 'Security Audit & Tamper Log — ' . config('app.name', 'SHILPA LMS'),
            'logs' => $logs,
            'integrity' => $integrity,
        ]);
    }

    public function verifyAuditLogIntegrity(Request $request): Response {
        if (!session()->has('user')) {
            return json(['error' => 'Unauthorized'], 401);
        }

        $userRole = (string)session()->get('user_role', '');
        if (!in_array($userRole, ['a', 'm', 'admin', 'administrator', 'superadmin'], true)) {
            return json(['error' => 'Forbidden'], 403);
        }

        $integrity = \App\Security\SecurityAuditLog::verifyChainIntegrity(500);
        return json($integrity);
    }
}
