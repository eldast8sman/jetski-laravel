<?php
$type = env('ENV_TYPE', 'LOCAL');

if($type == 'PRODUCTION'){
    $env = env('SPARKLE_ENV_PROD');
} elseif($type == 'STAGING'){
    $env = env('SPARKLE_ENV_DEV');
} else {
    $env = env('SPARKLE_ENV');
}

return [
    'api_credentials' => [
        'base_url' => ($env == 'PRODUCTION') ? env('SPARKLE_BASE_URL_PROD') : env('SPARKLE_BASE_URL_DEV'),
        'email' => ($env == 'DEVELOPMENT') ? env('SPARKLE_EMAIL_DEV') : env('SPARKLE_EMAIL_PROD'),
        'password' => ($env == 'DEVELOPMENT') ? env('SPARKLE_PASSWORD_DEV') : env('SPARKLE_PASSWORD_PROD'),
        'client_key' => env('SPARKLE_CLIENT_KEY'),
        'secret_key' => env('SPARKLE_SECRET_KEY')
    ]
];