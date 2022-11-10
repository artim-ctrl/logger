<?php

declare(strict_types = 1);

return [
    'user' => [
        'properties' => [
            'id',
            'email',
        ],
    ],

    'logs' => [
        'db' => env('LOGS_DB', false),
        'request' => env('LOGS_REQUEST', false),
        'application' => env('LOGS_APPLICATION', false),
    ],
];
