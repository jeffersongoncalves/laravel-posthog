<?php

use JeffersonGoncalves\PostHog\PostHog;
use JeffersonGoncalves\PostHog\PostHogClient;

it('registers the client and entry point as singletons', function () {
    expect(app(PostHogClient::class))->toBeInstanceOf(PostHogClient::class)
        ->and(app(PostHogClient::class))->toBe(app(PostHogClient::class))
        ->and(app(PostHog::class))->toBeInstanceOf(PostHog::class)
        ->and(app(PostHog::class))->toBe(app(PostHog::class));
});

it('publishes the config file', function () {
    expect(config('posthog.host'))->toBe('https://app.posthog.com')
        ->and(realpath(__DIR__.'/../../config/posthog.php'))->toBeString();
});
