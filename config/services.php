<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'osms' => [
        '+225' => [
            'client_id' => '83avz8VG4t0rKeZ5Q9GnKwCC2dkhO1th',
            'client_secret' => 'Z4seBBoeYG3LMvTX',
            'sender' => 'tel:+22500000000',
        ],
        '+221' => [
            'client_id' => 'zrKTIvuGADFsWjGSGxaEUIHofrBeoOAG',
            'client_secret' => 'lhZDs0wglHL37Bij',
            'sender' => 'tel:+22100000000',
        ],
        '+223' => [
            'client_id' => 'YYebGiokWShMlHnqVLJgsGPeX1kOiXkw',
            'client_secret' => 'hjebhYdWIGAWHP8X',
            'sender' => 'tel:+22300000000',
        ],
        '+216' => [
            'client_id' => 'VlMy7vfDGiIJI0LrVyzbDgdpjgP1qGLO',
            'client_secret' => 'D279oKBxqZOzpGPU',
            'sender' => 'tel:+21600000000',
        ],
    ],
];
