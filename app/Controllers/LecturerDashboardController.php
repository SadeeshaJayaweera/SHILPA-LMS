<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Models\User;
use App\Services\FacultyDashboardService;

class LecturerDashboardController extends Controller {
    public function index(Request $request): Response {
        if (!session()->has('user')) {
            session()->flash('error', 'Please sign in to access the Faculty Workspace.');
            return $this->redirect('/login');
        }

        $userRole = (string)session()->get('user_role', 'student');
        if (!in_array($userRole, ['l', 'lecturer', 'instructor', 'teacher', 'a', 'admin', 'administrator', 'superadmin'], true)) {
            session()->flash('error', 'Access restricted to faculty instructors and administrators.');
            return $this->redirect('/student/dashboard');
        }

        $userId = (int)session()->get('user_id', 0);
        $data = FacultyDashboardService::getDashboardData($userId);
        $data['title'] = 'Faculty Instructor Workspace — ' . config('app.name', 'SHILPA LMS');

        return $this->render('lecturer/dashboard', $data);
    }
}
