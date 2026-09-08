<?php

namespace JeffersonGoncalves\PostHog;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class PostHogServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('posthog')
            ->hasConfigFile();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(PostHogClient::class, function () {
            return new PostHogClient(
                host: rtrim((string) config('posthog.host'), '/'),
                projectApiKey: (string) config('posthog.project_api_key'),
                personalApiKey: (string) config('posthog.personal_api_key'),
                projectId: (string) config('posthog.project_id'),
                timeout: (int) config('posthog.timeout', 10),
            );
        });

        $this->app->singleton(PostHog::class, function ($app) {
            return new PostHog($app->make(PostHogClient::class));
        });
    }
}
