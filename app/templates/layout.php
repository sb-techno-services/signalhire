<!doctype html>
<html lang="en-GB">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= esc($title) ?> | SignalHire</title><link rel="stylesheet" href="/style.css"></head>
<body>
<a class="skip" href="#main">Skip to content</a>
<header class="top"><div class="wrap nav">
<a class="brand" href="/"><span aria-hidden="true">S</span> SignalHire</a>
<nav aria-label="Main navigation"><a href="/">Find jobs</a>
<?php if (!$user): ?>
<a href="<?= url('login') ?>">Sign in</a><a class="button small" href="<?= url('register') ?>">Create account</a>
<?php elseif ($user['role'] === 'candidate'): ?>
<a href="<?= url('applications') ?>">Applications</a><a href="<?= url('saved') ?>">Saved jobs</a><a href="<?= url('alerts') ?>">Alerts</a><a href="<?= url('profile') ?>">My profile</a>
<?php else: ?>
<a href="<?= url('workspace') ?>">Recruiter workspace</a><a href="<?= url('company') ?>">Company profile</a><a href="<?= url('directory') ?>">Candidate directory</a><a href="<?= url('billing') ?>">Billing</a>
<?php endif; ?>
<?php if ($user): formStart('logout'); ?><button class="quiet">Sign out</button></form><?php endif; ?>
</nav></div></header>
<main class="wrap main" id="main">
<?php if ($notice): ?><p class="notice" role="status"><?= esc($notice) ?></p><?php endif; ?>
<?= $body ?>
</main>
<footer class="wrap footer"><span>SignalHire</span><span>UK recruitment workspace</span></footer>
</body></html>
