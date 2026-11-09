<?php
declare(strict_types=1);
require dirname(__DIR__) . '/app/core.php';
require dirname(__DIR__) . '/app/features/alerts.php';
$db = database();
echo matchAlerts() . " new alert matches.\n";
