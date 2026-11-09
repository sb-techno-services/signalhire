<?php
declare(strict_types=1);

session_regenerate_id(true);
$_SESSION = [];
redirect('jobs', 'You’re signed out.');
