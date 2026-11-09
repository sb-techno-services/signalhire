<?php
declare(strict_types=1);

requireRole('recruiter');
if (array_key_exists('id', $_GET) && !$id) {
    fail(404, 'Job not found.');
}
saveJob($id);
