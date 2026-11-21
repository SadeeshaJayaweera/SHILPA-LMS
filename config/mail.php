<?php
declare(strict_types=1);

return [
    'default' => env('MAIL_MAILER', 'array'),

    'mailers' => [
        'array' => [
            'transport' => 'array',
        ],
        'log' => [
            'transport' => 'log',
            'channel' => 'mail',
        ],
        'smtp' => [
            'transport' => 'smtp',
            'host' => env('MAIL_HOST', '127.0.0.1'),
            'port' => (int)env('MAIL_PORT', 2525),
            'encryption' => env('MAIL_ENCRYPTION', 'tls'),
            'username' => env('MAIL_USERNAME', null),
            'password' => env('MAIL_PASSWORD', null),
        ],
    ],

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'noreply@shilpa-lms.edu'),
        'name' => env('MAIL_FROM_NAME', 'SHILPA LMS Academic Portal'),
    ],
];
