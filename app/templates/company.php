<section class="panel narrow">
<?php formStart('company'); ?>
<label>Company name<input name="name" maxlength="140" value="<?= esc($company['name'] ?? '') ?>" required></label>
<label>Base location<input name="location" maxlength="100" value="<?= esc($company['location'] ?? '') ?>" required></label>
<label>About your company<textarea name="description" maxlength="4000" rows="5" required><?= esc($company['description'] ?? '') ?></textarea></label>
<label>Website<input name="website" type="url" maxlength="300" value="<?= esc($details['website'] ?? '') ?>"></label>
<label>Benefits<textarea name="benefits" maxlength="2000" rows="4"><?= esc($details['benefits'] ?? '') ?></textarea></label>
<button>Save company profile</button></form></section>
<?php return 'Company profile';
