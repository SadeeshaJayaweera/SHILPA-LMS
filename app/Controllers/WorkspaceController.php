<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Models\Course;

class WorkspaceController extends Controller {
    public function index(Request $request): Response {
        $userRole = (string)session()->get('user_role', 'student');
        $userName = (string)session()->get('user_name', 'Student User');
        $courses = Course::query()->limit(6)->get();

        return $this->render('workspace/index', [
            'title' => 'Learning Workspace — ' . config('app.name', 'SHILPA LMS'),
            'userRole' => $userRole,
            'userName' => $userName,
            'courses' => $courses,
        ]);
    }
}
