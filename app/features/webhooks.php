<?php
declare(strict_types=1);

function webhookEvents(string $consumer): array
{
    return query('SELECT e.id,e.event,e.job_id,e.created_at FROM publication_events e
                  WHERE NOT EXISTS (SELECT 1 FROM webhook_deliveries d WHERE d.consumer=? AND d.event_id=e.id)
                  ORDER BY e.id LIMIT 100', [$consumer])->fetchAll();
}

function sendWebhook(string $endpoint, string $secret, array $event): bool
{
    $body = json_encode($event,JSON_THROW_ON_ERROR);
    $signature = hash_hmac('sha256',$body,$secret);
    $context = stream_context_create(['http'=>[
        'method'=>'POST',
        'header'=>[
            'Content-Type: application/json',
            'X-SignalHire-Signature: sha256=' . $signature,
            'X-SignalHire-Event-ID: ' . $event['id'],
        ],
        'content'=>$body,
        'ignore_errors'=>true,
        'follow_location'=>0,
        'timeout'=>5,
    ]]);
    $stream = @fopen($endpoint,'rb',false,$context);
    if (!$stream) {
        return false;
    }
    $headers = stream_get_meta_data($stream)['wrapper_data'];
    fclose($stream);
    return preg_match('~^HTTP/\S+ 2[0-9]{2}(?: |$)~',endHttpStatus($headers)) === 1;
}

function endHttpStatus(array $headers): string
{
    foreach (array_reverse($headers) as $header) {
        if (str_starts_with($header,'HTTP/')) {
            return $header;
        }
    }
    return '';
}
