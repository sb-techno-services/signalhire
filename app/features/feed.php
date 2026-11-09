<?php
declare(strict_types=1);

function publicFeed(): void
{
    $jobs = namedQuery('public-feed')->fetchAll();
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(['version'=>1,'currency'=>'GBP','jobs'=>$jobs],JSON_THROW_ON_ERROR | JSON_INVALID_UTF8_SUBSTITUTE);
}
