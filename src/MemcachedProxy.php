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

use Nasustop\HapiMemcached\Pool\PoolFactory;

/**
 * @mixin \Memcached
 */
class MemcachedProxy extends Memcached
{
    public function __construct(PoolFactory $factory, string $pool)
    {
        parent::__construct($factory);

        $this->poolName = $pool;
    }

    /**
     * WARN: Can't remove this function, because AOP need it.
     * @see https://github.com/hyperf/hyperf/issues/1239
     * @param string $name
     * @param array $arguments
     */
    public function __call($name, $arguments)
    {
        return parent::__call($name, $arguments);
    }
}
