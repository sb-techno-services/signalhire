<?php
declare(strict_types=1);

requireRole('recruiter');
$company = company();
$entries = $company ? namedQuery('company-credit-history',['company'=>$company['id']])->fetchAll() : [];
heading('RECRUITER','Demo billing','Try a credit-based posting workflow using fictional credits.');
return require __DIR__ . "/../../templates/billing.php";
