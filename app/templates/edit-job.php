<p><a href="<?= url('workspace') ?>">← Recruiter workspace</a></p>
<?php heading('RECRUITER',$title,$job['status'] ?? 'Your vacancy starts as a private draft.'); ?>
<div class="split"><section class="panel">
<?php formStart('edit-job',$params); ?>
<label>Job title<input name="title" maxlength="140" value="<?= esc($job['title'] ?? '') ?>" required></label>
<label>Job location<input name="location" maxlength="100" value="<?= esc($job['location'] ?? '') ?>" required></label>
<div class="fields"><label>Employment type<select name="employment"><?php options(EMPLOYMENT,$job['employment'] ?? ''); ?></select></label>
<label>Work arrangement<select name="arrangement"><?php options(ARRANGEMENTS,$job['arrangement'] ?? ''); ?></select></label></div>
<label>Pay (£) <span class="hint">Annual salary for permanent jobs, day rate for contracts</span>
<input name="pay" type="number" min="1" max="9999999" step="1" value="<?= esc($job['pay'] ?? '') ?>" required></label>
<label>Job description<textarea name="description" maxlength="8000" rows="8" required><?= esc($job['description'] ?? '') ?></textarea></label>
<button><?= $id ? 'Save changes' : 'Save draft' ?></button></form></section>
<?php if ($id): ?><aside class="panel"><h2>Vacancy status</h2><p><span class="badge"><?= esc($job['status']) ?></span></p>
<?php if ($job['status'] !== 'Closed'):
    $draft = $job['status'] === 'Draft';
    formStart('job-state',$params); ?>
<p><?= $draft ? 'Publish the saved draft to make it searchable.' : 'Closing removes this vacancy from search and stops new applications.' ?></p>
<button name="status" value="<?= $draft ? 'Published' : 'Closed' ?>"><?= $draft ? 'Publish saved vacancy' : 'Close vacancy' ?></button></form>
<?php endif; ?>
<p><a href="<?= esc(url('applicants',$params)) ?>">View applicants</a></p>
<?php if ($job['status'] !== 'Draft'): ?><p><a href="<?= esc(url('job',$params)) ?>">View candidate page</a></p><?php endif; ?>
</aside><?php endif; ?></div>
<?php return $title;
