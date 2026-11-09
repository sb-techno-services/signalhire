<?php
declare(strict_types=1);

requireRole('candidate');
$cv = one('SELECT filename FROM cvs WHERE candidate_id=? AND current=1', [$user['id']]);
heading('CANDIDATE','My profile','Manage your details and the CV you use for future applications.');
return require __DIR__ . "/../../templates/profile.php";
