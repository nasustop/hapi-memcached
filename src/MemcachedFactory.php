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

use Hyperf\Contract\ConfigInterface;
use Nasustop\HapiMemcached\Exception\InvalidMemcachedConnectionException;

class MemcachedFactory
{
    /**
     * @var MemcachedProxy[]
     */
    protected array $proxies = [];

    public function __construct(ConfigInterface $config)
    {
        $redisConfig = $config->get('memcached');

        foreach ($redisConfig as $poolName => $item) {
            $this->proxies[$poolName] = make(MemcachedProxy::class, ['pool' => $poolName]);
        }
    }

    public function get(string $poolName): MemcachedProxy
    {
        $proxy = $this->proxies[$poolName] ?? null;
        if (! $proxy instanceof MemcachedProxy) {
            throw new InvalidMemcachedConnectionException('Invalid Memcached proxy.');
        }

        return $proxy;
    }
}
