<?php

namespace Jeffersongoncalves\Posthog;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class PosthogServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-posthog')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigrations();
    }
}
