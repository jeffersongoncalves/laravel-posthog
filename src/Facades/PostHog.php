<?php

namespace JeffersonGoncalves\PostHog\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \JeffersonGoncalves\PostHog\PostHog
 *
 * @method static array capture(string $event, string $distinctId, array $properties = [])
 * @method static array batch(array $events)
 * @method static array identify(string $distinctId, array $properties = [])
 * @method static array alias(string $distinctId, string $alias)
 * @method static array featureFlags(string $distinctId, array $groups = [])
 * @method static bool|string|null featureFlag(string $key, string $distinctId, array $groups = [])
 * @method static bool isFeatureEnabled(string $key, string $distinctId, array $groups = [])
 * @method static array persons(?string $distinctId = null)
 * @method static array query(string $hogql)
 * @method static array insights(array $query = [])
 * @method static array sessionRecordings(array $query = [])
 */
class PostHog extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \JeffersonGoncalves\PostHog\PostHog::class;
    }
}
