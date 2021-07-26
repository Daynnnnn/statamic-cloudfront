<?php

namespace Daynnnnn\Statamic\Cloudfront;

use Illuminate\Cache\Repository;
use Statamic\Providers\AddonServiceProvider;
use Statamic\StaticCaching\StaticCacheManager;
use Statamic\Statamic;

class ServiceProvider extends AddonServiceProvider
{
    public function boot()
    {}

    public function register()
    {
        $config = config('statamic.static_caching.strategies.cloudfront');

        if ($config !== null) {
            $this->app[StaticCacheManager::class]->extend('cloudfront', function () use ($config) {
                return new CloudfrontCacher($this->app[Repository::class], $config);
            });

            $router = $this->app['router'];
            $router->pushMiddlewareToGroup('web', Middleware\CacheControlHeader::class);
        }
    }
}
