<?php
declare(strict_types=1);

requireRole('candidate');
$rows = query('SELECT j.id,j.title,j.status,c.name FROM saved_jobs s JOIN jobs j ON j.id=s.job_id
               JOIN companies c ON c.id=j.company_id WHERE s.candidate_id=? ORDER BY s.saved_at DESC,j.id DESC', [$user['id']])->fetchAll();
heading('CANDIDATE','Saved jobs','Keep vacancies on hand while you decide where to apply.');
return require __DIR__ . "/../../templates/saved.php";
