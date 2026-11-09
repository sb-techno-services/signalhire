<?php
declare(strict_types=1);

foreach (['directory','saved','alerts','messages','billing','company','feed'] as $feature) {
    require __DIR__ . '/' . $feature . '.php';
}
