<?php
declare(strict_types=1);

requireRole('recruiter');
$application = ownedApplication($id);
query('UPDATE applications SET status=?,updated_at=CURRENT_TIMESTAMP WHERE id=?',
    [choice('status', STATUSES),$application['id']]);
redirect('applicants', 'Application status updated.', ['id'=>$application['job_id']]);
