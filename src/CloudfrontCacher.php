<?php

namespace Daynnnnn\Statamic\Cloudfront;

use Illuminate\Cache\Repository;
use Illuminate\Http\Request;
use Statamic\Facades\Blink;
use Statamic\StaticCaching\Cachers\AbstractCacher;

class CloudfrontCacher extends AbstractCacher
{
    /**
     * @var Writer
     */
    private $cloudfront;

    /**
     * @param  Writer  $writer
     * @param  array  $config
     */
    public function __construct(Repository $cache, $config)
    {
        parent::__construct($cache, $config);
        $this->cloudfront = new Cloudfront($config);
    }

    /**
     * Cache a page.
     *
     * @param  \Illuminate\Http\Request  $request  Request associated with the page to be cached
     * @param  string  $content  The response content to be cached
     */
    public function cachePage(Request $request, $content)
    {
        $url = $this->getUrl($request);

        if ($this->isExcluded($url)) {
            return;
        }

        Blink::put('statamic-cloudfront', 'max-age='.($this->config('expiry') ?? '2592000').', public');

        $this->cacheUrl($this->makeHash($url), $url);
    }

    /**
     * @return string
     */
    public function getCachedPage(Request $request)
    {
        return null;
    }

    /**
     * Flush out the entire static cache.
     *
     * @return void
     */
    public function flush()
    {
        $this->cloudfront->flush();
        $this->flushUrls();
    }

    /**
     * Invalidate a URL.
     *
     * @param  string  $url
     * @return void
     */
    public function invalidateUrl($urls)
    {
        $urls = is_string($urls) ? [$urls] : $urls;

        $this->cloudfront->delete($urls);

        foreach ($urls as $url) {
            if (! $key = $this->getUrls()->flip()->get($url)) {
                // URL doesn't exist, nothing to invalidate.
                return;
            }

            $this->forgetUrl($key);
        }
    }

    /**
     * Invalidate multiple URLs.
     *
     * @param  array  $urls
     * @return void
     */
    public function invalidateUrls($urls)
    {
        if ($urls !== null) {
            $this->invalidateUrl($urls);
        }
    }
}
