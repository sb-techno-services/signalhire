<section class="panel narrow"><?php formStart('discovery'); ?>
<label>Skills <input name="skills" maxlength="500" value="<?= esc($settings['skills']) ?>" placeholder="Java, AWS, Terraform"></label>
<label class="check"><input type="checkbox" name="searchable" value="1" <?= $settings['searchable'] ? 'checked' : '' ?>>
Show my name, location, skills and professional summary to signed-in recruiters</label>
<p class="hint">Your CV and contact details stay private. You can turn discovery off here at any time.</p>
<button>Save discovery settings</button></form></section>
<?php return 'Recruiter discovery';
