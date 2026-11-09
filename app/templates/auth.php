<section class="panel narrow">
<?php formStart($page,$params); ?>
<?php if ($register): ?>
<label>Full name<input name="name" maxlength="100" autocomplete="name" required></label>
<label>Account type<select name="role"><option value="candidate">Candidate</option><option value="recruiter">Recruiter</option></select></label>
<?php endif; ?>
<label>Email address<input name="email" type="email" maxlength="254" autocomplete="email" required></label>
<label>Password<input name="password" type="password" maxlength="72" autocomplete="<?= $register ? 'new-password' : 'current-password' ?>"
<?= $register ? 'minlength="10"' : '' ?> required></label>
<button><?= esc($title) ?></button></form>
<p><a href="<?= esc(url($register ? 'login' : 'register',$params)) ?>"><?= $register ? 'Already registered? Sign in' : 'New here? Create an account' ?></a></p></section>
<?php return $title;
