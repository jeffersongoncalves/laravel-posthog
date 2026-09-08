<?php

namespace JeffersonGoncalves\PostHog;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use JeffersonGoncalves\PostHog\Exceptions\PostHogException;

/**
 * Thin wrapper around Laravel's Http client for PostHog.
 *
 * PostHog has two distinct auth models and this client keeps them apart:
 * ingestion endpoints (/capture, /batch, /decide) authenticate with the
 * public project API key sent inside the payload, while the project API
 * (/api/projects/...) needs a personal API key as a Bearer token.
 *
 * ponytail: Http::retry() covers transient failures if ever needed — no
 * custom retry/backoff layer built here speculatively.
 */
class PostHogClient
{
    public function __construct(
        protected string $host,
        protected string $projectApiKey,
        protected string $personalApiKey,
        protected string $projectId,
        protected int $timeout = 10,
    ) {}

    /**
     * POST to an ingestion endpoint, injecting the project API key.
     *
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function ingest(string $path, array $body = []): array
    {
        return $this->send(
            $this->request()->post($path, ['api_key' => $this->projectApiKey] + $body)
        );
    }

    /**
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     */
    public function get(string $path, array $query = []): array
    {
        return $this->send($this->projectRequest()->get($this->projectPath($path), $query));
    }

    /**
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function post(string $path, array $body = []): array
    {
        return $this->send($this->projectRequest()->post($this->projectPath($path), $body));
    }

    protected function projectPath(string $path): string
    {
        return '/api/projects/'.$this->projectId.'/'.ltrim($path, '/');
    }

    protected function request(): PendingRequest
    {
        return Http::baseUrl($this->host)
            ->timeout($this->timeout)
            ->acceptJson()
            ->asJson();
    }

    protected function projectRequest(): PendingRequest
    {
        return $this->request()->withToken($this->personalApiKey);
    }

    /** @return array<string, mixed> */
    protected function send(Response $response): array
    {
        if ($response->failed()) {
            throw PostHogException::fromResponse($response);
        }

        return (array) ($response->json() ?? []);
    }
}
