<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;

class HomeController extends Controller {
    public function index(Request $request): Response {
        return $this->render('home/index', [
            'appName' => config('app.name', 'SHILPA LMS'),
        ]);
    }
}
