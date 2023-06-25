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

use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Pool\Connection;
use Hyperf\Pool\Exception\ConnectionException;
use Hyperf\Pool\Pool;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class MemcachedConnection extends Connection
{
    protected array $config = [
        'servers' => [],
        'options' => [],
        'pool' => [
            'min_connections' => 1,
            'max_connections' => 10,
            'connect_timeout' => 10.0,
            'wait_timeout' => 3.0,
            'heartbeat' => -1,
        ],
    ];

    protected \Memcached $connection;

    public function __construct(ContainerInterface $container, Pool $pool, array $config)
    {
        parent::__construct($container, $pool);
        $this->config = array_replace_recursive($this->config, $config);

        $this->reconnect();
    }

    public function __call($name, $arguments)
    {
        try {
            $result = $this->connection->{$name}(...$arguments);
        } catch (\Throwable $exception) {
            $result = $this->retry($name, $arguments, $exception);
        }

        return $result;
    }

    /**
     * @throws ConnectionException
     */
    public function getActiveConnection(): static
    {
        if ($this->check()) {
            return $this;
        }

        if (! $this->reconnect()) {
            throw new ConnectionException('Connection reconnect failed.');
        }

        return $this;
    }

    public function reconnect(): bool
    {
        $memcached = new \Memcached();
        if (! empty($this->config['username']) && ! empty($this->config['password'])) {
            $memcached->setOption(\Memcached::OPT_BINARY_PROTOCOL, true);
            $memcached->setSaslAuthData($this->config['username'], $this->config['password']);
        }

        if (! empty($this->config['options']) && is_array($this->config['options']) && count($this->config['options'])) {
            $memcached->setOptions($this->config['options']);
        }

        if (empty($memcached->getServerList())) {
            $servers = array_map(function ($value) {
                $data = explode(':', $value);
                $data[1] = intval($data[1] ?? 11211);
                $data[2] = intval($data[2] ?? 100);
                return $data;
            }, explode(',', $this->config['servers']));
            $memcached->addServers($servers);
        }

        $this->connection = $memcached;
        $this->lastUseTime = microtime(true);

        return true;
    }

    public function close(): bool
    {
        unset($this->connection);

        return true;
    }

    /**
     * @param mixed $name
     * @param mixed $arguments
     * @throws ContainerExceptionInterface
     * @throws \Throwable
     * @throws NotFoundExceptionInterface
     */
    protected function retry($name, $arguments, \Throwable $exception)
    {
        /* @var $logger StdoutLoggerInterface */
        $logger = make(StdoutLoggerInterface::class);
        $logger->warning('Memcached::__call failed, because ' . $exception->getMessage());

        try {
            $this->reconnect();
            $result = $this->connection->{$name}(...$arguments);
        } catch (\Throwable $exception) {
            $this->lastUseTime = 0.0;
            throw $exception;
        }

        return $result;
    }
}
