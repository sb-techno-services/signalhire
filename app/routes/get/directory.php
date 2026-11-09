<?php
declare(strict_types=1);

requireRole('recruiter');
$keyword = input($_GET,'keyword');
$location = input($_GET,'location');
if (strlen($keyword)>140 || strlen($location)>100) {
    fail(422,'Check the search filters.');
}
$people = directorySearch($keyword,$location);
heading('RECRUITER','Candidate directory','Find candidates who have chosen to share their professional profile.');
return require __DIR__ . "/../../templates/directory.php";
