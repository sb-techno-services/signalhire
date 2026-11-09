<?php
declare(strict_types=1);

requireRole('candidate');
matchAlerts($user['id']);
$alerts = query('SELECT * FROM alerts WHERE candidate_id=? ORDER BY id DESC', [$user['id']])->fetchAll();
$matches = query("SELECT DISTINCT j.id,j.title,j.location,j.employment FROM alert_matches m
                  JOIN alerts a ON a.id=m.alert_id JOIN jobs j ON j.id=m.job_id
                  WHERE a.candidate_id=? AND j.status='Published' ORDER BY j.id DESC", [$user['id']])->fetchAll();
heading('CANDIDATE','Job alerts','Save a search and check here for new matching vacancies.');
return require __DIR__ . "/../../templates/alerts.php";
