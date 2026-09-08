<?php

use Illuminate\Support\Facades\Http;
use JeffersonGoncalves\PostHog\Exceptions\PostHogException;
use JeffersonGoncalves\PostHog\Facades\PostHog;

it('captures an event with the project api key in the payload', function () {
    Http::fake(['*/capture/' => Http::response(['status' => 1])]);

    expect(PostHog::capture('signup_completed', 'user_123', ['plan' => 'pro']))
        ->toBe(['status' => 1]);

    Http::assertSent(function ($request) {
        return $request->url() === 'https://app.posthog.com/capture/'
            && $request['api_key'] === 'phc_test'
            && $request['event'] === 'signup_completed'
            && $request['distinct_id'] === 'user_123'
            && $request['properties'] === ['plan' => 'pro'];
    });
});

it('sends a batch of events', function () {
    Http::fake(['*/batch/' => Http::response(['status' => 1])]);

    PostHog::batch([
        ['event' => 'pageview', 'distinct_id' => 'user_1'],
        ['event' => 'signup', 'distinct_id' => 'user_2'],
    ]);

    Http::assertSent(function ($request) {
        return $request->url() === 'https://app.posthog.com/batch/'
            && $request['api_key'] === 'phc_test'
            && count($request['batch']) === 2;
    });
});

it('identifies a person through the $identify event', function () {
    Http::fake(['*/capture/' => Http::response(['status' => 1])]);

    PostHog::identify('user_123', ['email' => 'user@example.com']);

    Http::assertSent(function ($request) {
        return $request['event'] === '$identify'
            && $request['properties']['$set'] === ['email' => 'user@example.com'];
    });
});

it('aliases a distinct id', function () {
    Http::fake(['*/capture/' => Http::response(['status' => 1])]);

    PostHog::alias('user_123', 'anon_abc');

    Http::assertSent(function ($request) {
        return $request['event'] === '$create_alias'
            && $request['properties']['alias'] === 'anon_abc';
    });
});

it('reads feature flags for a person', function () {
    Http::fake(['*/decide*' => Http::response([
        'featureFlags' => ['new-pricing' => true, 'checkout' => 'variant-b', 'legacy' => false],
    ])]);

    expect(PostHog::featureFlags('user_123'))
        ->toBe(['new-pricing' => true, 'checkout' => 'variant-b', 'legacy' => false])
        ->and(PostHog::featureFlag('checkout', 'user_123'))->toBe('variant-b')
        ->and(PostHog::featureFlag('missing', 'user_123'))->toBeNull()
        ->and(PostHog::isFeatureEnabled('new-pricing', 'user_123'))->toBeTrue()
        ->and(PostHog::isFeatureEnabled('checkout', 'user_123'))->toBeTrue()
        ->and(PostHog::isFeatureEnabled('legacy', 'user_123'))->toBeFalse()
        ->and(PostHog::isFeatureEnabled('missing', 'user_123'))->toBeFalse();
});

it('looks a person up by distinct id with the personal api key', function () {
    Http::fake(['*/api/projects/42/persons/*' => Http::response(['results' => []])]);

    PostHog::persons('user_123');

    Http::assertSent(function ($request) {
        return str_starts_with($request->url(), 'https://app.posthog.com/api/projects/42/persons/')
            && str_contains($request->url(), 'distinct_id=user_123')
            && $request->hasHeader('Authorization', 'Bearer phx_test');
    });
});

it('runs a hogql query', function () {
    Http::fake(['*/api/projects/42/query/' => Http::response(['results' => [['pageview', 10]]])]);

    expect(PostHog::query('SELECT event, count() FROM events'))
        ->toBe(['results' => [['pageview', 10]]]);

    Http::assertSent(function ($request) {
        return $request['query'] === [
            'kind' => 'HogQLQuery',
            'query' => 'SELECT event, count() FROM events',
        ];
    });
});

it('lists insights and session recordings', function () {
    Http::fake([
        '*/api/projects/42/insights/*' => Http::response(['results' => ['insight']]),
        '*/api/projects/42/session_recordings/*' => Http::response(['results' => ['recording']]),
    ]);

    expect(PostHog::insights())->toBe(['results' => ['insight']])
        ->and(PostHog::sessionRecordings())->toBe(['results' => ['recording']]);
});

it('throws a PostHogException on a failed response', function () {
    Http::fake(['*' => Http::response(['detail' => 'Invalid personal API key.'], 401)]);

    expect(fn () => PostHog::persons('user_123'))
        ->toThrow(PostHogException::class, 'Invalid personal API key.');
});
