<?php
declare(strict_types=1);

function saveVacancy(int $id): void
{
    global $user;
    requireRole('candidate');
    if (choice('operation',['save','remove']) === 'remove') {
        query('DELETE FROM saved_jobs WHERE candidate_id=? AND job_id=?', [$user['id'],$id]);
        redirect('saved','Vacancy removed from your list.');
    }
    $saved = query("INSERT INTO saved_jobs(candidate_id,job_id) SELECT ?,id FROM jobs WHERE id=? AND status='Published'
                   ON CONFLICT(candidate_id,job_id) DO NOTHING", [$user['id'],$id]);
    if (!$saved->rowCount() && !one('SELECT 1 FROM saved_jobs WHERE candidate_id=? AND job_id=?', [$user['id'],$id])) {
        fail(404,'Open vacancy not found.');
    }
    redirect('saved','Vacancy saved.');
}
