<?php

namespace Daynnnnn\Statamic\Cloudfront;

use Aws\CloudFront\CloudFrontClient;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Cloudfront
{
    private $cloudfront;

    private $config;

    public function __construct($config)
    {
        $this->config = $config;

        $clientConfig = [
            'version' => '2020-05-31',
            'region' => $config['region'],
        ];

        if (! empty($this->config['key']) && ! empty($this->config['secret'])) {
            $clientConfig['credentials'] = Arr::only($this->config, ['key', 'secret', 'token']);
        }

        $this->cloudfront = new CloudfrontClient($clientConfig);
    }

    /**
     * Clear specific URLs from cloudfront.
     *
     * @param  array  $urls
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
                    'Quantity' => count($urls),
                ],
            ],
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
                    'Quantity' => 1,
                ],
            ],
        ]);

        return true;
    }
}
