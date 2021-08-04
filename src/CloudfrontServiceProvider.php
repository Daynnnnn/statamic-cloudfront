<?php

namespace Daynnnnn\Statamic\Cloudfront;

use Illuminate\Cache\Repository;
use Statamic\Providers\AddonServiceProvider;
use Statamic\StaticCaching\StaticCacheManager;
use Statamic\Statamic;

class CloudfrontServiceProvider extends AddonServiceProvider
{

    public function register()
    {
        //
    }

    public function boot()
    {
        $cloudfrontStrategies = collect(config('statamic.static_caching.strategies'));

        $cloudfrontStrategies->where('driver', 'cloudfront')->each(function ($item, $strategy) {
            $this->app[StaticCacheManager::class]->extend($strategy, function () use ($strategy) {
                return new CloudfrontCacher($this->app[Repository::class], $this->getConfig($strategy));
            });
        });

        $router = $this->app['router'];
        $router->pushMiddlewareToGroup('web', Middleware\CacheControlHeader::class);
    }
}
