<section class="hero">
<?php heading('THE UK JOB BOARD','Find your next role','Explore permanent and contract vacancies across the UK.'); ?>
<form method="get" action="/" class="filters">
<label>Keyword<input name="keyword" value="<?= esc($keyword) ?>" maxlength="140" placeholder="Java, AWS, design"></label>
<label>Location<input name="location" value="<?= esc($location) ?>" maxlength="100" placeholder="London or remote"></label>
<label>Employment type<select name="employment"><option value="">All types</option><?php options(EMPLOYMENT,$type); ?></select></label>
<label>Work arrangement<select name="arrangement"><option value="">Any arrangement</option><?php options(ARRANGEMENTS,$arrangement); ?></select></label>
<button>Search jobs</button>
</form></section>
<section aria-labelledby="results"><div class="section-title"><h2 id="results">Open vacancies</h2>
<span><?= count($jobs) ?> <?= count($jobs) === 1 ? 'result' : 'results' ?></span></div>
<?php if (!$jobs): ?><p class="panel">No vacancies match these filters. <a href="/">Clear filters</a></p><?php endif; ?>
<div class="cards"><?php foreach ($jobs as $job): ?>
<article class="panel card"><p class="eyebrow"><?= esc($job['company']) ?></p>
<h3><a href="<?= esc(url('job',['id'=>$job['id']])) ?>"><?= esc($job['title']) ?></a></h3>
<p class="meta"><?= esc($job['location']) ?> · <?= esc($job['arrangement']) ?> · <?= esc($job['employment']) ?></p>
<p class="pay"><?= esc(payLabel($job)) ?></p></article>
<?php endforeach; ?></div></section>
<?php return 'Find jobs';
