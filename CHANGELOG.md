# Changelog

All notable changes to this project will be documented in this file.

## 1.0.0 - 2026-09-08

First release.

- Event capture and batch ingestion
- `identify` and `alias` helpers over the capture endpoint
- Feature flags: all flags for a person, single flag value (including multivariate variants) and boolean check, with group support
- Person lookup by `distinct_id`
- HogQL queries, insights and session recordings
- Handles PostHog's two auth models: project API key in the payload for ingestion, personal API key as a Bearer token for the project API
- `PostHogException` carrying the original API error body on any non-2xx response

## [Unreleased]
