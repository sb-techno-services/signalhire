<div class="list"><?php foreach ($rows as $row): ?>
<article class="panel row"><div><h2><a href="<?= esc(url('job',['id'=>$row['id']])) ?>"><?= esc($row['title']) ?></a></h2>
<p><?= esc($row['name']) ?> · <?= esc($row['status']) ?></p></div>
<?php formStart('save-job',['id'=>$row['id']]); ?><button name="operation" value="remove">Remove</button></form></article>
<?php endforeach; ?></div>
<?php if (!$rows): ?><p class="panel">You haven’t saved a vacancy yet. <a href="/">Find jobs</a></p><?php endif; ?>
<?php return 'Saved jobs';
