<?php
declare(strict_types=1);

requireRole('candidate');
query('UPDATE users SET name=?,location=?,summary=? WHERE id=?',
    [field('name',100),field('location',100,false),field('summary',2000,false),$user['id']]);
redirect('profile', 'Profile saved.');
