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
namespace Nasustop\HapiMemcached;

use Hyperf\Context\Context;
use Nasustop\HapiMemcached\Exception\InvalidMemcachedConnectionException;
use Nasustop\HapiMemcached\Pool\PoolFactory;

/**
 * @mixin \Memcached
 */
class Memcached
{
    protected string $poolName = 'default';

    public function __construct(protected PoolFactory $factory)
    {
    }

    public function __call($name, $arguments)
    {
        // Get a connection from coroutine context or connection pool.
        $hasContextConnection = Context::has($this->getContextKey());
        $connection = $this->getConnection($hasContextConnection);

        try {
            $connection = $connection->getConnection();
            // Execute the command with the arguments.
            $result = $connection->{$name}(...$arguments);
        } finally {
            // Release connection.
            if (! $hasContextConnection) {
                // Should storage the connection to coroutine context, then use defer() to release the connection.
                Context::set($this->getContextKey(), $connection);
                defer(function () use ($connection) {
                    Context::set($this->getContextKey(), null);
                    $connection->release();
                });
            }
        }

        return $result;
    }

    /**
     * Get a connection from coroutine context, or from redis connection pool.
     * @param mixed $hasContextConnection
     */
    private function getConnection($hasContextConnection): MemcachedConnection
    {
        $connection = null;
        if ($hasContextConnection) {
            $connection = Context::get($this->getContextKey());
        }
        if (! $connection instanceof MemcachedConnection) {
            $pool = $this->factory->getPool($this->poolName);
            $connection = $pool->get();
        }
        if (! $connection instanceof MemcachedConnection) {
            throw new InvalidMemcachedConnectionException('The connection is not a valid MemcachedConnection.');
        }
        return $connection;
    }

    /**
     * The key to identify the connection object in coroutine context.
     */
    private function getContextKey(): string
    {
        return sprintf('memcached.connection.%s', $this->poolName);
    }
}
