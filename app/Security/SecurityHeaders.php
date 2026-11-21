<?php
declare(strict_types=1);

namespace App\Security;

use App\Core\Response;

class SecurityHeaders {
    /**
     * Hardened security headers configuration.
     */
    public static function getHeaders(): array {
        $csp = [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
            "font-src 'self' data: https://fonts.gstatic.com",
            "img-src 'self' data: https:",
            "connect-src 'self'",
            "media-src 'self'",
            "object-src 'none'",
            "frame-ancestors 'none'",
            "base-uri 'self'",
            "form-action 'self'",
        ];

        return [
            'Content-Security-Policy' => implode('; ', $csp),
            'X-Frame-Options' => 'DENY',
            'X-Content-Type-Options' => 'nosniff',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Permissions-Policy' => 'camera=(), microphone=(), geolocation=(), payment=()',
            'X-XSS-Protection' => '1; mode=block',
        ];
    }

    /**
     * Alias for getHeaders().
     */
    public static function headers(): array {
        return self::getHeaders();
    }

    /**
     * Apply security headers directly to an HTTP Response object.
     */
    public static function apply(Response $response): Response {
        foreach (self::getHeaders() as $name => $value) {
            $response->setHeader($name, $value);
        }
        return $response;
    }
}
