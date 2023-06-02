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
namespace Nasustop\HapiMemcached\Pool;

use Hyperf\Di\Container;
use Psr\Container\ContainerInterface;

class PoolFactory
{
    /**
     * @var MemcachedPool[]
     */
    protected array $pools = [];

    protected bool $enable_rand_server = false;

    protected string $server = '';

    public function __construct(protected ContainerInterface $container)
    {
    }

    /**
     * set EnableRandServer.
     */
    public function setEnableRandServer(bool $enable_rand_server): self
    {
        $this->enable_rand_server = $enable_rand_server;
        return $this;
    }

    /**
     * set Server.
     */
    public function setServer(string $server): self
    {
        $this->server = $server;
        return $this;
    }

    public function getPool(string $name): MemcachedPool
    {
        if (isset($this->pools[$name]) && $this->pools[$name] instanceof MemcachedPool) {
            return $this->pools[$name];
        }
        if ($this->enable_rand_server && ! empty($this->server) && isset($this->pools[$name][$this->server]) && $this->pools[$name][$this->server] instanceof MemcachedPool) {
            return $this->pools[$name][$this->server];
        }

        if ($this->container instanceof Container) {
            $memcachedPool = $this->container->make(MemcachedPool::class, ['name' => $name]);
        } else {
            $memcachedPool = new MemcachedPool($this->container, $name);
        }
        if ($this->enable_rand_server && ! empty($this->server)) {
            $this->pools[$name][$this->server] = $memcachedPool->setConfig($this->server);
            return $this->pools[$name][$this->server];
        }
        $this->pools[$name] = $memcachedPool;
        return $this->pools[$name];
    }
}
