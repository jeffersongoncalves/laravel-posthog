<?php

use Illuminate\Http\Client\Response;
use JeffersonGoncalves\PostHog\Exceptions\PostHogException;

function posthogResponse(array $body, int $status): Response
{
    return new Response(new GuzzleHttp\Psr7\Response($status, [], (string) json_encode($body)));
}

it('prefers detail, then message, then error', function (array $body, string $expected) {
    expect(PostHogException::fromResponse(posthogResponse($body, 400))->getMessage())
        ->toBe($expected);
})->with([
    [['detail' => 'from detail', 'message' => 'from message', 'error' => 'from error'], 'from detail'],
    [['message' => 'from message', 'error' => 'from error'], 'from message'],
    [['error' => 'from error'], 'from error'],
]);

it('falls back to the status code and exposes the error body', function () {
    $exception = PostHogException::fromResponse(posthogResponse(['type' => 'validation_error'], 422));

    expect($exception->getMessage())->toBe('PostHog API error (HTTP 422).')
        ->and($exception->getCode())->toBe(422)
        ->and($exception->errorBody())->toBe(['type' => 'validation_error']);
});
