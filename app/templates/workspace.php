<?php if (!$company): ?><section class="panel"><h2>Create a company profile</h2><p>Add the details candidates will see on your vacancies.</p>
<a class="button" href="<?= url('company') ?>">Set up company</a></section><?php return 'Recruiter workspace'; endif;
    ?><div class="section-title"><h2>Your vacancies</h2><a class="button" href="<?= url('edit-job') ?>">New vacancy</a></div>
<?php if (!$jobs): ?><p class="panel">No vacancies yet. Create a draft to get started.</p><?php endif; ?>
<div class="list"><?php foreach ($jobs as $job): $params = ['id'=>$job['id']]; ?>
<article class="panel row"><div><span class="badge"><?= esc($job['status']) ?></span>
<h3><?= esc($job['title']) ?></h3><p class="meta"><?= esc($job['location']) ?> · <?= esc($job['employment']) ?></p></div>
<div class="actions"><a href="<?= esc(url('edit-job',$params)) ?>">Edit</a>
<a href="<?= esc(url('applicants',$params)) ?>">Applicants (<?= esc($job['total']) ?>)</a></div></article>
<?php endforeach; ?></div>
<?php return 'Recruiter workspace';
