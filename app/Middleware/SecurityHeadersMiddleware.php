<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Security\SecurityHeaders;

class SecurityHeadersMiddleware {
    public function handle(Request $request, callable $next): Response {
        $response = $next($request);
        if ($response instanceof Response) {
            SecurityHeaders::apply($response);
        }
        return $response;
    }
}
