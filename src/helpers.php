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
use Nasustop\HapiMemcached\MemcachedFactory;
use Nasustop\HapiMemcached\MemcachedProxy;

if (! function_exists('memcached')) {
    /**
     * 获取memcached连接.
     */
    function memcached(string $pool = 'default'): MemcachedProxy
    {
        /* @var $memcached MemcachedFactory */
        $memcached = make(MemcachedFactory::class);
        return $memcached->get($pool);
    }
}
