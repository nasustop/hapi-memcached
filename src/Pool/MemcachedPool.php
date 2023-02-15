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
namespace Nasustop\HapiMemcached\Pool;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\ConnectionInterface;
use Hyperf\Pool\Frequency;
use Hyperf\Pool\Pool;
use Nasustop\HapiMemcached\MemcachedConnection;
use Psr\Container\ContainerInterface;

class MemcachedPool extends Pool
{
    protected array $config;

    public function __construct(ContainerInterface $container, protected string $name)
    {
        $config = $container->get(ConfigInterface::class);
        $key = sprintf('memcached.%s', $this->name);
        if (! $config->has($key)) {
            throw new \InvalidArgumentException(sprintf('config[%s] is not exist!', $key));
        }

        $this->config = $config->get($key);
        $options = $this->config['pool'] ?? [];

        $this->frequency = make(Frequency::class);

        parent::__construct($container, $options);
    }

    public function getName(): string
    {
        return $this->name;
    }

    protected function createConnection(): ConnectionInterface
    {
        // TODO: Implement createConnection() method.
        return new MemcachedConnection($this->container, $this, $this->config);
    }
}
