## Statamic Cloudfront

Use cloudfront as a static cache driver.

## Installation

From a standard Statamic V3 site, you can run:
`composer require daynnnnn/statamic-cloudfront`

Then you'll need to add the cloudfront strategy to your static cache config:

```
    'strategies' => [

        ...

        'cloudfront' => [
            'driver' => 'cloudfront',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'distribution' => env('CLOUDFRONT_DISTRIBUTION_ID'),
        ],

    ],
```

### How it works
It's pretty simple; if the page should be cached, it sets the responses cache control header to cache for 30 days. Then if a page is updated, an invalidation request will be sent to cloudfront for that page.

### Things to work on
- Add some tests.
- Try and remove `aws/aws-sdk-php` dependency.
- Make sure app cache isn't a source of truth for which pages are in cache, as this can be out of sync with cloudfront.