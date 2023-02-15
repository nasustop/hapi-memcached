<?php

declare(strict_types=1);
/**
 * This file is part of HapiBase.
 *
 * @link     https://www.nasus.top
 * @document https://wiki.nasus.top
 * @contact  xupengfei@xupengfei.net
 * @license  https://github.com/nasustop/hapi-memcached/blob/master/LICENSE
 */
namespace Nasustop\HapiMemcached;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                \Memcached::class => Memcached::class,
            ],
            'commands' => [
            ],
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                ],
            ],
            'publish' => [
                [
                    'id' => 'memcached',
                    'description' => 'The config for memcached.',
                    'source' => __DIR__ . '/../publish/memcached.php',
                    'destination' => BASE_PATH . '/config/autoload/memcached.php',
                ],
            ],
        ];
    }
}
