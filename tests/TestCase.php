<?php

namespace JeffersonGoncalves\PostHog\Tests;

use JeffersonGoncalves\PostHog\PostHogServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    /** @return array<int, class-string> */
    protected function getPackageProviders($app): array
    {
        return [
            PostHogServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('posthog.host', 'https://app.posthog.com');
        $app['config']->set('posthog.project_api_key', 'phc_test');
        $app['config']->set('posthog.personal_api_key', 'phx_test');
        $app['config']->set('posthog.project_id', '42');
    }
}
