<?php

namespace Daynnnnn\Statamic\Cloudfront;

use Illuminate\Cache\Repository;
use Illuminate\Http\Request;
use Statamic\StaticCaching\Cachers\AbstractCacher;
use Statamic\Facades\Blink;

class CloudfrontCacher extends AbstractCacher
{
    /**
     * @var Writer
     */
    private $cloudfront;

    /**
     * @param Writer $writer
     * @param Repository $cache
     * @param array $config
     */
    public function __construct(Repository $cache, $config)
    {
        parent::__construct($cache, $config);
        $this->cloudfront = new Cloudfront($config);
    }

    /**
     * Cache a page.
     *
     * @param \Illuminate\Http\Request $request     Request associated with the page to be cached
     * @param string                   $content     The response content to be cached
     */
    public function cachePage(Request $request, $content)
    {
        $url = $this->getUrl($request);

        if ($this->isExcluded($url)) {
            return;
        }

        Blink::put('statamic-cloudfront', 'max-age=2592000, public');

        $this->cacheUrl($this->makeHash($url), $url);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return string
     */
    public function getCachedPage(Request $request)
    {
        return false;
    }

    public function hasCachedPage(Request $request)
    {
        return false;
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
     * @param string $url
     * @return void
     */
    public function invalidateUrl($url)
    {
        if (! $key = $this->getUrls()->flip()->get($url)) {
            // URL doesn't exist, nothing to invalidate.
            return;
        }

        $this->cloudfront->delete([$url]);

        $this->forgetUrl($key);
    }

    public function getCachePaths()
    {
        $paths = $this->config('path');

        if (! is_array($paths)) {
            $paths = [$this->config('locale') => $paths];
        }

        return $paths;
    }

    /**
     * Get the path where static files are stored.
     *
     * @param string|null $locale  A specific locale's path.
     * @return string
     */
    public function getCachePath($locale = null)
    {

    }

    private function isBasenameTooLong($basename)
    {
        return strlen($basename) > $this->config('max_filename_length', 255);
    }

    private function isLongQueryStringPath($path)
    {
        return Str::contains($path, '_lqs_');
    }
}
