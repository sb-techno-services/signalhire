<p><a href="<?= url('workspace') ?>">← Recruiter workspace</a></p>
<?php heading('APPLICANTS',$job['title'],count($rows) . ' received'); ?>
<?php if (!$rows): ?><p class="panel">No applications received yet.</p><?php endif; ?>
<div class="list"><?php foreach ($rows as $row): ?>
<article class="panel row"><div><h2><?= esc($row['name']) ?></h2><p class="meta"><?= esc($row['email']) ?></p>
<a href="<?= esc(url('cv',['id'=>$row['id']])) ?>">Download <?= esc($row['filename']) ?></a><p><a href="<?= esc(url('thread',['id'=>$row['id']])) ?>">Messages and status history</a></p></div>
<?php formStart('application-status',['id'=>$row['id']]); ?>
<label>Application status<select name="status"><?php options(STATUSES,$row['status']); ?></select></label>
<button class="small">Update status</button></form></article>
<?php endforeach; ?></div>
<?php return 'Applicants';
