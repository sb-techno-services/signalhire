<?php
declare(strict_types=1);

function directoryPreferences(): array
{
    global $user;
    return one('SELECT searchable,skills FROM candidate_visibility WHERE candidate_id=?', [$user['id']])
        ?: ['searchable'=>0,'skills'=>''];
}

function saveDirectoryPreferences(): void
{
    global $user;
    requireRole('candidate');
    $skills = field('skills',500,false);
    $visible = input($_POST,'searchable') === '1';
    if ($visible && (!$skills || !$user['summary'])) {
        fail(422,'Add skills and a professional summary before enabling discovery.');
    }
    query('INSERT INTO candidate_visibility(candidate_id,searchable,skills) VALUES(?,?,?)
           ON CONFLICT(candidate_id) DO UPDATE SET searchable=excluded.searchable,skills=excluded.skills',
        [$user['id'],(int) $visible,$skills]);
    redirect('discovery','Discovery settings saved.');
}

function directorySearch(string $keyword, string $location): array
{
    return namedQuery('search-candidates',['keyword'=>$keyword,'location'=>$location])->fetchAll();
}
