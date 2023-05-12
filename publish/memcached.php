<?php

declare(strict_types=1);
/**
 * This file is part of Hapi.
 *
 * @link     https://www.nasus.top
 * @document https://wiki.nasus.top
 * @contact  xupengfei@xupengfei.net
 * @license  https://github.com/nasustop/hapi-memcached/blob/master/LICENSE
 */
return [
    'default' => [
        'servers' => [
            [
                'host' => env('MEMCACHED_HOST', 'localhost'),
                'port' => (int) env('MEMCACHED_PORT', 11211),
                'weight' => (int) env('MEMCACHED_WEIGHT', 100),
            ],
        ],
        'username' => env('MEMCACHED_USERNAME', ''),
        'password' => env('MEMCACHED_PASSWORD', ''),
        'options' => [],
        'pool' => [
            'min_connections' => swoole_cpu_num(),
            'max_connections' => swoole_cpu_num() * 2,
            'connect_timeout' => 10.0,
            'wait_timeout' => 3.0,
            'heartbeat' => -1,
            'max_idle_time' => (float) env('MEMCACHED_MAX_IDLE_TIME', 60),
        ],
    ],
];
