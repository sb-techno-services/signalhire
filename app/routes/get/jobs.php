<?php
declare(strict_types=1);

$keyword = input($_GET,'keyword');
$location = input($_GET,'location');
$type = input($_GET,'employment');
$arrangement = input($_GET,'arrangement');
if (strlen($keyword) > 140 || strlen($location) > 100 || !in_array($type,['',...EMPLOYMENT],true)
    || !in_array($arrangement,['',...ARRANGEMENTS],true)) {
    fail(422,'Check the search filters.');
}
$jobs = namedQuery('search-jobs',[
    'keyword'=>$keyword,'location'=>$location,'employment'=>$type,'arrangement'=>$arrangement,
])->fetchAll();
return require __DIR__ . '/../../templates/jobs.php';
