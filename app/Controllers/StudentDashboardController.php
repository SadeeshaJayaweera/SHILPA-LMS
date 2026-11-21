<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Models\User;
use App\Services\StudentDashboardService;

class StudentDashboardController extends Controller {
    public function index(Request $request): Response {
        if (!session()->has('user')) {
            session()->flash('error', 'Please sign in to access your student dashboard.');
            return $this->redirect('/login');
        }

        $userId = (int)session()->get('user_id', 0);
        if ($userId <= 0) {
            $email = (string)session()->get('email', '');
            if ($email !== '') {
                $user = User::findByEmail($email);
                if ($user) {
                    $userId = (int)$user->UserId;
                    session()->set('user_id', $userId);
                }
            }
        }

        $data = StudentDashboardService::getDashboardData($userId);
        $data['title'] = 'Student Learning Dashboard — ' . config('app.name', 'SHILPA LMS');

        return $this->render('student/dashboard', $data);
    }

    public function analytics(Request $request): Response {
        if (!session()->has('user')) {
            return json(['error' => 'Unauthorized'], 401);
        }

        $userId = (int)session()->get('user_id', 0);
        $data = StudentDashboardService::getDashboardData($userId);

        return json([
            'weeklyActivity' => $data['weeklyActivity'],
            'subjectMastery' => $data['subjectMastery'],
            'metrics' => $data['metrics'],
        ]);
    }
}
