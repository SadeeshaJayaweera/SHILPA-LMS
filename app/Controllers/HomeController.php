<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Models\Course;

class HomeController extends Controller {
    public function index(Request $request): Response {
        $topCourses = Course::getTopCourses(4);
        $latestCourses = Course::getLatest(4);

        return $this->render('home/index', [
            'appName' => config('app.name', 'SHILPA LMS'),
            'topCourses' => $topCourses,
            'latestCourses' => $latestCourses,
        ]);
    }
}
