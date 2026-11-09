<?php
declare(strict_types=1);

function accessibleApplication(int $id): array
{
    global $user;
    if (!$user) {
        fail(401,'Sign in to access this application.');
    }
    $application = namedQuery('application-participant',[
        'application'=>$id,'role'=>$user['role'],'user'=>$user['id'],
    ])->fetch();
    return $application ?: fail(404,'Application not found.');
}

function sendMessage(int $id): void
{
    global $user;
    $application = accessibleApplication($id);
    query('INSERT INTO messages(application_id,sender_id,body) VALUES(?,?,?)',
        [$application['id'],$user['id'],field('message',4000)]);
    redirect('thread','Message sent.',['id'=>$id]);
}
