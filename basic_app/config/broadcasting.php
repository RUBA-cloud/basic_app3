<?php

$cluster = env('PUSHER_APP_CLUSTER', 'ap2');
$host    = env('PUSHER_HOST');
$port    = env('PUSHER_PORT', 443);
$scheme  = env('PUSHER_SCHEME', 'https');
$useTLS  = $scheme === 'https';

$pusherOptions = [
    'cluster' => $cluster,
    'useTLS'  => $useTLS,
];

if (!empty($host)) {
    $pusherOptions = array_merge($pusherOptions, [
        'host'   => $host,
        'port'   => (int) $port,
        'scheme' => $scheme,
    ]);
}

return [

    /*
    |--------------------------------------------------------------------------
    | Default Broadcaster
    |--------------------------------------------------------------------------
    */
    'default' => env('BROADCAST_CONNECTION', 'pusher'),

    /*
    |--------------------------------------------------------------------------
    | Broadcast Connections
    |--------------------------------------------------------------------------
    */
    'connections' => [

        'pusher' => [
            'driver'  => 'pusher',
            'key'     => env('PUSHER_APP_KEY'),
            'secret'  => env('PUSHER_APP_SECRET'),
            'app_id'  => env('PUSHER_APP_ID'),
            'options' => $pusherOptions,
            'client_options' => [],
        ],

        'ably' => [
            'driver' => 'ably',
            'key'    => env('ABLY_KEY'),
        ],

        'log' => [
            'driver' => 'log',
        ],

        'null' => [
            'driver' => 'null',
        ],
    ],
];
