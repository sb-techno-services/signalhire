<?php
declare(strict_types=1);

requireRole('recruiter');
$job = ownedJob($id);
$rows = query('SELECT a.*,u.name,u.email,cv.filename FROM applications a JOIN users u ON u.id=a.candidate_id
               JOIN cvs cv ON cv.id=a.cv_id WHERE a.job_id=? ORDER BY a.id DESC', [$id])->fetchAll();
return require __DIR__ . "/../../templates/applicants.php";
