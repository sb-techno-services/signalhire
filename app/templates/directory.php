<section class="panel"><form method="get" action="/" class="filters">
<input type="hidden" name="page" value="directory">
<label>Skills or keyword<input name="keyword" maxlength="140" value="<?= esc($keyword) ?>"></label>
<label>Location<input name="location" maxlength="100" value="<?= esc($location) ?>"></label><button>Find candidates</button></form></section>
<h2 class="section-title">Matching profiles</h2>
<?php if (!$people): ?><p class="panel">No profiles match these filters.</p><?php endif; ?>
<div class="cards"><?php foreach ($people as $person): ?>
<article class="panel"><h3><?= esc($person['name']) ?></h3><p class="meta"><?= esc($person['location']) ?></p>
<p><strong><?= esc($person['skills']) ?></strong></p><p class="prose"><?= esc($person['summary']) ?></p></article>
<?php endforeach; ?></div>
<?php return 'Candidate directory';
