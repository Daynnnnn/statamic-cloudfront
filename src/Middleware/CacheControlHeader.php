<?php

namespace Daynnnnn\Statamic\Cloudfront\Middleware;

use Closure;
use Statamic\Facades\Blink;

class CacheControlHeader
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if ($header = Blink::get('statamic-cloudfront')) {
            return $response->header('Cache-Control', $header);
        }

        return $response;
    }
}
