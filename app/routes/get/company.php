<?php
declare(strict_types=1);

requireRole('recruiter');
$company = company();
$details = $company ? one('SELECT * FROM company_details WHERE company_id=?', [$company['id']]) : [];
heading('RECRUITER','Company profile','These details are shown on your published vacancies.');
return require __DIR__ . "/../../templates/company.php";
