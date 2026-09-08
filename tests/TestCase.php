<?php

namespace Jeffersongoncalves\Posthog\Tests;

use Jeffersongoncalves\Posthog\PosthogServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            PosthogServiceProvider::class,
        ];
    }
}
