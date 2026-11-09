<p><a href="/">← All vacancies</a></p>
<?php heading($job['company'],$job['title'],$job['location'] . ' · ' . $job['arrangement'] . ' · ' . $job['employment']); ?>
<div class="split"><article class="panel"><p class="pay"><?= esc(payLabel($job)) ?></p>
<h2>About the role</h2><p class="prose"><?= esc($job['description']) ?></p>
<h2>About <?= esc($job['company']) ?></h2><p class="meta">Based in <?= esc($job['company_location']) ?></p><p class="prose"><?= esc($job['company_description']) ?></p>
<?php if ($job['website']): ?><p><a href="<?= esc($job['website']) ?>" rel="noreferrer">Company website</a></p><?php endif; ?>
<?php if ($job['benefits']): ?><h2>Benefits</h2><p class="prose"><?= esc($job['benefits']) ?></p><?php endif; ?></article>
<aside class="panel"><h2>Apply for this role</h2>
<?php if ($applied): ?>
<p>You’ve already applied.</p><a href="<?= url('applications') ?>">Track your application</a>
<?php elseif ($job['status'] === 'Closed'): ?>
<p>This vacancy is closed.</p>
<?php elseif (!$user): ?>
<p>Sign in or create a candidate account to apply.</p>
<p><a class="button" href="<?= esc(url('login',$params)) ?>">Sign in</a></p><a href="<?= esc(url('register',$params)) ?>">Create an account</a>
<?php elseif ($user['role'] === 'recruiter'): ?>
<p>Applications are submitted from candidate accounts.</p>
<?php elseif (!$cv): ?>
<p>Upload your CV to continue. Your CV is shared with the hiring company when you apply.</p>
<?php uploadForm($params); ?>
<?php else: ?>
<p>CV to submit <strong><?= esc($cv['filename']) ?></strong></p>
<?php formStart('apply',$params); ?><button>Submit application</button></form>
<p><a href="<?= url('profile') ?>">Change your CV</a></p>
<?php endif; ?>
<?php if ($user && $user['role'] === 'candidate' && $job['status'] === 'Published'): ?>
<hr><?php formStart('save-job',$params); ?><button name="operation" value="save">Save vacancy</button></form>
<?php endif; ?></aside></div>
<?php return $job['title'];
