<?php if (!$company): ?><p class="panel"><a href="<?= url('company') ?>">Create a company profile</a> to use the billing sandbox.</p>
<?php return 'Demo billing'; endif;
    ?>
<div class="split"><section class="panel"><h2><?= $entries ? esc(array_sum(array_column($entries,'amount'))) . ' demo credits' : 'Free posting mode' ?></h2>
<p>Adding credits activates the sandbox for your company. Each new publication then costs one demo credit.</p>
<?php formStart('credits-add'); ?><button>Add 10 demo credits</button></form></section>
<section class="panel"><h2>Credit activity</h2>
<?php if (!$entries): ?><p>No credit activity yet.</p><?php endif; ?>
<?php foreach ($entries as $entry): ?>
<p><strong><?= esc(($entry['amount'] > 0 ? '+' : '') . $entry['amount']) ?></strong> <?= esc($entry['description']) ?>
<?= $entry['title'] ? ' · ' . esc($entry['title']) : '' ?><br><span class="meta"><?= esc($entry['recorded_at']) ?> UTC</span></p>
<?php endforeach; ?></section></div>
<?php return 'Demo billing';
