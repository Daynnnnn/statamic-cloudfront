<?php

namespace Daynnnnn\Statamic\Cloudfront;

use Illuminate\Cache\Repository;
use Statamic\Providers\AddonServiceProvider;
use Statamic\StaticCaching\StaticCacheManager;
use Illuminate\Contracts\Http\Kernel;

class CloudfrontServiceProvider extends AddonServiceProvider
{
    public function boot()
    {
        $kernel = $this->app->make(Kernel::class);
        $kernel->appendMiddlewareToGroup('web', Middleware\CacheControlHeader::class);

        $cloudfrontStrategies = collect(config('statamic.static_caching.strategies'));

        $cloudfrontStrategies->where('driver', 'cloudfront')->each(function ($item, $strategy) {
            $this->app[StaticCacheManager::class]->extend($strategy, function () use ($strategy) {
                return new CloudfrontCacher($this->app[Repository::class], $this->getConfig($strategy));
            });
        });
    }
}
