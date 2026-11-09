<?php
declare(strict_types=1);

requireRole('recruiter');
if (array_key_exists('id', $_GET) && !$id) {
    fail(404, 'Job not found.');
}
$job = $id ? ownedJob($id) : [];
if (!company()) {
    redirect('company', 'Create a company profile before drafting a vacancy.');
}
$title = $id ? 'Edit vacancy' : 'New vacancy';
$params = $id ? ['id'=>$id] : [];
return require __DIR__ . "/../../templates/edit-job.php";
