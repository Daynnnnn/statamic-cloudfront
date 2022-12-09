<?php

namespace Daynnnnn\Statamic\Cloudfront;

use Aws\CloudFront\CloudFrontClient;
use Illuminate\Support\Str;

class Cloudfront
{
    private $cloudfront;
    private $config;

    public function __construct($config) {
        $this->config = $config;

        $this->cloudfront = new CloudfrontClient([
            'version'     => '2020-05-31',
            'region'      => $config['region'],
            'credentials' => [
                'key'    => $config['key'],
                'secret' => $config['secret'],
                'token' => $config['token'] ?? null,
            ]
        ]);
    }

    /**
     * Clear specific URLs from cloudfront.
     *
     * @param array $urls
     * @return bool
     */
    public function delete($urls)
    {
        $this->cloudfront->createInvalidation([
            'DistributionId' => $this->config['distribution'], 
            'InvalidationBatch' => [
                'CallerReference' => Str::random(16),
                'Paths' => [
                    'Items' => $urls,
                    'Quantity' => count($urls)
                ]
            ]
        ]);
        return true;
    }

    /**
     * Clear all URLs from cloudfront.
     *
     * @return bool
     */
    public function flush()
    {
        $this->cloudfront->createInvalidation([
            'DistributionId' => $this->config['distribution'], 
            'InvalidationBatch' => [
                'CallerReference' => Str::random(16),
                'Paths' => [
                    'Items' => ['/*'],
                    'Quantity' => 1
                ]
            ]
        ]);
        return true;
    }
}
