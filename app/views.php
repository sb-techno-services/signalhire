<?php
declare(strict_types=1);

function layout(string $title, string $body): void
{
    global $user;
    $notice = $_SESSION['notice'] ?? '';
    unset($_SESSION['notice']);
    require __DIR__ . "/templates/layout.php";
}

function formStart(string $page, array $params = [], bool $upload = false): void
{
    ?>
<form method="post" action="<?= esc(url($page,$params)) ?>"<?= $upload ? ' enctype="multipart/form-data"' : '' ?>>
<input type="hidden" name="csrf" value="<?= esc($_SESSION['csrf']) ?>">
<?php
}

function options(array $choices, string $selected): void
{
    foreach ($choices as $option): ?>
<option<?= $option === $selected ? ' selected' : '' ?>><?= esc($option) ?></option>
<?php endforeach;
}

function heading(string $label, string $title, string $description = ''): void
{
    ?><div class="heading"><p class="eyebrow"><?= esc($label) ?></p><h1><?= esc($title) ?></h1>
<?php if ($description): ?><p><?= esc($description) ?></p><?php endif; ?></div><?php
}

function uploadForm(array $params = []): void
{
    formStart('upload-cv',$params,true); ?>
<label>CV file <span class="hint">PDF or UTF-8 text, up to 2 MB</span>
<input type="file" name="cv" accept=".pdf,.txt" required></label>
<button>Upload CV</button></form>
<?php
}
