<?php
declare(strict_types=1);

namespace App\Security;

use RuntimeException;

class AuthorizationException extends RuntimeException {
    protected $code = 403;

    public function __construct(string $message = 'This action is unauthorized.', int $code = 403) {
        parent::__construct($message, $code);
    }
}
