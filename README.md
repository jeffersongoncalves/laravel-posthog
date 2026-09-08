<div class="filament-hidden">

![Laravel PostHog](https://raw.githubusercontent.com/jeffersongoncalves/laravel-posthog/main/art/jeffersongoncalves-laravel-posthog.png)

</div>

# Laravel PostHog

[![Latest Version on Packagist](https://img.shields.io/packagist/v/jeffersongoncalves/laravel-posthog.svg?style=flat-square)](https://packagist.org/packages/jeffersongoncalves/laravel-posthog)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/jeffersongoncalves/laravel-posthog/tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/jeffersongoncalves/laravel-posthog/actions?query=workflow%3ATests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/jeffersongoncalves/laravel-posthog/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/jeffersongoncalves/laravel-posthog/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/jeffersongoncalves/laravel-posthog.svg?style=flat-square)](https://packagist.org/packages/jeffersongoncalves/laravel-posthog)
[![License](https://img.shields.io/packagist/l/jeffersongoncalves/laravel-posthog.svg?style=flat-square)](LICENSE.md)

A PHP/Laravel client for [PostHog](https://posthog.com). Capture events, batch ingestion, identify people, evaluate feature flags and read back persons, insights, session recordings and HogQL queries — through a simple, typed API built on Laravel's `Http` client. Works with PostHog Cloud (US/EU) and self-hosted instances.

## Features

- Event capture: single events and batched ingestion
- Person identification: `$identify` and `$create_alias` helpers over the capture endpoint
- Feature flags: read every flag for a person, a single flag value (including multivariate variants), or a simple boolean check
- Persons: look people up by `distinct_id`
- HogQL: run SQL-like queries against your event data
- Insights and session recordings: list them from the project API
- Handles PostHog's two auth models for you — the public project API key in the payload for ingestion, the personal API key as a Bearer token for reads
- Throws `PostHogException` (with the original API error body) on any non-2xx response

## Installation

You can install the package via composer:

```bash
composer require jeffersongoncalves/laravel-posthog
```

Publish the config file:

```bash
php artisan vendor:publish --tag=posthog-config
```

Then set your credentials:

```env
POSTHOG_HOST=https://us.i.posthog.com
POSTHOG_PROJECT_API_KEY=phc_your_project_api_key
POSTHOG_PERSONAL_API_KEY=phx_your_personal_api_key
POSTHOG_PROJECT_ID=12345
```

`POSTHOG_PROJECT_API_KEY` is the public write-only key and is all you need for capture, batch and feature flags. `POSTHOG_PERSONAL_API_KEY` plus `POSTHOG_PROJECT_ID` are only required for the read endpoints (persons, HogQL, insights, session recordings).

## Configuration

```php
// config/posthog.php
return [
    'host' => env('POSTHOG_HOST', 'https://app.posthog.com'),
    'project_api_key' => env('POSTHOG_PROJECT_API_KEY', ''),
    'personal_api_key' => env('POSTHOG_PERSONAL_API_KEY', ''),
    'project_id' => env('POSTHOG_PROJECT_ID', ''),
    'timeout' => env('POSTHOG_TIMEOUT', 10),
];
```

## Usage

The package is resolved via the `PostHog` facade or by injecting `JeffersonGoncalves\PostHog\PostHog`.

### Capturing events

```php
use JeffersonGoncalves\PostHog\Facades\PostHog;

PostHog::capture('signup_completed', 'user_123', [
    'plan' => 'pro',
    '$current_url' => 'https://example.com/signup',
]);
```

### Batching events

```php
PostHog::batch([
    ['event' => 'pageview', 'distinct_id' => 'user_1'],
    ['event' => 'signup', 'distinct_id' => 'user_2'],
]);
```

### Identifying people

```php
PostHog::identify('user_123', [
    'email' => 'user@example.com',
    'plan' => 'pro',
]);

// Merge an anonymous session into an identified person
PostHog::alias('user_123', 'anon_abc');
```

### Feature flags

```php
// Every flag evaluated for this person
$flags = PostHog::featureFlags('user_123');

// A single flag: false when off, true when on, the variant key when multivariate
$variant = PostHog::featureFlag('new-pricing', 'user_123');

// Simple boolean check (a variant counts as enabled)
if (PostHog::isFeatureEnabled('new-pricing', 'user_123')) {
    // Show new pricing
}
```

Group-based flags are supported by passing the groups map:

```php
PostHog::isFeatureEnabled('beta-dashboard', 'user_123', ['company' => 'acme-inc']);
```

### Persons

```php
$persons = PostHog::persons('user_123');
```

### HogQL queries

```php
$result = PostHog::query(
    'SELECT event, count() FROM events WHERE timestamp > now() - interval 7 day GROUP BY event ORDER BY count() DESC LIMIT 10'
);
```

### Insights and session recordings

```php
$insights = PostHog::insights();

$recordings = PostHog::sessionRecordings(['limit' => 20]);
```

### Error handling

Any non-2xx API response throws `JeffersonGoncalves\PostHog\Exceptions\PostHogException`, which exposes the decoded error body:

```php
use JeffersonGoncalves\PostHog\Exceptions\PostHogException;

try {
    PostHog::persons('user_123');
} catch (PostHogException $e) {
    logger()->error($e->getMessage(), $e->errorBody());
}
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Jefferson Gonçalves](https://github.com/jeffersongoncalves)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
