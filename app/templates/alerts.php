<div class="split"><section class="panel"><h2>New alert</h2>
<?php formStart('alert-add'); ?>
<label>Keyword<input name="keyword" maxlength="140" placeholder="Java"></label>
<label>Location<input name="location" maxlength="100" placeholder="London"></label>
<label>Employment type<select name="employment"><option value="">All types</option><?php options(EMPLOYMENT,''); ?></select></label>
<button>Save alert</button></form></section>
<section class="panel"><h2>Active alerts</h2>
<?php if (!$alerts): ?><p>No alerts yet.</p><?php endif; ?>
<?php foreach ($alerts as $alert): ?>
<div class="row"><p><?= esc(($alert['keyword'] ?: 'Any keyword') . ' · ' . ($alert['location'] ?: 'Any location') . ' · ' . ($alert['employment'] ?: 'All types')) ?></p>
<?php formStart('alert-remove',['id'=>$alert['id']]); ?><button class="small">Remove</button></form></div>
<?php endforeach; ?></section></div>
<h2 class="section-title">Matches</h2><div class="list">
<?php if (!$matches): ?><p class="panel">No new vacancies match your alerts.</p><?php endif; ?>
<?php foreach ($matches as $match): ?>
<article class="panel"><h3><a href="<?= esc(url('job',['id'=>$match['id']])) ?>"><?= esc($match['title']) ?></a></h3>
<p><?= esc($match['location']) ?> · <?= esc($match['employment']) ?></p></article>
<?php endforeach; ?></div>
<?php return 'Job alerts';
