<?php

return [
    'default' => env('DB_CONNECTION', 'mysql'),
    'migrations' => 'migrations',
    'connections' => [
        'mysql' => [
            'driver' => 'mysql',
            'host' => env('DATABASE_HOST', '127.0.0.1'),
            'port' => env('DATABASE_PORT', '3306'),
            'database' => env('DATABASE_NAME', 'forge'),
            'username' => env('DATABASE_USER', 'forge'),
            'password' => env('DATABASE_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => env('DATABASE_CHARSET', 'utf8mb4'),
            'collation' => env('DATABASE_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],
    ],
    'redis' => [
        'client' => 'phpredis',
        'default' => [
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', 6379),
            'database' => env('REDIS_DB', 0),
        ],
    ],
];
