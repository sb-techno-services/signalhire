<?php
declare(strict_types=1);
require dirname(__DIR__) . '/app/core.php';
require dirname(__DIR__) . '/app/features/webhooks.php';

$endpoint = getenv('SIGNALHIRE_WEBHOOK_URL') ?: '';
$secret = getenv('SIGNALHIRE_WEBHOOK_SECRET') ?: '';
$parts = parse_url($endpoint);
if (!filter_var($endpoint,FILTER_VALIDATE_URL) || isset($parts['user']) || isset($parts['pass']) || isset($parts['fragment'])
    || !(($parts['scheme'] ?? '') === 'https'
        || (($parts['scheme'] ?? '') === 'http' && ($parts['host'] ?? '') === '127.0.0.1'))
    || strlen($secret) < 24) {
    fwrite(STDERR,"Set SIGNALHIRE_WEBHOOK_URL to HTTPS or loopback HTTP and SIGNALHIRE_WEBHOOK_SECRET to 24 or more characters.\n");
    exit(2);
}
$db = database();
$consumer = hash('sha256',$endpoint);
$count = 0;
foreach (webhookEvents($consumer) as $event) {
    if (!sendWebhook($endpoint,$secret,$event)) {
        fwrite(STDERR,"Delivery stopped at event {$event['id']}. Retry to resume.\n");
        exit(1);
    }
    query('INSERT INTO webhook_deliveries(consumer,event_id) VALUES(?,?) ON CONFLICT DO NOTHING', [$consumer,$event['id']]);
    $count++;
}
echo "$count events delivered.\n";
