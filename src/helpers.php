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
if (! function_exists('memcached')) {
    /**
     * 获取memcached连接.
     */
    function memcached(string $pool = 'default'): Nasustop\HapiMemcached\MemcachedProxy
    {
        try {
            return \Hyperf\Utils\ApplicationContext::getContainer()->get(\Nasustop\HapiMemcached\MemcachedFactory::class)->get($pool);
        } catch (\Psr\Container\NotFoundExceptionInterface|\Psr\Container\ContainerExceptionInterface $e) {
            return make(\Nasustop\HapiMemcached\MemcachedFactory::class)->get($pool);
        }
    }
}
