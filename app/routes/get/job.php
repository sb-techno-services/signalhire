<?php
declare(strict_types=1);

$job = one("SELECT j.*,c.name AS company,c.description AS company_description,c.location AS company_location,d.website,d.benefits FROM jobs j
    JOIN companies c ON c.id=j.company_id LEFT JOIN company_details d ON d.company_id=c.id WHERE j.id=? AND j.status!='Draft'", [$id]);
if (!$job) {
    fail(404,'Vacancy not found.');
}
$params = ['id'=>$id];
$applied = $user && $user['role'] === 'candidate' &&
    one('SELECT id FROM applications WHERE job_id=? AND candidate_id=?', [$id,$user['id']]);
$cv = $user && $user['role'] === 'candidate'
    ? one('SELECT filename FROM cvs WHERE candidate_id=? AND current=1', [$user['id']]) : false;
return require __DIR__ . "/../../templates/job.php";
