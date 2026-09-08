<?php

namespace JeffersonGoncalves\PostHog\Exceptions;

use Illuminate\Http\Client\Response;
use RuntimeException;

class PostHogException extends RuntimeException
{
    /** @var array<string, mixed> */
    protected array $errorBody = [];

    public static function fromResponse(Response $response): self
    {
        $body = (array) ($response->json() ?? []);

        // Ingestion endpoints answer with "error"; the project API uses
        // "detail" (DRF) and occasionally "message" for HogQL failures.
        $message = $body['detail']
            ?? $body['message']
            ?? $body['error']
            ?? "PostHog API error (HTTP {$response->status()}).";

        $exception = new self((string) $message, $response->status());
        $exception->errorBody = $body;

        return $exception;
    }

    /** @return array<string, mixed> */
    public function errorBody(): array
    {
        return $this->errorBody;
    }
}
