<?php
declare(strict_types=1);

requireRole('candidate');
$rows = query('SELECT a.*,j.title,c.name AS company FROM applications a JOIN jobs j ON j.id=a.job_id
               JOIN companies c ON c.id=j.company_id WHERE a.candidate_id=? ORDER BY a.id DESC', [$user['id']])->fetchAll();
heading('CANDIDATE','My applications','See the latest status shared by each hiring company.');
return require __DIR__ . "/../../templates/applications.php";
