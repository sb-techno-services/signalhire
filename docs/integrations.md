# External integrations

## Public job feed

Request `GET /?page=feed` for the latest 500 published vacancies in descending job ID order. The JSON object contains `version`, `currency` (GBP) and `jobs`. Each job includes the public ID, title, location, employment, work arrangement, pay, description, publication timestamp and company name. Interpret pay as annual for permanent work and daily for contracts. Retrieve the feed again to reflect edits and removals. Drafts, closed adverts and applicant data are excluded.

## Publication webhooks

A local operator can dispatch events to an HTTPS receiver. Loopback HTTP is accepted for local development. Use a fresh secret and set both variables in the worker environment.

```sh
export SIGNALHIRE_WEBHOOK_URL='https://receiver.example.test/signalhire'
export SIGNALHIRE_WEBHOOK_SECRET='replace-with-a-long-secret-from-your-secret-store'
php bin/dispatch-webhooks.php
```

The dispatcher sends up to 100 pending events per invocation, ordered by event ID. Body keys are `id`, `event`, `job_id` and `created_at`. Event names are `job.published` and `job.closed`. Existing adverts seeded or published before the event migration do not generate retroactive events. Refer to the feed for the current public advert.

Verify `X-SignalHire-Signature` by calculating the hex HMAC-SHA256 of the raw request body and comparing against the header's `sha256=` value using a constant-time comparison. Use `X-SignalHire-Event-ID` as an idempotency key per source installation. Respond with HTTP 2xx after accepting the event. Failures stop the batch and remain queued for a later run. Retries or concurrent workers can deliver the same event more than once, so the receiver must deduplicate. Successful events are remembered per receiver URL.
