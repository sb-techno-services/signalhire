<?php
declare(strict_types=1);

function saveAlert(): void
{
    global $user;
    requireRole('candidate');
    query('INSERT INTO alerts(candidate_id,keyword,location,employment,after_event)
           SELECT ?,?,?,?,COALESCE(MAX(id),0) FROM publication_events WHERE true
           ON CONFLICT(candidate_id,keyword,location,employment) DO NOTHING',
        [$user['id'],field('keyword',140,false),field('location',100,false),choice('employment',['',...EMPLOYMENT])]);
    redirect('alerts','Job alert saved. It will match new publications.');
}

function removeAlert(int $id): void
{
    global $user;
    requireRole('candidate');
    if (!query('DELETE FROM alerts WHERE id=? AND candidate_id=?', [$id,$user['id']])->rowCount()) {
        fail(404,'Alert not found.');
    }
    redirect('alerts','Alert removed.');
}

function matchAlerts(?int $candidateId = null): int
{
    return namedQuery('match-alerts',['candidate'=>$candidateId])->rowCount();
}
