<?php

return [
    'api_credentials' => [
        'base_url' => (env('SPARKLE_ENV') == 'PRODUCTION') ? env('SPARKLE_BASE_URL_PROD') : env('SPARKLE_BASE_URL_DEV'),
        'email' => (env('SPARKLE_ENV') == 'DEVELOPMENT') ? env('SPARKLE_EMAIL_DEV') : env('SPARKLE_EMAIL_PROD'),
        'password' => (env('SPARKLE_ENV') == 'DEVELOPMENT') ? env('SPARKLE_PASSWORD_DEV') : env('SPARKLE_PASSWORD_PROD'),
        'client_key' => env('SPARKLE_CLIENT_KEY'),
        'secret_key' => env('SPARKLE_SECRET_KEY')
    ]
];