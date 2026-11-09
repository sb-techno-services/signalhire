<?php
declare(strict_types=1);

$application = accessibleApplication($id);
$messages = query('SELECT m.body,m.sent_at,u.name FROM messages m JOIN users u ON u.id=m.sender_id
                   WHERE m.application_id=? ORDER BY m.id', [$id])->fetchAll();
$timeline = query('SELECT status,changed_at FROM application_events WHERE application_id=? ORDER BY id DESC', [$id])->fetchAll();
heading('APPLICATION',$application['title'],$application['company']);
return require __DIR__ . "/../../templates/thread.php";
