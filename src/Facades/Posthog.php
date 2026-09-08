<?php

namespace Jeffersongoncalves\Posthog\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Jeffersongoncalves\Posthog\Posthog
 */
class Posthog extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'laravel-posthog';
    }
}
