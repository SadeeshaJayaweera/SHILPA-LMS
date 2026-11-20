<?php
declare(strict_types=1);

use App\Controllers\HomeController;
use App\Core\Router;

return function (Router $router): void {
    $router->get('/', [HomeController::class, 'index']);
    $router->get('/health', function () {
        return json([
            'status' => 'healthy',
            'timestamp' => date('c'),
            'app' => config('app.name'),
            'env' => config('app.env'),
            'php' => PHP_VERSION,
        ]);
    });
};
