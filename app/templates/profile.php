<div class="split"><section class="panel"><h2>Profile details</h2>
<?php formStart('profile'); ?>
<label>Full name<input name="name" value="<?= esc($user['name']) ?>" maxlength="100" required></label>
<label>Location<input name="location" value="<?= esc($user['location']) ?>" maxlength="100"></label>
<label>Professional summary<textarea name="summary" maxlength="2000" rows="5"><?= esc($user['summary']) ?></textarea></label>
<button>Save profile</button></form><p><a href="<?= url('discovery') ?>">Recruiter discovery settings</a></p></section>
<section class="panel"><h2>My CV</h2><p><?= $cv ? 'Current file · ' . esc($cv['filename']) : 'No CV uploaded yet.' ?></p>
<p class="hint">A replacement CV applies to future submissions. Each existing application keeps its original CV.</p>
<?php uploadForm(); ?></section></div>
<?php return 'My profile';
