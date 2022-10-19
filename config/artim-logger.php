<?php

declare(strict_types=1);

return [
    'log_request' => env('LOG_REQUEST', false),

    'user' => [
        'properties' => [
            'id',
            'email',
        ],
    ],
];
