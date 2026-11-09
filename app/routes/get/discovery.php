<?php
declare(strict_types=1);

requireRole('candidate');
$settings = directoryPreferences();
heading('CANDIDATE','Recruiter discovery','Choose whether recruiters can find your profile in the candidate directory.');
return require __DIR__ . "/../../templates/discovery.php";
