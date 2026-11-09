<?php if (!$rows): ?><p class="panel">You haven’t applied for a vacancy yet. <a href="/">Find jobs</a></p><?php endif; ?>
<div class="list"><?php foreach ($rows as $row): ?>
<article class="panel row"><div><h2><a href="<?= esc(url('job',['id'=>$row['job_id']])) ?>"><?= esc($row['title']) ?></a></h2>
<p class="meta"><?= esc($row['company']) ?> · Applied <?= esc(gmdate('j M Y',strtotime($row['submitted_at'].' UTC'))) ?></p></div>
<div><span class="badge"><?= esc($row['status']) ?></span><p><a href="<?= esc(url('thread',['id'=>$row['id']])) ?>">Messages and status history</a></p></div></article>
<?php endforeach; ?></div>
<?php return 'My applications';
