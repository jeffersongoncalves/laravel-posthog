<?php

namespace JeffersonGoncalves\PostHog;

/**
 * Entry point exposing the PostHog surface used by this package: event
 * capture and batching, person identification, feature flags, person
 * lookups, HogQL queries, insights and session recordings.
 */
class PostHog
{
    public function __construct(protected PostHogClient $client) {}

    /**
     * Capture a single event.
     *
     * @param  array<string, mixed>  $properties
     * @return array<string, mixed>
     */
    public function capture(string $event, string $distinctId, array $properties = []): array
    {
        return $this->client->ingest('/capture/', [
            'event' => $event,
            'distinct_id' => $distinctId,
            'properties' => $properties,
        ]);
    }

    /**
     * Capture many events in one request.
     *
     * @param  array<int, array<string, mixed>>  $events
     * @return array<string, mixed>
     */
    public function batch(array $events): array
    {
        return $this->client->ingest('/batch/', ['batch' => array_values($events)]);
    }

    /**
     * Set person properties via the reserved $identify event.
     *
     * @param  array<string, mixed>  $properties
     * @return array<string, mixed>
     */
    public function identify(string $distinctId, array $properties = []): array
    {
        return $this->capture('$identify', $distinctId, ['$set' => $properties]);
    }

    /**
     * Merge an anonymous id into an identified person.
     *
     * @return array<string, mixed>
     */
    public function alias(string $distinctId, string $alias): array
    {
        return $this->capture('$create_alias', $distinctId, ['alias' => $alias]);
    }

    /**
     * All feature flags evaluated for a person.
     *
     * @param  array<string, string>  $groups
     * @return array<string, bool|string>
     */
    public function featureFlags(string $distinctId, array $groups = []): array
    {
        $response = $this->client->ingest('/decide?v=3', array_filter([
            'distinct_id' => $distinctId,
            'groups' => $groups,
        ]));

        /** @var array<string, bool|string> */
        return (array) ($response['featureFlags'] ?? []);
    }

    /**
     * Value of a single flag: false when off, true when on, or the variant
     * key for a multivariate flag. Null when the flag does not exist.
     *
     * @param  array<string, string>  $groups
     */
    public function featureFlag(string $key, string $distinctId, array $groups = []): bool|string|null
    {
        return $this->featureFlags($distinctId, $groups)[$key] ?? null;
    }

    /** @param array<string, string> $groups */
    public function isFeatureEnabled(string $key, string $distinctId, array $groups = []): bool
    {
        // A variant key means the flag is on, so anything but false/null counts.
        return (bool) $this->featureFlag($key, $distinctId, $groups);
    }

    /**
     * Look persons up, optionally filtered by distinct id.
     *
     * @return array<string, mixed>
     */
    public function persons(?string $distinctId = null): array
    {
        return $this->client->get('persons/', array_filter(['distinct_id' => $distinctId]));
    }

    /**
     * Run a HogQL query.
     *
     * @return array<string, mixed>
     */
    public function query(string $hogql): array
    {
        return $this->client->post('query/', [
            'query' => [
                'kind' => 'HogQLQuery',
                'query' => $hogql,
            ],
        ]);
    }

    /**
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     */
    public function insights(array $query = []): array
    {
        return $this->client->get('insights/', $query);
    }

    /**
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     */
    public function sessionRecordings(array $query = []): array
    {
        return $this->client->get('session_recordings/', $query);
    }
}
