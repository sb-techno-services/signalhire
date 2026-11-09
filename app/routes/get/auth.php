<?php
declare(strict_types=1);

if ($user) {
    redirect($user['role'] === 'recruiter' ? 'workspace' : 'jobs');
}
$register = $page === 'register';
$title = $register ? 'Create your account' : 'Sign in';
$params = $id ? ['id'=>$id] : [];
heading('YOUR SIGNALHIRE ACCOUNT',$title);
return require __DIR__ . "/../../templates/auth.php";
