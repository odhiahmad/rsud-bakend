<?php

return [


    'secret' => env('JWT_SECRET'),

    'keys' => [

        'public' => env('JWT_PUBLIC_KEY'),

        'private' => env('JWT_PRIVATE_KEY'),

        'passphrase' => env('JWT_PASSPHRASE'),
    ],

    'ttl' => env('JWT_TTL', 1440),

    'refresh_ttl' => env('JWT_REFRESH_TTL', 60),

    'algo' => env('JWT_ALGO', 'HS256'),

    'required_claims' => [
        'exp',
        'iss',
        'iat',
        'nbf',
        'sub',
        'jti',
    ],

    'persistent_claims' => [
        // 'foo',
        // 'bar',
    ],

    'lock_subject' => true,

    'leeway' => env('JWT_LEEWAY', 0),

    'blacklist_enabled' => env('JWT_BLACKLIST_ENABLED', true),

    'blacklist_grace_period' => env('JWT_BLACKLIST_GRACE_PERIOD', 0),

    'decrypt_cookies' => false,

    'providers' => [

        'jwt' => Tymon\JWTAuth\Providers\JWT\Lcobucci::class,

        'auth' => Tymon\JWTAuth\Providers\Auth\Illuminate::class,

        'storage' => Tymon\JWTAuth\Providers\Storage\Illuminate::class,
    ],
];
