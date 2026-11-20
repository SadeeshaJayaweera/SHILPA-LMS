<?php
declare(strict_types=1);

use App\Core\Router;

return function (Router $router): void {
    $router->group(['prefix' => 'api'], function (Router $api) {
        $api->get('/v1/ping', function () {
            return json([
                'success' => true,
                'message' => 'SHILPA LMS API Gateway Ready',
                'version' => '1.0.0',
            ]);
        });
    });
};
