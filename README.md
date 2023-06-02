# HapiMemcached
hyperf的memcached协程组件

## 安装
```
composer require nasustop/hapi-memcached
```

## 声称配置文件
```
php bin/hyperf.php vendor:publish nasustop/hapi-memcached
```

## 新增多服务器随机算法
如果配置中开启了`enable_rand_server=true`，则每个协程中会随机获取`servers`配置多服务器中的一个建立连接并使用
该方法主要应用场景为多台memcached服务器的缓存数据一致的情况，会造成多次刷新缓存，不推荐使用