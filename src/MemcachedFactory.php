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
        $memcachedConfig = $config->get('memcached');

        foreach ($memcachedConfig as $poolName => $item) {
            $enable_rand_server = $item['enable_rand_server'] ?? false;
            if (! $enable_rand_server) {
                $this->proxies[$poolName] = make(MemcachedProxy::class, ['pool' => $poolName]);
            } else {
                $servers = explode(',', $item['servers']);
                foreach ($servers as $server) {
                    /* @var MemcachedProxy $proxy */
                    $proxy = make(MemcachedProxy::class, ['pool' => $poolName]);
                    $proxy->setEnableRandServer($enable_rand_server);
                    $proxy->setServer($server);
                    $this->proxies[$poolName][$server] = $proxy;
                }
            }
        }
    }

    public function get(string $poolName): MemcachedProxy
    {
        $proxy = $this->proxies[$poolName] ?? null;
        if (is_array($proxy)) {
            $hasContextServer = Context::has($this->getContextKey($poolName));
            if ($hasContextServer) {
                $proxy = Context::get($this->getContextKey($poolName));
            } else {
                $proxy = $this->proxies[$poolName][array_rand($proxy)];
                Context::set($this->getContextKey($poolName), $proxy);
            }
        }
        if (! $proxy instanceof MemcachedProxy) {
            throw new InvalidMemcachedConnectionException('Invalid Memcached proxy.');
        }

        return $proxy;
    }

    /**
     * The key to identify the connection object in coroutine context.
     */
    private function getContextKey(string $poolName): string
    {
        return sprintf('memcached.server.%s', $poolName);
    }
}
