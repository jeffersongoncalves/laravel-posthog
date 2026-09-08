<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Host
    |--------------------------------------------------------------------------
    |
    | PostHog instance URL. Use https://us.i.posthog.com or
    | https://eu.i.posthog.com for PostHog Cloud, or your own URL when
    | self-hosting.
    |
    */
    'host' => env('POSTHOG_HOST', 'https://app.posthog.com'),

    /*
    |--------------------------------------------------------------------------
    | Project API Key
    |--------------------------------------------------------------------------
    |
    | Public write-only key (phc_...) sent inside the payload of ingestion
    | calls: capture, batch and feature flag evaluation.
    |
    */
    'project_api_key' => env('POSTHOG_PROJECT_API_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Personal API Key
    |--------------------------------------------------------------------------
    |
    | Private key (phx_...) sent as a Bearer token when reading from the
    | project API: persons, HogQL queries, insights and session recordings.
    | Never expose this one to the browser.
    |
    */
    'personal_api_key' => env('POSTHOG_PERSONAL_API_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Project ID
    |--------------------------------------------------------------------------
    |
    | Numeric project id used to build /api/projects/{id}/... URLs. Only
    | needed for the read endpoints that use the personal API key.
    |
    */
    'project_id' => env('POSTHOG_PROJECT_ID', ''),

    /*
    |--------------------------------------------------------------------------
    | Request Timeout
    |--------------------------------------------------------------------------
    |
    | Seconds to wait on a PostHog HTTP request before giving up.
    |
    */
    'timeout' => env('POSTHOG_TIMEOUT', 10),

];
