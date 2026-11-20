<?php
declare(strict_types=1);

return [
    'lifetime' => (int)env('SESSION_LIFETIME', 120),
    'secure' => (bool)env('SESSION_SECURE', false),
    'same_site' => env('SESSION_SAME_SITE', 'Lax'),
    'cookie' => 'shilpa_session',
];
